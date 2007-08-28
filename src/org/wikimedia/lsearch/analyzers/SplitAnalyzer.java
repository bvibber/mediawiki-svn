package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.io.Reader;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenStream;
import org.apache.lucene.analysis.Tokenizer;

/** Split the text by some specific char */
public class SplitAnalyzer extends Analyzer {
	class SplitTokenStream extends Tokenizer {
		String[] tokens;
		int in = 0;
		int start = 0;
		SplitTokenStream(String inputStr){
			tokens = inputStr.split(""+splitChar);
		}
		@Override
		public Token next() throws IOException {
			if(in >= tokens.length)
				return null;
			else{
				int s = start;
				int e = start + tokens[in].length();
				start = e + 1;
				return new Token(tokens[in++],s,e);
			}
		}		
	}
	char splitChar;
	
	public SplitAnalyzer(char splitChar){
		this.splitChar = splitChar;
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
