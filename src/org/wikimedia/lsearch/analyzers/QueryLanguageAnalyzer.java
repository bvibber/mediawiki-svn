package org.wikimedia.lsearch.analyzers;

import java.io.Reader;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.TokenStream;

/**
 * Analyzer used to analyzer search queries. 
 * 
 * @author rainman
 *
 */
public class QueryLanguageAnalyzer extends LanguageAnalyzer {
	static org.apache.log4j.Logger log = Logger.getLogger(QueryLanguageAnalyzer.class);
	
	public QueryLanguageAnalyzer(Class languageClass, Class customFilter){
		super(languageClass,null,customFilter);
	}
	
	/**
	 * Used in {@link WikiQueryParser} to parse parts of the query.
	 */
	@Override
	public TokenStream tokenStream(String fieldName, String text) {
		wikitokenizer = new WikiTokenizer(text); 
		return super.tokenStream(fieldName,(Reader)null);
	}

	@Override
	public TokenStream tokenStream(String fieldName, Reader reader) {
		Thread.dumpStack();
		log.error("Invalid usage of QueryLanguageAnalyzer.tokenStream(String,Reader). Use tokenStream(String,String). Probably bug in the software. ");
		return null;
	}
	
	
}
