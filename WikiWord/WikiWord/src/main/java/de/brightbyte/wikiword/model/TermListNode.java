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

	public T getTermReference() {
		return terms.get(index);
	}
	
	public List<TermListNode<T>> getSuccessors() {
		if (successors == null) {
			if (index+1>=terms.size()) successors = Collections.emptyList();
			else  Collections.singletonList(new TermListNode<T>(terms, index+1));
		}
		
		return successors;
	}

}