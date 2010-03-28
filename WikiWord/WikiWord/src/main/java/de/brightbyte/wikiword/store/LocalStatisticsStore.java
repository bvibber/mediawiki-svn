package de.brightbyte.wikiword.store;

import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordConcept;

public interface LocalStatisticsStore<T extends WikiWordConcept> extends StatisticsStore<T> {
	
	public int getNumberOfTerms() throws PersistenceException;
	
	public DataSet<TermReference> getAllTerms()
		throws PersistenceException;
	
	/**
	 * Returns a TermReference for a random term from the top-n 
	 * terms with repect to the frequency of occurance.
	 * @param top the maximum rank of the terms to be returned. If top is 0, 
	 *        any terms from the full range may be returned. If it is negative,
	 *        it's interpreted as a percentage of the total number of terms.
	 * @return a random terms from the range specified by the top argument.
	 */
	public TermReference pickRandomTerm(int top) 
		throws PersistenceException;
}
