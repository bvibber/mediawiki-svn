package de.brightbyte.wikiword.disambig;

import java.util.List;
import java.util.Map;

import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordConcept;

public abstract class AbstractDisambiguator<T extends TermReference, C extends WikiWordConcept> implements Disambiguator<T, C> {

	protected MeaningCache.Manager<C> meaningCacheManager;
	
	protected Output trace;
	
	public AbstractDisambiguator(MeaningFetcher<? extends C> meaningFetcher) {
		if (meaningFetcher==null) throw new NullPointerException();
		this.meaningCacheManager = new MeaningCache.Manager<C>(meaningFetcher, 10);
	}

	public <X extends T>Result<X, C> disambiguate(List<X> terms) throws PersistenceException {
		MeaningCache<C> mcache = meaningCacheManager.newCache();
		Map<X, List<? extends C>> meanings = mcache.getMeanings(terms);
		return disambiguate(terms, meanings);
	}
	
	public abstract <X extends T>Result<X, C> disambiguate(List<X> terms, Map<X, List<? extends C>> meanings) throws PersistenceException;

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