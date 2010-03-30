package de.brightbyte.wikiword.disambig;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordConcept;

public abstract class AbstractDisambiguator<T extends TermReference, C extends WikiWordConcept> implements Disambiguator<T, C> {

	protected MeaningFetcher<? extends C> meaningFetcher;
	protected Output trace;
	
	public AbstractDisambiguator(MeaningFetcher<? extends C> meaningFetcher) {
		if (meaningFetcher==null) throw new NullPointerException();
		this.meaningFetcher = meaningFetcher;
	}

	protected <X extends T>Map<X, List<? extends C>> fetchMeanings(List<X> terms) throws PersistenceException {
		Map<X, List<? extends C>> meanings = new HashMap<X, List<? extends C>>();
		
	   for (X t: terms) {
		   List<? extends C> m = meaningFetcher.getMeanings(t.getTerm());
		   if (m!=null && m.size()>0) meanings.put(t, m);
	   }
	   
		return meanings;
	}
	
	public <X extends T>Result<X, C> disambiguate(List<X> terms) throws PersistenceException {
		Map<X, List<? extends C>> meanings = fetchMeanings(terms);
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