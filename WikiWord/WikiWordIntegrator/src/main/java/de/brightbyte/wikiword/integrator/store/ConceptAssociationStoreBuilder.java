package de.brightbyte.wikiword.integrator.store;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.ConceptEntityRecord;
import de.brightbyte.wikiword.integrator.data.ForeignEntityRecord;
import de.brightbyte.wikiword.integrator.data.Record;
import de.brightbyte.wikiword.store.WikiWordConceptStoreBase;
import de.brightbyte.wikiword.store.builder.WikiWordStoreBuilder;

public interface ConceptAssociationStoreBuilder extends WikiWordStoreBuilder, WikiWordConceptStoreBase {
	public void storeAssociation(ForeignEntityRecord subject, ConceptEntityRecord object, Record qualifiers) throws PersistenceException;
}
