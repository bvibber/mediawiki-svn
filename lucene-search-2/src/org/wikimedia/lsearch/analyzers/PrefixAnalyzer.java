package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.io.Reader;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenStream;
import org.apache.lucene.analysis.Tokenizer;

public class PrefixAnalyzer extends Analyzer {
	static public class PrefixTokenizer extends Tokenizer {
		String in;
		int count = 0;
		
		public PrefixTokenizer(String input){
			in = input;
		}
		@Override
		public Token next() throws IOException {
			count++;
			if(count > in.length())
				return null;
			else
				return new Token(in.substring(0,count),0,count);
		}		
	}
	
	public TokenStream tokenStream(String fieldName, String str) {
		return new PrefixTokenizer(str.toLowerCase());
	}

	@Override
	public TokenStream tokenStream(String fieldName, Reader reader) {
		throw new UnsupportedOperationException("Use tokenStream(String,String)");
	}
}
