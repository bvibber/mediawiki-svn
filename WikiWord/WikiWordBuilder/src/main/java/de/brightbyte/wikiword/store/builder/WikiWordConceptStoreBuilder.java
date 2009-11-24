package de.brightbyte.wikiword.store.builder;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.model.WikiWordConceptReference;
import de.brightbyte.wikiword.store.WikiWordConceptStore;
import de.brightbyte.wikiword.store.WikiWordConceptStoreBase;

public interface WikiWordConceptStoreBuilder<T extends WikiWordConcept> extends WikiWordStoreBuilder, WikiWordConceptStoreBase {

	public StatisticsStoreBuilder getStatisticsStoreBuilder() throws PersistenceException;
	public ConceptInfoStoreBuilder<T> getConceptInfoStoreBuilder() throws PersistenceException;
	public ProximityStoreBuilder getProximityStoreBuilder() throws PersistenceException;

	public WikiWordConceptStore<T, ? extends WikiWordConceptReference<T>> getConceptStore() throws PersistenceException;

}
