package de.brightbyte.wikiword.store.builder;

import de.brightbyte.util.PersistenceException;

public interface StatisticsStoreBuilder extends WikiWordStoreBuilder {
	
	public void buildStatistics() throws PersistenceException;
	public void clear() throws PersistenceException;
	
}