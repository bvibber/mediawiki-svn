package org.wikimedia.lsearch.analyzers;

import java.io.IOException;

import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenFilter;
import org.apache.lucene.analysis.TokenStream;

/**
 * Return tokens with positional incremental less than some limit
 * 
 * @author rainman
 *
 */
public class LimitTokenFilter extends TokenFilter {
	protected int limit = 0;
	protected int position = 0;
	
	public LimitTokenFilter(TokenStream input, int limit) {
		super(input);
		this.limit = limit;
	}

	@Override
	public Token next() throws IOException {
		Token t = input.next();
		if(t == null)
			return null;
		position += t.getPositionIncrement();
		if(position >= limit)
			return null;
		return t;
			
	}

}
