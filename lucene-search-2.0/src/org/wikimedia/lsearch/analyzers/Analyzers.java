package org.wikimedia.lsearch.analyzers;

import java.util.ArrayList;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.PerFieldAnalyzerWrapper;
import org.apache.lucene.analysis.PorterStemFilter;
import org.apache.lucene.analysis.SimpleAnalyzer;
import org.apache.lucene.analysis.de.GermanStemFilter;
import org.apache.lucene.analysis.fr.FrenchStemFilter;
import org.apache.lucene.analysis.nl.DutchStemFilter;
import org.apache.lucene.analysis.ru.RussianStemFilter;
import org.apache.lucene.analysis.th.ThaiWordFilter;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.test.AliasPorterStemFilter;

/**
 * Global functions related to creation/usage of analyzers.
 * 
 * @author rainman
 *
 */
public class Analyzers {
	static org.apache.log4j.Logger log = Logger.getLogger(Analyzers.class);
	
	protected static GlobalConfiguration global = null;

	/** Get a token filter (usually stemmer) */
	public static Class getStemmerForLanguage(String language) {

		if(language == null)
			language = "en";
		
		if(language.equals("en")){
			log.debug("Using english alias stemmer.");
			return PorterStemFilter.class;
		} else if(language.equals("de"))
			return GermanStemFilter.class;
		else if(language.equals("eo"))
			return EsperantoStemFilter.class;
		else if(language.equals("ru"))
			return RussianStemFilter.class;
		else if(language.equals("fr"))
			return FrenchStemFilter.class;
		else if(language.equals("nl"))
			return DutchStemFilter.class;
		else{ 
			log.debug("Not using a language stemmer");
			return null;
		}
	}
	
	/** Get additional filter */
	public static Class getCustomFilterForLanguage(String language) {

		if(language == null)
			language = "en";
		
		if(language.equals("th"))
			return ThaiWordFilter.class;
		else if(language.equals("sr"))
			return SerbianFilter.class;
		else{ 
			return null;
		}
	}
	
	/** Analyzer for titles, for most languages just a plain
	 *  wiki tokenizer (lowercase, unicode normalization), but
	 *  no stemming or aliases. 
	 * @param language
	 * @return
	 */
	public static Analyzer getTitleAnalyzer(FilterFactory filters){
		return new QueryLanguageAnalyzer(filters);
	}

	/**
	 * Construct new analyzer for indexer. The text is first tokenized to get categories, and then a
	 * new per field analyzer is constructed, that outputs categories (each token is one category) 
	 * for category field, and regular text tokens for contents field. <br/>
	 * 
	 * This analyzer IS NOT to be reused. It is constructed specifically, and only for this input
	 * text. 
	 * 
	 * @param text   text to be tokenized
	 * @param languageAnalyzer  language filter class (e.g. PorterStemFilter)
	 * @return
	 */
	public static PerFieldAnalyzerWrapper getIndexerAnalyzer(String text, FilterFactory filters) {
		PerFieldAnalyzerWrapper perFieldAnalyzer = null;
		// parse wiki-text to get categories
		WikiTokenizer tokenizer = new WikiTokenizer(text,filters.getLanguage());
		tokenizer.tokenize();
		ArrayList<String> categories = tokenizer.getCategories();
		
		perFieldAnalyzer = new PerFieldAnalyzerWrapper(new SimpleAnalyzer());
		perFieldAnalyzer.addAnalyzer("contents", 
				new LanguageAnalyzer(filters,tokenizer));
		perFieldAnalyzer.addAnalyzer("category", 
				new CategoryAnalyzer(categories));
		perFieldAnalyzer.addAnalyzer("title",
				getTitleAnalyzer(filters.getNoStemmerFilterFactory()));
		
		return perFieldAnalyzer;
	}
	
	public static PerFieldAnalyzerWrapper getSearcherAnalyzer(IndexId iid){
		if(global == null)
			global = GlobalConfiguration.getInstance();
		return getSearcherAnalyzer(global.getLanguage(iid.getDBname()));
		
	}
	
	public static PerFieldAnalyzerWrapper getSearcherAnalyzer(String langCode){
		return getSearcherAnalyzer(new FilterFactory(langCode));
	}
	
	/**
	 * Analyzer for search queries. Can be reused to parse many queries. 
	 * 
	 * @param text
	 * @return
	 */
	public static PerFieldAnalyzerWrapper getSearcherAnalyzer(FilterFactory filters) {
		PerFieldAnalyzerWrapper perFieldAnalyzer = null;
		
		perFieldAnalyzer = new PerFieldAnalyzerWrapper(getTitleAnalyzer(filters));
		perFieldAnalyzer.addAnalyzer("contents", 
				new QueryLanguageAnalyzer(filters));
		perFieldAnalyzer.addAnalyzer("title",
				getTitleAnalyzer(filters.getNoStemmerFilterFactory()));
		
		return perFieldAnalyzer;
	}
}
