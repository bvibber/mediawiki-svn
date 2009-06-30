package de.brightbyte.wikiword.model;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Corpus;

public class LocalConcept extends WikiWordConcept {
	
	/*
	public static final Factory factory = new Factory<LocalConcept>() {
	
		public LocalConcept newInstance(Map<String, Object> m) {
			Corpus corpus = (Corpus)m.get("corpus"); //XXX: evil hack!
			
			int id = (Integer)m.get("cId");
			String name = (String)m.get("cName");
			ConceptType type = corpus.getConceptTypes().getType((Integer)m.get("cType"));
			
			int cardinality = m.get("qFreq") != null ? ((Number)m.get("qFreq")).intValue() : -1;
			double relevance = m.get("qConf") != null ? ((Number)m.get("qConf")).doubleValue() : -1;
			
			int rcId = m.get("rcId") != null ? ((Number)m.get("rcId")).intValue() : 0;
			String rcName = (String)m.get("rcName");
			ResourceType rcType = m.get("rcType") != null ? ResourceType.getType((Integer)m.get("rcType")) : null;

			String definition = (String)m.get("fDefinition");
			
			WikiWordRanking ranking = new WikiWordRanking(cardinality, relevance);
			LocalConceptReference[] broader = LocalConceptReference.parseList( (String)m.get("rBroader"), ConceptDescriptionStoreSchema.broaderReferenceListEntry ); 
			LocalConceptReference[] narrower = LocalConceptReference.parseList( (String)m.get("rNarrower"), ConceptDescriptionStoreSchema.narrowerReferenceListEntry ); 
			TranslationReference[] langlinks = TranslationReference.parseList( (String)m.get("rLanglinks"), ConceptDescriptionStoreSchema.langlinkReferenceListEntry ); 
			TermReference[] terms = TermReference.parseList( (String)m.get("dTerms"), ConceptDescriptionStoreSchema.termReferenceListEntry );
			
			WikiWordResource resource = rcId <= 0 ? null : new WikiWordResource(corpus, rcId, rcName, rcType);
			ConceptDescription description = new ConceptDescription(corpus, resource, type, definition, terms); 
			ConceptRelations<LocalConceptReference> relations = new ConceptRelations<LocalConceptReference>(broader, narrower, langlinks);
			
			return new LocalConcept(id, name, corpus, type, relations, description, ranking);
		}
	
	};
	*/
	protected ConceptRelations<LocalConceptReference> relations;
	protected ConceptDescription description;
	
	public LocalConcept(LocalConceptReference reference, Corpus corpus, ConceptType type, /*URI uri,*/ ConceptRelations<LocalConceptReference> relations, ConceptDescription description) {
		super(reference, corpus, type);

		if (relations==null) throw new NullPointerException();
		if (description==null) throw new NullPointerException();
		//if (corpus==null) throw new NullPointerException();

		this.relations = relations;
		this.description = description;
	}

	public Corpus getCorpus() {
		return (Corpus)getDatasetIdentifier();
	}

	public ConceptDescription getDescription() {
		return description;
	}

	public ConceptRelations getRelations() {
		return relations;
	}

	public String getDefinition() {
		return description.getDefinition();
	}

	public WikiWordResource getResource() {
		return description.getResource();
	}

	public TermReference[] getTerms() {
		return description.getTerms();
	}

	public TranslationReference[] getLanglinks() {
		return relations.getLanglinks();
	}

	@Override
	public LocalConceptReference[] getBroader() {
		return relations.getBroader();
	}

	@Override
	public LocalConceptReference[] getNarrower() {
		return relations.getNarrower();
	}	

	@Override
	public LocalConceptReference[] getRelated() {
		return relations.getRelated();
	}

	@Override
	public LocalConceptReference[] getSimilar() {
		return relations.getSimilar();
	}	

	@Override
	public LocalConceptReference[] getInLinks() {
		return relations.getInLinks();
	}

	@Override
	public LocalConceptReference[] getOutLinks() {
		return relations.getOutLinks();
	}	
}
