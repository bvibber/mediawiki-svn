package de.brightbyte.wikiword.model;

import java.util.List;

public interface PhraseNode<T extends TermReference>  {

	public T getTermReference();
	
	public List<? extends PhraseNode<T>> getSuccessors();

}