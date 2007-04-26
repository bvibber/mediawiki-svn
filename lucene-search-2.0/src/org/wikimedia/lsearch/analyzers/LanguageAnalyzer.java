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
	public class ArrayTokens extends TokenStream {
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
	protected Constructor language = null;
	protected Constructor customFilter = null;
	
	/** Make a new analyzer that process input as: wikitokenizer -> customFilter -> languageStemmer */
	public LanguageAnalyzer(Class languageStemmer, WikiTokenizer wikitokenizer, Class customFilter){
		this.wikitokenizer = wikitokenizer;
		try{
			if(languageStemmer != null)
				language = languageStemmer.getConstructor(TokenStream.class);
			if(customFilter != null)
				this.customFilter = customFilter.getConstructor(TokenStream.class);

		} catch (SecurityException e) {
			log.error("The constructor that takes TokenStream is hidden. Class: "+language.getClass().getCanonicalName());
		} catch (NoSuchMethodException e) {
			log.error("The constructor that takes TokenStream is missing.Class: "+language.getClass().getCanonicalName());
		}		
	}

	/**
	 * Note: the token stream is read via the prepared wiki tokenizer,
	 * the reader is not actually used.
	 */
	@Override
	public TokenStream tokenStream(String fieldName, Reader reader) {
		wikitokenizer.resetIterator();
		ArrayList<Token> tokens = wikitokenizer.getTokens();
		if(customFilter != null)
			tokens = applyCustomFilter(tokens);
		
		return new AliasFilter(language,
				new ArrayTokens(tokens), new ArrayTokens(tokens)); 
	}

	/** Filter the tokens via the custom filter. For instance, to delete
	 * stop words, or in Thai to tokenize words properly. 
	 */
	protected ArrayList<Token> applyCustomFilter(ArrayList<Token> tokens) {
		if(customFilter != null){
			try {
				TokenStream ts =  (TokenStream) customFilter.newInstance(new Object[] {new ArrayTokens(tokens)});
				ArrayList<Token> filtered = new ArrayList<Token>();
				Token t;
				while((t = ts.next())!=null)
					filtered.add(t);
				
				return filtered;				
			} catch (Exception e){
				log.error("Error applying custom filter "+customFilter.getName());
			}
		}
		return tokens;
	}
}
