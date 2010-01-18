package de.brightbyte.wikiword.store;

import de.brightbyte.data.LabeledVector;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.model.WikiWordConceptReference;

public interface FeatureStore<T extends WikiWordConcept, R extends WikiWordConceptReference<T>> {

	public LabeledVector<Integer> getFeatureVector(int concept) throws PersistenceException;

}
