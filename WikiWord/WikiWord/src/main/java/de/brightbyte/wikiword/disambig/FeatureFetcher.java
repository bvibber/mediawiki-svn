package de.brightbyte.wikiword.disambig;

import de.brightbyte.data.LabeledVector;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.WikiWordConcept;

public interface FeatureFetcher<K> {
	public LabeledVector<K> getFeatures(WikiWordConcept c) throws PersistenceException;
}
