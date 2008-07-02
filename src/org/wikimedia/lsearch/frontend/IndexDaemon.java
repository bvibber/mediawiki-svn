/*
 * Created on Feb 2, 2007
 *
 */
package org.wikimedia.lsearch.frontend;

import org.wikimedia.lsearch.beans.Title;
import org.wikimedia.lsearch.index.IndexThread;
import org.wikimedia.lsearch.index.IndexUpdateRecord;

/**
 * Abstracts operations of indexer for various frontends. 
 * 
 * @author rainman
 *
 */
public class IndexDaemon {
	static protected IndexThread indexer = null; 

	public IndexDaemon() {
		if(indexer == null){
			indexer = IndexThread.getInstance();
		}
	}
	public String getStatus() {
		return indexer.getStatus();
	}

	public void stop() {
		indexer.stopThread();
	}

	public void start() {
		indexer.startThread();
	}

	public void flushAll() {
		indexer.flush();
	}

	public void quit() {
		indexer.quit();
	}

	public void makeSnapshots(){
		makeSnapshots("");
	}
	
	public void makeSnapshots(String pattern){
		indexer.makeSnapshotsNow(true,pattern,false);
	}
	
	public void snapshot(){
		makeSnapshots();
	}
	
	public void snapshot(String pattern){
		makeSnapshots(pattern);
	}
	
	public void snapshotPrecursors(){
		snapshotPrecursors("","true");
	}
	public void snapshotPrecursors(String pattern){
		indexer.makeSnapshotsNow(false,pattern,true);
	}
	
	public void snapshotPrecursors(String pattern, String optimize){
		indexer.makeSnapshotsNow(optimize.equalsIgnoreCase("true"),pattern,true);
	}

}
