package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.io.Reader;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenStream;
/**
 * Analyzer that just lowecases the text, doesn't split up anything, etc..
 *  
 * @author rainman
 *
 */
public class LowercaseAnalyzer extends Analyzer {
	public static class LowercaseTokenizer extends TokenStream {
		String text;
		boolean sent = false;
		LowercaseTokenizer(String in){
			text = in.toLowerCase();
		}
		@Override
		public Token next() throws IOException {
			if(sent)
				return null;
			else{
				sent = true;
				return new Token(text,0,text.length());
			}
		}
		
	}
	
	@Override
	public TokenStream tokenStream(String fieldName, String text) {
		return new LowercaseTokenizer(text);
	}
	@Override
	public TokenStream tokenStream(String fieldName, Reader reader) {
		throw new UnsupportedOperationException("Use tokenStream(String,String)");
	}
	

}
