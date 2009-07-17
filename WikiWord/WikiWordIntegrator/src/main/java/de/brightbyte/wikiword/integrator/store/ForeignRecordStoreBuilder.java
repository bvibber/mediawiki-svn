package de.brightbyte.wikiword.integrator.store;

import java.util.Map;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.store.WikiWordConceptStoreBase;
import de.brightbyte.wikiword.store.builder.WikiWordStoreBuilder;

public interface ForeignRecordStoreBuilder extends WikiWordStoreBuilder, WikiWordConceptStoreBase {
	public abstract void storeRecord(Map<String, Object> rec) throws PersistenceException;
}
