package de.brightbyte.wikiword.integrator.store;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.Record;
import de.brightbyte.wikiword.store.WikiWordConceptStoreBase;
import de.brightbyte.wikiword.store.builder.WikiWordStoreBuilder;

public interface ForeignRecordStoreBuilder extends WikiWordStoreBuilder, WikiWordConceptStoreBase {
	public abstract void storeRecord(Record rec) throws PersistenceException;
}
