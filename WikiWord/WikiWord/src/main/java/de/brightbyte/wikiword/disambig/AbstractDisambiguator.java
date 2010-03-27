package de.brightbyte.wikiword.disambig;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.disambig.Disambiguator.Result;
import de.brightbyte.wikiword.model.LocalConcept;

public abstract class AbstractDisambiguator implements Disambiguator {

	protected MeaningFetcher<LocalConcept> meaningFetcher;
	protected Output trace;
	
	public AbstractDisambiguator(MeaningFetcher<LocalConcept> meaningFetcher) {
		if (meaningFetcher==null) throw new NullPointerException();
		this.meaningFetcher = meaningFetcher;
	}

	protected Map<String, List<LocalConcept>> fetchMeanings(List<String> terms) throws PersistenceException {
		Map<String, List<LocalConcept>> meanings = new HashMap<String, List<LocalConcept>>();
		
	   for (String t: terms) {
		   List<LocalConcept> m = meaningFetcher.getMeanings(t);
		   if (m!=null && m.size()>0) meanings.put(t, m);
	   }
	   
		return meanings;
	}
	
	public Result disambiguate(List<String> terms) throws PersistenceException {
		Map<String, List<LocalConcept>> meanings = fetchMeanings(terms);
		return disambiguate(terms, meanings);
	}
	
	public abstract Result disambiguate(List<String> terms, Map<String, List<LocalConcept>> meanings) throws PersistenceException;

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