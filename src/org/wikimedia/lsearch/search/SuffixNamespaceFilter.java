package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.util.BitSet;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.search.Filter;
import org.wikimedia.lsearch.config.IndexId;

public class SuffixNamespaceFilter extends Filter {
	protected SuffixFilter suffix;
	protected NamespaceFilter ns;
	protected String dbrole;
	protected String titles;
	
	public SuffixNamespaceFilter(NamespaceFilter ns, SuffixFilter suffix, IndexId iid, IndexId titles){
		this.ns = ns;
		this.suffix = suffix;
		this.dbrole = iid.toString();
		this.titles = titles.toString();
	}
	
	@Override
	public BitSet bits(IndexReader reader) throws IOException {
		if(ns == null) // search eveything
			return SuffixFilterCache.bits(suffix,reader);

		BitSet bitset = null;
		IndexId iid = IndexId.get(dbrole); 
		if(iid.getDefaultNamespace().getNamespaces().containsAll(ns.getNamespaces())){
			// expand by default namespaces over titles indexes
			bitset = (BitSet)NamespaceCache.defaultTitleBits(IndexId.get(titles),reader).clone();
		}

		if(suffix == null && bitset == null)
			bitset = NamespaceCache.bits(ns,reader);
		else{
			// intersect bitsets
			BitSet b = NamespaceCache.bits(ns,reader);
			if(bitset != null)
				bitset.or(b);
			else
				bitset = suffix==null? b : (BitSet)b.clone();
			
			if(suffix != null)
				bitset.and(SuffixFilterCache.bits(suffix,reader));
		}
		return bitset;
	}
	
	@Override
	public String toString() {
		if(ns == null)
			return suffix.toString();
		else if(suffix == null)
			return ns.toString();
		else		
			return ns+", "+suffix; 
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
	
	/** Return if we should cache this filter */
	public boolean shouldCache(){
		return suffix!=null && ns!=null && ns.contains(0);
	}

	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + ((dbrole == null) ? 0 : dbrole.hashCode());
		result = PRIME * result + ((ns == null) ? 0 : ns.hashCode());
		result = PRIME * result + ((suffix == null) ? 0 : suffix.hashCode());
		result = PRIME * result + ((titles == null) ? 0 : titles.hashCode());
		return result;
	}

	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		final SuffixNamespaceFilter other = (SuffixNamespaceFilter) obj;
		if (dbrole == null) {
			if (other.dbrole != null)
				return false;
		} else if (!dbrole.equals(other.dbrole))
			return false;
		if (ns == null) {
			if (other.ns != null)
				return false;
		} else if (!ns.equals(other.ns))
			return false;
		if (suffix == null) {
			if (other.suffix != null)
				return false;
		} else if (!suffix.equals(other.suffix))
			return false;
		if (titles == null) {
			if (other.titles != null)
				return false;
		} else if (!titles.equals(other.titles))
			return false;
		return true;
	}


	
	

}
