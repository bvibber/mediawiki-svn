package org.wikimedia.lsearch.beans;

import org.wikimedia.lsearch.config.IndexId;

/**
 * Identifies a local copy of index. 
 * 
 * @author rainman
 *
 */
public class LocalIndex {
	public IndexId iid;
	public String path;
	public long timestamp;
	
	public LocalIndex() {
		iid = null;
		path = null;
		timestamp = 0;
	}

	public LocalIndex(IndexId iid, String path, long timestamp) {
		this.iid = iid;
		this.path = path;
		this.timestamp = timestamp;
	}

	public IndexId getIid() {
		return iid;
	}

	public void setIid(IndexId iid) {
		this.iid = iid;
	}

	public String getPath() {
		return path;
	}

	public void setPath(String path) {
		this.path = path;
	}

	public long getTimestamp() {
		return timestamp;
	}

	public void setTimestamp(long timestamp) {
		this.timestamp = timestamp;
	}
	
	public String toString(){
		return path+" at "+timestamp+" for "+iid;
	}
	
	
}
