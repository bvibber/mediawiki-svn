package de.brightbyte.wikiword.disambig;

import java.util.Collection;
import java.util.HashMap;
import java.util.List;
import java.util.ListIterator;
import java.util.Map;

import de.brightbyte.data.Functor2;
import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.store.WikiWordConceptStore;
import de.brightbyte.wikiword.store.WikiWordConceptStore.ConceptQuerySpec;

public class StoredMeaningFetcher implements MeaningFetcher<WikiWordConcept> {
	protected WikiWordConceptStore  store; 
	protected ConceptQuerySpec spec;
	protected Output trace;
	protected Functor2<WikiWordConcept, WikiWordConcept, String> meaningMangler;
	
	public StoredMeaningFetcher(WikiWordConceptStore  store) {
		this(store, null);
	}
	
	public StoredMeaningFetcher(WikiWordConceptStore  store, ConceptQuerySpec type) {
		if (store==null) throw new NullPointerException();
		
		this.store = store;
		this.spec = type;
	}

	public Functor2<WikiWordConcept, WikiWordConcept, String> getMeaningMangler() {
		return meaningMangler;
	}

	public void setMeaningMangler(Functor2<WikiWordConcept, WikiWordConcept, String> meaningMangler) {
		this.meaningMangler = meaningMangler;
	}

	public List<WikiWordConcept> getMeanings(String term) throws PersistenceException {
		DataSet<WikiWordConcept> m = store.getMeanings(term, spec); 
		List<WikiWordConcept> meanigns = m.load();
		
		if ( meaningMangler != null ) {
			ListIterator<WikiWordConcept> it = meanigns.listIterator();
			while (it.hasNext()) {
				WikiWordConcept c = it.next();
				WikiWordConcept c2 = meaningMangler.apply(c, term);
				
				if ( c2 == null ) it.remove();
				else if ( c != c2 ) it.set(c2);
			}
		}
		
		trace("fetched "+meanigns.size()+" meanings for \""+term+"\""); 
		return meanigns;
	}

	public <X extends TermReference> Map<X, List<WikiWordConcept>> getMeanings(Collection<X> terms) throws PersistenceException {
		Map<X, List<WikiWordConcept>> meanings = new HashMap<X, List<WikiWordConcept>>();
		
	   for (X t: terms) {
		   List<WikiWordConcept> m = getMeanings(t.getTerm());
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
