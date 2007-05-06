package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.util.Hashtable;

import org.apache.log4j.Logger;
import org.apache.lucene.index.Term;
import org.apache.lucene.queryParser.ParseException;
import org.apache.lucene.search.Hits;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.TermQuery;
import org.wikimedia.lsearch.analyzers.Analyzers;
import org.wikimedia.lsearch.analyzers.WikiQueryParser;
import org.wikimedia.lsearch.benchmark.Terms;
import org.wikimedia.lsearch.benchmark.WordTerms;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;

/**
 * Methods to warm up index and preload caches.  
 * 
 * @author rainman
 *
 */
public class Warmup {
	static Logger log = Logger.getLogger(Warmup.class);
	protected static GlobalConfiguration global = null;
	protected static Hashtable<String,Terms> langTerms = new Hashtable<String,Terms>();
	
	/** Runs some typical queries on a local index searcher to preload caches, pages into memory, etc .. */
	public static void warmupIndexSearcher(IndexSearcherMul is, IndexId iid){
		log.info("Warming up index "+iid+" ...");
		long start = System.currentTimeMillis();
		
		if(global == null)
			global = GlobalConfiguration.getInstance();		
		
		Hashtable<String,String> warmup = global.getDBParams(iid.getDBname(),"warmup");
		if(warmup == null){
			simpleWarmup(is,iid);
			log.info("Warmed up "+iid);
		}
		else{
			int count;
			try{
				count = Integer.parseInt(warmup.get("count"));
			} catch(Exception e){
				log.warn("Wrong parameters for warmup of database "+iid+" in global settings");
				simpleWarmup(is,iid);
				return;
			}
			makeNamespaceFilters(is,iid);
			warmupSearchTerms(is,iid,count);
			long delta = System.currentTimeMillis() - start;
			log.info("Warmed up "+iid+" in "+delta+" ms");
		}					
	}
	
	/** Warmup index using some number of simple searches */
	protected static void warmupSearchTerms(IndexSearcherMul is, IndexId iid, int count) {
		WikiQueryParser parser = new WikiQueryParser("contents","main",Analyzers.getSearcherAnalyzer(iid),WikiQueryParser.NamespacePolicy.IGNORE);
		Terms terms = getTermsForLang(global.getLanguage(iid.getDBname()));
		
		try{	
			for(int i=0; i < count ; i++){
				Query q = parser.parseTwoPass(terms.next(),WikiQueryParser.NamespacePolicy.IGNORE);
				Hits hits = is.search(q);
				for(int j =0; j<20 && j<hits.length(); j++)
					hits.doc(j); // retrieve some documents
			}
		} catch (IOException e) {
			log.error("Error warming up local IndexSearcherMul for "+iid);
		} catch (ParseException e) {
			log.error("Error parsing query in warmup of IndexSearcherMul for "+iid);
		}		
	}

	/** Get database of example search terms for language */
	protected static Terms getTermsForLang(String language) {
		if(langTerms.get("en")==null)
			langTerms.put("en",new WordTerms("./test-data/words-wikilucene.ngram.gz"));
		return langTerms.get("en");
	}

	/** Generate filters for all namespace from 0 to 15 */
	protected static void makeNamespaceFilters(IndexSearcherMul is, IndexId iid) {
		if(iid.isMainPart())
			return; // no filters are ever used on mainpart
		for(int ns = 0; ns < 15 ; ns ++){
			if(ns==0 && iid.isRestPart())
				continue;
			try {
				is.search(new TermQuery(new Term("contents","wikipedia")),
						new NamespaceFilterWrapper(new NamespaceFilter(Integer.toString(ns))));
			} catch (IOException e) {
				log.warn("I/O error while preloading filter for "+iid+" for namespace "+ns+" : "+e.getMessage());
			}
		}
	}

	/** Just run one complex query and rebuild the main namespace filter */
	public static void simpleWarmup(IndexSearcherMul is, IndexId iid){
		try{
			WikiQueryParser parser = new WikiQueryParser("contents","main",Analyzers.getSearcherAnalyzer(iid),WikiQueryParser.NamespacePolicy.IGNORE);
			Query q = parser.parseTwoPass("a OR very OR long OR title OR involving OR both OR wikipedia OR and OR pokemons",WikiQueryParser.NamespacePolicy.IGNORE);
			is.search(q,new NamespaceFilterWrapper(new NamespaceFilter("0")));
		} catch (IOException e) {
			log.error("Error warming up local IndexSearcherMul for "+iid);
		} catch (ParseException e) {
			log.error("Error parsing query in warmup of IndexSearcherMul for "+iid);
		}
	}

}
