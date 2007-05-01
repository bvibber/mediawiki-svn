package org.wikimedia.lsearch.analyzers;

import java.util.LinkedList;

import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenFilter;
import org.apache.lucene.analysis.TokenStream;

/**
 * Simple CJK (Chinese Japanese Korean) token filter. One CJK symbol per token. 
 */

public final class CJKFilter extends TokenFilter {
	LinkedList<Token> buffer = new LinkedList<Token>();
	
	public CJKFilter(TokenStream input) {
		super(input);
	}

	@Override
	public Token next() throws java.io.IOException {
		if(buffer.size()!=0)
			return buffer.removeFirst();
	
		Token token = input.next();
		if(token == null)
			return null;
		
		String text = token.termText();
		
		int i,offset,len,c;		
		for(i=0,offset=0,len=0;i<text.length();i++){
			c = text.codePointAt(i);
			if(isCJKChar(c)){
				if(len != 0)
					buffer.add(new Token(text.substring(offset,offset+len),token.startOffset()+offset,token.startOffset()+offset+len));
				offset = i+1;
				len = 0;
				buffer.add(new Token(text.substring(i,i+1),token.startOffset()+i,token.startOffset()+i+1));
			} else
				len++;
		}
		if(len != 0 && len != text.length())
			buffer.add(new Token(text.substring(offset,offset+len),token.startOffset()+offset,token.startOffset()+offset+len));
		
		if(buffer.size() == 0)
			return token;
		else
			return buffer.removeFirst();
	}
	
	public final boolean isCJKChar(int c){
		return (c >= 0x3040 && c <= 0x318f) ||
		(c >= 0x3300 && c <= 0x337f) ||
		(c >= 0x3400 && c <= 0x3d2d) ||
		(c >= 0x4e00 && c <= 0x9fff) ||
      (c >= 0xf900 && c <= 0xfaff) ||
      (c >= 0xac00 && c <= 0xd7af);
	}

}