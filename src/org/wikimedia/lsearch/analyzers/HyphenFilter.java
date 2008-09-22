package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.util.ArrayList;
import java.util.BitSet;

import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenFilter;
import org.apache.lucene.analysis.TokenStream;
import org.apache.lucene.index.IndexReader;

public class HyphenFilter extends TokenFilter {
	ArrayList<Token> buffer = new ArrayList<Token>();
	int inx = 0;

	public HyphenFilter(TokenStream input) {
		super(input);
	}

	@Override
	public Token next() throws IOException {
		// return buferred
		if(inx < buffer.size())
			return buffer.get(inx++);
		
		// examine next
		Token t = input.next();
		if(t == null)
			return null; // EOS
		if(t.type().equals("with_hyphen")){
			buffer.clear();
			inx = 0;
			Token next = null;
			while((next = input.next()) != null){
				if(next.getPositionIncrement()==0)
					buffer.add(next);
				else{
					String text = strip(t.termText()) + next.termText();
					Token join = new Token(text,t.startOffset(),next.endOffset());
					join.setPositionIncrement(0);
					buffer.add(join);
					buffer.add(next);
					break;
				}
			}			
		}
		return t;
	}
	
	protected String strip(String str){
		assert str.endsWith("-");
		return str.substring(0,str.length()-1);
	}
	
	

}
