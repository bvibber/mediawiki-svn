package de.brightbyte.wikiword.store;

import java.util.Map;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.ConceptFeatures;
import de.brightbyte.wikiword.model.WikiWordConcept;

public interface FeatureStore<T extends WikiWordConcept, K> {

	public ConceptFeatures<T, K> getConceptFeatures(int concept) throws PersistenceException;
	public Map<Integer, ConceptFeatures<T, K>> getConceptsFeatures(int[] concepts) throws PersistenceException;

}
