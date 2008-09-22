package org.wikimedia.lsearch.analyzers;

import java.io.IOException;

import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenFilter;
import org.apache.lucene.analysis.TokenStream;

public class AcronymFilter extends TokenFilter {
	Token buffered = null;
	
	public AcronymFilter(TokenStream input) {
		super(input);
	}

	@Override
	public Token next() throws IOException {
		if(buffered != null){
			Token t = buffered;
			buffered = null;
			return t;
		}
		Token t = input.next();
		if(t == null)
			return null;
		if(t.termText().contains(".") && !isNumber(t.termText())){
			buffered = new Token(t.termText().replace(".",""),t.startOffset(),t.endOffset(),t.type());
			buffered.setPositionIncrement(0);
		}
		return t;
	}
	
	protected boolean isNumber(String str){
		for(int i=0;i<str.length();i++){
			char c = str.charAt(i);
			if(! ((c >= '0' && c <='9') || (c=='.') ))
				return false;
		}
		return true;
	}
	
	
	
}
