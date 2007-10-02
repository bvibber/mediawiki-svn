package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.io.Reader;
import java.util.Iterator;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenStream;
import org.apache.lucene.analysis.Tokenizer;
import org.wikimedia.lsearch.ranks.StringList;

/** Analyzes serialized StringLists into its components */
public class SplitAnalyzer extends Analyzer {
	class SplitTokenStream extends Tokenizer {
		Iterator<String> it = null;
		int in = 0;
		int start = 0;
		SplitTokenStream(String input){
			it = new StringList(input).iterator();
		}
		@Override
		public Token next() throws IOException {
			if(!it.hasNext())
				return null;
			else{
				String str = it.next();
				int s = start;
				int e = start + str.length();
				return new Token(str,s,e);
			}			
		}		
	}
	
	@Override
	public TokenStream tokenStream(String fieldName, String text) {
		return new SplitTokenStream(text);
	}

	@Override
	public TokenStream tokenStream(String fieldName, Reader reader) {
		throw new RuntimeException("Use tokenStream(String,String)");
	}
	
	

}
