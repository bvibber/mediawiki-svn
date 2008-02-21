package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.util.ArrayList;
import java.util.BitSet;
import java.util.Hashtable;
import java.util.Map.Entry;

import org.apache.log4j.Logger;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.search.CachingWrapperFilter;
import org.apache.lucene.search.Filter;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.QueryFilter;
import org.wikimedia.lsearch.analyzers.WikiQueryParser;
import org.wikimedia.lsearch.config.GlobalConfiguration;

/**
 * Local cache of Filter, or more precisely of {@link CachingWrapperFilter}.
 * 
 * @author rainman
 *
 */
public class NamespaceCache {
	static org.apache.log4j.Logger log = Logger.getLogger(NamespaceCache.class);
	protected static Hashtable<NamespaceFilter,CachingWrapperFilter> cache = new Hashtable<NamespaceFilter,CachingWrapperFilter>();
	protected static Hashtable<NamespaceFilter,CachingWrapperFilter> redirectCache = new Hashtable<NamespaceFilter,CachingWrapperFilter>();
	
	public static CachingWrapperFilter get(NamespaceFilter key){
		return cache.get(key);
	}
	
	public static void put(NamespaceFilter key, CachingWrapperFilter value){
		cache.put(key,value);
	}
	
	/** Returns true if the filter can be composed from filters in cache */
	public static boolean isComposable(NamespaceFilter key){
		return true;
		/* ArrayList<NamespaceFilter> dec = key.decompose();
		for(NamespaceFilter nsf : dec){
			if(!cache.containsKey(nsf))
				return false;
		}
		return true; */
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
			// try to compose the filter from existing ones
			if(key.cardinality() > 1){
				ArrayList<NamespaceFilter> dec = key.decompose();
				ArrayList<Filter> filters = new ArrayList<Filter>();
				ArrayList<Filter> redirects = new ArrayList<Filter>();
				for(NamespaceFilter nsf : dec){
					if(cache.containsKey(nsf))
						filters.add(cache.get(nsf));
					else{ // didn't find the apropriate filter, make it
						log.info("Making filter for "+nsf);
						CachingWrapperFilter cwf = makeFilter(nsf);
						cache.put(nsf,cwf);
						filters.add(cwf);
					}
					redirects.add(getRedirectFilter(nsf));
				}
				log.debug("Made composite filter for "+key);
				// never cache composite filters
				return new NamespaceCompositeFilter(filters,redirects).bits(reader);				
			} else if(key.isAll()){
				ArrayList<Filter> redirects = new ArrayList<Filter>();
				for(NamespaceFilter nsf : cache.keySet())
					redirects.add(getRedirectFilter(nsf));
				CachingWrapperFilter cwf = new CachingWrapperFilter(new NamespaceCompositeFilter(new ArrayList<Filter>(),redirects));
				cache.put(key,cwf); // always cache
				log.info("Made \"all\" filter");
				return cwf.bits(reader);
			}
			// build new filter from query
			CachingWrapperFilter cwf = makeFilter(key);
			// cache only if defined as a textual prefix in global conf, or filters one namespace
			if(GlobalConfiguration.getInstance().getNamespacePrefixes().containsValue(key) || key.cardinality()==1)
				cache.put(key,cwf);
			log.info("Making new bitset for nsfilter "+key);
			return cwf.bits(reader);
		}
	}
	
	protected static Filter getRedirectFilter(NamespaceFilter nsf){
		if(redirectCache.containsKey(nsf))
			return redirectCache.get(nsf);
		else{ // make the bitset of all pages that redirect to namespace
			log.info("Making redirect cache for "+nsf);
			CachingWrapperFilter cwf = makeRedirectFilter(nsf);
			redirectCache.put(nsf,cwf);
			return cwf;						
		}
	}
	
	protected static CachingWrapperFilter makeFilter(NamespaceFilter key){
		Query q = WikiQueryParser.generateRewrite(key);
		return new CachingWrapperFilter(new QueryFilter(q));
	}
	
	protected static CachingWrapperFilter makeRedirectFilter(NamespaceFilter key){
		Query q = WikiQueryParser.generateRedirectRewrite(key);
		return new CachingWrapperFilter(new QueryFilter(q));
	}
}
