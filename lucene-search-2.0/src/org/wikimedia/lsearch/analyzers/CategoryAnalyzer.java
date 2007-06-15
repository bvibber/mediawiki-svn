package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.io.Reader;
import java.util.ArrayList;
import java.util.Iterator;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.LowerCaseFilter;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenStream;

/** Produces a token stream for category field in the lucene index.
 *  Each token is a single category (category names themself are
 *  not tokenized) */
public class CategoryAnalyzer extends Analyzer {
	public class ArrayTokenStream extends TokenStream {
		protected ArrayList<String> tokens;
		protected Iterator<String> tokensIt;
		protected int start;
		
		ArrayTokenStream(ArrayList<String> tokens){
			this.tokens = tokens;
			tokensIt = tokens.iterator();
			start = 0;
		}
		
		@Override
		public Token next() throws IOException {
			if(tokensIt.hasNext()){
				String text = tokensIt.next();
				Token token = new Token(text,start,start+text.length());
				start += text.length()+1;
				return token;
			} else
				return null;
		}

	}
	
	ArrayList<String> categories;
		
	public CategoryAnalyzer(ArrayList<String> categories) {
		this.categories = categories;
	}

	@Override
	public TokenStream tokenStream(String fieldName, Reader reader) {
		return new ArrayTokenStream(categories);
	}

}
