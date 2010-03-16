package org.wikimedia.lsearch.analyzers;

import java.io.IOException;

import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenFilter;
import org.apache.lucene.analysis.TokenStream;

/**
 * Vietnamese standard transliterations to ascii. Most of the stuff is done by unicode decomposed,
 * we just additionaly convert Đ/đ -> D/d
 * 
 * @author rainman
 *
 */
public class VietnameseFilter extends TokenFilter {
	private static final int MAX_WORD_LEN = 255;
	private final char[] buffer = new char[MAX_WORD_LEN];
	private int len;
	private Token next, afterNext;
	
	public VietnameseFilter(TokenStream input) {
		super(input);
		next = null;
		afterNext = null;
	}

	@Override
	public Token next() throws IOException {
		Token t;
		if(next!=null){
			t = next;
			next = afterNext;
			afterNext = null;
		} else
			t = input.next();
		if(t == null)
			return null;
				
		len = 0;		
		boolean replace = false;
		for(char c : t.termText().toCharArray()){
			if(c == 'Đ'){
				buffer[len++] = 'D';
				replace = true;
			} else if(c == 'đ'){
				buffer[len++] = 'd';
				replace = true;
			} else
				buffer[len++] = c;				
		}
		if(replace){
			Token tt = new Token(new String(buffer,0,len),t.startOffset(),t.endOffset(),"alias");
			tt.setPositionIncrement(0);
			next = input.next();
			if(next!=null && next.getPositionIncrement()==0)
				return t; // we'll replace d's in next token
			else if(t.getPositionIncrement()==0){
				return tt; // replace the transliterated token with one with d's
			} else{
				afterNext = next;
				next = tt;
				return t;
			}
		} else
			return t;
		
	}

}
