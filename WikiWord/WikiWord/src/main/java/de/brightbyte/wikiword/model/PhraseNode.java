package de.brightbyte.wikiword.model;

import java.util.Collection;

public interface PhraseNode<T extends TermReference>  {

	public T getTermReference();
	
	public Collection<? extends PhraseNode<T>> getSuccessors();

}