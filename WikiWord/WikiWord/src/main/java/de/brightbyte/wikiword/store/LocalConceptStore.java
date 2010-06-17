package de.brightbyte.wikiword.store;

import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.model.TermMeaning;
import de.brightbyte.wikiword.model.TermReference;


/**
 * Base interface for a store containing wiki information 
 */
public interface LocalConceptStore extends WikiWordConceptStore<LocalConcept>, WikiWordLocalStore {
	
	public int getNumberOfTerms() throws PersistenceException;

	public abstract DataSet<TermMeaning> getAllTerms() throws PersistenceException;
	//public abstract DataSet<ResourceReference> getAllResources() throws PersistenceException;
	
	//public abstract DataSet<LocalConcept> getLocalConcepts(DataSet<LocalConceptReference> refs) throws PersistenceException ;
	//public abstract LocalConcept getLocalConcept(LocalConceptReference ref) throws PersistenceException ;
	//public abstract LocalConcept getLocalConcept(int id) throws PersistenceException ;

	/**
	 * Returns a TermMeaning for a random term from the top-n 
	 * terms with repect to the frequency of occurance.
	 * @param top the maximum rank of the terms to be returned. If top is 0, 
	 *        any terms from the full range may be returned. If it is negative,
	 *        it's interpreted as a percentage of the total number of terms.
	 * @return a random terms from the range specified by the top argument.
	 */
	public TermReference pickRandomTerm(int top) 
		throws PersistenceException;

	public LocalConcept getConceptByName(String name, ConceptQuerySpec spec) throws PersistenceException;
	
}
