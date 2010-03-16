package org.wikimedia.lsearch.analyzers;

import java.util.LinkedList;

import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenFilter;
import org.apache.lucene.analysis.TokenStream;
import org.wikimedia.lsearch.analyzers.ExtToken.Type;

/**
 * Simple CJK (Chinese Japanese Korean) token filter. 
 * Filter: C1C2C3C4 -> C1C2 C2C3 C3C4. 
 * Ordinary word breaks are handled by lower level tokenizer. 
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
	
		Token token;
		do{
			token = input.next();
			if(token == null)
				return null;
		} while(token.getPositionIncrement()==0); // discard aliases
		
		if(token instanceof ExtToken && ((ExtToken)token).getType()!=Type.TEXT)
			return token;
		
		String text = token.termText();
		
		int i,offset,c;
		int len; // length of single token (if it's non-cjk word)
		char last=0,cur; // last/cur cjk char
		// split the token into cjk chars
		for(i=0,offset=0,len=0;i<text.length();i++){
			c = text.codePointAt(i);
			if(isCJKChar(c)){
				if(len != 0)
					buffer.add(new Token(text.substring(offset,offset+len+1),token.startOffset()+offset,token.startOffset()+offset+len+1));
				offset = i+1;
				len = 0;
				cur = text.charAt(i);
				if(last != 0)
					buffer.add(new Token(""+last+cur,token.startOffset()+i-1,token.startOffset()+i+1));					
				last = cur;
			} else if(last != 0){
				buffer.add(new Token(""+last,token.startOffset()+i,token.startOffset()+i+1));
				last = 0;
			} else
				len++;
		}
		if(len != 0 && len != text.length())
			buffer.add(new Token(text.substring(offset,offset+len+1),token.startOffset()+offset,token.startOffset()+offset+len+1));
		
		if(buffer.size() == 0)
			return token;
		else
			return buffer.removeFirst();
	}
	
	public static final boolean isCJKChar(int c){
		return (c >= 0x3040 && c <= 0x318f) ||
		(c >= 0x3300 && c <= 0x337f) ||
		(c >= 0x3400 && c <= 0x3d2d) ||
		(c >= 0x4e00 && c <= 0x9fff) ||
      (c >= 0xf900 && c <= 0xfaff) ||
      (c >= 0xac00 && c <= 0xd7af);
	}

}