package de.brightbyte.wikiword.model;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.DatasetIdentifier;


public class GlobalConcept extends WikiWordConcept {
	protected Map<String, LocalConcept> concepts;
	
	public GlobalConcept(DatasetIdentifier dataset, int id,  ConceptType type) {
		super(dataset, id, type);
	}

	public Map<String, LocalConcept> getLocalConcepts() throws PersistenceException {
		return concepts;
	}

	public LocalConcept getLocalConcept(String lang) throws PersistenceException {
		if (concepts==null) return null;
		return concepts.get(lang);
	}

	public void setConcepts(Map<String, LocalConcept> concepts) {
		this.concepts = concepts;
	}

	public void setConcepts(List<LocalConcept> concepts) {
		Map<String, LocalConcept> m = new HashMap<String, LocalConcept>();
		
		for(LocalConcept c: concepts) {
			m.put(c.getLanguage(), c);
		}
		
		setConcepts(m);
	}

}
