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
import org.wikimedia.lsearch.analyzers.FieldBuilder;
import org.wikimedia.lsearch.analyzers.FieldNameFactory;
import org.wikimedia.lsearch.analyzers.WikiQueryParser;
import org.wikimedia.lsearch.benchmark.SampleTerms;
import org.wikimedia.lsearch.benchmark.Terms;
import org.wikimedia.lsearch.benchmark.WordTerms;
import org.wikimedia.lsearch.config.Configuration;
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
	public static void warmupIndexSearcher(IndexSearcherMul is, IndexId iid, boolean useDelay){
		log.info("Warming up index "+iid+" ...");
		long start = System.currentTimeMillis();
		
		if(global == null)
			global = GlobalConfiguration.getInstance();		
		
		Hashtable<String,String> warmup = global.getDBParams(iid.getDBname(),"warmup");
		if(iid.isSpell() || iid.isPrefix()); // no warmup for spell-chekers and prefixes (for now)
		else if(warmup == null){
			makeNamespaceFilters(is,iid);
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
			warmupSearchTerms(is,iid,count,useDelay);
			long delta = System.currentTimeMillis() - start;
			log.info("Warmed up "+iid+" in "+delta+" ms");
		}					
	}
	
	/** Warmup index using some number of simple searches */
	protected static void warmupSearchTerms(IndexSearcherMul is, IndexId iid, int count, boolean useDelay) {
		String lang = global.getLanguage(iid.getDBname());
		FieldBuilder.BuilderSet b = new FieldBuilder(lang).getBuilder();
		WikiQueryParser parser = new WikiQueryParser(b.getFields().contents(),"0",Analyzers.getSearcherAnalyzer(iid,false),b,WikiQueryParser.NamespacePolicy.IGNORE,null);
		Terms terms = getTermsForLang(lang);
		
		try{	
			for(int i=0; i < count ; i++){
				Query q = parser.parseFourPass(terms.next(),WikiQueryParser.NamespacePolicy.IGNORE,iid.getDBname());
				Hits hits = is.search(q);
				for(int j =0; j<20 && j<hits.length(); j++)
					hits.doc(j); // retrieve some documents
				if(useDelay){
					if(i<1000)
						Thread.sleep(100);
					else
						Thread.sleep(50);
				}
			}
		} catch (IOException e) {
			log.error("Error warming up local IndexSearcherMul for "+iid);
		} catch (ParseException e) {
			log.error("Error parsing query in warmup of IndexSearcherMul for "+iid);
		} catch (Exception e) {
			log.error("Exception during warmup "+e.getMessage());
		}		
	}

	/** Get database of example search terms for language */
	protected static Terms getTermsForLang(String lang) {
		String lib = Configuration.open().getString("MWConfig","lib","./lib");
		if("en".equals(lang) || "de".equals(lang) || "es".equals(lang) || "fr".equals(lang) || "it".equals(lang) || "pt".equals(lang))
			langTerms.put(lang,new WordTerms(lib+"/dict/terms-"+lang+".txt.gz"));		
		if(lang.equals("sample"))
			return new SampleTerms();
		
		if(langTerms.containsKey(lang))
			return langTerms.get(lang);
		else
			return langTerms.get("en");
	}

	/** Preload all predefined filters */
	protected static void makeNamespaceFilters(IndexSearcherMul is, IndexId iid) {
		Hashtable<String,NamespaceFilter> filters = global.getNamespacePrefixes();
		for(NamespaceFilter filter : filters.values()){
			try {
				is.search(new TermQuery(new Term("contents","wikipedia")),
						new NamespaceFilterWrapper(filter));
			} catch (IOException e) {
				log.warn("I/O error while preloading filter for "+iid+" for filter "+filter+" : "+e.getMessage());
			}
		}
	}

	/** Just run one complex query and rebuild the main namespace filter */
	public static void simpleWarmup(IndexSearcherMul is, IndexId iid){
		try{
			String lang = global.getLanguage(iid.getDBname());
			FieldBuilder.BuilderSet b = new FieldBuilder(lang).getBuilder();
			WikiQueryParser parser = new WikiQueryParser(b.getFields().contents(),"0",Analyzers.getSearcherAnalyzer(iid,false),b,WikiQueryParser.NamespacePolicy.IGNORE,null);
			Query q = parser.parseFourPass("a OR very OR long OR title OR involving OR both OR wikipedia OR and OR pokemons",WikiQueryParser.NamespacePolicy.IGNORE,iid.getDBname());
			is.search(q,new NamespaceFilterWrapper(new NamespaceFilter("0")));
		} catch (IOException e) {
			log.error("Error warming up local IndexSearcherMul for "+iid);
		} catch (ParseException e) {
			log.error("Error parsing query in warmup of IndexSearcherMul for "+iid);
		}
	}

}
