package org.wikimedia.lsearch.highlight;

import java.net.ServerSocket;
import java.net.Socket;
import java.util.HashSet;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;

import org.apache.log4j.Logger;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.util.Localization;
import org.wikimedia.lsearch.util.UnicodeDecomposer;

public class HighlightServer {
	public static void main(String args[]){
		int port = 8333;
		ServerSocket sock;
		
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
		// preload the unicode decomposer
		UnicodeDecomposer.getInstance();
		/** Logger */
		org.apache.log4j.Logger log = Logger.getLogger(HighlightServer.class);
		
		log.info("Binding server to port " + port);
		
		try {
			sock = new ServerSocket(port);
		} catch (Exception e) {
			log.fatal("Error: bind error: " + e.getMessage());
			return;
		}
		
		ExecutorService pool = Executors.newFixedThreadPool(25);
		
		for (;;) {
			Socket client;
			try {
				log.debug("Listening...");
				client = sock.accept();
			} catch (Exception e) {
				log.error("accept() error: " + e.getMessage());
				continue;
			}
			
			HighlightDaemon worker = new HighlightDaemon(client);
			pool.execute(worker);
		}
	}
}
