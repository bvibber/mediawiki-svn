package de.brightbyte.wikiword.disambig;

import java.util.Collection;
import java.util.Map;

import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.ConceptFeatures;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.store.FeatureStore;

public class StoredFeatureFetcher<C extends WikiWordConcept, K> implements FeatureFetcher<C, K> {
	protected FeatureStore<C, K>  store; 
	protected Output trace;
	
	public StoredFeatureFetcher(FeatureStore<C, K>  store) {
		if (store==null) throw new NullPointerException();
		
		this.store = store;
	}

	public ConceptFeatures<C, K> getFeatures(C c) throws PersistenceException {
		trace("fetching features for "+c); 
		return store.getConceptFeatures(c.getId());
	}

	public Map<Integer, ConceptFeatures<C, K>> getFeatures(Collection<? extends C> concepts) throws PersistenceException {
		trace("fetching features for "+concepts.size()+" concepts"); 
		
		int[] ids = new int[concepts.size()];
		int i = 0;
		for (C c: concepts) ids[i++] = c.getId(); 
		return store.getConceptsFeatures(ids);
	}
	
	public Output getTrace() {
		return trace;
	}

	public void setTrace(Output trace) {
		this.trace = trace;
	}

	protected void trace(String msg) {
		if (trace!=null) trace.println(msg);
	}

	public boolean getFeaturesAreNormalized() {
		return true;
	}
	
}
