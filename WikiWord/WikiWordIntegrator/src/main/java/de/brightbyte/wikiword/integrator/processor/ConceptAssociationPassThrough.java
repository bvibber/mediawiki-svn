package de.brightbyte.wikiword.integrator.processor;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.Association;
import de.brightbyte.wikiword.integrator.store.ConceptAssociationStoreBuilder;

public class ConceptAssociationPassThrough extends AbstractConceptAssociationProcessor {
	protected ConceptAssociationStoreBuilder store;
	
	public ConceptAssociationPassThrough(ConceptAssociationStoreBuilder store) {
		if (store==null) throw new NullPointerException();
		this.store = store;
	}
	
	protected  void processAssociation(Association m) throws PersistenceException {
			store.storeAssociation(m.getForeignEntity(), m.getConceptEntity(), m.getQualifiers());
	}

}
