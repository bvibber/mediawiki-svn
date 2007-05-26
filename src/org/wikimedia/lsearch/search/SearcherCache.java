package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.rmi.NotBoundException;
import java.rmi.RemoteException;
import java.rmi.registry.LocateRegistry;
import java.rmi.registry.Registry;
import java.util.Collections;
import java.util.HashSet;
import java.util.Hashtable;
import java.util.Set;
import java.util.Map.Entry;

import org.apache.log4j.Level;
import org.apache.log4j.Logger;
import org.apache.lucene.search.IndexSearcher;
import org.apache.lucene.search.Searchable;
import org.apache.lucene.search.SearchableMul;
import org.wikimedia.lsearch.beans.SearchHost;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.config.IndexRegistry;
import org.wikimedia.lsearch.index.WikiSimilarity;

/**
 * Caches both local and remote {@link Searchable} objects.  
 * 
 * @author rainman
 *
 */
public class SearcherCache {
	class SimpleInt {
		int count;
		public SimpleInt(int value) {
			this.count = value;
		}
	}
	class DeferredClose extends Thread {
		long interval;
		Searchable s;
		public DeferredClose(Searchable s, int interval){
			this.s=s;
			this.interval = interval;
		}
		@Override
		public void run() {
			try {
				Thread.sleep(interval);
			} catch (InterruptedException e) {
			}
			try {
				log.debug("Closing searchable "+s);
				s.close();
			} catch (IOException e) {
				log.warn("I/O error closing searchable "+s);
			}
		}
		
		
	}
	static org.apache.log4j.Logger log = Logger.getLogger(SearcherCache.class);
	/** dbrole@host -> RemoteSearchable */
	protected Hashtable<String,CachedSearchable> remoteCache;
	/** dbrole -> set(dbrole@host) */
	protected Hashtable<String,Set<String>> remoteKeys;
	/** dbrole -> IndexSearcher */
	protected Hashtable<String,IndexSearcherMul> localCache;
	/** searchable -> host */
	protected Hashtable<Searchable,String> searchableHost;
	
	/** lazy initalization of search indexes (set of dbroles) */
	protected Set<String> initialized;
	
	/** Hosts for which RemoteSearcher could not be created,
	 *  update via NetworkStatusThread */
	protected Set<SearchHost> deadHosts;
	
	/** Instances of searchables in use by some searcher */
	protected Hashtable<Searchable,SimpleInt> inUse;
	/** Searchables that should be closed after all searchers are done with them */
	protected HashSet<Searchable> closeQueue;
	
	static protected SearcherCache instance = null;
	
	protected Object lock;
	
	protected GlobalConfiguration global;
	
	/** Get the host where the searchable is */
	public String getSearchableHost(Searchable s){
		return searchableHost.get(s);
	}
	
	/** Contact all hosts the first time iid is being searched,
	 * and and them to cache */
	protected void initialize(IndexId iid){
		log.debug("Initializing "+iid);
		synchronized (iid) {
			if(!initialized.contains(iid.toString())){
				for(String host : iid.getSearchHosts()){
					try {
						getSearchable(iid,host);
					} catch (Exception e) {
						e.printStackTrace();
						// logged at lower level
					}
				}
				initialized.add(iid.toString());
			}
		}
	}
	
	/** Get one of the remote objects for this iid, randomly */
	public Searchable getRandomRemoteSearchable(IndexId iid){
		if(!initialized.contains(iid.toString()))
			initialize(iid);
		synchronized(lock){
			Set<String> keys = remoteKeys.get(iid.toString());
			if(keys==null || keys.size()==0)
				return null;
			int random = (int)(Math.floor(Math.random()*keys.size()));
			String[] strkeys = keys.toArray(new String[] {});
			log.debug("getRandomSearchable returns "+strkeys[random]);
			return remoteCache.get(strkeys[random]);			
		}
	}
	
	/** 
	 * Returns a searchable from cache. If the searchable is not
	 * in cache, the method will create it (via local call or RMI)
	 * @return  searchable instance
	 * @throws IOException 
	 * @throws NotBoundException 
	 */
	public Searchable getSearchable(IndexId iid, String host) throws NotBoundException, IOException{
		Searchable s = null;
		if(global.isLocalhost(host))
			s = localCache.get(makeKey(iid,host));
		else
			s = remoteCache.get(makeKey(iid,host));
		
		if(s == null)
			s = addSearchableToCache(iid,host);			
		
		return s;
	}
	
	/**
	 * Get {@link IndexSearcher} for IndexId from cache, if it not is cached
	 * new object will be created.
	 * @param iid
	 * @throws IOException 
	 */
	public IndexSearcherMul getLocalSearcher(IndexId iid) throws IOException{
		IndexSearcherMul s = localCache.get(iid.toString());

		if(s == null)
			s = addLocalSearcherToCache(iid);
		
		return s;
	}
	
	/** Warmup all local IndexSearcher (create if necessary) */
	public void warmupLocalCache(){
		HashSet<IndexId> mys =  global.getMySearch();
		for(IndexId iid : mys){
			try {
				IndexSearcherMul is = getLocalSearcher(iid);
				Warmup.warmupIndexSearcher(is,iid,false);
			} catch (IOException e) {
				log.warn("I/O error warming index for "+iid);				
			}
		}
	}
	
	/** 
	 * Make a searchable instance, and add it to cache
	 * @return   the created searchable instance
	 * @throws NotBoundException 
	 * @throws IOException 
	 */
	protected Searchable addSearchableToCache(IndexId iid, String host) throws NotBoundException, IOException{
		CachedSearchable rs = null;
		
		if(global.isLocalhost(host)){
			return addLocalSearcherToCache(iid);
		} else{
			rs = new CachedSearchable(getRemote(iid,host));
			synchronized (lock) {
				String key = makeKey(iid,host);
				// sync only here, after the remote procedure call
				if(remoteCache.get(key) == null){
					remoteCache.put(key,rs);
					if(remoteKeys.get(iid.toString()) == null)
						remoteKeys.put(iid.toString(),Collections.synchronizedSet(new HashSet<String>()));
					remoteKeys.get(iid.toString()).add(key);
					searchableHost.put(rs,host);
					return rs;
				}
				else 
					return remoteCache.get(makeKey(iid,host));				
			}			
		}		
	}
	
	protected IndexSearcherMul addLocalSearcherToCache(IndexId iid) throws IOException{
		synchronized(iid){
			// make sure some other thread has not opened the searcher
			if(localCache.get(iid.toString()) == null){
				IndexSearcherMul searcher = getLocal(iid);
				localCache.put(iid.toString(),searcher);
				searchableHost.put(searcher,"");
				return searcher;
			} else
				return localCache.get(iid.toString());
		}
	}
	
	/** Modify list of active keys and inactive hosts to tell the cache that the host is down */
	protected void registerBadIndex(IndexId iid, String host){ 
		deadHosts.add(new SearchHost(iid,host));
		String key = makeKey(iid,host);
		if(remoteKeys.get(key)!=null)
			remoteKeys.get(iid.toString()).remove(key);
	}
	
	/** Get remote {@link Searchable} object (via RMI) 
	 * @throws NotBoundException 
	 * @throws IOException */
	protected SearchableMul getRemote(IndexId iid, String host) throws NotBoundException, IOException{
		Registry registry;
		SearchableMul r;
		String name = "RemoteSearchable<"+iid+">";
		try {
			if(deadHosts.contains(new SearchHost(iid,host)))
				return null; // no retry for dead hosts
			registry = LocateRegistry.getRegistry(host);
			r = (SearchableMul) registry.lookup(name);
			r.maxDoc(); // call one method to be sure the reference is OK
			return r;
		} catch (RemoteException e) {
			log.warn("Could not get a registry for host "+host);
			registerBadIndex(iid,host);
			throw e;
		} catch (NotBoundException e) {
			log.warn("Could not find RemoteSearchable instance on host \""+host+"\" for indexid \""+iid+"\"");
			registerBadIndex(iid,host);
			throw e;
		} catch (IOException e) {
			log.warn("Failed to call remote method for object remote object "+iid+" at "+host);
			registerBadIndex(iid,host);
			throw e;
		} 
	}
	
	/** Get a local {@link Searchable} object. This function is called only when 
	 *  index is openned for the first time (e.g. at startup).
	 *  The preferred way of openning/updating indexes is via {@link UpdateThread}.
	 */
	protected IndexSearcherMul getLocal(IndexId iid) throws IOException{
		IndexSearcherMul searcher = null;
		log.debug("Openning local index for "+iid);
		if(!iid.isMySearch())
			throw new IOException(iid+" is not searched by this host.");
		try {
			searcher = new IndexSearcherMul(iid.getCanonicalSearchPath());
			searcher.setSimilarity(new WikiSimilarity());
		} catch (IOException e) {
			// tell registry this is not a good index
			IndexRegistry.getInstance().invalidateCurrent(iid);
			log.error("I/O Error opening index at path "+iid.getCanonicalSearchPath()+" : "+e.getMessage());
			throw e;
		}
		return searcher;
	}
	
	/** make a key for cache hashtables */
	protected String makeKey(IndexId iid, String host){
		return iid+"@"+host;
	}
	
	/** Check if this (remote) searchable is good, and if not, invalide its cache. 
	 *  Update: since there is no good way to check if searchable is OK, always get 
	 *  another instance of the remote object. */
	public void checkSearchable(Searchable s, CachedSearchable rs){
		// something is wrong, invalidate cache
		log.warn("Checking searchable "+s);
		if(remoteCache.containsValue(rs)){
			for(Entry<String,CachedSearchable> es : remoteCache.entrySet()){
				if(es.getValue().equals(rs)){
					String parts[] = es.getKey().split("@");
					if(parts!=null && parts.length==2){
						invalidateSearchable(IndexId.get(parts[0]),parts[1]);
					}
				}
			}
		}
	}
	
	/**
	 * Invalidates (remote) searchable cache 
	 * @param iid
	 * @param host
	 */
	public void invalidateSearchable(IndexId iid, String host){
		invalidateSearchable(iid,host,null);
	}

	/**
	 * Invalidates (remote) searchable cache, replaces the old
	 * value (if any) with the passed remote Searchable object. 
	 * 
	 * @param iid
	 * @param host
	 * @param rs
	 */
	public void invalidateSearchable(IndexId iid, String host, SearchableMul rs){
		if(global.isLocalhost(host)){
			try {
				invalidateLocalSearcher(iid,getLocal(iid));
			} catch (IOException e) {
				// error logged elsewhere
			}
			return;
		}
		String key = makeKey(iid,host);
		CachedSearchable oldrs = remoteCache.get(key);
		
		CachedSearchable newrs;
		if(rs == null){
			try {
				newrs = new CachedSearchable(getRemote(iid,host));
			} catch (Exception e) {
				synchronized(lock){
					// remove old values
					if(remoteCache.get(key) == oldrs){
						remoteCache.remove(key);
						if(oldrs != null)
							searchableHost.remove(oldrs);
					}
				}
				return;
			}
		}
		else
			newrs = new CachedSearchable(rs);
		
		synchronized(lock){
			if(remoteCache.get(key) == oldrs){
				remoteCache.put(key,newrs);
				if(oldrs != null)
					searchableHost.remove(oldrs);
				searchableHost.put(newrs,host);
				// register key 
				if(remoteKeys.get(iid.toString()) == null)
					remoteKeys.put(iid.toString(),Collections.synchronizedSet(new HashSet<String>()));
				remoteKeys.get(iid.toString()).add(key);
				// mark that host is not dead any more (if it was previously)
				deadHosts.remove(new SearchHost(iid,host));					
			}
		}			
	}
	
	/**
	 * Invalidate the old instance of IndexSearcher, and replace with
	 * a new one. (IndexSearcher is create by the calling method)
	 * 
	 * @param iid
	 * @param searcher
	 */
	public void invalidateLocalSearcher(IndexId iid, IndexSearcherMul searcher){
		IndexSearcherMul olds;
		boolean close = false;
		log.debug("Invalidating local searcher for "+iid);
		synchronized(lock){
			olds = localCache.get(iid.toString());
			// put in the new value
			localCache.put(iid.toString(),searcher);
			searchableHost.put(searcher,"");
			if(olds == null)
				return; // no old searcher
			searchableHost.remove(olds);
			// close the old index searcher (imediatelly, or queue if in use)
			SimpleInt useCount = inUse.get(olds);
			if(useCount == null || useCount.count == 0)
				close = true;
			else{
				log.debug("Searcher for "+iid+" will be closed after local searches are done.");
			}
			
			closeQueue.add(olds);
		}
		// close outside of sync block
		if(close)
			closeSearcher(olds);
	}	

	/** Tell the cache that the searcher is in use */
	public void checkout(SearchableMul searcher) {
		synchronized(lock){
			SimpleInt i = inUse.get(searcher);
			if(i == null)
				inUse.put(searcher,new SimpleInt(1));
			else
				i.count++;
		}
	}

	/** Tell the cache that the searcher is no longer in use */
	public void release(SearchableMul searcher) {		
		synchronized(lock){
			SimpleInt i = inUse.get(searcher);
			if(i == null)
				log.warn("Error in returnBack, returned Searcher that is not checked out. "+searcher);
			else
				i.count--;
		}
		
		closeSearcher(searcher);
	}
	
	protected void closeSearcher(SearchableMul searcher){
		boolean close = false;
		synchronized(lock){
			SimpleInt used = inUse.get(searcher); 
			if(used == null || used.count == 0){
				if(closeQueue.contains(searcher)){
					closeQueue.remove(searcher);
					searchableHost.remove(searcher);
					close = true;
				}
			} 
		}
		// deferred close
		if(close){
			log.debug("Deferred closure of searcher "+searcher);
			new DeferredClose(searcher,15000).start();
		}
	}
	
	/** Get a copy of array of dead hosts */
	public HashSet<SearchHost> getDeadHosts(){
		synchronized(lock){
			HashSet<SearchHost> copy = new HashSet<SearchHost>();
			copy.addAll(deadHosts);
			return copy;
		}
	}
	
	/** Get singleton instance */
	public static synchronized SearcherCache getInstance(){
		if(instance == null)
			instance = new SearcherCache();
		
		return instance;
	}
	
	protected SearcherCache(){
		remoteCache = new Hashtable<String,CachedSearchable>();
		localCache = new Hashtable<String,IndexSearcherMul>();
		deadHosts = Collections.synchronizedSet(new HashSet<SearchHost>());
		global = GlobalConfiguration.getInstance();
		inUse = new Hashtable<Searchable,SimpleInt>();
		closeQueue = new HashSet<Searchable>();
		searchableHost = new Hashtable<Searchable,String>();
		remoteKeys = new Hashtable<String,Set<String>>();
		lock = new Object();
		initialized = Collections.synchronizedSet(new HashSet<String>());
	}


	
	
}
