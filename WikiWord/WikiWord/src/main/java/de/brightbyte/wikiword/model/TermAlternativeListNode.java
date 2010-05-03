package de.brightbyte.wikiword.model;

import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.List;
import java.util.NoSuchElementException;

public class TermAlternativeListNode<T extends TermReference>  implements PhraseNode<T> {

	protected List<? extends Collection<T>> terms;
	protected int index;
	protected T term;
	
	protected List<TermAlternativeListNode<T>> successors;
	
	public TermAlternativeListNode(T term, List<? extends Collection<T>> terms, int index) {
		if (term==null) throw new NullPointerException();
		if (terms==null) throw new NullPointerException();
		if (index<0 || index>=terms.size()) throw new NoSuchElementException("index out of range");
		
		this.term = term;
		this.terms = terms;
		this.index = index;
	}

	public T getTermReference() {
		return term;
	}
	
	public List<TermAlternativeListNode<T>> getSuccessors() {
		if (successors == null) {
			if (index+1>=terms.size()) successors = Collections.emptyList();
			else  {
				Collection<T> alternatives = terms.get(index+1);
				successors = new ArrayList<TermAlternativeListNode<T>>(); 
				for (T t: alternatives) {
					TermAlternativeListNode<T> n = new TermAlternativeListNode<T>(t, terms, index+1);
					successors.add(n);
				}
			}
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
		final TermAlternativeListNode other = (TermAlternativeListNode) obj;
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