package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.util.BitSet;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.search.Filter;

/** 
 * Instance of this class is passed around during distributed searching.
 * It hold NamespaceFilter as a key to local cache of namespace filters 
 * {@link NamespaceCache}. 
 * 
 * @author rainman
 *
 */
public class NamespaceFilterWrapper extends Filter {
	protected NamespaceFilter filter;
	
	NamespaceFilterWrapper(NamespaceFilter filter){
		this.filter = filter;
	}
	
	@Override
	public BitSet bits(IndexReader reader) throws IOException {
		return NamespaceCache.bits(filter,reader);
	}
	
	@Override
	public String toString() {
		return "wrap: "+filter; 
	}

	public NamespaceFilter getFilter() {
		return filter;
	}

}
