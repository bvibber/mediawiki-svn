package org.wikimedia.lsearch.search;

import java.io.File;
import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Hashtable;
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
import org.wikimedia.lsearch.analyzers.WikiQueryParser;
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
	/** After waiting for some time process updates 
	 *  Warning: the delay should not be too long, else 
	 *  the fetch will end in error... 
	 *  */
	class DeferredUpdate extends Thread {
		ArrayList<LocalIndex> forUpdate;
		long delay;
		
		DeferredUpdate(ArrayList<LocalIndex> forUpdate, long delay){
			this.forUpdate = forUpdate;
			this.delay = delay;
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
					rebuild(li); // rsync, update registry, cache
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
	
	@Override
	public void run() {
		long lastCheck, now;
		while(true){
			lastCheck = System.currentTimeMillis();
			checkForUpdate();
			now = System.currentTimeMillis(); 
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
	
	protected void checkForUpdate(){
		HashSet<IndexId> iids = global.getMySearch();
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
		if(forUpdate.size()>0)
			new DeferredUpdate(forUpdate,urgent? 0 : delayInterval).start();
	}
	
	/** Rsync a remote snapshot to a local one, updates registry, cache */
	protected void rebuild(LocalIndex li){
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
				Command.exec("/usr/bin/rsync -W --delete -r rsync://"+iid.getIndexHost()+snapshotpath+" "+iid.getUpdatePath());
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
			updateCache(pool,li);
			RMIServer.rebind(iid);
			registry.refreshCurrent(li);
			
			// notify all remote searchers of change
			messenger.notifyIndexUpdated(iid,iid.getDBSearchHosts());
			
		} catch(IOException ioe){
			log.error("I/O error updating index "+iid+" at "+li.path+" : "+ioe.getMessage());
			badIndexes.put(li.iid.toString(),li.timestamp);
		}
	}
	
	/** Update search cache after successful rsync of update version of index */
	protected void updateCache(SearcherCache.SearcherPool pool, LocalIndex li){
		// do some typical queries to preload some lucene caches, pages into memory, etc..
		for(IndexSearcherMul is : pool.searchers)
			Warmup.warmupIndexSearcher(is,li.iid,true);			
		// add to cache
		cache.invalidateLocalSearcher(li.iid,pool);		
	}
	
	protected UpdateThread(){
		messenger = new RMIMessengerClient();
		global = GlobalConfiguration.getInstance();
		registry = IndexRegistry.getInstance();
		Configuration config = Configuration.open();
		// query interval in config is in minutes
		queryInterval = (long)(config.getDouble("Search","updateinterval",15) * 60 * 1000);
		delayInterval = (long)(config.getDouble("Search","updatedelay",0)*1000);
		cache = SearcherCache.getInstance();
	}
	
	public static synchronized UpdateThread getInstance(){
		if(instance == null)
			instance = new UpdateThread();
		
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
