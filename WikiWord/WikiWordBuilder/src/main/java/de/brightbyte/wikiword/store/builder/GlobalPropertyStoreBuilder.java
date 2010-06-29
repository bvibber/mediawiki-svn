package de.brightbyte.wikiword.store.builder;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.Corpus;

public interface GlobalPropertyStoreBuilder extends WikiWordPropertyStoreBuilder {

	public abstract int importProperties(Corpus from)
		throws PersistenceException;

}