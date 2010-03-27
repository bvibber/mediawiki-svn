package de.brightbyte.wikiword.disambig;

import java.util.List;
import java.util.Map;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.ConceptFeatures;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.store.FeatureStore;

public class StoredFeatureFetcher<C extends WikiWordConcept, K> implements FeatureFetcher<C, K> {
	protected FeatureStore<C, K>  store; 
	
	public StoredFeatureFetcher(FeatureStore<C, K>  store) {
		if (store==null) throw new NullPointerException();
		
		this.store = store;
	}

	public ConceptFeatures<C, K> getFeatures(C c) throws PersistenceException {
		return store.getConceptFeatures(c.getId());
	}

	public Map<Integer, ConceptFeatures<C, K>> getFeatures(List<C> concepts) throws PersistenceException {
		int[] ids = new int[concepts.size()];
		int i = 0;
		for (C c: concepts) ids[i++] = c.getId(); 
		return store.getConceptsFeatures(ids);
	}
	
}
