package de.brightbyte.wikiword.store.builder;

import de.brightbyte.util.PersistenceException;

public interface IncrementalStoreBuilder extends WikiWordStoreBuilder {

	public void preparePostProcessing() throws PersistenceException;
	
	public void deleteDataAfter(int delAfter, boolean inclusive) throws PersistenceException;

	public void prepareMassProcessing() throws PersistenceException;
	public void prepareMassInsert() throws PersistenceException;

}
