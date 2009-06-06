package de.brightbyte.wikiword.integrator.processor;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.FeatureSet;
import de.brightbyte.wikiword.integrator.data.MappingCandidates;
import de.brightbyte.wikiword.integrator.store.AssociationFeatureStoreBuilder;

public class ConceptMappingPassThrough extends AbstractConceptMappingProcessor {
	protected AssociationFeatureStoreBuilder store;
	
	public ConceptMappingPassThrough(AssociationFeatureStoreBuilder store) {
		if (store==null) throw new NullPointerException();
		this.store = store;
	}
	
	protected  void processMappingCandidates(MappingCandidates m) throws PersistenceException {
		for (FeatureSet f: m.getCandidates()) {
			store.storeMapping(m.getSubject(), f, f);
		}
	}

}
