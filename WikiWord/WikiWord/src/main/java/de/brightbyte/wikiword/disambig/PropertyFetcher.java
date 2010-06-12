package de.brightbyte.wikiword.disambig;

import java.util.Collection;
import java.util.Map;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.ConceptProperties;
import de.brightbyte.wikiword.model.WikiWordConcept;

public interface PropertyFetcher<C extends WikiWordConcept> {
	public ConceptProperties<C> getProperties(C c) throws PersistenceException;
	public Map<Integer, ConceptProperties<C>> getProperties(Collection<? extends C> c) throws PersistenceException;
}
