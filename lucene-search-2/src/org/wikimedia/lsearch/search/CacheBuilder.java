package org.wikimedia.lsearch.search;

import java.io.IOException;

import org.apache.lucene.document.Document;

/** Class that builds cache for meta fields or documents */
public interface CacheBuilder {
	/** init cach values, etc.. */
	public void init();
	
	/** Cache info about document */
	public void cache(int docid, Document doc) throws IOException;
	
	/** end of caching, compact, etc.. */
	public void end();

}
