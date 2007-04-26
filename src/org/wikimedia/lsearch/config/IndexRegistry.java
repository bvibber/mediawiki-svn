package org.wikimedia.lsearch.config;

import java.io.File;
import java.io.IOException;
import java.util.Hashtable;

import org.apache.log4j.Logger;
import org.wikimedia.lsearch.beans.LocalIndex;

/**
 * Maintains a registry of local copies of lucene indexes (for both 
 * searching and indexing). Use this class to obtain information about
 * the latest index snapshots, updates, if the current index is good,
 * etc...<br/>
 * 
 * Singleton.
 * 
 * @author rainman
 *
 */
public class IndexRegistry {
	static org.apache.log4j.Logger log = Logger.getLogger(IndexRegistry.class);
	/** latest index snapshot */
	protected Hashtable<String,LocalIndex> latestSnapshot;	
	/** latest search index update */
	protected Hashtable<String,LocalIndex> latestUpdate;	
	/** current search index */
	protected Hashtable<String,LocalIndex> currentSearch;
	
	protected static IndexRegistry instance = null;

	/** Get info about the latest index snapshot */
	public LocalIndex getLatestSnapshot(IndexId iid){
		return latestSnapshot.get(iid.toString()); // hashtable is synchronized		
	}
	
	/** Get info about the latest search index update */
	public LocalIndex getLatestUpdate(IndexId iid){
		return latestUpdate.get(iid.toString());
	}
	
	/** Get info about the index currently being searched, returns null if 
	 * index is bad or doesn not exist */
	public LocalIndex getCurrentSearch(IndexId iid){
		return currentSearch.get(iid.toString());
	}
	
	/** If string is all digits */
	protected boolean digits(String str){
		for(char ch: str.toCharArray()){
			if(!Character.isDigit(ch))
				return false;
		}
		return true;
	}
	
	/** Checks if the directory name is a timestamp of format yyyyMMddHHmmss  */
	protected boolean isValidTimestamp(String timestamp){
		return digits(timestamp) && timestamp.length() == 14;
	}
	
	protected long getTimestamp(String timestamp){
		return Long.parseLong(timestamp);
	}
	
	protected LocalIndex getLatestLocalIndex(File dir, IndexId iid){
		LocalIndex latest = null;
		if(dir.isDirectory()){			
			// find the index with the latest timestamp
			for(File snapshot : dir.listFiles()){
				if(snapshot.isDirectory() && isValidTimestamp(snapshot.getName())){
					LocalIndex li = new LocalIndex(
							iid,
							snapshot.getPath(),
							getTimestamp(snapshot.getName()));
					
					if(latest == null || li.timestamp > latest.timestamp)
						latest = li;
				}
			}
		}
		if(latest != null)
			log.debug("Latest timestamp of index "+latest.iid+" is "+latest.timestamp);
		return latest;
	}
	
	/** Refresh latest snapshot info */
	public synchronized void refreshSnapshots(IndexId iid){
		File snapshotDir = new File(iid.getSnapshotPath());
		LocalIndex latest = getLatestLocalIndex(snapshotDir,iid);
		if(latest != null){
			latestSnapshot.put(iid.toString(),latest);
		} else if(latestSnapshot.get(iid.toString()) != null){
			latestSnapshot.remove((iid.toString()));
		}
	}
	
	/** Refresh latest search update info */
	public synchronized void refreshUpdates(IndexId iid){
		File updateDir = new File(iid.getUpdatePath());
		LocalIndex latest = getLatestLocalIndex(updateDir,iid);
		if(latest != null){
			latestUpdate.put(iid.toString(),latest);
		} else if(latestUpdate.get(iid.toString()) != null){
			latestUpdate.remove((iid.toString()));
		}
	}
	
	/** Tell registry this is the most current version of search index */
	public synchronized void refreshCurrent(LocalIndex li){
		currentSearch.put(li.iid.toString(),li);
	}
	
	/** Tell registry that the current search index is bad */
	public synchronized void invalidateCurrent(IndexId iid){
		currentSearch.remove(iid.toString());
	}
	
	/** Automatically try to get the most current search index */
	public synchronized void refreshCurrent(IndexId iid){
		try {
			File cur = new File(iid.getSearchPath());
			if(cur.exists()){ 
				cur = cur.getCanonicalFile(); // follow symbolic links
				if(isValidTimestamp(cur.getName())){
					LocalIndex li = new LocalIndex(
							iid,
							cur.getPath(),
							getTimestamp(cur.getName()));
					
					currentSearch.put(iid.toString(),li);
				}
			}
		} catch (IOException e) {
			log.warn("Cannot follow symlink for file "+iid.getSearchPath());
		}
		
	}
	
	protected IndexRegistry(){
		GlobalConfiguration global = GlobalConfiguration.getInstance();
		latestSnapshot = new Hashtable<String,LocalIndex>();
		latestUpdate = new Hashtable<String,LocalIndex>();
		currentSearch = new Hashtable<String,LocalIndex>();
		
		// process all indexes of indexer
		for(IndexId iid : global.getMyIndex()){
			refreshSnapshots(iid);
		}
		
		// process all searched indexes
		for(IndexId iid : global.getMySearch()){
			refreshUpdates(iid);
			refreshCurrent(iid);
		}
	}
	
	public static synchronized IndexRegistry getInstance(){
		if(instance == null)
			instance = new IndexRegistry();
		
		return instance;
	}
}
