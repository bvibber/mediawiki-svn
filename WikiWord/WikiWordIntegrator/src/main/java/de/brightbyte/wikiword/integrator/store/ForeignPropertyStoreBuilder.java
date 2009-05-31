package de.brightbyte.wikiword.integrator.store;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.store.WikiWordConceptStoreBase;
import de.brightbyte.wikiword.store.builder.WikiWordStoreBuilder;

public interface ForeignPropertyStoreBuilder extends WikiWordStoreBuilder, WikiWordConceptStoreBase {
	public abstract void storeProperty(String authority, String extId, int conceptId, String conceptName, String property, String value, String qualifier) throws PersistenceException;
}
