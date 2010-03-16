package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.util.BitSet;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.search.Filter;
import org.wikimedia.lsearch.config.IndexId;

public class SuffixNamespaceWrapper extends Filter {
	SuffixNamespaceFilter filter = null;
		
	public SuffixNamespaceWrapper(SuffixNamespaceFilter filter) {
		this.filter = filter;
	}

	@Override
	public BitSet bits(IndexReader reader) throws IOException {
		return SuffixNamespaceCache.bits(filter,reader);
	}

	@Override
	public String toString() {
		return "wrap: "+filter;
	}

}
