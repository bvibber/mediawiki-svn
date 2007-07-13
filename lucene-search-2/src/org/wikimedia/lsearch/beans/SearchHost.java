package org.wikimedia.lsearch.beans;

import org.wikimedia.lsearch.config.IndexId;

/** Identifies a single searcher */
public class SearchHost {
	public String host;
	public IndexId iid;
	
	public SearchHost(IndexId iid, String host) {
		this.host = host;
		this.iid = iid;
	}
	
	@Override
	public boolean equals(Object obj) {
		if(obj instanceof SearchHost)
			return ((SearchHost)obj).host.equals(host) && ((SearchHost)obj).iid.equals(iid);  
		else
			return super.equals(obj);
	}
	
	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + ((host == null) ? 0 : host.hashCode());
		result = PRIME * result + ((iid == null) ? 0 : iid.toString().hashCode());
		return result;
	}
	
	public String getHost() {
		return host;
	}

	public void setHost(String host) {
		this.host = host;
	}

	public IndexId getIid() {
		return iid;
	}

	public void setIid(IndexId iid) {
		this.iid = iid;
	}
	
	
}
