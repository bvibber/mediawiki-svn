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
import java.util.List;
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
				log.warn("I/O error closing searchables "+s+" : "+e.getMessage(),e);
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
			initialWarmup.add(iid.toString());
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
				if(iid.isArticleIndex() || iid.isTitlesBySuffix()){
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
								if( !reader.isDeleted(i) )
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
				log.error("I/O Error opening index at path "+iid.getCanonicalSearchPath()+" : "+e.getMessage(),e);
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
		
		public String toString(){
			return "ok="+ok+", poolSize="+poolSize;
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
	/** special pool sizes */
	protected Hashtable<String,Integer> specialPoolSizes = new Hashtable<String,Integer>();
	
	protected static SearcherCache instance = null;
	
	/** Remote hosts being deployed, never use their searchers, unless necessary! (host->deployment level) */
	protected Hashtable<String,Integer> hostsDeploying = new Hashtable<String,Integer>();
	
	/** dbrole -> hosts - indexes taken out of rotation */
	protected Hashtable<String,Set<String>> outOfRotation = new Hashtable<String,Set<String>>();

	/** deployment has been tried at least once for these */
	protected static Set<String> initialWarmup = Collections.synchronizedSet(new HashSet<String>());
	
	/** hosts excluded in lsearch.conf - don't use these unless they are the only ones */
	protected static Set<String> excludedHosts = Collections.synchronizedSet(new HashSet<String>());
	
	protected boolean initialDeploymentRunning = false;
	
	/** Number of threads to use for initial deployment */
	protected int initialDeploymentThreads = 1;
	
	/** If there is local searcher always use that */
	protected boolean forceLocal = true;
	
	/**
	 * If there is a cached local searcher of iid
	 * 
	 * @param iid
	 * @return
	 */
	public boolean hasLocalSearcher(IndexId iid){
		return localCache.containsKey(iid.toString());		
	}
	
	/** Take a certain index on a remote or localhost out of rotation */
	public void takeOutOfRotation(String host, String dbrole){
		synchronized(outOfRotation){
			Set<String> hosts = outOfRotation.get(dbrole);
			if(hosts == null)
				outOfRotation.put(dbrole, hosts = Collections.synchronizedSet(new HashSet<String>()));
			hosts.add(host);
		}
	}
	
	/** Put certain index back into rotation */
	public void returnToRotation(String host, String dbrole){
		synchronized(outOfRotation){
			Set<String> hosts = outOfRotation.get(dbrole);
			if(hosts == null){
				log.warn("Tried to put host="+host+", dbrole="+dbrole+" back into rotation, but hasn't been out of rotation.");
				return; 
			}
			hosts.remove(host);
			if(hosts.isEmpty())
				outOfRotation.remove(dbrole);
		}
	}
	
	/** Check if this index is out of rotation */
	public boolean isOutOfRotation(String host, IndexId iid){
		synchronized(outOfRotation){
			Set<String> hosts = outOfRotation.get(iid.toString());
			if(hosts != null && hosts.contains(host))
				return true;
			return false;
		}
	}
	
	/** Signalize that host is begining it's index update, and that we shouldn't touch it */
	public void hostDeploying(String host){
		synchronized(hostsDeploying){
			Integer level = hostsDeploying.get(host);
			if(level == null) // first level of deployment
				hostsDeploying.put(host,1);
			else // more concurrent threads doing deployment on remote host
				hostsDeploying.put(host,level+1);
		}
	}
	
	/** Remote host has been deployed */
	public void hostDeployed(String host){
		synchronized(hostsDeploying){
			Integer level = hostsDeploying.get(host);
			if(level == null){				
				log.warn("Cannot deploy host="+host+" since it hasn't been deploying");
				return;
			}
			if(level == 1)
				hostsDeploying.remove(host);
			else
				hostsDeploying.put(host,level-1);
		}
	}
	
	/** Produce nice human-readable list of indexes out of rotation */
	public ArrayList<String> indexesTakenOutOfRotation(){
		ArrayList<String> out = new ArrayList<String>();
		synchronized(hostsDeploying){
			for(Entry<String,Set<String>> e : outOfRotation.entrySet()){
				for(String host : e.getValue()){
					out.add(e.getKey()+" at "+host);
				}
			}
		}
		return out;
	}
	
	public boolean thisHostIsDeploying(){
		return hostsDeploying.containsKey("localhost");
	}
	
	/**
	 * Get a random host for iid, if local and being deployed
	 * always return the localhost
	 * 
	 * @param iid
	 * @return
	 */
	public String getRandomHost(IndexId iid){
		if(iid.isMySearch() && hasLocalSearcher(iid) && forceLocal
				&& !hostsDeploying.containsKey("localhost") && !isOutOfRotation("localhost",iid))
			return "localhost";
		if(!initialized.contains(iid.toString()))
			initializeRemote(iid);
		synchronized(iid.getSearcherCacheLock()){
			Hashtable<String,RemoteSearcherPool> pools = remoteCache.get(iid.toString());
			if(pools == null)
				return null;			
			// generate all suitable remote hosts
			HashSet<String> hosts = new HashSet<String>();
			hosts.addAll(pools.keySet());
			if(!forceLocal && iid.isMySearch())
				hosts.add("localhost");
			hosts.removeAll(hostsDeploying.keySet());
			// remove the hosts excluded by configuration
			if(!hosts.equals(excludedHosts))
				hosts.removeAll(excludedHosts);
			// get hosts for which this index is out of rotation
			Set<String> takenOut = outOfRotation.get(iid.toString());
			if(takenOut != null)
				hosts.removeAll(takenOut);
			// no hosts left
			if(hosts.size() == 0)
				return null;
			int num = (int)(Math.random()*hosts.size());
			for(String host : hosts){
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
		if(!initialWarmup.contains(iid.toString()))
			throw new RuntimeException(iid+" is being deployed or is not searched by this host");
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
					log.info("Reinitialized iid="+iid);
					return;
				}
				log.warn("Cannot reinitialize iid="+iid+", remote pool status="+status);
			}
		} catch(RemoteException e){
			e.printStackTrace();
			log.warn("Cannot get searcher status for "+iid+" on "+host+" : "+e.getMessage(),e);
		} catch (IOException e) {
			e.printStackTrace();
			log.warn("I/O error trying to construct remote searcher pool for "+iid+" on "+host+" : "+e.getMessage(),e);
		} catch (NotBoundException e) {
			e.printStackTrace();
			log.warn("Remote searcher for "+iid+" on "+host+" not bound : "+e.getMessage(),e);
		}
		// if we reach this point something went wrong
		deadPools.add(new SearchHost(iid,host));
	}
	
	/**
	 * Initialize all local searcher pools 
	 */
	protected class InitialDeploymentThread extends Thread {
		IndexRegistry registry = null;
		
		protected class InitialDeployer extends Thread {
			ArrayList<IndexId> iids = new ArrayList<IndexId>();
			
			protected InitialDeployer(List<IndexId> iids){
				this.iids.addAll(iids);
			}
			
			public void run(){
				log.info("Starting initial deployer for "+iids);
				for(IndexId iid : iids){
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
						log.warn("I/O error warming index for "+iid+" : "+e.getMessage(),e);				
					}
				}
			}
		}
		public void run(){
			try{
				initialDeploymentRunning = true;
				registry = IndexRegistry.getInstance();
				// get local search indexes, deploy sorted by name
				ArrayList<IndexId> mys = new ArrayList<IndexId>();
				mys.addAll(GlobalConfiguration.getInstance().getMySearch());
				Collections.sort(mys,new Comparator<IndexId>(){
					public int compare(IndexId o1, IndexId o2) {
						return o1.toString().compareTo(o2.toString());
					}
				});
				int threadNum = initialDeploymentThreads;
				ArrayList<InitialDeployer> threads = new ArrayList<InitialDeployer>();
				
				// divide mys list into chunks and assign them to different worker threads
				float inc = (float)mys.size() / threadNum;
				if( inc < 1 )
					inc = 1;
				float start = 0;
				for(int i=0;i<threadNum;i++){
					int end = Math.min((int)(start+inc), mys.size());
					if( i == threadNum-1 )
						end = mys.size(); // take rest of the list
					
					threads.add(new InitialDeployer( mys.subList((int)(start), end) ));
					start += inc;
					// config error, too many threads
					if( start >= mys.size())
						break;
				}
				
				// start all threads
				for(InitialDeployer t : threads)
					t.start();
				
				// wait for all of the threads to finish
				for(InitialDeployer t : threads)
					try {
						t.join();
					} catch (InterruptedException e) {
						log.error("Thread "+t+" didn't finish properly", e);
					}
				
				
			} finally {
				initialDeploymentRunning = false;
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
				return new SearcherPool(iid,iid.getCanonicalSearchPath(),getSearchPoolSize(iid));
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
		Configuration config = Configuration.open();
		searchPoolSize = config.getInt("SearcherPool","size",1);
		String[] specials = config.getArray("SearcherPool", "special");
		if( specials != null){
			for(String s : specials){
				String[] parts = s.split(":");
				if(parts.length == 2)
					specialPoolSizes.put( parts[0].trim(), new Integer(parts[1].trim()));
			}
		}
		String[] excluded = config.getArray("SearcherPool", "excludedHosts");
		if(excluded != null){
			for(String s : excluded)
				excludedHosts.add(s.trim());
			log.info("Excluding hosts: "+excludedHosts);
		}
		initialDeploymentThreads = config.getInt("SearcherPool", "initThreads",1);
		
		forceLocal = config.getBoolean("SearcherPool", "forceLocal", true);
		
		if(initialize){
			initialDeploymentRunning = true;
			new InitialDeploymentThread().start();
		}
	}
	
	public int getSearchPoolSize(IndexId iid) {
		Integer special = specialPoolSizes.get(iid.toString());
		if(special != null)
			return special;
		return searchPoolSize;
	}

	public Set<SearchHost> getDeadPools() {
		return deadPools;
	}
	
	/** Sleep until initial deployment is finished */
	public void waitForInitialDeployment(){
		while(initialDeploymentRunning){
			try {
				Thread.sleep(100);
			} catch (InterruptedException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}
	}
}
