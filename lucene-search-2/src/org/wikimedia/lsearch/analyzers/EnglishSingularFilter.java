package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.util.HashMap;

import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenFilter;
import org.apache.lucene.analysis.TokenStream;

/**
 * Add english singular forms of words as aliases of
 * type "singular"
 * 
 * @author rainman
 *
 */
public class EnglishSingularFilter extends TokenFilter{
	Singular singular = new EnglishKStemSingular();
	
	Token next = null, next2=null;
	public EnglishSingularFilter(TokenStream input) {
		super(input);
	}

	@Override
	public Token next() throws IOException {
		if(next != null){
			Token t = next;
			next = null;
			return t;
		}
		Token t = null;
		if(next2 != null){
			t = next2;
			next2 = null;
		} else
			t = input.next();
		if(t == null) // EOS
			return null;
		
		if(t.getPositionIncrement() != 0 && !t.type().equals("upper") 
				// && !(t.type().equals("titlecase") && !t.termText().endsWith("'s")) 
				&& !t.termText().contains(".")
				&& !(t instanceof ExtToken && ((ExtToken)t).getType()!=ExtToken.Type.TEXT)){			
			next = singular(t);
			if(next != null){
				next2 = input.next();
				if(next2 != null && next2.getPositionIncrement()==0 && next2.termText().equals(next.termText()))
					next2 = null; // next2 (stemmed) will be same as next, ignore!
			}
		}
		
		return t;
	}
	
	/** Return token with sigular form of the noun, or null if none found */
	protected final Token singular(Token t){
		String w = singular.getSingular(t.termText());
		if(w != null){
			Token n = new Token(w,t.startOffset(),t.endOffset(),"singular");
			n.setPositionIncrement(0);
			return n;
		}
		
		return null;
	}
	
	

}
