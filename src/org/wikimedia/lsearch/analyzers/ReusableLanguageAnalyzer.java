package org.wikimedia.lsearch.analyzers;

import java.io.Reader;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.TokenStream;

/**
 * Reusable language analyzer. Can be used to tokenize arbitrary text. 
 * 
 * @author rainman
 *
 */
public class ReusableLanguageAnalyzer extends LanguageAnalyzer {
	static org.apache.log4j.Logger log = Logger.getLogger(ReusableLanguageAnalyzer.class);
	protected boolean exactCase;
	
	public ReusableLanguageAnalyzer(FilterFactory filters, boolean exactCase){
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
		throw new RuntimeException("Use tokenStream(String,String)");
	}

	@Override
	public String toString() {
		return "QueryLanguageAnalyzer for "+filters.getLanguage();
	}
	
	
	
}
