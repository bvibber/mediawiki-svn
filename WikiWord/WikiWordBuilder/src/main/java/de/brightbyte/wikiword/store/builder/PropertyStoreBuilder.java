package de.brightbyte.wikiword.store.builder;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.store.WikiWordLocalStore;

public interface PropertyStoreBuilder extends WikiWordStoreBuilder, WikiWordLocalStore {
	public abstract void storeProperty(int resourceId, int conceptId, String concept, String property, String value)
		throws PersistenceException;

	/*
	public abstract int storeConcept(int rcId, String name, ConceptType ctype)
		throws PersistenceException;

	public abstract int storeResource(String name, ResourceType ptype, Date time)
		throws PersistenceException;

	public abstract void storeConceptAlias(int rcId, int source, String sourceName, int target, String targetName, AliasScope scope)
		throws PersistenceException; 
	*/

	public abstract void finishAliases() throws PersistenceException;

}