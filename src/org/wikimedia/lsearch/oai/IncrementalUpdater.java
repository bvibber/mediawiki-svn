package org.wikimedia.lsearch.oai;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.net.Authenticator;
import java.net.PasswordAuthentication;
import java.util.ArrayList;
import java.util.Properties;

import org.apache.log4j.Logger;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.index.IndexUpdateRecord;
import org.wikimedia.lsearch.interoperability.RMIMessengerClient;
import org.wikimedia.lsearch.util.Localization;
import org.wikimedia.lsearch.util.UnicodeDecomposer;

/**
 * Handles incremental updates using OAI-PMH interface.
 * 
 * @author rainman
 *
 */
public class IncrementalUpdater {
	static Logger log = Logger.getLogger(IncrementalUpdater.class);
	
	static public class OAIAuthenticator extends Authenticator {
		protected String username,password;
		
		public OAIAuthenticator(){
			Configuration config = Configuration.open();
			username = config.getString("OAI","username");
			password = config.getString("OAI","password");
			
		}
		
		@Override
		protected PasswordAuthentication getPasswordAuthentication() {
			if(username == null || password == null){
				log.error("OAI authentication error. Username/password pair not specified in configuration file.");
				return null;
			}
			return new PasswordAuthentication(username,password.toCharArray());
		}		
	}
	
	/** 
	 * Syntax:
	 * java IncrementalUpdater [-d] [-t timestamp] [-s sleep] dbname1 dbname2 ... 
	 * Options:
	 *   -d   - daemonize, otherwise runs only one round of updates to dbs
	 *   -s   - sleep time after one cycle (default: 3000ms)
	 *   -t   - default timestamp if status file is missing (default: 2001-01-01)
	 * 
	 * @param args
	 */
	public static void main(String[] args){
		ArrayList<String> dbnames = new ArrayList<String>();
		boolean daemon = false;
		long sleepTime = 3000;
		String timestamp = "2001-01-01";
		// args
		for(int i=0; i<args.length; i++){
			if(args[i].equals("-d"))
				daemon = true;
			else if(args[i].equals("-s"))
				sleepTime = Long.parseLong(args[++i]);
			else if(args[i].equals("-t"))
				timestamp = args[++i];
			else
				dbnames.add(args[i]);
		}		
		if(dbnames.size() == 0){
			System.out.println("Syntax: java IncrementalUpdater [-d] [-s sleep] [-t timestamp] dbname1 dbname2 ...");
			System.out.println("Options:");
			System.out.println("  -d   - daemonize, otherwise runs only one round of updates to dbs");
			System.out.println("  -s   - sleep time after one cycle (default: "+sleepTime+"ms)");
			System.out.println("  -t   - default timestamp if status file is missing (default: "+timestamp+")");
			return;
		}
		// config
		Configuration.open();
		GlobalConfiguration global = GlobalConfiguration.getInstance();		
		// preload
		UnicodeDecomposer.getInstance();
		for(String dbname: dbnames){
			Localization.readLocalization(global.getLanguage(dbname));
		}
		Localization.loadInterwiki();
		OAIAuthenticator auth = new OAIAuthenticator();
		
		// update
		do{
			main_loop: for(String dbname : dbnames){
				IndexId iid = IndexId.get(dbname);
				OAIHarvester harvester = new OAIHarvester(iid,iid.getOAIRepository(),auth);
				Properties status = new Properties();				
				// read timestamp from status file
				File statf = new File(iid.getStatusPath());
				try {										
					if(statf.exists()){					
						FileInputStream fileis = new FileInputStream(iid.getStatusPath());
						status.load(fileis);
						fileis.close();
					}
				} catch (IOException e) {
					log.warn("I/O error reading status file for "+iid+" at "+iid.getStatusPath()+" : "+e.getMessage());
				}				
				String from = status.getProperty("timestamp",timestamp);
				log.debug("Resuming update of "+iid+" from "+from);
				ArrayList<IndexUpdateRecord> records = harvester.getRecords(from);
				if(records.size() == 0)
					continue;
				boolean hasMore = false;
				do{
					// send to indexer
					RMIMessengerClient messenger = new RMIMessengerClient(true);
					try {
						log.info(iid+": Sending "+records.size()+" records to indexer");
						messenger.enqueueUpdateRecords(records.toArray(new IndexUpdateRecord[] {}),iid.getIndexHost());
					} catch (Exception e) {
						log.warn("Error sending index update records of "+iid+" to indexer at "+iid.getIndexHost());
						continue main_loop;
					}
					// more results?
					hasMore = harvester.hasMore();
					if(hasMore)
						records = harvester.getMoreRecords();
				} while(hasMore);
				
				// write updated timestamp
				status.setProperty("timestamp",harvester.getResponseDate());
				try {
					if(!statf.exists())
						statf.mkdirs();
					FileOutputStream fileos = new FileOutputStream(statf,false);
					status.store(fileos,"Last incremental update timestamp");
					fileos.close();
				} catch (IOException e) {
					log.warn("I/O error writing status file for "+iid+" at "+iid.getStatusPath()+" : "+e.getMessage());
				}				
			}
		if(daemon){
			try { // in case we are on a server that has very infrequent updates sleep for a while
				Thread.sleep(sleepTime);
			} catch (InterruptedException e) {
			}
		}
		} while(daemon);
	}
}
