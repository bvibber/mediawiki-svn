package de.brightbyte.wikiword.disambig;

import java.util.ArrayList;
import java.util.Collection;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import de.brightbyte.data.MRUHashMap;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.ConceptProperties;
import de.brightbyte.wikiword.model.WikiWordConcept;

public class CachingPropertyFetcher<C extends WikiWordConcept> implements PropertyFetcher<C> {
	
	protected PropertyFetcher<C> fetcher;
	protected MRUHashMap<Integer, ConceptProperties<C>> cache;
	
	public CachingPropertyFetcher(PropertyFetcher<C> fetcher, int capacity) {
		this.fetcher = fetcher;
		this.cache = new MRUHashMap<Integer, ConceptProperties<C>>(capacity);
	}

	public ConceptProperties<C> getProperties(C c)
			throws PersistenceException {
		
		ConceptProperties<C> f = cache.get(c.getId());
		if (f!=null) return f;
		
		f = fetcher.getProperties(c);
		cache.put(c.getId(), f);
		
		return f;
	}

	public Map<Integer, ConceptProperties<C>> getProperties(Collection<? extends C> concepts) throws PersistenceException {
		Map<Integer, ConceptProperties<C>> features = new HashMap<Integer, ConceptProperties<C>> ();
		List<C> todo = new ArrayList<C>(concepts.size());
		
		for (C c: concepts) {
			   if (c==null) continue;
			   
				ConceptProperties<C> f = cache.get(c.getId());
				if (f!=null) {
					features.put(c.getId(), f);
					continue;
				} else {
					todo.add(c);
				}
		}
		
		if (!todo.isEmpty()) {
			Map<Integer, ConceptProperties<C>> parentProperties = fetcher.getProperties(todo);
			features.putAll(parentProperties);
		}
		
		cache.putAll(features);
		return features;
	}

}
