package de.brightbyte.wikiword.store.builder;

import de.brightbyte.util.PersistenceException;

public interface ConceptBasedStoreBuilder extends WikiWordStoreBuilder {

	public void finishIdReferences() throws PersistenceException;
	public void finishAliases() throws PersistenceException;

}
