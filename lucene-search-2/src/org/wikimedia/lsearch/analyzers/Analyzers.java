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
	public static Analyzer getReusableIndexAnalyzer(FilterFactory filters, boolean exactCase){
		if(filters.isSpellCheck())
			return new ReusableLanguageAnalyzer(filters,new TokenizerOptions.SpellCheck());
		return new ReusableLanguageAnalyzer(filters,new TokenizerOptions.NoRelocation(exactCase));
	}
	public static Analyzer getReusableTitleIndexAnalyzer(FilterFactory filters, boolean exactCase){
		if(filters.isSpellCheck())
			return new ReusableLanguageAnalyzer(filters,new TokenizerOptions.SpellCheckTitle());
		return new ReusableLanguageAnalyzer(filters,new TokenizerOptions.Title(exactCase));
	}
	
	public static Analyzer getReusableAnalyzer(FilterFactory filters, TokenizerOptions options){
		return new ReusableLanguageAnalyzer(filters,options);
	}
	
	public static Analyzer getReusableSearchAnalyzer(FilterFactory filters, boolean exactCase){
		if(filters.isSpellCheck())
			return new ReusableLanguageAnalyzer(filters,new TokenizerOptions.SpellCheck());
		return new ReusableLanguageAnalyzer(filters,new TokenizerOptions.NoRelocationNoSplit(exactCase));
	}
	public static Analyzer getReusableTitleSearchAnalyzer(FilterFactory filters, boolean exactCase){
		if(filters.isSpellCheck())
			return new ReusableLanguageAnalyzer(filters,new TokenizerOptions.SpellCheckTitle());
		return new ReusableLanguageAnalyzer(filters,new TokenizerOptions.TitleNoSplit(exactCase));
	}
	
	/** 
	 * Reusable analyzer, actually parses the input text
	 * 
	 * @param language
	 * @return
	 */
	public static ReusableLanguageAnalyzer getReusableHighlightAnalyzer(FilterFactory filters, boolean exactCase){
		return getReusableHighlightAnalyzer(filters,false,exactCase);
	}
	
	public static ReusableLanguageAnalyzer getReusableHighlightAnalyzer(FilterFactory filters, boolean original, boolean exactCase){
		TokenizerOptions options = null;
		if(original)
			options = new TokenizerOptions.HighlightOriginal(exactCase);
		else
			options = new TokenizerOptions.Highlight(exactCase);
		return new ReusableLanguageAnalyzer(filters,options);
	}

	/**
	 * Analyzer used during indexing 
	 * 
	 * @return analyzer
	 */
	public static PerFieldAnalyzerWrapper getIndexerAnalyzer(FieldBuilder builder){
		PerFieldAnalyzerWrapper analyzer = new PerFieldAnalyzerWrapper(getReusableIndexAnalyzer(builder.getBuilder().getFilters().getNoStemmerFilterFactory(),false));
		
		for(FieldBuilder.BuilderSet bs : builder.getBuilders()){
			FieldNameFactory fields = bs.getFields();
			FilterFactory filters = bs.getFilters();
			boolean exactCase = bs.isExactCase();
			 
			// all fields are pre-analyzed, except titles
			analyzer.addAnalyzer(fields.title(),
					getReusableTitleIndexAnalyzer(filters.getNoStemmerFilterFactory(),exactCase));
			analyzer.addAnalyzer(fields.stemtitle(),
					getReusableTitleIndexAnalyzer(filters,exactCase));
			analyzer.addAnalyzer(fields.reverse_title(),
					getReusableTitleIndexAnalyzer(filters.getNoStemmerFilterFactory(),exactCase));
			// and aggregates:
			analyzer.addAnalyzer(fields.related(),
					getReusableTitleIndexAnalyzer(filters.getNoStemmerFilterFactory(),exactCase));
			analyzer.addAnalyzer(fields.alttitle(),
					getReusableTitleIndexAnalyzer(filters.getNoStemmerFilterFactory(),exactCase));
			analyzer.addAnalyzer(fields.sections(),
					getReusableTitleIndexAnalyzer(filters.getNoStemmerFilterFactory(),exactCase));
			
			// for testing
			analyzer.addAnalyzer(fields.contents(), 
					getReusableIndexAnalyzer(filters,exactCase));
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
		FieldBuilder builder = new FieldBuilder(iid,FieldBuilder.Case.IGNORE_CASE,FieldBuilder.Stemmer.NO_STEMMER,FieldBuilder.Options.SPELL_CHECK);
		builder.getBuilder().getFilters().setStopWords(stopWords);
		return getIndexerAnalyzer(builder);
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
		
		analyzer = new PerFieldAnalyzerWrapper(getReusableSearchAnalyzer(filters,exactCase));
		analyzer.addAnalyzer(fields.contents(), 
				getReusableSearchAnalyzer(filters,exactCase));
		analyzer.addAnalyzer(fields.title(),
				getReusableTitleSearchAnalyzer(filters.getNoStemmerFilterFactory(),exactCase));
		analyzer.addAnalyzer(fields.stemtitle(),
				getReusableTitleSearchAnalyzer(filters,exactCase));
		analyzer.addAnalyzer(fields.alttitle(),
				getReusableTitleSearchAnalyzer(filters.getNoStemmerFilterFactory(),exactCase));
		analyzer.addAnalyzer(fields.related(),
				getReusableTitleSearchAnalyzer(filters.getNoStemmerFilterFactory(),exactCase));
		analyzer.addAnalyzer(fields.sections(),
				getReusableTitleSearchAnalyzer(filters.getNoStemmerFilterFactory(),exactCase));
		analyzer.addAnalyzer(fields.keyword(), 
				getReusableTitleSearchAnalyzer(filters.getNoStemmerFilterFactory(),exactCase));
		
		if(!exactCase){ // add exact-case analyzer, needed for titles indexes
			FieldNameFactory fieldsExact = new FieldNameFactory(true);
			analyzer.addAnalyzer(fieldsExact.title(),
					getReusableSearchAnalyzer(filters.getNoStemmerFilterFactory(),true));
		}
		
		return analyzer;
	}
	
	public static PerFieldAnalyzerWrapper getHighlightAnalyzer(IndexId iid, boolean exactCase){
		return getHighlightAnalyzer(new FilterFactory(iid), new FieldNameFactory(),exactCase);
	}
	
	/**
	 * Analyzer used to process raw text for highlighting 
	 * 
	 * @param text
	 * @return
	 */
	public static PerFieldAnalyzerWrapper getHighlightAnalyzer(FilterFactory filters, FieldNameFactory fields, boolean exactCase) {
		PerFieldAnalyzerWrapper analyzer = null;
		
		analyzer = new PerFieldAnalyzerWrapper(getReusableHighlightAnalyzer(filters,exactCase));
		analyzer.addAnalyzer(fields.contents(), 
				getReusableHighlightAnalyzer(filters,exactCase));
		analyzer.addAnalyzer(fields.title(),
				getReusableHighlightAnalyzer(filters.getNoStemmerFilterFactory(),exactCase));
		analyzer.addAnalyzer(fields.stemtitle(),
				getReusableHighlightAnalyzer(filters,exactCase));
		analyzer.addAnalyzer(fields.alttitle(),
				getReusableHighlightAnalyzer(filters.getNoStemmerFilterFactory(),true,exactCase));
		analyzer.addAnalyzer(fields.keyword(), 
				getReusableHighlightAnalyzer(filters.getNoStemmerFilterFactory(),exactCase));
		
		return analyzer;
	}
}
