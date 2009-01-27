package de.brightbyte.wikiword.store.builder;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.WikiWordConcept;

public interface ConceptInfoStoreBuilder<C extends WikiWordConcept> extends WikiWordStoreBuilder {
	public void buildConceptInfo() throws PersistenceException;
}
