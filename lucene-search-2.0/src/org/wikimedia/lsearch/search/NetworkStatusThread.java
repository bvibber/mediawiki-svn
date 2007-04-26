package org.wikimedia.lsearch.search;

import java.rmi.NotBoundException;
import java.rmi.RemoteException;
import java.rmi.registry.LocateRegistry;
import java.rmi.registry.Registry;
import java.util.ArrayList;
import java.util.HashSet;
import java.util.Set;

import org.apache.log4j.Logger;
import org.apache.lucene.search.RemoteSearchable;
import org.apache.lucene.search.Searchable;
import org.apache.lucene.search.SearchableMul;
import org.wikimedia.lsearch.beans.SearchHost;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.interoperability.RMIMessenger;
import org.wikimedia.lsearch.interoperability.RMIMessengerImpl;


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
	
	protected NetworkStatusThread(RMIMessenger messenger){
		this.messenger = messenger;
		Configuration config = Configuration.open();
		pingInterval = config.getInt("Search","checkinterval",30) * 1000;
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
		Registry registry;
		HashSet<String> noRetryHosts = new HashSet<String>();
		Set<SearchHost> deadHosts = cache.getDeadHosts();
		
		log.debug("Pinging remote hosts to see it they are up");
		for(SearchHost sh : deadHosts){
			String name = "RemoteSearchable<"+sh.iid+">";
			if(noRetryHosts.contains(sh.host)){
				continue;
			}
			try {
				log.debug("Pinging "+sh.host+" for "+sh.iid);
				registry = LocateRegistry.getRegistry(sh.host);
				SearchableMul rs = (SearchableMul) registry.lookup(name);
				rs.maxDoc(); // call some method
				// if we made it to here without exception, flush caches
				cache.invalidateSearchable(sh.iid,sh.host,rs);
			} catch (RemoteException e) {
				log.warn("Host "+sh.host+" still down.");
				noRetryHosts.add(sh.host);
			} catch (NotBoundException e) {
				log.warn("Still could not find RemoteSearchable instance on host \""+sh.host+"\" for indexid \""+sh.iid+"\"");
			} catch (Exception e) {
				// error making the wiki searcher				
				log.warn("Error making WikiSearcher on host \""+sh.host+"\" for indexid \""+sh.iid+"\"");
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
			cache.invalidateSearchable(update.iid, update.host);
		}
		
	}

	/** Get singleton instance */
	public static synchronized NetworkStatusThread getInstance(){
		if(instance == null){
			// ensure the RMI messenger is constructed
			RMIMessengerImpl ms = RMIMessengerImpl.getInstance();
			instance = new NetworkStatusThread(ms);
		}
		
		return instance;
	}
	
}
