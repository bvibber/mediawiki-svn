package de.brightbyte.wikiword.model;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.store.GlobalConceptStore;


public class GlobalConcept extends WikiWordConcept {
	protected Map<String, ConceptDescription> descriptions;
	protected List<LocalConcept> concepts;
	protected ConceptRelations<GlobalConceptReference> relations;
	
	protected GlobalConceptStore store;
	protected Corpus[] languages;
	
	public GlobalConcept(GlobalConceptReference reference, DatasetIdentifier dataset, Corpus[] languages, ConceptType type,  
			GlobalConceptStore store, ConceptRelations<GlobalConceptReference> relations, 
			List<LocalConcept> concepts) {
		
		super(reference, dataset, type);
		
		if (relations==null) throw new NullPointerException();
		
		this.languages = languages;
		this.type = type;
		
		this.concepts = concepts;
		this.relations = relations;
		
		this.store = store;
	}

	@Deprecated
	public Map<String, ConceptDescription> getDescriptions() throws PersistenceException {
		if (descriptions==null) {
			List<LocalConcept> cc = getLocalConcepts();
			Map<String, ConceptDescription> m = new HashMap<String, ConceptDescription>(cc.size());
			for (LocalConcept c: cc) {
				m.put(c.getCorpus().getLanguage(), c.getDescription());
			}
		}
		
		return descriptions;
	}

	public List<LocalConcept> getLocalConcepts() throws PersistenceException {
		if (concepts==null) {
			concepts = store.getLocalConcepts(getId());
		}
		
		return concepts;
	}

	public LocalConcept getLocalConcept(String lang) throws PersistenceException {
		List<LocalConcept> cc = getLocalConcepts();
		
		for (LocalConcept c: cc) {
			if (c.getCorpus().getLanguage().equals(lang)) return c;
		}
		
		return null;
	}

	public ConceptRelations<GlobalConceptReference> getRelations() {
		return relations;
	}

	@Override
	public GlobalConceptReference[] getInLinks() {
		return relations.getInLinks();
	}

	@Override
	public GlobalConceptReference[] getOutLinks() {
		return relations.getOutLinks();
	}

	@Override
	public GlobalConceptReference[] getBroader() {
		return relations.getBroader();
	}

	@Override
	public GlobalConceptReference[] getNarrower() {
		return relations.getNarrower();
	}

	public TranslationReference[] getLanglinks() {
		return relations.getLanglinks();
	}
	
	public GlobalConceptReference[] getSimilar() {
		return relations.getSimilar();
	}
	
	public GlobalConceptReference[] getRelated() {
		return relations.getRelated();
	}
	
	
}
