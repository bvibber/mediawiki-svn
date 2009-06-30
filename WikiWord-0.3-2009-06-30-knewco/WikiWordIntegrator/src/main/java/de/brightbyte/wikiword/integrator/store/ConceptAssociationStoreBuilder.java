package de.brightbyte.wikiword.integrator.store;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.store.WikiWordConceptStoreBase;
import de.brightbyte.wikiword.store.builder.WikiWordStoreBuilder;

public interface ConceptAssociationStoreBuilder extends WikiWordStoreBuilder, WikiWordConceptStoreBase {
		public void storeAssociation(String authority, String extId, String extName, 
																int concept, String name,
																String foreignProperty,
																String conceptProperty, String conceptPropertySource, int conceptPropertyFreq,
																String value, double weight) throws PersistenceException;
}
