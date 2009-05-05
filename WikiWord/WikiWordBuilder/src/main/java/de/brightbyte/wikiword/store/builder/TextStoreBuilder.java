package de.brightbyte.wikiword.store.builder;

import de.brightbyte.util.PersistenceException;

public interface TextStoreBuilder extends WikiWordStoreBuilder, IncrementalStoreBuilder {
	public abstract void storeRawText(int rcId, String rcName, String text)
		throws PersistenceException;

	public abstract void storePlainText(int rcId, String rcName, String text)
		throws PersistenceException;

	//public abstract void finishAliases() throws PersistenceException;
	//public abstract void finishIdReferences() throws PersistenceException;
	
}