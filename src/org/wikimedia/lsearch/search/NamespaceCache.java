package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.util.ArrayList;
import java.util.BitSet;
import java.util.Hashtable;

import org.apache.log4j.Logger;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.Term;
import org.apache.lucene.index.TermDocs;
import org.apache.lucene.index.TermEnum;
import org.apache.lucene.search.BooleanQuery;
import org.apache.lucene.search.Filter;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.QueryWrapperFilter;
import org.apache.lucene.search.TermQuery;
import org.apache.lucene.search.BooleanClause.Occur;
import org.wikimedia.lsearch.analyzers.WikiQueryParser;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;

/**
 * Local cache of Filter, or more precisely of {@link CachedFilter}.
 * 
 * @author rainman
 *
 */
public class NamespaceCache {
	static org.apache.log4j.Logger log = Logger.getLogger(NamespaceCache.class);
	protected static Hashtable<NamespaceFilter,CachedFilter> cache = new Hashtable<NamespaceFilter,CachedFilter>();
	protected static Hashtable<NamespaceFilter,CachedFilter> redirectCache = new Hashtable<NamespaceFilter,CachedFilter>();
	/** for special cases, key is custom string - used for default namespaces filters on titles indexes */
	protected static Hashtable<String,CachedFilter> specialCache = new Hashtable<String,CachedFilter>();
	
	public static class AllFilter extends Filter {

		@Override
		public BitSet bits(IndexReader reader) throws IOException {
			BitSet bits = new BitSet(reader.maxDoc());
			bits.set(0,reader.maxDoc());
			TermEnum terms = reader.terms(new Term("redirect_namespace",""));
			Term t;
			while((t = terms.term()) != null){
				if(!"redirect_namespace".equals(t.field()))
					break;
				// unset all documents that redirect somewhere
				TermDocs td = reader.termDocs(t);
				while(td.next()){
					bits.clear(td.doc());
				}
				if(!terms.next())
					break;
			}
			return bits;
		}
		
	}
	
	public static CachedFilter get(NamespaceFilter key){
		return cache.get(key);
	}
	
	public static void put(NamespaceFilter key, CachedFilter value){
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
	public static BitSet bits(NamespaceFilter key, IndexReader reader) throws IOException{
		synchronized(reader){
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
							log.debug("Making filter for "+nsf);
							CachedFilter cwf = makeFilter(nsf);
							cache.put(nsf,cwf);
							filters.add(cwf);
						}
						redirects.add(getRedirectFilter(nsf));
					}
					log.debug("Made composite filter for "+key);
					// never cache composite filters
					return new NamespaceCompositeFilter(filters,redirects).bits(reader);				
				} else if(key.isAll()){
					CachedFilter cwf = new CachedFilter(new AllFilter());
					cache.put(key,cwf); // always cache
					log.debug("Made \"all\" filter");
					return cwf.bits(reader);
				}
				// build new filter from query
				CachedFilter cwf = makeFilter(key);
				// cache only if defined as a textual prefix in global conf, or filters one namespace
				if(GlobalConfiguration.getInstance().getNamespacePrefixes().containsValue(key) || key.cardinality()==1)
					cache.put(key,cwf);
				log.info("Making new bitset for nsfilter "+key);
				return cwf.bits(reader);
			}
		}
	}
	
	/** 
	 * Get a filter for default namespace search over titles index, 
	 * e.g. if default are: enwiki -> {0}, enwiktionary -> {0,100},
	 * function will separately filter namespaces for each suffix
	 * 
	 * @param titles
	 * @param reader
	 * @return
	 * @throws IOException 
	 */
	public static BitSet defaultTitleBits(IndexId titles, IndexReader reader) throws IOException {
		synchronized(reader){
			String key = titles.toString()+":<default>";
			CachedFilter cwf = specialCache.get(key);
			if(cwf != null)
				return cwf.bits(reader);
			
			// create new
			BooleanQuery bq = new BooleanQuery(true);
			for(IndexId iid : titles.getPhysicalIndexIds()){
				for(String dbname : iid.getSuffixToDbname().values()){
					BooleanQuery sub = new BooleanQuery(true);
					sub.add(new TermQuery(new Term("dbname",dbname)),Occur.MUST);
					sub.add(WikiQueryParser.generateRewrite(IndexId.get(dbname).getDefaultNamespace()),Occur.MUST);
					bq.add(sub,Occur.SHOULD);
				}
			}
			log.info("Caching "+key+" with "+bq);
			cwf = new CachedFilter(new QueryWrapperFilter(bq));
			specialCache.put(key,cwf);
			return cwf.bits(reader);
		}
	}

	
	protected static Filter getRedirectFilter(NamespaceFilter nsf){
		if(redirectCache.containsKey(nsf))
			return redirectCache.get(nsf);
		else{ // make the bitset of all pages that redirect to namespace
			log.info("Making redirect cache for "+nsf);
			CachedFilter cwf = makeRedirectFilter(nsf);
			redirectCache.put(nsf,cwf);
			return cwf;						
		}
	}
	
	protected static CachedFilter makeFilter(NamespaceFilter key){
		Query q = WikiQueryParser.generateRewrite(key);
		return new CachedFilter(new QueryWrapperFilter(q));
	}
	
	protected static CachedFilter makeRedirectFilter(NamespaceFilter key){
		Query q = WikiQueryParser.generateRedirectRewrite(key);
		return new CachedFilter(new QueryWrapperFilter(q));
	}

}
