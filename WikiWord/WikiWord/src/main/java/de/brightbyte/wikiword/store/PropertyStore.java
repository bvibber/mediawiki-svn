package de.brightbyte.wikiword.store;

import java.util.Collection;
import java.util.Map;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.ConceptProperties;
import de.brightbyte.wikiword.model.WikiWordConcept;

public interface PropertyStore<T extends WikiWordConcept> {

	public ConceptProperties<T> getConceptProperties(int concept) throws PersistenceException;
	public ConceptProperties<T> getConceptProperties(int concept, Collection<String> props) throws PersistenceException;

	public Map<Integer, ConceptProperties<T>> getConceptsProperties(int[] concepts) throws PersistenceException;
	public Map<Integer, ConceptProperties<T>> getConceptsProperties(int[] concepts, Collection<String> props) throws PersistenceException;

}
