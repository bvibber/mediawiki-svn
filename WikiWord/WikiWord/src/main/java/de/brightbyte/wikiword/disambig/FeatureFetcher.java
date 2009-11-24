package de.brightbyte.wikiword.disambig;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.WikiWordConcept;

public interface FeatureFetcher<C extends WikiWordConcept> {
	public ConceptFeatures<C> getFeatures(C c) throws PersistenceException;
}
