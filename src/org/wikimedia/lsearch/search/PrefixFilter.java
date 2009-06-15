package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.util.Arrays;
import java.util.BitSet;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.Term;
import org.apache.lucene.index.TermDocs;
import org.apache.lucene.index.TermEnum;
import org.apache.lucene.search.Filter;

public class PrefixFilter extends Filter {
	String[] prefixes;
	
	public PrefixFilter(String[] prefixes){
		this.prefixes = prefixes;
	}
	
	@Override
	public BitSet bits(IndexReader reader) throws IOException {
		BitSet bits = new BitSet();
		for(String prefix : prefixes){
			TermEnum terms = reader.terms(new Term("prefix",prefix));		
			try{
				// get all titles and documents which begin with prefix			
				while(terms.term().text().startsWith(prefix) && terms.term().field().equals("prefix")){
					TermDocs td = reader.termDocs(new Term("prefix",terms.term().text()));
					while(td.next()){
						bits.set(td.doc());
					}
					td.close();
					terms.next();
				}
			} finally{
				terms.close();
			}
		}
		
		return bits;
	}

	@Override
	public String toString() {
		return "prefix="+Arrays.toString(prefixes);
	}
	
	

}
