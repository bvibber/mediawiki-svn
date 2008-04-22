package org.wikimedia.lsearch.search;

import java.rmi.RemoteException;
import java.util.ArrayList;
import java.util.HashSet;
import java.util.Set;

import org.apache.log4j.Logger;
import org.wikimedia.lsearch.beans.SearchHost;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.interoperability.RMIMessenger;
import org.wikimedia.lsearch.interoperability.RMIMessengerClient;
import org.wikimedia.lsearch.search.SearcherCache.SearcherPoolStatus;


/**
 * A thread that checks network of searchers in regular intervals.
 * It will ping search nodes to see if they are still alive. It
 * recieves messages (via {@link RMIMessenger}) to invalidate
 * distributed search caches, etc.. 
 * 
 * @author rainman
 *
 */
public class NetworkStatusThread extends Thread {
	static org.apache.log4j.Logger log = Logger.getLogger(NetworkStatusThread.class);
	
	protected long pingInterval;
	protected long lastPing;
	protected final long sleepInterval = 1000;	
	protected RMIMessenger messenger;
	protected ArrayList<SearchHost> indexUpdates;
	protected SearcherCache cache;
	
	protected static NetworkStatusThread instance = null;
	
	protected NetworkStatusThread(){
		Configuration config = Configuration.open();
		pingInterval = config.getInt("Search","checkinterval",10) * 1000;
		indexUpdates = new ArrayList<SearchHost>();
		cache = SearcherCache.getInstance();
	}
	
	@Override
	public void run() {
		lastPing = System.currentTimeMillis();
		while(true){
			if(indexUpdates.size() != 0)
				processIndexUpdates();
			if((System.currentTimeMillis() - lastPing) > pingInterval){
				pingHosts();
				lastPing = System.currentTimeMillis();
			}
			try {
				Thread.sleep(sleepInterval);
			} catch (InterruptedException e) {
				// do nothing
			}
		}
	}	

	/** Notify the thread that index at remote host is updated */
	public synchronized void indexUpdated(IndexId iid, String host){
		indexUpdates.add(new SearchHost(iid,host));
	}
	
	/** Check dead hosts, and flush caches is host is alive */
	protected void pingHosts() {
		HashSet<String> noRetryHosts = new HashSet<String>();
		HashSet<SearchHost> deadPool = new HashSet<SearchHost>();
		deadPool.addAll(cache.getDeadPools());
		RMIMessengerClient messenger = new RMIMessengerClient();
		
		log.debug("Pinging remote hosts to see if they are up");
		for(SearchHost sh : deadPool){
			if(noRetryHosts.contains(sh.host)){
				continue;
			}
			try {
				log.debug("Pinging "+sh.host+" for "+sh.iid);
				SearcherPoolStatus status = messenger.getSearcherPoolStatus(sh.host,sh.iid.toString());
				if(status != null && status.ok){
					cache.reInitializeRemote(sh.iid,sh.host);
				}
			} catch (RemoteException e) {
				log.warn("Host "+sh.host+" still down.");
				noRetryHosts.add(sh.host);
			} catch (Exception e) {
				e.printStackTrace();
			}
		}
	}

	/** invalidate cache for updated remote indexes */
	protected void processIndexUpdates() {
		ArrayList<SearchHost> updates;
		synchronized(this){
			updates = indexUpdates;
			indexUpdates = new ArrayList<SearchHost>();
		}
		
		for(SearchHost update : updates){
			cache.reInitializeRemote(update.iid, update.host);
		}
		
	}

	/** Get singleton instance */
	public static synchronized NetworkStatusThread getInstance(){
		if(instance == null){
			instance = new NetworkStatusThread();
		}
		
		return instance;
	}
	
}
