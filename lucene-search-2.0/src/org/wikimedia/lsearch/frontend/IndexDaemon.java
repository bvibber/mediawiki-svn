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
			indexer = new IndexThread();
			indexer.start();
		}
	}

	public void updatePage(String databaseName, Title title, String isRedirect, String text ) {
		IndexThread.enqueue(new IndexUpdateRecord(databaseName, title, text, isRedirect.equals("1"), IndexUpdateRecord.Action.UPDATE));
	}

	public void deletePage(String databaseName, Title title) {
		IndexThread.enqueue(new IndexUpdateRecord(databaseName,title,"",false,IndexUpdateRecord.Action.DELETE));
	}

	public void addPage(String databaseName, Title title, String text) {
		IndexThread.enqueue(new IndexUpdateRecord(databaseName, title, text, false, IndexUpdateRecord.Action.ADD));
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
		indexer.makeSnapshotsNow();
	}

}
