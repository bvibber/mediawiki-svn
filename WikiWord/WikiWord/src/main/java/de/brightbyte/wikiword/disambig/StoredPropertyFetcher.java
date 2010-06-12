package de.brightbyte.wikiword.disambig;

import java.util.Collection;
import java.util.Map;

import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.ConceptProperties;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.store.PropertyStore;

public class StoredPropertyFetcher<C extends WikiWordConcept> implements PropertyFetcher<C> {
	protected PropertyStore<C>  store; 
	protected Output trace;
	private Collection<String> properties;
	
	public StoredPropertyFetcher(PropertyStore<C>  store, Collection<String> properties) {
		if (store==null) throw new NullPointerException();
		
		this.store = store;
		this.properties = properties;
	}

	public ConceptProperties<C> getProperties(C c) throws PersistenceException {
		trace("fetching properties for "+c); 
		
		if (properties == null) return store.getConceptProperties(c.getId());
		else return store.getConceptProperties(c.getId(), properties);
	}

	public Map<Integer, ConceptProperties<C>> getProperties(Collection<? extends C> concepts) throws PersistenceException {
		trace("fetching properties for "+concepts.size()+" concepts"); 
		
		int[] ids = new int[concepts.size()];
		int i = 0;
		for (C c: concepts) ids[i++] = c.getId();
		
		if (properties == null) return store.getConceptsProperties(ids);
		else return store.getConceptsProperties(ids, properties);
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

	public boolean getPropertiesAreNormalized() {
		return true;
	}
	
}
