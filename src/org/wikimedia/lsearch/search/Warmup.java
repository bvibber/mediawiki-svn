package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.HashSet;
import java.util.Hashtable;
import java.util.Set;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.SimpleAnalyzer;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.Term;
import org.apache.lucene.search.Hits;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.TermQuery;
import org.wikimedia.lsearch.analyzers.Analyzers;
import org.wikimedia.lsearch.analyzers.FieldBuilder;
import org.wikimedia.lsearch.analyzers.FieldNameFactory;
import org.wikimedia.lsearch.analyzers.StopWords;
import org.wikimedia.lsearch.analyzers.WikiQueryParser;
import org.wikimedia.lsearch.benchmark.SampleTerms;
import org.wikimedia.lsearch.benchmark.Terms;
import org.wikimedia.lsearch.benchmark.WordTerms;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.spell.Suggest;
import org.wikimedia.lsearch.spell.SuggestSimilar;

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
	protected static Set<String> beingWarmedup = Collections.synchronizedSet(new HashSet<String>());
	
	public static boolean isBeingWarmedup(IndexId iid){
		return beingWarmedup.contains(iid.toString());
	}
	
	protected static int getWarmupCount(IndexId iid){
		String primary = "warmup";
		String secondary = "warmup";
		if(iid.isPrefix())
			secondary += "_prefix";
		else if(iid.isHighlight())
			secondary += "_hl";
		else if(iid.isSpell())
			secondary += "_spell";
		else if(iid.isTitlesBySuffix())
			secondary += "_titles_by_suffix";
		else if(iid.isRelated())
			secondary += "_related";
		else if(iid.isTitleNgram())
			secondary += "_title_ngram";
		
		Hashtable<String,String> warmup = global.getDBParams(iid.getDBname(),secondary);
		int count = warmup!=null? Integer.parseInt(warmup.get("count")) : 0;
		
		if(count == 0){
			warmup = global.getDBParams(iid.getDBname(),primary);
			count = warmup!=null? Integer.parseInt(warmup.get("count")) : 0;
		}
		return count;
	}
	
	/** If set in local config file waits for aggregate fields to finish caching */
	public static void waitForAggregate(IndexSearcherMul[] pool){
		try{
			boolean waitForAggregate = true; //Configuration.open().getString("Search","warmupaggregate","false").equalsIgnoreCase("true");
			if(waitForAggregate){ // wait for aggregate fields to be cached
				log.info("Waiting for aggregate caches on "+pool[0].getIndexReader().directory());
				boolean wait;
				do{
					wait = false;
					for(IndexSearcherMul is : pool){
						if(AggregateMetaField.isBeingCached(is.getIndexReader()) || ArticleMeta.isBeingCached(is.getIndexReader())){
							wait = true;
							break;
						}
					}
					if(wait)
						Thread.sleep(500);
				} while(wait);
			}
		} catch(InterruptedException e){
			e.printStackTrace();
		}
	}
	
	public static void warmupPool(IndexSearcherMul[] pool, IndexId iid, boolean useDelay, Integer useCount) throws IOException {
		for(IndexSearcherMul is : pool)
			warmupIndexSearcher(is,iid,useDelay,useCount);
	}
	
	/** Runs some typical queries on a local index searcher to preload caches, pages into memory, etc .. */
	public static void warmupIndexSearcher(IndexSearcherMul is, IndexId iid, boolean useDelay, Integer useCount) throws IOException {
		if(iid.isLinks() || iid.isPrecursor())
			return; // no warmaup for these
		try{
			beingWarmedup.add(iid.toString());
			log.info("Warming up index "+iid+" ...");
			long start = System.currentTimeMillis();
			IndexReader reader = is.getIndexReader();

			if(global == null)
				global = GlobalConfiguration.getInstance();		

			int count = useCount == null? getWarmupCount(iid) : useCount;

			if(iid.isSpell()){
				if(count > 0){
					Terms terms = getTermsForLang(iid.getLangCode());
					Suggest sug = new Suggest(iid,is,false);
					WikiQueryParser parser = new WikiQueryParser("contents",new SimpleAnalyzer(),new FieldBuilder(iid).getBuilder(),StopWords.getPredefinedSet(iid));
					NamespaceFilter nsf = iid.getDefaultNamespace();
					for(int i=0;i<count;i++){
						String searchterm = terms.next();
						sug.suggest(searchterm,parser.tokenizeForSpellCheck(searchterm),new Suggest.ExtraInfo(),nsf);
					}
				}
			} else if(iid.isTitleNgram()){
				if(count > 0){
					Terms terms = getTermsForLang(iid.getLangCode());
					SuggestSimilar sim = new SuggestSimilar(iid,is);
					for(int i=0;i<count;i++){
						sim.getSimilarTitles(terms.next(),new NamespaceFilter(),4);
					}
				}
			} else if(iid.isPrefix()){
				if(count > 0){
					Terms terms = getTermsForLang(iid.getLangCode());
					SearchEngine search = new SearchEngine();
					for(int i=0;i<count;i++){
						String searchterm = terms.next();
						searchterm = searchterm.substring(0,(int)Math.min(8*Math.random()+1,searchterm.length()));
						search.searchPrefixLocal(iid,searchterm,20,iid.getDefaultNamespace(),is);
					}
				}
			} else if((iid.isHighlight() || iid.isRelated()) && !iid.isTitlesBySuffix()){
				if(count > 0){
					// NOTE: this might not warmup all caches, but should read stuff into memory buffers
					for(int i=0;i<count;i++){
						int docid = (int)(Math.random()*is.maxDoc());
						reader.document(docid).get("key");
					}			
				}
			} else if(iid.isTitlesBySuffix()){
				// just initiate meta field caching, we want to avoid caching unnecessary filters
				AggregateMetaField.getCachedSource(is.getIndexReader(),"alttitle");
			} else{
				// normal indexes
				if(count == 0){
					makeNamespaceFilters(is,iid);
					simpleWarmup(is,iid);				
				} else{				
					makeNamespaceFilters(is,iid);
					warmupWithSearchTerms(is,iid,count,useDelay);
				}
			}	
			long delta = System.currentTimeMillis() - start;
			log.info("Warmed up "+iid+" in "+delta+" ms");
		} finally{
			beingWarmedup.remove(iid.toString());
		}
	}
	
	/** Warmup index using some number of simple searches */
	protected static void warmupWithSearchTerms(IndexSearcherMul is, IndexId iid, int count, boolean useDelay) {
		String lang = iid.getLangCode();
		FieldBuilder.BuilderSet b = new FieldBuilder(iid).getBuilder();
		WikiQueryParser parser = new WikiQueryParser(b.getFields().contents(),"0",Analyzers.getSearcherAnalyzer(iid,false),b,WikiQueryParser.NamespacePolicy.IGNORE,null);
		Terms terms = getTermsForLang(lang);
		
		try{	
			for(int i=0; i < count ; i++){
				Query q = parser.parse(terms.next());
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
			e.printStackTrace();
			log.error("Error warming up local IndexSearcherMul for "+iid);
		} catch (Exception e) {
			e.printStackTrace();
			log.error("Exception during warmup of "+iid+" : "+e.getMessage());
		}		
	}

	/** Get database of example search terms for language */
	protected static Terms getTermsForLang(String lang) {
		String lib = Configuration.open().getLibraryPath();
		if("en".equals(lang) || "de".equals(lang) || "es".equals(lang) || "fr".equals(lang) || "it".equals(lang) || "pt".equals(lang)){
			if( !langTerms.contains(lang) )
				langTerms.put(lang,new WordTerms(lib+Configuration.PATH_SEP+"dict"+Configuration.PATH_SEP+"terms-"+lang+".txt.gz"));
			
			return langTerms.get(lang);
		} else
			return new SampleTerms();		
	}

	/** Preload all predefined filters */
	protected static void makeNamespaceFilters(IndexSearcherMul is, IndexId iid) {
		ArrayList<NamespaceFilter> filters = new ArrayList<NamespaceFilter>();
		filters.addAll(global.getNamespacePrefixes().values());
		filters.add(new NamespaceFilter()); // "all"
		for(NamespaceFilter filter : filters){
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
			FieldBuilder.BuilderSet b = new FieldBuilder(iid).getBuilder();
			WikiQueryParser parser = new WikiQueryParser(b.getFields().contents(),"0",Analyzers.getSearcherAnalyzer(iid,false),b,WikiQueryParser.NamespacePolicy.IGNORE,null);
			Query q = parser.parse("wikimedia foundation");
			is.search(q,new NamespaceFilterWrapper(new NamespaceFilter("0")));
		} catch (IOException e) {
			log.error("Error warming up local IndexSearcherMul for "+iid);
		}
	}

}
