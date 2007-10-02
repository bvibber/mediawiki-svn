package org.wikimedia.lsearch.analyzers;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;

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
import org.apache.lucene.search.FieldSortedHitQueue;
import org.wikimedia.lsearch.beans.Title;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.index.WikiIndexModifier;
import org.wikimedia.lsearch.ranks.Links;
import org.wikimedia.lsearch.related.RelatedTitle;
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
	
	/** 
	 * Reusable analyzer, actually parses the input text
	 * 
	 * @param language
	 * @return
	 */
	public static Analyzer getReusableAnalyzer(FilterFactory filters, boolean exactCase){
		return new QueryLanguageAnalyzer(filters,exactCase);
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
	 * @return  {PerFieldAnalyzerWrapper,WikiTokenizer}
	 */
	public static Object[] getIndexerAnalyzer(String text, FieldBuilder builder, ArrayList<String> redirects, ArrayList<String> anchors, 
			ArrayList<RelatedTitle> related, int[] relatedPartition, Title title, Links links) {
		PerFieldAnalyzerWrapper perFieldAnalyzer = new PerFieldAnalyzerWrapper(new SimpleAnalyzer());
		WikiTokenizer tokenizer = null;
		for(FieldBuilder.BuilderSet bs : builder.getBuilders()){
			tokenizer = addFieldsForIndexing(perFieldAnalyzer, text, bs.getFilters(), bs.getFields(),
					redirects, anchors, related, relatedPartition, title, links, bs.isExactCase(), bs.isAddKeywords());
		} 
		return new Object[] {perFieldAnalyzer,tokenizer};
	}
	
	/**
	 * Add some fields to indexer's analyzer.
	 * 
	 */
	public static WikiTokenizer addFieldsForIndexing(PerFieldAnalyzerWrapper perFieldAnalyzer, String text, 
			FilterFactory filters, FieldNameFactory fields, ArrayList<String> redirects, ArrayList<String> anchors,
			ArrayList<RelatedTitle> related, int[] relatedPartition, Title title, Links links, boolean exactCase, boolean addKeywords) {
		// parse wiki-text to get categories
		WikiTokenizer tokenizer = new WikiTokenizer(text,filters.getIndexId(),exactCase);
		tokenizer.tokenize();
		ArrayList<String> categories = tokenizer.getCategories();
		HashMap<String,String> interwiki = tokenizer.getInterwikis();
		
		ArrayList<String> allKeywords = new ArrayList<String>();
		if(addKeywords && tokenizer.getKeywords()!=null) 
			allKeywords.addAll(tokenizer.getKeywords());
		if(redirects!=null)
			allKeywords.addAll(redirects);
		
		perFieldAnalyzer.addAnalyzer(fields.contents(), 
				new LanguageAnalyzer(filters,tokenizer));
		perFieldAnalyzer.addAnalyzer("category", 
				new CategoryAnalyzer(categories,exactCase));
		perFieldAnalyzer.addAnalyzer("interwiki", 
				new InterwikiAnalyzer(interwiki));
		perFieldAnalyzer.addAnalyzer(fields.title(),
				getReusableAnalyzer(filters.getNoStemmerFilterFactory(),exactCase));
		// stemtitles
		perFieldAnalyzer.addAnalyzer(fields.stemtitle(),
				getReusableAnalyzer(filters,exactCase));
		// alttitle
		setAltTitleAnalyzer(perFieldAnalyzer,fields.alttitle(),
				getReusableAnalyzer(filters.getNoStemmerFilterFactory(),exactCase));
		// keywords
		setKeywordAnalyzer(perFieldAnalyzer,fields.keyword(), 
				new KeywordsAnalyzer(allKeywords,filters.getNoStemmerFilterFactory(),fields.keyword(),exactCase));
		// anchors
		setKeywordAnalyzer(perFieldAnalyzer,fields.anchor(), 
				new KeywordsAnalyzer(anchors,filters,fields.anchor(),exactCase));
		// related
		setRelatedAnalyzer(perFieldAnalyzer,fields.related(), 
				new RelatedAnalyzer(related,relatedPartition,filters.getNoStemmerFilterFactory(),fields.related(),exactCase));
		// context
		setContextAnalyzer(perFieldAnalyzer,fields.context(), 
				new ContextAnalyzer(title,links,related,relatedPartition,filters.getNoStemmerFilterFactory(),fields.context(),exactCase));
		return tokenizer;
	}
	
	protected static void setAltTitleAnalyzer(PerFieldAnalyzerWrapper perFieldAnalyzer, String prefix, Analyzer analyzer) {
		for(int i=1;i<=WikiIndexModifier.ALT_TITLES;i++){
			perFieldAnalyzer.addAnalyzer(prefix+i,analyzer);
		}
	}
	
	protected static void setKeywordAnalyzer(PerFieldAnalyzerWrapper perFieldAnalyzer, String prefix, KeywordsAnalyzer analyzer) {
		for(int i=1;i<=KeywordsAnalyzer.KEYWORD_LEVELS;i++){
			perFieldAnalyzer.addAnalyzer(prefix+i,analyzer);
		}
	}
	
	protected static void setRelatedAnalyzer(PerFieldAnalyzerWrapper perFieldAnalyzer, String prefix, RelatedAnalyzer analyzer) {
		for(int i=1;i<=RelatedAnalyzer.RELATED_GROUPS;i++){
			perFieldAnalyzer.addAnalyzer(prefix+i,analyzer);
		}
	}
	
	protected static void setContextAnalyzer(PerFieldAnalyzerWrapper perFieldAnalyzer, String prefix, ContextAnalyzer analyzer) {
		for(int i=1;i<=ContextAnalyzer.CONTEXT_GROUPS;i++){
			perFieldAnalyzer.addAnalyzer(prefix+i,analyzer);
		}
	}
	
	
	public static PerFieldAnalyzerWrapper getSearcherAnalyzer(IndexId iid){
		return getSearcherAnalyzer(iid,false);
	}
	
	public static PerFieldAnalyzerWrapper getSearcherAnalyzer(IndexId iid, boolean exactCase){
		return getSearcherAnalyzer(new FilterFactory(iid),new FieldNameFactory(exactCase));
	}
	
	public static PerFieldAnalyzerWrapper getSpellCheckAnalyzer(IndexId iid, HashSet<String> stopWords){
		FilterFactory filters = new FilterFactory(iid,FilterFactory.Type.SPELL_CHECK);
		filters.setStopWords(stopWords);
		return getSearcherAnalyzer(filters,new FieldNameFactory());
	}
	
	/**
	 * Analyzer for search queries. Can be reused to parse many queries. 
	 * 
	 * @param text
	 * @return
	 */
	public static PerFieldAnalyzerWrapper getSearcherAnalyzer(FilterFactory filters, FieldNameFactory fields) {
		PerFieldAnalyzerWrapper perFieldAnalyzer = null;
		boolean exactCase = fields.isExactCase();
		
		perFieldAnalyzer = new PerFieldAnalyzerWrapper(getReusableAnalyzer(filters,exactCase));
		perFieldAnalyzer.addAnalyzer(fields.contents(), 
				new QueryLanguageAnalyzer(filters,exactCase));
		perFieldAnalyzer.addAnalyzer(fields.title(),
				getReusableAnalyzer(filters.getNoStemmerFilterFactory(),exactCase));
		perFieldAnalyzer.addAnalyzer(fields.stemtitle(),
				getReusableAnalyzer(filters,exactCase));
		setAltTitleAnalyzer(perFieldAnalyzer,fields.alttitle(),
				getReusableAnalyzer(filters.getNoStemmerFilterFactory(),exactCase));
		perFieldAnalyzer.addAnalyzer(fields.keyword(), 
				getReusableAnalyzer(filters.getNoStemmerFilterFactory(),exactCase));
		perFieldAnalyzer.addAnalyzer(fields.anchor(), 
				getReusableAnalyzer(filters.getNoStemmerFilterFactory(),exactCase));
		
		return perFieldAnalyzer;
	}
}
