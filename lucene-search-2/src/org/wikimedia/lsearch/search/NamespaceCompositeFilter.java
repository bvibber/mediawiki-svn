package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.util.ArrayList;
import java.util.BitSet;
import java.util.WeakHashMap;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.search.Filter;
/**
 * Make a new filter doing logical OR on existing filters.
 * This is used for making custom filters from cached filters
 * for every namespace.
 * 
 * 
 * @author rainman
 *
 */
public class NamespaceCompositeFilter extends Filter {
	protected ArrayList<Filter> filters;
	protected ArrayList<Filter> redirects;
	
	public NamespaceCompositeFilter(ArrayList<Filter> filters, ArrayList<Filter> redirects){
		this.filters = filters;
		this.redirects = redirects;
	}
	
	@Override
	public BitSet bits(IndexReader reader) throws IOException {
		BitSet bits = new BitSet(reader.maxDoc());
		
		if(filters.size() == 0)
			bits.set(0,reader.maxDoc());
		else{
			// do logical OR to get composite filter
			for(Filter f : filters){
				bits.or(f.bits(reader));
			}
		}
		// delete cross-namespace redirects
		for(Filter f : redirects){
			bits.andNot(f.bits(reader));
		}

		return bits;
	}

}
