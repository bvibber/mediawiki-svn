package de.brightbyte.wikiword.store.builder;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ResourceType;

public interface TextStoreBuilder extends WikiWordStoreBuilder {
	public abstract void storeRawText(int textId, String name, ResourceType ptype, String text)
		throws PersistenceException;

	public abstract void storePlainText(int textId, String name, ResourceType ptype, String text)
		throws PersistenceException;
	
}