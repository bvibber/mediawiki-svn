package de.brightbyte.wikiword.integrator.store;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.store.WikiWordConceptStoreBase;
import de.brightbyte.wikiword.store.builder.WikiWordStoreBuilder;

public interface ConceptMappingStoreBuilder extends WikiWordStoreBuilder, WikiWordConceptStoreBase {
		public void storeMapping(String authority, String extId, String extName, int concept, String name, String via, double weight) throws PersistenceException;
}
