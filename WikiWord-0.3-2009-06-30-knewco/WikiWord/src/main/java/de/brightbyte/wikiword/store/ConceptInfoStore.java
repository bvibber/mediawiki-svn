package de.brightbyte.wikiword.store;

import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.model.WikiWordConceptReference;


/**
 * Base interface for a store containing wiki information 
 */
public interface ConceptInfoStore<C extends WikiWordConcept> extends WikiWordStore {

	public C getConcept(int id) throws PersistenceException;
	
	public DataSet<C> getConcepts(int[] ids) throws PersistenceException;
	
	public DataSet<C> getConcepts(Iterable<? extends WikiWordConceptReference<? extends WikiWordConcept>> refs) throws PersistenceException;

	public DataSet<C> getAllConcepts() throws PersistenceException;

}
