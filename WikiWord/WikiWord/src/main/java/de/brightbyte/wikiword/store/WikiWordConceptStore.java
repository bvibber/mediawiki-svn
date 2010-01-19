package de.brightbyte.wikiword.store;

import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.model.WikiWordConceptReference;


public interface WikiWordConceptStore<T extends WikiWordConcept, R extends WikiWordConceptReference<T>> extends WikiWordConceptStoreBase {

	public DataSet<? extends R> listAllConcepts() throws PersistenceException;
	
	public ConceptType getConceptType(int type) throws PersistenceException;
	
	public StatisticsStore getStatisticsStore() throws PersistenceException;
	public ConceptInfoStore<T> getConceptInfoStore() throws PersistenceException;
	public ProximityStore<T, R> getProximityStore() throws PersistenceException;

	public T getConcept(int id) throws PersistenceException;
	
	public DataSet<? extends T> getConcepts(int[] ids) throws PersistenceException;
	
	public DataSet<? extends T> getConcepts(Iterable<R> refs) throws PersistenceException;
	
	/**
	 * Returns a WikiWordConceptReference for a random concept from the top-n 
	 * concepts with repect to in-degree.
	 * @param top the maximum rank of the concept to be returned. If top is 0, 
	 *        any concept from the full range may be returned. If it is negative,
	 *        it's interpreted as a percentage of the total number of concepts.
	 * @return a random concept from the range specified by the top argument.
	 */
	public R pickRandomConcept(int top) throws PersistenceException;

	/**
	 * Returns a WikiWordConcept for a random concept from the top-n 
	 * concepts with repect to in-degree.
	 * @param top the maximum rank of the concept to be returned. If top is 0, 
	 *        any concept from the full range may be returned. If it is negative,
	 *        it's interpreted as a percentage of the total number of concepts.
	 * @return a random concept from the range specified by the top argument.
	 */
	public T getRandomConcept(int top) throws PersistenceException;
	
	/**
	 * Returns the number of concepts in the store. Since the content of a concept
	 * store is not expected to change, this value may be cached. 
	 */
	public int getNumberOfConcepts() throws PersistenceException;
}
