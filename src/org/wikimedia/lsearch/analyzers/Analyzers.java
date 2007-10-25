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
import org.wikimedia.lsearch.analyzers.FieldBuilder.BuilderSet;
import org.wikimedia.lsearch.beans.Article;
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
		return new ReusableLanguageAnalyzer(filters,exactCase);
	}

	/**
	 * Analyzer used during indexing 
	 * 
	 * @return analyzer
	 */
	public static PerFieldAnalyzerWrapper getIndexerAnalyzer(FieldBuilder builder){
		PerFieldAnalyzerWrapper analyzer = new PerFieldAnalyzerWrapper(new SimpleAnalyzer());
		
		for(FieldBuilder.BuilderSet bs : builder.getBuilders()){
			FieldNameFactory fields = bs.getFields();
			FilterFactory filters = bs.getFilters();
			boolean exactCase = bs.isExactCase();
			 
			// all fields are pre-analyzed, except titles
			analyzer.addAnalyzer(fields.title(),
					getReusableAnalyzer(filters.getNoStemmerFilterFactory(),exactCase));
			analyzer.addAnalyzer(fields.stemtitle(),
					getReusableAnalyzer(filters,exactCase));
			analyzer.addAnalyzer(fields.reverse_title(),
					getReusableAnalyzer(filters.getNoStemmerFilterFactory(),exactCase));
		}
		
		return analyzer;
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
		PerFieldAnalyzerWrapper analyzer = null;
		boolean exactCase = fields.isExactCase();
		
		analyzer = new PerFieldAnalyzerWrapper(getReusableAnalyzer(filters,exactCase));
		analyzer.addAnalyzer(fields.contents(), 
				new ReusableLanguageAnalyzer(filters,exactCase));
		analyzer.addAnalyzer(fields.title(),
				getReusableAnalyzer(filters.getNoStemmerFilterFactory(),exactCase));
		analyzer.addAnalyzer(fields.stemtitle(),
				getReusableAnalyzer(filters,exactCase));
		analyzer.addAnalyzer(fields.alttitle(),
				getReusableAnalyzer(filters.getNoStemmerFilterFactory(),exactCase));
		analyzer.addAnalyzer(fields.keyword(), 
				getReusableAnalyzer(filters.getNoStemmerFilterFactory(),exactCase));
		analyzer.addAnalyzer(fields.anchor(), 
				getReusableAnalyzer(filters.getNoStemmerFilterFactory(),exactCase));
		
		return analyzer;
	}
}
