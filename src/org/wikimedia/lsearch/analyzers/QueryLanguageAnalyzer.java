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
	protected boolean exactCase;
	
	public QueryLanguageAnalyzer(FilterFactory filters, boolean exactCase){
		super(filters,null);
		this.exactCase = exactCase;
	}
	
	/**
	 * Used in {@link WikiQueryParser} to parse parts of the query.
	 */
	@Override
	public TokenStream tokenStream(String fieldName, String text) {
		wikitokenizer = new WikiTokenizer(text,filters.getIndexId(),exactCase); 
		return super.tokenStream(fieldName,(Reader)null);
	}

	@Override
	public TokenStream tokenStream(String fieldName, Reader reader) {
		Thread.dumpStack();
		log.error("Invalid usage of QueryLanguageAnalyzer.tokenStream(String,Reader). Use tokenStream(String,String). Probably bug in the software. ");
		return null;
	}

	@Override
	public String toString() {
		return "QueryLanguageAnalyzer for "+filters.getLanguage();
	}
	
	
	
}
