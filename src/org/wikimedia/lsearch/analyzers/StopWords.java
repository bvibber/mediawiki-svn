package org.wikimedia.lsearch.analyzers;

import java.io.BufferedReader;
import java.io.File;
import java.io.FilenameFilter;
import java.io.IOException;
import java.io.InputStreamReader;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Hashtable;

import org.apache.log4j.Logger;
import org.apache.lucene.index.Term;
import org.apache.lucene.index.TermDocs;
import org.wikimedia.lsearch.benchmark.WordTerms;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.config.IndexRegistry;
import org.wikimedia.lsearch.search.IndexSearcherMul;
import org.wikimedia.lsearch.search.SearcherCache;
import org.wikimedia.lsearch.util.HighFreqTerms;
import org.wikimedia.lsearch.util.UnicodeDecomposer;

/**
 * Offer various ways of retrieving a list of stop words.
 * For some languages, it's a fixed list, for other, it will
 * be extracted from an index snapshot
 * 
 * @author rainman
 *
 */
public class StopWords {
	static Logger log = Logger.getLogger(StopWords.class);
	protected static Hashtable<String,HashSet<String>> cachePredefined = new Hashtable<String,HashSet<String>>();
	protected static boolean loadedPredefined = false;
	
	public static String[] getStopWords(IndexId iid){
		HashSet<String> pre = getPredefinedSet(iid);
		if(pre.size() > 0)
			return pre.toArray(new String[]{});
		try{
			return HighFreqTerms.getHighFreqTerms(iid.getDB(),"contents",50).toArray(new String[] {});
		} catch(Exception e){
			log.warn("Failed to fetch stop words for "+iid,e);
			return new String[] {};
		}		
	}
	
	protected static Hashtable<String,ArrayList<String>> cache = new Hashtable<String,ArrayList<String>>();
	protected static IndexRegistry registry = null;
	
	/** Get a cached entry from spell-check metadata 
	 * @throws IOException */
	@SuppressWarnings("unchecked")
	public static ArrayList<String> getCached(IndexId iid) throws IOException{
		ArrayList<String> stopWords = cache.get(iid.toString());
		if(stopWords != null)
			return (ArrayList<String>) stopWords.clone();
		else{
			if(registry == null)
				registry = IndexRegistry.getInstance();
			if(iid.hasSpell() && registry.getCurrentSearch(iid.getSpell()) != null){
				synchronized(cache){
					stopWords = new ArrayList<String>();
					IndexSearcherMul searcher = SearcherCache.getInstance().getLocalSearcher(iid.getSpell());
					TermDocs d = searcher.getIndexReader().termDocs(new Term("metadata_key","stopWords"));
					if(d.next()){
						String val = searcher.doc(d.doc()).get("metadata_value");
						for(String sw : val.split(" ")){
							stopWords.add(sw);
						}
					}
					cache.put(iid.toString(),stopWords);
				}
				return (ArrayList<String>) stopWords.clone();
			}
		}
		return new ArrayList<String>();
	}
	
	/** Get brand new set of stop words (to be used within one thread) */
	public static HashSet<String> getCachedSet(IndexId iid) {
		HashSet<String> ret = new HashSet<String>();		
		try {
			ret.addAll(getCached(iid));
		} catch (IOException e) {
			log.warn("Cannot get cached stop words for "+iid,e);
		}
		return ret;
	}
	
	/** Get a brand new hash set of predifined stop words (i.e. not those generated from lucene indexes) */
	public static HashSet<String> getPredefinedSet(String langCode){
		loadPredefined();
		HashSet<String> ret = new HashSet<String>();
		HashSet<String> cached = cachePredefined.get(langCode);
		if(cached != null){
			synchronized(cached){
				ret.addAll(cached);
			}
		}
		return ret;
	}
	public static HashSet<String> getPredefinedSet(IndexId iid){
		return getPredefinedSet(iid.getLangCode());
	}
	
	protected static void loadPredefined(){
		if(loadedPredefined)
			return;
		synchronized(cachePredefined){
			try{
				long start = System.currentTimeMillis();
				BufferedReader list = new BufferedReader(new InputStreamReader(StopWords.class.getResourceAsStream("/dict/stopwords-list.txt")));
				String line = null;
				while((line = list.readLine()) != null){
					String name = line.trim();
					if(name.indexOf('-') != -1){
						String lang = name.substring(name.indexOf('-')+1,name.indexOf('.'));
						ArrayList<String> words = WordTerms.loadWordFreq(StopWords.class.getResourceAsStream("/dict/"+name));
						HashSet<String> set = new HashSet<String>();
						for(String w : words){
							set.add(w);
							set.add(FastWikiTokenizerEngine.decompose(w));
						}
						cachePredefined.put(lang,set);
					}
				}
				log.info("Successfully loaded stop words for: "+cachePredefined.keySet()+" in "+(System.currentTimeMillis()-start)+" ms");
			} catch(IOException e){
				e.printStackTrace();
				log.error("Cannot load stop words definitions: "+e.getMessage(),e);
			}
			loadedPredefined = true;
		}
	}
}
