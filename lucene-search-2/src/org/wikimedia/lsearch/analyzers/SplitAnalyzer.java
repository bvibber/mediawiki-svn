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
	public final static int GROUP_GAP = 200;
	class SplitTokenStream extends Tokenizer {
		Iterator<String> it = null;
		int in = 0;
		int start = 0;
		Token next = null;
		SplitTokenStream(String input){
			it = new StringList(input).iterator();
		}
		@Override
		public Token next() throws IOException {
			if(next != null){
				Token t = next;
				next = null;
				return t;
			}
			if(!it.hasNext())
				return null;
			else{
				int gap = tokenGap;
				String str = it.next();
				if(splitPhrases){
					int d = str.indexOf('|');
					// two-token phrase delimited by |
					if(d != -1 && d != str.length()-1){
						next = new Token(str.substring(d+1),start+d+1,start+str.length());
						str = str.substring(0,d);
						gap = 1;
					}
				}
				int s = start;
				int e = start + str.length();
				start = e + 1;
				Token t = new Token(str,s,e);
				if(str.length() == 0)
					t.setPositionIncrement(GROUP_GAP);
				else
					t.setPositionIncrement(gap);		
				return t;
			}			
		}		
	}
	
	protected int tokenGap;
	protected boolean splitPhrases;
	
	public SplitAnalyzer(int tokenGap, boolean splitPhrases){
		this.tokenGap = tokenGap;
		this.splitPhrases = splitPhrases;
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
