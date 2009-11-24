package de.brightbyte.wikiword.store.builder;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.WikiWordConcept;

public interface ConceptInfoStoreBuilder<C extends WikiWordConcept> extends WikiWordStoreBuilder {

	/**
	 * Build cache of properties imanent to a concent (level 0)
	 * @throws PersistenceException
	 */
	public void buildConceptDescriptionCache() throws PersistenceException;
	
	/**
	 * Build cache of direct, explicit relations between concent (level 1)
	 * @throws PersistenceException
	 */
	public void buildConceptRelationCache() throws PersistenceException;
	
	/**
	 * Build cache of derived features (level 1b, abstraction of level 1)
	 * @throws PersistenceException
	 */
	public void buildConceptFeatureCache() throws PersistenceException;
	
	/**
	 * Build cache of proximities between concepts (level 2, derived from 1b) 
	 * @throws PersistenceException
	 */
	public void buildConceptProximetyCache() throws PersistenceException;
}
