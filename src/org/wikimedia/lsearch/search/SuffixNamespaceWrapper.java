package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.util.BitSet;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.search.Filter;

public class SuffixNamespaceWrapper extends Filter {
	protected SuffixFilter suffix;
	protected NamespaceFilter ns;
	
	public SuffixNamespaceWrapper(NamespaceFilter ns, SuffixFilter suffix){
		this.ns = ns;
		this.suffix = suffix;
	}
	
	@Override
	public BitSet bits(IndexReader reader) throws IOException {
		BitSet bitset = null;
		if(suffix == null)
			bitset = NamespaceCache.bits(ns,reader);
		else if(ns == null)
			bitset = SuffixFilterCache.bits(suffix,reader);
		else{
			// intersect bitsets
			bitset = NamespaceCache.bits(ns,reader);
			bitset.and(SuffixFilterCache.bits(suffix,reader));
		}
		return bitset;
	}
	
	@Override
	public String toString() {
		if(ns == null)
			return "wrap: "+suffix;
		else if(suffix == null)
			return "wrap: "+ns;
		else		
			return "wrap: "+ns+", "+suffix; 
	}

	public NamespaceFilter getNamespaceFilter() {
		return ns;
	}

	public void setNamespaceFilter(NamespaceFilter ns) {
		this.ns = ns;
	}

	public SuffixFilter getSuffixFilter() {
		return suffix;
	}

	public void setSuffixFilter(SuffixFilter suffix) {
		this.suffix = suffix;
	}


}
