package de.brightbyte.wikiword.disambig;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.store.LocalConceptStore;
import de.brightbyte.wikiword.store.WikiWordConceptStore.ConceptQuerySpec;

public class StoredMeaningFetcher implements MeaningFetcher<LocalConcept> {
	protected LocalConceptStore  store; 
	protected ConceptQuerySpec spec;
	protected Output trace;
	
	public StoredMeaningFetcher(LocalConceptStore  store) {
		this(store, null);
	}
	
	public StoredMeaningFetcher(LocalConceptStore  store, ConceptQuerySpec type) {
		if (store==null) throw new NullPointerException();
		
		this.store = store;
		this.spec = type;
	}

	public List<LocalConcept> getMeanings(String term) throws PersistenceException {
		trace("fetching meanings for \""+term+"\""); 
		DataSet<LocalConcept> m = store.getMeanings(term, spec); //FIXME: filter/cut-off rules, sort order! //XXX: relevance value?
		return m.load();
	}

	public <X extends TermReference> Map<X, List<? extends LocalConcept>> getMeanings(List<X> terms) throws PersistenceException {
		Map<X, List<? extends LocalConcept>> meanings = new HashMap<X, List<? extends LocalConcept>>();
		
	   for (X t: terms) {
		   List<LocalConcept> m = getMeanings(t.getTerm());
		   if (m!=null && m.size()>0) meanings.put(t, m);
	   }
	   
		return meanings;
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
	 
}
