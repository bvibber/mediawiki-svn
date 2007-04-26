/*
 * Created on Feb 1, 2007
 *
 */
package org.wikimedia.lsearch.config;

import java.util.HashSet;

import org.wikimedia.lsearch.frontend.HTTPIndexServer;
import org.wikimedia.lsearch.frontend.RPCIndexServer;
import org.wikimedia.lsearch.frontend.SearchServer;
import org.wikimedia.lsearch.interoperability.RMIServer;
import org.wikimedia.lsearch.search.NetworkStatusThread;
import org.wikimedia.lsearch.search.UpdateThread;
import org.wikimedia.lsearch.util.Localization;
import org.wikimedia.lsearch.util.UnicodeDecomposer;

/**
 * The main entry point for the program, reads configuration,
 * and initializes search, index and frontend threads
 * 
 * @author rainman
 *
 */
public class StartupManager {
	
	/** Program prameters: -configfile <file> */
	public static void main(String[] args) {
		RMIServer.createRegistry();
		
		for (int i =0; i < args.length; i++) {
			if (args[i].equals("-configfile")) {
				Configuration.setConfigFile(args[++i]);
			}
		}
		
		Configuration config = Configuration.open();
		GlobalConfiguration global = GlobalConfiguration.getInstance();
		// preload localizations
		HashSet<String> langCodes = new HashSet<String>();
		for(IndexId iid : global.getMyIndex())
			langCodes.add(global.getLanguage(iid.getDBname()));
		for(IndexId iid : global.getMySearch())
			langCodes.add(global.getLanguage(iid.getDBname()));
		Localization.readLocalizations(langCodes);
		Localization.loadInterwiki();
		
		RMIServer.bindRMIObjects(); 
		
		if(global.isIndexer()){
			// preload the unicode decomposer
			UnicodeDecomposer.getInstance();
			// start the fronend deamon if needed
			if(global.hasIndexFrontendDBs()){
				String daemon = config.getString("Index","daemon");
				// default is http server
				if(daemon == null || daemon.equalsIgnoreCase("http"))
					(new HTTPIndexServer()).start();
				else if(daemon.equalsIgnoreCase("xmlrpc"))
					(new RPCIndexServer()).start();
				else{
					System.out.println("Warning: Unknown server type \""+daemon+"\" for indexer, using http frontend.");
					(new HTTPIndexServer()).start();			
				}
			}
		}
		if(global.isSearcher()){
			(new SearchServer()).start();
			UpdateThread.getInstance().start(); // updater for local indexes
			NetworkStatusThread.getInstance().start(); // network monitor
		}
		
	}
}
