package de.brightbyte.wikiword.store.builder;

import de.brightbyte.util.PersistenceException;

public interface ConceptBasedStoreBuilder extends WikiWordStoreBuilder, IncrementalStoreBuilder {

	public void finishIdReferences() throws PersistenceException;
	public void finishAliases() throws PersistenceException;

}
