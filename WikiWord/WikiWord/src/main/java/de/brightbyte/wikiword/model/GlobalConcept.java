package de.brightbyte.wikiword.model;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.DatasetIdentifier;


public class GlobalConcept extends WikiWordConcept {
	private Map<String, LocalConcept> concepts;
	private Corpus[] languages;
	
	public GlobalConcept(DatasetIdentifier dataset, int id,  ConceptType type) {
		super(dataset, id, type);
	}

	public Map<String, LocalConcept> getLocalConcepts() throws PersistenceException {
		return concepts;
	}

	public Corpus[] getLanguages() throws PersistenceException {
		return languages;
	}

	public LocalConcept getLocalConcept(String lang) throws PersistenceException {
		if (concepts==null) return null;
		return concepts.get(lang);
	}

	public void setConcepts(Map<String, LocalConcept> concepts) {
		if (this.concepts!=null) throw new IllegalStateException("property already initialized");
		this.concepts = concepts;
	}

	public void setLanguages(Corpus[] languages) {
		if (this.languages!=null) throw new IllegalStateException("property already initialized");
		this.languages = languages;
	}

	public void setConcepts(List<LocalConcept> concepts) {
		Map<String, LocalConcept> m = new HashMap<String, LocalConcept>();
		
		for(LocalConcept c: concepts) {
			m.put(c.getLanguage(), c);
		}
		
		setConcepts(m);
	}

}
