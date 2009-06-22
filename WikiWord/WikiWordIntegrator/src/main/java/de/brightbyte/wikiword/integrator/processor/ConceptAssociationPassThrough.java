package de.brightbyte.wikiword.integrator.processor;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.Association;
import de.brightbyte.wikiword.integrator.store.AssociationFeatureStoreBuilder;

public class ConceptAssociationPassThrough extends AbstractConceptAssociationProcessor {
	protected AssociationFeatureStoreBuilder store;
	
	public ConceptAssociationPassThrough(AssociationFeatureStoreBuilder store) {
		if (store==null) throw new NullPointerException();
		this.store = store;
	}
	
	protected  void processAssociation(Association m) throws PersistenceException {
			store.storeAssociationFeatures(m.getSourceItem(), m.getTargetItem(), m.getProperties());
	}

}
