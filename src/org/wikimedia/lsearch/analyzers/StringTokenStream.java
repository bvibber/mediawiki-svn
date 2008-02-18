package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.util.Collection;
import java.util.Iterator;

import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenStream;

public class StringTokenStream extends TokenStream {
	Collection<String> tokens = null;
	Iterator<String> it = null;
	int len = 0;
	
	public StringTokenStream(Collection<String> tokens){
		this.tokens = tokens;
		this.it = tokens.iterator();
	}
	
	@Override
	public Token next() throws IOException {
		if(!it.hasNext())
			return null;
		String s = it.next();
		Token t = new Token(s,len,len+s.length());
		len += s.length() + 1;
		return t;
	}

}
