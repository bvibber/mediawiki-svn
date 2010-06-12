package de.brightbyte.wikiword.disambig;

import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import de.brightbyte.data.MRUHashMap;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordConcept;

public class CachingMeaningFetcher<C extends WikiWordConcept>  implements MeaningFetcher<C> {

	protected MeaningFetcher<? extends C> fetcher;
	protected MRUHashMap<String, List<? extends C>> cache;
	
	public CachingMeaningFetcher(MeaningFetcher<? extends C> fetcher, int capacity) {
		this.fetcher = fetcher;
		this.cache = new MRUHashMap<String, List<? extends C>>(capacity);
	}

	public List<? extends C> getMeanings(String term) throws PersistenceException {
		List<? extends C> meanings = cache.get(term); 
		
		if (meanings==null) {
			meanings = fetcher.getMeanings(term);
			cache.put(term, meanings);
		}
		
		return meanings;
	}


	public <X extends TermReference> Map<X, List<? extends C>> getMeanings(Collection<X> terms) throws PersistenceException {
		Map<X, List<? extends C>> meanings= new HashMap<X, List<? extends C>>();
		List<X> todo = new ArrayList<X>(terms.size());
		
		for (X t: terms) {
				 List<? extends C> m = cache.get(t.getTerm());
				if (m!=null) {
					meanings.put(t, m);
					cache.put(t.getTerm(), m);
					continue;
				} else {
					todo.add(t);
				}
		}
		
		if (!todo.isEmpty()) {
			Map<X, List<? extends C>> parentMeanings = (Map<X, List<? extends C>>)(Object)fetcher.getMeanings(todo); //XXX: ugly cast, generics are a pain
			meanings.putAll(parentMeanings);
			
			for (X t: todo) {
					List<? extends C> m = parentMeanings.get(t);
					if (m==null) m = Collections.emptyList();
					cache.put(t.getTerm(), m);
			}
		}
		
		return meanings;
	}

}
