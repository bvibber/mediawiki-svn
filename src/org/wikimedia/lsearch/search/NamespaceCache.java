package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.util.BitSet;
import java.util.Hashtable;

import org.apache.log4j.Logger;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.Term;
import org.apache.lucene.search.CachingWrapperFilter;
import org.apache.lucene.search.Filter;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.QueryFilter;
import org.apache.lucene.search.TermQuery;
import org.wikimedia.lsearch.analyzers.WikiQueryParser;

/**
 * Local cache of Filter, or more precisely of {@link CachingWrapperFilter}.
 * 
 * @author rainman
 *
 */
public class NamespaceCache {
	static org.apache.log4j.Logger log = Logger.getLogger(NamespaceCache.class);
	protected static Hashtable<NamespaceFilter,CachingWrapperFilter> cache = new Hashtable<NamespaceFilter,CachingWrapperFilter>();
	
	public static CachingWrapperFilter get(NamespaceFilter key){
		return cache.get(key);
	}
	
	public static void put(NamespaceFilter key, CachingWrapperFilter value){
		cache.put(key,value);
	}
		
	/** 
	 * Get bits from filter, if filter does not exist, new one will be 
	 * created. 
	 *  
	 * @param key
	 * @param reader
	 * @return
	 * @throws IOException
	 */
	public synchronized static BitSet bits(NamespaceFilter key, IndexReader reader) throws IOException{
		Filter f = cache.get(key);
		if(f != null){
			log.debug("Got bitset from cache for nsfilter "+key);
			return f.bits(reader);
		} else {
			Query q = WikiQueryParser.generateRewrite(key);
			CachingWrapperFilter cwf = new CachingWrapperFilter(new QueryFilter(q));
			cache.put(key,cwf);
			log.debug("Making new bitset for nsfilter "+key);
			return cwf.bits(reader);
		}
	}
}
