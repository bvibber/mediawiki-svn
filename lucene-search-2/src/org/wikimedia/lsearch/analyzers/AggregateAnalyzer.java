package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.io.Reader;
import java.util.ArrayList;
import java.util.HashSet;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenStream;
import org.wikimedia.lsearch.config.IndexId;

public class AggregateAnalyzer extends Analyzer {
	public static final int TOKEN_GAP = 256;
	int item=0;
	int token=0;
	int pos = 0;
	public class AggregateTokenStream extends TokenStream {
		@Override
		public Token next() throws IOException {
			boolean gap = false;
			if(items == null)
				return null;
			if(item >= items.size())
				return null;
			Aggregate ag = items.get(item);
			if(token >= ag.length() || token >= TOKEN_GAP-1){
				gap = true;
				do{
					// find next nonempty item
					item++;
					if(item >= items.size()) // eos
						return null;
				} while(items.get(item).length()==0);
				ag = items.get(item);
				token = 0; // we want the first token
			}
			Token t = ag.getToken(token++);
			if(gap) // position to whole number product of TOKEN_GAP
				t.setPositionIncrement((pos/TOKEN_GAP + 1) * TOKEN_GAP - pos + 1 );
			
			pos+=t.getPositionIncrement();
			return t;
		}
		
	}
	
	protected ArrayList<Aggregate> items = new ArrayList<Aggregate>();
	
	public AggregateAnalyzer(ArrayList<Aggregate> items){
		this.items = items;
	}
	
	@Override
	public TokenStream tokenStream(String fieldName, Reader reader) {
		return new AggregateTokenStream();
	}	
	
}
