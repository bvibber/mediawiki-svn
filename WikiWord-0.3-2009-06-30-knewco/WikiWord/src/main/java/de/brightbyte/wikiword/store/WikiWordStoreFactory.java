package de.brightbyte.wikiword.store;

import de.brightbyte.util.PersistenceException;

public interface WikiWordStoreFactory<S extends WikiWordStore> {
	public S newStore() throws PersistenceException;
}
