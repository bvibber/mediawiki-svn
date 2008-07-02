package org.apache.lucene.search;

import java.io.IOException;

import org.apache.lucene.index.IndexReader;
import org.wikimedia.lsearch.search.NamespaceFilter;

/** Meta information about the whole article */
public interface ArticleInfo {
	/** Initialize for retrieval */
	public void init(IndexReader reader) throws IOException;
	
	/** Check if docid is subpage (valid after init()) */
	public boolean isSubpage(int docid) throws IOException;
	
	/** How old the indexed page is in days (relative to now) */
	public float daysOld(int docid) throws IOException;
}
