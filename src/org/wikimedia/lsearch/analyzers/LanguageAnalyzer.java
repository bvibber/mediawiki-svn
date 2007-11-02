package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.io.Reader;
import java.lang.reflect.Constructor;
import java.util.ArrayList;
import java.util.Iterator;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenFilter;
import org.apache.lucene.analysis.TokenStream;

/**
 * Wraps wiki tokenizer with a custom language stemmer. 
 * The objects of this class are "single-use". This means that 
 * tokenStream() is valid only when called first time.  
 * 
 * @author rainman
 *
 */
public class LanguageAnalyzer extends Analyzer {
	public static class ArrayTokens extends TokenStream {
		protected Iterator<Token> tokensIt = null;
		
		public ArrayTokens(ArrayList<Token> tokens){
			if(tokens!=null)
				tokensIt = tokens.iterator();
		}
		
		@Override
		public Token next() throws IOException {
			if(tokensIt == null)
				return null;
			else if(tokensIt.hasNext())
				return tokensIt.next();
			else
				return null;
		}
		
	}
	static org.apache.log4j.Logger log = Logger.getLogger(LanguageAnalyzer.class);
	protected WikiTokenizer wikitokenizer = null;
	protected FilterFactory filters;
	
	/** Make a new analyzer that process input as: wikitokenizer -> customFilter -> languageStemmer */
	public LanguageAnalyzer(FilterFactory filters, WikiTokenizer wikitokenizer){
		this.wikitokenizer = wikitokenizer;
		this.filters = filters;
	}

	/**
	 * Note: the token stream is read via the prepared wiki tokenizer,
	 * the reader is not actually used.
	 */
	@Override
	public TokenStream tokenStream(String fieldName, Reader reader) {
		wikitokenizer.resetIterator();
		ArrayList<Token> tokens = wikitokenizer.getTokens();
		if(filters.hasCustomFilter())
			tokens = applyCustomFilter(tokens);
		
		TokenStream out = new AliasFilter(filters,
				new ArrayTokens(tokens), new ArrayTokens(tokens));
		if(filters.hasAdditionalFilters())
			return filters.makeAdditionalFilterChain(fieldName,out);
		else
			return out;
	}

	/** Filter the tokens via the custom filter. For instance, to delete
	 * stop words, or in Thai to tokenize words properly. 
	 */
	protected ArrayList<Token> applyCustomFilter(ArrayList<Token> tokens) {
		if(filters.hasCustomFilter()){
			try {
				TokenStream ts =  filters.makeCustomFilter(new ArrayTokens(tokens));
				ArrayList<Token> filtered = new ArrayList<Token>();
				Token t;
				while((t = ts.next())!=null)
					filtered.add(t);
				
				return filtered;				
			} catch (Exception e){
				log.error("Error applying custom filter for "+filters.getLanguage());
			}
		}
		return tokens;
	}
	
	@Override
	public String toString() {
		return "LanguageAnalyzer for "+filters.getLanguage();
	}
	
	public WikiTokenizer getWikiTokenizer(){
		return wikitokenizer;
	}
}
