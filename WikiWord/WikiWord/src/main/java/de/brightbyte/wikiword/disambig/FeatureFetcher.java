package de.brightbyte.wikiword.disambig;

import java.util.Collection;
import java.util.Map;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.ConceptFeatures;
import de.brightbyte.wikiword.model.WikiWordConcept;

public interface FeatureFetcher<C extends WikiWordConcept, K> {
	public ConceptFeatures<C, K> getFeatures(C c) throws PersistenceException;
	public Map<Integer, ConceptFeatures<C, K>> getFeatures(Collection<? extends C> c) throws PersistenceException;
	public boolean getFeaturesAreNormalized();
}
