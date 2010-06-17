package de.brightbyte.wikiword.store.builder;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.model.GlobalConcept;


public interface GlobalConceptStoreBuilder extends WikiWordConceptStoreBuilder<GlobalConcept> {

	public void importConcepts() throws PersistenceException;
	
	public void buildGlobalConcepts() throws PersistenceException;
	
	public int getNextIdOffset() throws PersistenceException;

	public Corpus[] getLanguages() throws PersistenceException;

	public Corpus[] detectLanguages() throws PersistenceException;

	public void setLanguages(Corpus[] languages);
	
	public int getMaxConceptId() throws PersistenceException;

	public void setLanguages(String[] languages) throws PersistenceException;

	public void buildGlobalIndexes() throws PersistenceException;
	
}