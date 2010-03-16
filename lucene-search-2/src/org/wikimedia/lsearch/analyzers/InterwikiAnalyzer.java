package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.io.Reader;
import java.util.HashMap;
import java.util.Iterator;
import java.util.Map.Entry;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenStream;

public class InterwikiAnalyzer extends Analyzer {	
	public class InterwikiTokenStream extends TokenStream {
		protected Iterator<Entry<String,String>> tokensIt;
		protected int start;
		protected Token next = null;
				
		InterwikiTokenStream(){
			tokensIt = interwiki.entrySet().iterator();
			start = 0;
		}
		
		@Override
		public Token next() throws IOException {
			if(next != null){
				Token t = next;
				next = null;
				return t;
			}
			if(tokensIt.hasNext()){
				Entry<String,String> map = tokensIt.next();
				String iw = map.getKey()+":"; // e.g. en:
				String title = map.getValue().toLowerCase(); // e.g. "douglas adams"
				Token t = new Token(iw,start,start+iw.length());
				start += iw.length()+1;
				next = new Token(title,start,start+title.length());
				start += title.length()+1;
				
				return t;
			} else
				return null;
		}

	}
	
	HashMap<String,String> interwiki;

	public InterwikiAnalyzer(HashMap<String,String> interwiki) {
		this.interwiki = interwiki;
	}

	@Override
	public TokenStream tokenStream(String fieldName, Reader reader) {
		return new InterwikiTokenStream();
	}

}
