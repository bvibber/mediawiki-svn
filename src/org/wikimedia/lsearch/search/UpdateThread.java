package org.wikimedia.lsearch.search;

import java.io.File;
import java.io.IOException;
import java.rmi.RemoteException;
import java.util.ArrayList;
import java.util.Collections;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Hashtable;
import java.util.Set;
import java.util.Map.Entry;

import org.apache.log4j.Logger;
import org.apache.lucene.index.Term;
import org.apache.lucene.queryParser.ParseException;
import org.apache.lucene.search.BooleanClause;
import org.apache.lucene.search.BooleanQuery;
import org.apache.lucene.search.IndexSearcher;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.TermQuery;
import org.wikimedia.lsearch.analyzers.Analyzers;
import org.wikimedia.lsearch.beans.LocalIndex;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.config.IndexRegistry;
import org.wikimedia.lsearch.index.WikiSimilarity;
import org.wikimedia.lsearch.interoperability.RMIMessengerClient;
import org.wikimedia.lsearch.interoperability.RMIServer;
import org.wikimedia.lsearch.util.Command;
import org.wikimedia.lsearch.util.FSUtils;


/**
 * Thread that periodically check indexer hosts for index updates. 
 * 
 * @author rainman
 *
 */
public class UpdateThread extends Thread {
	
	enum RebuildType { STANDALONE, FULL };
	
	/** iids currently being deployed and out of rotation */
	protected static Set<String> beingDeployed = Collections.synchronizedSet(new HashSet<String>());	
	
	public static boolean isBeingDeployed(IndexId iid){
		return beingDeployed.contains(iid.toString());
	}
	
	/** After waiting for some time process updates 
	 *  Warning: the delay should not be too long, else 
	 *  the fetch will end in error... 
	 *  */
	class DeferredUpdate extends Thread {
		ArrayList<LocalIndex> forUpdate;
		long delay;
		RebuildType type;
		
		DeferredUpdate(ArrayList<LocalIndex> forUpdate, long delay, RebuildType type){
			this.forUpdate = forUpdate;
			this.delay = delay;
			this.type = type;
		}

		@Override
		public void run() {
			try {
				log.debug("Init deferred update ( "+delay+" ms )");
				Thread.sleep(delay);
			} catch (InterruptedException e) {				
			}
			// get the new snapshots via rsync, might be lengthy
			for(LocalIndex li : forUpdate){
				try{
					log.debug("Syncing "+li.iid);
					rebuild(li,type); // rsync, update registry, cache
					pending.remove(li.iid.toString());
				} catch(Exception e){
					e.printStackTrace();
					log.error("Error syncing "+li+" : "+e.getMessage());
				}
			}
		}		
	}
		
	static org.apache.log4j.Logger log = Logger.getLogger(UpdateThread.class);
	protected RMIMessengerClient messenger;
	protected static GlobalConfiguration global;
	protected IndexRegistry registry;
	protected long queryInterval;
	protected SearcherCache cache;
	protected long delayInterval;
	/** Pending updates, dbrole -> timestamp */
	protected Hashtable<String,Long> pending = new Hashtable<String,Long>();	
	/** Bad indexes at indexer, prevent infinite rsync attempts */
	protected Hashtable<String,Long> badIndexes =  new Hashtable<String,Long>();
	protected static UpdateThread instance = null;
	protected String rsyncPath = null;
	protected String rsyncParams = null;
	protected long numChecks = 0;
	
	@Override
	public void run() {
		long lastCheck, now;
		while(true){
			lastCheck = System.currentTimeMillis();
			checkForUpdate();
			now = System.currentTimeMillis();
			numChecks++;
			if((now-lastCheck) < queryInterval){
				try {
					// try to check for updates in regular intervals
					Thread.sleep(queryInterval - (now-lastCheck));
				} catch (InterruptedException e) {
					// do nothing
				}
			}
		}
	}
	
	/** Update one index - call ONLY in standalone mode (i.e. in index importers, etc) */
	public void update(IndexId iid) throws IOException {
		HashSet<IndexId> iids = new HashSet<IndexId>();
		iids.add(iid);
		ArrayList<LocalIndex> forUpdate = checkForUpdate(iids,false);
		for(LocalIndex li : forUpdate){
			log.info("Syncing "+li.iid);
			if(!iid.isMySearch())
				iid.forceMySearch();
			rebuild(li,RebuildType.STANDALONE); // rsync, update registry, cache
			pending.remove(li.iid.toString());
		}
	}
	/** Thread mode, check for updates on all index searched by this host, and schedule updates */
	protected ArrayList<LocalIndex> checkForUpdate(){
		return checkForUpdate(global.getMySearch(),true);
	}
	
	protected ArrayList<LocalIndex> checkForUpdate(HashSet<IndexId> iids, boolean scheduleUpdate){
		HashMap<String,ArrayList<IndexId>> hostMap = new HashMap<String,ArrayList<IndexId>>();
		ArrayList<LocalIndex> forUpdate = new ArrayList<LocalIndex>();
		boolean urgent = false; // if indexes are broke and should be rsynced right away
		
		// organize into hostmap: host -> iids (indexes at that host)
		for(IndexId iid : iids){
			String host = iid.getIndexHost();
			ArrayList<IndexId> hostiids = hostMap.get(host);
			if(hostiids == null){
				hostiids = new ArrayList<IndexId>();
				hostMap.put(host,hostiids);
			}
			hostiids.add(iid);			
		}		
		// check for new snapshots
		for(Entry<String,ArrayList<IndexId>> hostiid : hostMap.entrySet()){			
			ArrayList<IndexId> hiids = hostiid.getValue();
			String host = hostiid.getKey();
			long[] timestamps = messenger.getIndexTimestamp(hiids, host);
			if(timestamps == null)
				continue;
			
			for(int i = 0; i < hiids.size(); i++){
				IndexId iid = hiids.get(i);
				if(pending.containsKey(iid.toString()))
					continue; // pending update, ignore
				LocalIndex myli = registry.getCurrentSearch(iid);
				if(timestamps[i]!= 0 && (myli == null || myli.timestamp < timestamps[i])){
					LocalIndex li = new LocalIndex(
							iid,
							iid.getUpdatePath(),
							timestamps[i]);
					forUpdate.add(li); // newer snapshot available
					pending.put(iid.toString(),new Long(timestamps[i]));
					if(registry.getCurrentSearch(iid) == null)
						urgent = true;
				}
			}
		}
		if(scheduleUpdate && forUpdate.size()>0)
			new DeferredUpdate(
					forUpdate,
					(urgent || numChecks==0)? 0 : delayInterval, 
					RebuildType.FULL).start();
		return forUpdate;
	}
	
	/** Rsync a remote snapshot to a local one, updates registry, cache */
	protected void rebuild(LocalIndex li, RebuildType type){
		// check if index has previously failed
		if(badIndexes.containsKey(li.iid.toString()) && badIndexes.get(li.iid.toString()).equals(li.timestamp))
			return;

		final String sep = Configuration.PATH_SEP;
		IndexId iid = li.iid;		
		// update path:  updatepath/timestamp
		String updatepath = iid.getUpdatePath();
		if(!updatepath.endsWith(Configuration.PATH_SEP))
			updatepath += Configuration.PATH_SEP;
		updatepath += li.timestamp;
		
		li.path = updatepath;
		
		// cleanup the update dir for this iid
		File spd = new File(iid.getUpdatePath());
		LocalIndex myli = registry.getCurrentSearch(iid);
		if(myli!=null){
			String current = Long.toString(myli.timestamp);
			if(spd.exists() && spd.isDirectory()){
				File[] files = spd.listFiles();
				for(File f: files){
					if(!f.getName().equals(current))
						deleteDirRecursive(f);
				}
			}
		}
		new File(updatepath).mkdirs();
		try{
			// if local, use cp -lr instead of rsync
			if(global.isLocalhost(iid.getIndexHost())){
				FSUtils.createHardLinkRecursive(
						iid.getSnapshotPath()+sep+li.timestamp,
						updatepath);
			} else{
				File ind = new File(iid.getCanonicalSearchPath());

				if(ind.exists()){ // prepare a local hard-linked copy of index
					FSUtils.createHardLinkRecursive(
							ind.getCanonicalPath(),
							updatepath);					
				}
				long startTime = System.currentTimeMillis();
				// rsync
				log.info("Starting rsync of "+iid);
				String snapshotpath = iid.getRsyncSnapshotPath()+"/"+li.timestamp;
				Command.exec(rsyncPath+" "+rsyncParams+" -W --delete -r rsync://"+iid.getIndexHost()+snapshotpath+" "+iid.getUpdatePath());
				log.info("Finished rsync of "+iid+" in "+(System.currentTimeMillis()-startTime)+" ms");

			}

			// make the search path if it doesn't exist
			File searchpath = new File(iid.getSearchPath()).getParentFile();
			if(!searchpath.exists())
				searchpath.mkdir();

			// check if updated index is a valid one (throws an exception on error)
			SearcherCache.SearcherPool pool = new SearcherCache.SearcherPool(iid,li.path,cache.getSearchPoolSize()); 
			
			// refresh the symlink
			FSUtils.delete(iid.getSearchPath());
			FSUtils.createSymLink(updatepath,iid.getSearchPath());
			
			// update registry, cache, rmi object
			registry.refreshUpdates(iid);
			warmupAndDeploy(pool,li,type);
			registry.refreshCurrent(li);
			if(type != RebuildType.STANDALONE)
				RMIServer.rebind(iid);
			
			// notify all remote searchers of change
			messenger.notifyIndexUpdated(iid,iid.getDBSearchHosts());
			
		} catch(IOException ioe){
			ioe.printStackTrace();
			log.error("I/O error updating index "+iid+" at "+li.path+" : "+ioe.getMessage());
			badIndexes.put(li.iid.toString(),li.timestamp);
		}
	}
	
	/** Update searcher cache after warming up searchers */
	protected void warmupAndDeploy(SearcherCache.SearcherPool pool, LocalIndex li, RebuildType type){
		try{
			// see if we can go ahead and deploy the searcher or should we wait
			IndexId iid = li.iid;
			HashSet<String> group = iid.getSearchHosts();
			int succ = 0, fail = 0;
			boolean reroute = false;
			if(type == RebuildType.FULL){			
				// never deploy more than one searcher of iid in a search group
				// wait for other peers to finish deploying before proceeding
				boolean wait = false;			
				do{
					if(group.size() >= 2){
						log.info("Attempting deployment of "+iid);
						for(String host : group){
							if(!RMIMessengerClient.isLocal(host)){
								try{
									if(messenger.attemptIndexDeployment(host,iid.toString()))
										succ ++;
									else
										fail ++;
								} catch(RemoteException e){
									e.printStackTrace();
									log.warn("Error response from "+host+" : "+e.getMessage());
								}
							}
						}
						if(fail == 0 && succ >= 1){
							wait = false; // proceed to deployment
							reroute = true;
						} else if(fail == 0 && succ == 0){
							wait = false; // we're the only one alive, just deploy.. 
						} else
							wait = true;
					}
					if(wait){ // wait random time (5 -> 15 seconds)
						try {
							Thread.sleep((long)(10000 * (Math.random()+0.5)));
						} catch (InterruptedException e) {
							e.printStackTrace();
						}
					}
				} while(wait);

				// reoute queries to other servers
				if( reroute ){
					log.info("Deploying "+iid);
					beingDeployed.add(iid.toString());
					try{
						RMIServer.unbind(iid,cache.getLocalSearcherPool(iid));
					} catch(Exception e) {
						// we gave it a shot...
					}
					cache.updateLocalSearcherPool(iid,null);
				}

			}
			
			// do some typical queries to preload some lucene caches, pages into memory, etc..
			for(IndexSearcherMul is : pool.searchers){
				try{
					// do one to trigger caching
					//Warmup.warmupIndexSearcher(is,li.iid,true,1);
					//Warmup.waitForAggregate(pool.searchers);
					// do proper warmup
					Warmup.warmupIndexSearcher(is,li.iid,true,null);
				} catch(IOException e){
					e.printStackTrace();
					log.warn("Error warmup up "+li+" : "+e.getMessage());
				}
			}
			
			
			// add to cache
			cache.updateLocalSearcherPool(li.iid,pool);
			if( reroute ){
				log.info("Deployed "+iid);
				beingDeployed.remove(iid.toString());
			}
		} finally{
			// be sure stuff is not stuck as being deployed
			beingDeployed.remove(li.iid.toString());
		}
	}
	
	protected UpdateThread(RebuildType type){
		messenger = new RMIMessengerClient();
		global = GlobalConfiguration.getInstance();
		registry = IndexRegistry.getInstance();
		Configuration config = Configuration.open();
		// query interval in config is in minutes
		queryInterval = (long)(config.getDouble("Search","updateinterval",15) * 60 * 1000);
		delayInterval = (long)(config.getDouble("Search","updatedelay",0)*1000);
		if(type == RebuildType.STANDALONE)
			cache = SearcherCache.getStandalone();
		else
			cache = SearcherCache.getInstance();
		rsyncPath = config.getString("Rsync","path","/usr/bin/rsync");
		rsyncParams = config.getString("Rsync","params","");
	}
	
	public static UpdateThread getStandalone(){
		return new UpdateThread(RebuildType.STANDALONE);
	}
	
	public static synchronized UpdateThread getInstance(){
		if(instance == null)
			instance = new UpdateThread(RebuildType.FULL);
		
		return instance;
	}
	
	protected void deleteDirRecursive(File file){
		if(!file.exists())
			return;
		else if(file.isDirectory()){
			File[] files = file.listFiles();
			for(File f: files)
				deleteDirRecursive(f);
			file.delete();
			log.debug("Deleted old update at "+file);
		} else{
			file.delete();			
		}
	}
}
