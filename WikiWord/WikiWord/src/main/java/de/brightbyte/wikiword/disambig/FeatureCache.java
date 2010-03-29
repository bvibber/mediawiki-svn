package de.brightbyte.wikiword.disambig;

import java.util.ArrayList;
import java.util.Collection;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.ConceptFeatures;
import de.brightbyte.wikiword.model.WikiWordConcept;

public class FeatureCache<C extends WikiWordConcept, K> implements FeatureFetcher<C, K> {

	protected FeatureFetcher<C, K> parent;
	
	protected Map<Integer, ConceptFeatures<C, K>> cache;
	
	public FeatureCache(FeatureFetcher<C, K> parent) {
		if (parent==null) throw new NullPointerException();
		this.parent = parent;
		this.cache = new HashMap<Integer, ConceptFeatures<C,K>>();
	}

	public ConceptFeatures<C, K> getFeatures(C c)
			throws PersistenceException {
		 
		ConceptFeatures<C, K> f = cache.get(c.getId());
		if (f!=null) return f;
		
		f = parent.getFeatures(c);
		cache.put(c.getId(), f);
		
		return f;
	}

	public Map<Integer, ConceptFeatures<C, K>> getFeatures(Collection<C> concepts) throws PersistenceException {
		Map<Integer, ConceptFeatures<C, K>> features = new HashMap<Integer, ConceptFeatures<C, K>> ();
		List<C> todo = new ArrayList<C>(concepts.size());
		for (C c: concepts) {
				ConceptFeatures<C, K> f = cache.get(c.getId());
				if (f!=null) {
					features.put(c.getId(), f);
					continue;
				} else {
					todo.add(c);
				}
		}
		
		Map<Integer, ConceptFeatures<C, K>> parentFeatures = parent.getFeatures(todo);
		features.putAll(parentFeatures);
		cache.putAll(parentFeatures);
		
		return features;
	}
	
	public FeatureFetcher getParent() {
		return parent;
	}
	
	public void setParent(FeatureCache<C, K> parent) { 
		if (parent == null) throw new NullPointerException();
		if (parent == this) throw new IllegalArgumentException("can't be my own parent");
		//TODO: prevent cycles
		
		this.parent = parent;
	}
	
	public void clear() {
		cache.clear();
	}

}
