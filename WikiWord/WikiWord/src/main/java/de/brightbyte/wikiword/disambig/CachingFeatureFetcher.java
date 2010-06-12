package de.brightbyte.wikiword.disambig;

import java.util.ArrayList;
import java.util.Collection;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import de.brightbyte.data.MRUHashMap;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.ConceptFeatures;
import de.brightbyte.wikiword.model.WikiWordConcept;

public class CachingFeatureFetcher<C extends WikiWordConcept, K> implements FeatureFetcher<C, K> {
	
	protected FeatureFetcher<C, K> fetcher;
	protected MRUHashMap<Integer, ConceptFeatures<C, K>> cache;
	
	public CachingFeatureFetcher(FeatureFetcher<C, K> fetcher, int capacity) {
		this.fetcher = fetcher;
		this.cache = new MRUHashMap<Integer, ConceptFeatures<C, K>>(capacity);
	}

	public boolean getFeaturesAreNormalized() {
		return fetcher.getFeaturesAreNormalized();
	}

	public ConceptFeatures<C, K> getFeatures(C c)
			throws PersistenceException {
		
		ConceptFeatures<C, K> f = cache.get(c.getId());
		if (f!=null) return f;
		
		f = fetcher.getFeatures(c);
		cache.put(c.getId(), f);
		
		return f;
	}

	public Map<Integer, ConceptFeatures<C, K>> getFeatures(Collection<? extends C> concepts) throws PersistenceException {
		Map<Integer, ConceptFeatures<C, K>> features = new HashMap<Integer, ConceptFeatures<C, K>> ();
		List<C> todo = new ArrayList<C>(concepts.size());
		
		for (C c: concepts) {
			   if (c==null) continue;
			   
				ConceptFeatures<C, K> f = cache.get(c.getId());
				if (f!=null) {
					features.put(c.getId(), f);
					continue;
				} else {
					todo.add(c);
				}
		}
		
		if (!todo.isEmpty()) {
			Map<Integer, ConceptFeatures<C, K>> parentFeatures = fetcher.getFeatures(todo);
			features.putAll(parentFeatures);
		}
		
		cache.putAll(features);
		return features;
	}

}
