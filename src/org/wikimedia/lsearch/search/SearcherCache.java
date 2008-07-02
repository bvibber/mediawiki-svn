package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.io.Serializable;
import java.rmi.NotBoundException;
import java.rmi.RemoteException;
import java.rmi.registry.LocateRegistry;
import java.rmi.registry.Registry;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.Comparator;
import java.util.HashSet;
import java.util.Hashtable;
import java.util.Set;
import java.util.Map.Entry;

import org.apache.log4j.Logger;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.IndexReader.FieldOption;
import org.apache.lucene.search.IndexSearcher;
import org.apache.lucene.search.Searchable;
import org.apache.lucene.search.SearchableMul;
import org.apache.lucene.store.RAMDirectory;
import org.wikimedia.lsearch.beans.SearchHost;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.config.IndexRegistry;
import org.wikimedia.lsearch.index.WikiSimilarity;
import org.wikimedia.lsearch.interoperability.RMIMessengerClient;
import org.wikimedia.lsearch.interoperability.RMIServer;

public class SearcherCache {
	protected static Logger log = Logger.getLogger(SearcherCache.class);
	
	static class DeferredClose extends Thread {
		long interval;
		IndexSearcherMul s;
		public DeferredClose(IndexSearcherMul s, int interval){
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
				// invalidate various caches!
				CachedFilter.invalideAllFilterCache(s.getIndexReader());
				AggregateMetaField.invalidateCache(s.getIndexReader());
				ArticleMeta.invalidateCache(s.getIndexReader());
				s.close();				
			} catch (IOException e) {
				e.printStackTrace();
				log.warn("I/O error closing searchables "+s+" : "+e.getMessage());
			}
		}		
	}	
	
	/** Holds a number of index searchers, for multiprocessor workstations */
	public static class SearcherPool {
		IndexSearcherMul searchers[];
		IndexId iid;
		int index = 0;
		static Configuration config = null;
		
		SearcherPool(IndexId iid, String path, int poolsize) throws IOException {
			this.iid = iid;
			searchers = new IndexSearcherMul[poolsize];
			if(config == null)
				config = Configuration.open();
			RAMDirectory dir = null;
			if(config.getBoolean("Search","ramdirectory"))
				dir = new RAMDirectory(path);
			for(int i=0;i<poolsize;i++){
				searchers[i] = open(iid, path, dir);
			}
		}
		
		private IndexSearcherMul open(IndexId iid, String path, RAMDirectory directory) throws IOException {
			IndexSearcherMul searcher = null;
			log.debug("Opening local index for "+iid);
			if(!iid.isMySearch())
				throw new IOException(iid+" is not searched by this host.");
			if(iid.isLogical())
				throw new IOException(iid+": will not open logical index.");
			try {
				if(directory != null)
					searcher = new IndexSearcherMul(directory);
				else
					searcher = new IndexSearcherMul(path);
				searcher.setSimilarity(new WikiSimilarity());
				
				// preload meta caches
				if(iid.isArticleIndex()){
					IndexReader reader = searcher.getIndexReader();
					ArrayList<CacheBuilder> builders = new ArrayList<CacheBuilder>();
					Collection fields = reader.getFieldNames(FieldOption.ALL);
					for(Object fieldObj : fields){
						String field = (String)fieldObj;
						if(field.endsWith("_meta")){
							String metaname = field.substring(0,field.lastIndexOf('_'));
							builders.add( AggregateMetaField.getCacherBuilder(reader,metaname) );
						}
					}
					builders.add( ArticleMeta.getCacherBuilder(reader,iid.getNamespacesWithSubpages()) );
					while(builders.remove(null)); // remove null builders
					if(builders.size() > 0){
						long start = System.currentTimeMillis();
						log.info("Caching meta fields for "+iid+" ... ");
						// if cache builders exist, send documents to them
						for(CacheBuilder b : builders){
							b.init();
						}
						for(int i=0;i<reader.maxDoc();i++){
							for(CacheBuilder b : builders){
								b.cache(i,reader.document(i));
							}
						}
						for(CacheBuilder b : builders){
							b.end();
						}
						log.info("Finished caching "+iid+" in "+(System.currentTimeMillis()-start)+" ms");
					}
				}
			} catch (IOException e) {
				e.printStackTrace();
				// tell registry this is not a good index
				IndexRegistry.getInstance().invalidateCurrent(iid);
				log.error("I/O Error opening index at path "+iid.getCanonicalSearchPath()+" : "+e.getMessage());
				throw e;
			}
			return searcher;
		}
		
		synchronized IndexSearcherMul get(){
			if(index >= searchers.length)
				index = 0;
			log.debug("Using "+iid+" remote searcher "+index);
			return searchers[index++];
		}		
		
		void close(){
			for(IndexSearcherMul s : searchers){
				// deferred close
				log.debug("Deferred closure of searcher "+s);
				new DeferredClose(s,15000).start();
			}
		}
	}
	
	public static class SearcherPoolStatus implements Serializable {
		boolean ok = false;
		int poolSize = 0;
		
		public SearcherPoolStatus(boolean ok){
			this.ok = ok;
		}
		public SearcherPoolStatus(boolean ok, int poolSize){
			this.ok = ok;
			this.poolSize = poolSize;
		}
	}
	
	public static class RemoteSearcherPool {
		CachedSearchable searchers[];
		IndexId iid;
		int index = 0;
		
		RemoteSearcherPool(IndexId iid, String host, int poolsize) throws IOException, NotBoundException {
			Registry registry = LocateRegistry.getRegistry(host);
			this.iid = iid;
			String name = "RemoteSearchable<"+iid+">";
			this.searchers = new CachedSearchable[poolsize];
			for(int i=0;i<poolsize;i++){
				searchers[i] = new CachedSearchable( (SearchableMul) registry.lookup(name+"$"+i), iid, host );
			}
		}
		
		synchronized CachedSearchable get(){
			if(index >= searchers.length)
				index = 0;
			log.debug("Using "+iid+" searcher "+index);
			return searchers[index++];
		}	
	}
	
	/** dbrole -> host -> remote pool */
	protected Hashtable<String,Hashtable<String,RemoteSearcherPool>> remoteCache = new Hashtable<String,Hashtable<String,RemoteSearcherPool>>();	
	/** dbrole -> local pool */
	protected Hashtable<String,SearcherPool> localCache = new Hashtable<String,SearcherPool>();
	/** update lock */
	protected Object lock = new Object();
	/** dbroles we have initialized */
	protected Set<String> initialized = Collections.synchronizedSet(new HashSet<String>());
	/** default size of the local search pool */
	protected int searchPoolSize;
	/** Host/iid pairs for which remote pool couldn't be initialized */
	protected Set<SearchHost> deadPools = Collections.synchronizedSet(new HashSet<SearchHost>());
	
	protected static SearcherCache instance = null;
	
	/**
	 * If there is a cached local searcher of iid
	 * 
	 * @param iid
	 * @return
	 */
	public boolean hasLocalSearcher(IndexId iid){
		return localCache.containsKey(iid.toString());		
	}
	
	/**
	 * Get a random host for iid, if local and being deployed
	 * always return the localhost
	 * 
	 * @param iid
	 * @return
	 */
	public String getRandomHost(IndexId iid){
		if(iid.isMySearch() && !UpdateThread.isBeingDeployed(iid) && hasLocalSearcher(iid))
			return "localhost";
		if(!initialized.contains(iid.toString()))
			initializeRemote(iid);
		synchronized(iid.getSearcherCacheLock()){
			Hashtable<String,RemoteSearcherPool> pools = remoteCache.get(iid.toString());
			if(pools == null)
				return null;
			int num = (int)(Math.random()*pools.size());
			for(String host : pools.keySet()){
				if(--num < 0)
					return host;
			}
		}
		return null;		
	}
	
	/** Get a remote searchable object from a remote pool */
	public CachedSearchable getRemoteSearcher(IndexId iid, String host){
		Hashtable<String,RemoteSearcherPool> pools = remoteCache.get(iid.toString());
		if(pools == null)
			return null;
		RemoteSearcherPool pool = pools.get(host);
		if(pool == null)
			return null;
		return pool.get();
	}
	
	
	/**
	 * Get {@link IndexSearcherMul} for IndexId from cache, if it not is cached
	 * new object will be created.
	 * @param iid
	 * @throws IOException 
	 */
	public IndexSearcherMul getLocalSearcher(IndexId iid) throws IOException{
		if(iid == null)
			throw new RuntimeException("No such index");
		if(UpdateThread.isBeingDeployed(iid))
			throw new IOException(iid+" is being deployed");
		return fromLocalCache(iid.toString());
	}
	
	/** Get single searcher from local cached pool, or if doesn't exist null */
	protected IndexSearcherMul fromLocalCache(String key){
		SearcherPool pool = localCache.get(key);
		if(pool != null)
			return pool.get();
		return null;
	}
	
	/**
	 * On update or error, call this function to reinitialize
	 * the remote search pool
	 * @param iid
	 */
	public void reInitializeRemote(IndexId iid, String host){
		if(RMIMessengerClient.isLocal(host))
			return;
		log.debug("Reinitializing remote for "+iid);
		synchronized (iid.getSearcherCacheLock()) {
			// delete old values
			Hashtable<String,RemoteSearcherPool> hostpool = remoteCache.get(iid.toString());
			if(hostpool != null){
				hostpool.remove(host);
			}
			// init
			initializeRemote(iid,host);
		}
	}
	
	/** 
	 *  Initialize all remote search pools for iid 
	 */
	protected void initializeRemote(IndexId iid){
		log.debug("Initializing remote for "+iid);
		synchronized (iid.getSearcherCacheLock()) {
			
			if(!initialized.contains(iid.toString())){
				for(String host : iid.getSearchHosts()){
					if(!RMIMessengerClient.isLocal(host))
						initializeRemote(iid,host);
				}
				initialized.add(iid.toString());
			}
			
		}
	}
	
	/** Construct a remote searcher pool for iid on host */
	protected void initializeRemote(IndexId iid, String host){
		RMIMessengerClient messenger = new RMIMessengerClient();
		try{
			synchronized (iid.getSearcherCacheLock()) {
				SearcherPoolStatus status = messenger.getSearcherPoolStatus(host,iid.toString());
				if(status!=null && status.ok){
					// host -> remote pool
					Hashtable<String,RemoteSearcherPool> hostpool = remoteCache.get(iid.toString());
					if(hostpool == null)
						remoteCache.put(iid.toString(), hostpool = new Hashtable<String,RemoteSearcherPool>());
					hostpool.put(host,new RemoteSearcherPool(iid,host,status.poolSize));
					deadPools.remove(new SearchHost(iid,host)); // make sure not marked as dead
					return;
				}
			}
		} catch(RemoteException e){
			e.printStackTrace();
			log.warn("Cannot get searcher status for "+iid+" on "+host+" : "+e.getMessage());
		} catch (IOException e) {
			e.printStackTrace();
			log.warn("I/O error trying to construct remote searcher pool for "+iid+" on "+host+" : "+e.getMessage());
		} catch (NotBoundException e) {
			e.printStackTrace();
			log.warn("Remote searcher for "+iid+" on "+host+" not bound : "+e.getMessage());
		}
		// if we reach this point something went wrong
		deadPools.add(new SearchHost(iid,host));
	}
	
	/**
	 * Initialize all local searcher pools 
	 */
	protected class InitialDeploymentThread extends Thread {
		public void run(){
			IndexRegistry registry = IndexRegistry.getInstance();
			// get local search indexes, deploy sorted by name
			ArrayList<IndexId> mys = new ArrayList<IndexId>();
			mys.addAll(GlobalConfiguration.getInstance().getMySearch());
			Collections.sort(mys,new Comparator<IndexId>(){
				public int compare(IndexId o1, IndexId o2) {
					return o1.toString().compareTo(o2.toString());
				}
			});
			for(IndexId iid : mys){
				try {
					// when searcher is linked into "search" path it's good, initialize it
					if(!iid.isLogical() && registry.getCurrentSearch(iid) != null){
						log.debug("Initializing local for "+iid);
						SearcherPool pool = initLocalPool(iid);
						//Warmup.warmupPool(pool.searchers,iid,false,1);
						//Warmup.waitForAggregate(pool.searchers);
						localCache.put(iid.toString(),pool);
						
						RMIServer.bind(iid,pool.searchers);
					}
				} catch (IOException e) {
					log.warn("I/O error warming index for "+iid+" : "+e.getMessage());				
				}
			}
		}
	}
	
	/** Provide new value for the local searcher pool */
	public void updateLocalSearcherPool(IndexId iid, SearcherPool pool){
		SearcherPool old = localCache.get(iid.toString());
		// passed null - remove searcher altogether
		if(pool == null)
			localCache.remove(iid.toString());
		else
			localCache.put(iid.toString(),pool);
		
		// finally, close the old searcher pool 
		if(old != null)
			old.close();
	}
	
	/** Get a searcher pool, will create if doesn't exist */
	public IndexSearcherMul[] getLocalSearcherPool(IndexId iid) throws IOException {
		SearcherPool pool = localCache.get(iid.toString());
		if(pool == null){
			// try to init
			pool = initLocalPool(iid);
			localCache.put(iid.toString(),pool);
		}
		
		if(pool == null)
			return null;
		else
			return pool.searchers;
	}
	
	/** Make local searcher pool */
	protected SearcherPool initLocalPool(IndexId iid) throws IOException{
		synchronized(iid.getSearcherCacheLock()){
			// make sure some other thread has not opened the searcher
			if(localCache.get(iid.toString()) == null){
				if(!iid.isMySearch())
					throw new IOException(iid+" is not searched by this host.");
				if(iid.isLogical())
					throw new IOException(iid+": will not open logical index.");
				return new SearcherPool(iid,iid.getCanonicalSearchPath(),searchPoolSize);
			} else
				return localCache.get(iid.toString());
		}
	}
	
	/**
	 * Get status of local search pool
	 * @throws IOException
	 */
	public SearcherPoolStatus getSearcherPoolStatus(IndexId iid) throws IOException {
		 IndexSearcherMul[] pool = getLocalSearcherPool(iid);
		 if(pool == null){
			 return new SearcherPoolStatus(false);
		 } else
			 return new SearcherPoolStatus(true,pool.length);
	}
	
	/** Get a singleton standalon instance */
	public static synchronized SearcherCache getStandalone(){
		if(instance == null)
			instance = new SearcherCache(false);
		
		return instance;
		
	}

	/** Get singleton instance */
	public static synchronized SearcherCache getInstance(){
		if(instance == null)
			instance = new SearcherCache(true);
		
		return instance;
	}
	
	protected SearcherCache(boolean initialize){
		searchPoolSize = Configuration.open().getInt("SearcherPool","size",1);
		if(initialize)
			new InitialDeploymentThread().start();
	}
	
	public int getSearchPoolSize() {
		return searchPoolSize;
	}

	public Set<SearchHost> getDeadPools() {
		return deadPools;
	}
}
