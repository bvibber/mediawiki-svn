package de.brightbyte.wikiword.integrator.processor;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.FeatureMapping;
import de.brightbyte.wikiword.integrator.data.FeatureSet;
import de.brightbyte.wikiword.integrator.data.MappingCandidates;
import de.brightbyte.wikiword.integrator.store.AssociationFeature2ConceptAssociationStoreBuilder;
import de.brightbyte.wikiword.integrator.store.AssociationFeature2ConceptMappingStoreBuilder;
import de.brightbyte.wikiword.integrator.store.AssociationFeatureStoreBuilder;
import de.brightbyte.wikiword.integrator.store.ConceptAssociationStoreBuilder;
import de.brightbyte.wikiword.integrator.store.ConceptMappingStoreBuilder;

public class AssociationFeaturePassThrough extends AbstractConceptMappingProcessor {
	protected AssociationFeatureStoreBuilder store;
	
	public AssociationFeaturePassThrough(ConceptAssociationStoreBuilder store, FeatureMapping foreignMapping, FeatureMapping conceptMapping, FeatureMapping assocMapping) {
		this(new AssociationFeature2ConceptAssociationStoreBuilder(store, foreignMapping, conceptMapping, assocMapping));
	}
	
	public AssociationFeaturePassThrough(ConceptMappingStoreBuilder store, FeatureMapping foreignMapping, FeatureMapping conceptMapping, FeatureMapping assocMapping) {
		this(new AssociationFeature2ConceptMappingStoreBuilder(store, foreignMapping, conceptMapping, assocMapping));
	}
	
	public AssociationFeaturePassThrough(AssociationFeatureStoreBuilder store) {
		if (store==null) throw new NullPointerException();
		this.store = store;
	}
	
	protected  void processMappingCandidates(MappingCandidates m) throws PersistenceException {
		for (FeatureSet f: m.getCandidates()) {
			store.storeAssociationFeatures(m.getSubject(), f, f);
		}
	}

}
