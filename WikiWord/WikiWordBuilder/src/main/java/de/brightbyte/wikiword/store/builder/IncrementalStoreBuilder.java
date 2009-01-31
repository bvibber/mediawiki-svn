package de.brightbyte.wikiword.store.builder;

import de.brightbyte.util.PersistenceException;

public interface IncrementalStoreBuilder {

	public void deleteDataAfter(int delAfter, boolean inclusive) throws PersistenceException;

}
