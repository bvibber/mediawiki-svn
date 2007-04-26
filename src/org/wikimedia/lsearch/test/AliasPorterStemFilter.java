package org.wikimedia.lsearch.test;


import java.io.IOException;

import org.apache.lucene.analysis.PorterStemmer;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenFilter;
import org.apache.lucene.analysis.TokenStream;

/**
 * For each stemmed word, produce an unstemmed alias (positional increment 0)
 * 
 * @author rainman
 *
 */
public class AliasPorterStemFilter extends TokenFilter {
	protected PorterStemmer stemmer;
	protected Token nextToken; 

	public AliasPorterStemFilter(TokenStream in) {
		super(in);
		stemmer = new PorterStemmer();
		nextToken = null;
	}

	/** Returns the next input Token, after being stemmed */
	public final Token next() throws IOException {
		Token token;
		
		if(nextToken != null){
			token = nextToken;
			nextToken = null;
			return token;
		}
			
		token = input.next();
		if (token == null)
			return null;
		else {
			String s = stemmer.stem(token.termText());
			if (s != token.termText()){ 
				// generate stemmed alias for the current token
				nextToken = new Token(s,token.startOffset(),token.endOffset());
				nextToken.setPositionIncrement(0);
			}
			return token;
		}
	}
}

