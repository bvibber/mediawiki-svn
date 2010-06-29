package de.brightbyte.wikiword.store.builder;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.store.WikiWordLocalStore;

public interface LocalPropertyStoreBuilder extends WikiWordStoreBuilder, WikiWordLocalStore, ConceptBasedStoreBuilder {

	public abstract void storeProperty(int resourceId, int conceptId, String concept, String property, String value)
		throws PersistenceException;

	public abstract void finishAliases() throws PersistenceException;
	public abstract void finishIdReferences() throws PersistenceException;

}