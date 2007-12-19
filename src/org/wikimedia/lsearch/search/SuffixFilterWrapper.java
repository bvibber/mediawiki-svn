package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.util.BitSet;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.search.Filter;
/**
 * A search filter: excludes a certain suffix in titles indexes from the
 * search results. Always uses the local filter cache repository. 
 * @author rainman
 *
 */
public class SuffixFilterWrapper extends Filter {
	protected SuffixFilter filter;	
	
	public SuffixFilterWrapper(SuffixFilter filter){
		this.filter = filter;
	}
	
	@Override
	public BitSet bits(IndexReader reader) throws IOException {
		return SuffixFilterCache.bits(filter,reader);
	}
	
	@Override
	public String toString() {
		return "wrap: "+filter; 
	}

	public SuffixFilter getFilter() {
		return filter;
	}
}
