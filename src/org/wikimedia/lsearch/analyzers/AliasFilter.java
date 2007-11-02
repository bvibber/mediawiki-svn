package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.lang.reflect.Constructor;
import java.lang.reflect.InvocationTargetException;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenFilter;
import org.apache.lucene.analysis.TokenStream;

/**
 * Manages aliases for stemmers.
 * 
 * @author rainman
 *
 */
public class AliasFilter extends TokenStream {
	static org.apache.log4j.Logger log = Logger.getLogger(AliasFilter.class);
	protected TokenStream input;
	protected TokenStream stemmer;
	protected Token last;
	
	/**
	 * Takes a constructor of stemmer class, and two identical token streams.
	 * 
	 * TWO VERY IMPORTANT NOTES: 
	 * 1) if the stemmer doesn't change the token text it needs to output the 
	 * SAME OBJECT as on input (not just an object that is equal()). 
	 * 2) stemmers should never change tokens, if the text needs to be
	 * changed, return a new Token object
	 * 
	 * @param language
	 */
	public AliasFilter(FilterFactory filters, TokenStream input, TokenStream duplicate){
		this.input = input;
		stemmer = null;
		last = null;
		if(filters.hasStemmer())
			stemmer =  filters.makeStemmer(duplicate);
	}
	
	@Override
	public Token next() throws IOException {
		if(last != null){
			Token ret = last;
			last = null;
			return ret;			
		}
		Token original = input.next();
		if(stemmer == null)
			return original;
		Token stemmed = stemmer.next();
		// NOTE: we require them to be the SAME OBJECT, so we don't waste time doing equal()
		if(original == stemmed || (original instanceof ExtToken && ((ExtToken)original).getType()!=ExtToken.Type.TEXT))
			return original;
		else{
			stemmed.setPositionIncrement(0); // alias
			stemmed.setType("stemmed");
			last = stemmed;
			return original;
		}
	}

}
