package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.util.BitSet;
import java.util.Hashtable;

import org.apache.log4j.Logger;
import org.apache.lucene.index.IndexReader;


public class SuffixNamespaceCache {
	protected static Logger log = Logger.getLogger(SuffixNamespaceCache.class);
	protected static Hashtable<SuffixNamespaceFilter,CachedFilter> cache = new Hashtable<SuffixNamespaceFilter,CachedFilter>();
	
	public static BitSet bits(SuffixNamespaceFilter filter, IndexReader reader) throws IOException{
		synchronized(reader){
			CachedFilter cwf = cache.get(filter);
			if(cwf == null){
				log.info("Making filter for "+filter);
				if( !filter.shouldCache() )
					return filter.bits(reader);				
				// cache filters
				cwf = new CachedFilter(filter);
				cache.put(filter,cwf);
				log.info("Cached "+filter);
			}
			return cwf.bits(reader);			
		}
	}
}
