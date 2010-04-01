package de.brightbyte.wikiword.disambig;

import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.List;
import java.util.Map;

import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordConcept;

public abstract class AbstractDisambiguator<T extends TermReference, C extends WikiWordConcept> implements Disambiguator<T, C> {

	private MeaningCache.Manager<C> meaningCacheManager;
	
	private Output trace;

	private Map<? extends T, C> meaningOverrides;
	
	public AbstractDisambiguator(MeaningFetcher<? extends C> meaningFetcher) {
		if (meaningFetcher==null) throw new NullPointerException();
		this.meaningCacheManager = new MeaningCache.Manager<C>(meaningFetcher, 10);
	}

	public void setMeaningOverrides(Map<? extends T, C> overrideMap) {
		this.meaningOverrides = overrideMap;
	}	

	protected <X extends T>Map<X, List<? extends C>> getMeanings(List<X> terms) throws PersistenceException {
		List<X> todo = terms;
		
		if (meaningOverrides!=null) {
			todo = new ArrayList<X>();
			for (X t: terms) {
				if (!meaningOverrides.containsKey(t)) todo.add(t);
			}
		}
		
		MeaningCache<C> mcache = meaningCacheManager.newCache();
		Map<X, List<? extends C>> meanings = mcache.getMeanings(todo);
		
		if (meaningOverrides!=null && todo.size()!=terms.size()) {
			for (X t: terms) {
				C c = meaningOverrides.get(t);
				if (c!=null) meanings.put(t, Collections.singletonList(c));
			}
		}

		return meanings;
	}
	
	public <X extends T>Result<X, C> disambiguate(List<X> terms, Collection<C> context) throws PersistenceException {
		Map<X, List<? extends C>> meanings = getMeanings(terms);
		return disambiguate(terms, meanings, context);
	}
	
	public abstract <X extends T>Result<X, C> disambiguate(List<X> terms, Map<X, List<? extends C>> meanings, Collection<C> context) throws PersistenceException;

	public Output getTrace() {
		return trace;
	}

	public void setTrace(Output trace) {
		this.trace = trace;
	}

	protected void trace(String msg) {
		if (trace!=null) trace.println(msg);
	}

}