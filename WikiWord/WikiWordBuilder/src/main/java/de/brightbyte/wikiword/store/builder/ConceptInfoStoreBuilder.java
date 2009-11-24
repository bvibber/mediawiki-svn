package de.brightbyte.wikiword.store.builder;

import de.brightbyte.data.cursor.CursorProcessor;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.disambig.ConceptFeatures;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.schema.ConceptInfoStoreSchema.ReferenceListEntrySpec;

public interface ConceptInfoStoreBuilder<C extends WikiWordConcept> extends WikiWordStoreBuilder {
	public void buildConceptInfo() throws PersistenceException;
	
	public int buildConceptPropertyCache(String targetField, String propertyTable, String propertyConceptField,
			ReferenceListEntrySpec spec, String threshold)  throws PersistenceException;
	
	public void storeConceptFeatures(ConceptFeatures<C> features) throws PersistenceException;
	
	public int processConcepts(CursorProcessor<C> processor) throws PersistenceException;
}
