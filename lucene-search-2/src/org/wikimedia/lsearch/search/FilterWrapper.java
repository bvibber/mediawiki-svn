package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.util.ArrayList;
import java.util.BitSet;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.search.Filter;

/** 
 * Instance of this class is passed around during distributed searching.
 * It hold list of different filters that need to be locally calculated and
 * used during search
 * 
 * @author rainman
 *
 */
public class FilterWrapper extends Filter {
	protected NamespaceFilter nsFilter;
	/** custom filters AND-ed with nsFilter */
	protected ArrayList<Filter> filters = new ArrayList<Filter>();
	
	public FilterWrapper(){
	}
	
	public FilterWrapper(NamespaceFilter nsFilter){
		this.nsFilter = nsFilter;
	}
	
	public void setNamespaceFilter(NamespaceFilter nsFilter){
		this.nsFilter = nsFilter;
	}
	
	public void addFilter(Filter f){
		filters.add(f);
	}
	
	@Override
	public BitSet bits(IndexReader reader) throws IOException {
		BitSet bits = null;
		boolean cloned = false;
		if(nsFilter != null)
			bits = NamespaceCache.bits(nsFilter,reader);
		for(Filter f : filters){
			if(bits != null && !cloned){ // we need to clone namespace bits since they get cached
				bits = (BitSet)bits.clone();
				cloned = true;
			}
			if(bits == null)
				bits = f.bits(reader);
			else
				bits.and(f.bits(reader));
		}
		return bits;
		
	}
	
	@Override
	public String toString() {
		return "wrap: "+nsFilter+" "+filters; 
	}

	public NamespaceFilter getNamespaceFilter() {
		return nsFilter;
	}
	
	public ArrayList<Filter> getFilters(){
		return filters;
	}
	
	public boolean hasNamespaceFilter(){
		return nsFilter != null;
	}
	
	public boolean hasCustomFilters(){
		return filters.size() > 0;
	}
	
	public boolean hasAnyFilters(){
		return hasNamespaceFilter() || hasCustomFilters();
	}
	
	/** If filter is not empty, get this filter, otherwise just get null */
	public Filter getFilterOrNull(){
		if(hasAnyFilters())
			return this;
		else 
			return null;
	}

}
