package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.util.BitSet;
import java.util.WeakHashMap;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.search.Filter;
import org.apache.lucene.store.Directory;

/**
 * Cache filter over a directory (valid only for readonly readers!)
 * 
 * @author rainman
 *
 */
public class CachedFilter extends Filter {
	protected Filter filter;
	protected transient WeakHashMap<Directory,BitSet> cache = new WeakHashMap<Directory,BitSet>();
	
	/** register all filters every made as long as they are used */
	protected static transient WeakHashMap<CachedFilter,Boolean> allFilters = new WeakHashMap<CachedFilter,Boolean>();
	
	public CachedFilter(Filter f){
		this.filter = f;
		synchronized(allFilters){
			allFilters.put(this,true);
		}
	}
	
	@Override
	public BitSet bits(IndexReader reader) throws IOException {
		BitSet bits = null;
		synchronized(cache){
			bits = cache.get(reader.directory());
			if(bits != null)
				return bits;
		}
		bits = filter.bits(reader);
		synchronized(cache){
			cache.put(reader.directory(),bits);
		}
		return bits;
	}
	
	public void invalidateCache(IndexReader reader){
		synchronized(cache){
			cache.remove(reader.directory());
		}
	}
	
	public static void invalideAllFilterCache(IndexReader reader){
		synchronized(allFilters){
			for(CachedFilter f : allFilters.keySet())
				f.invalidateCache(reader);
		}
	}
	
	public String toString() {
		return "CachedFilter("+filter+")";	
	}
	
}
