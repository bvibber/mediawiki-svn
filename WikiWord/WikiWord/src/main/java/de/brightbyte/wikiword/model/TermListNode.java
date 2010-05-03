package de.brightbyte.wikiword.model;

import java.util.Collections;
import java.util.List;
import java.util.NoSuchElementException;

public class TermListNode<T extends TermReference>  implements PhraseNode<T> {

	protected List<T> terms;
	protected int index;
	
	protected List<TermListNode<T>> successors;
	
	public TermListNode(List<T> terms, int index) {
		if (terms==null) throw new NullPointerException();
		if (index<0 || index>=terms.size()) throw new NoSuchElementException("index out of range");
		
		this.terms = terms;
		this.index = index;
	}
	
	public String toString() {
		return getTermReference().toString();
	}

	public T getTermReference() {
		return terms.get(index);
	}
	
	public List<TermListNode<T>> getSuccessors() {
		if (successors == null) {
			if (index+1>=terms.size()) successors = Collections.emptyList();
			else  successors = Collections.singletonList(new TermListNode<T>(terms, index+1));
		}
		
		return successors;
	}

	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + index;
		result = PRIME * result + ((terms == null) ? 0 : terms.hashCode());
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
		final TermListNode other = (TermListNode) obj;
		if (index != other.index)
			return false;
		if (terms == null) {
			if (other.terms != null)
				return false;
		} else if (!terms.equals(other.terms))
			return false;
		return true;
	}

}