package de.brightbyte.wikiword.store.builder;

import de.brightbyte.util.PersistenceException;

public interface ProximityStoreBuilder extends WikiWordStoreBuilder {
	
	public void buildFeatures() throws PersistenceException;
	public void buildProximity() throws PersistenceException;
	
}