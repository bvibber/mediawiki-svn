package de.brightbyte.wikiword.store;

import java.util.Map;

import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.model.WikiWordConceptReference;

public interface StatisticsStore<T extends WikiWordConcept, R extends WikiWordConceptReference<T>> extends WikiWordStore {
	public abstract Map<String, ? extends Number> getStatistics()
		throws PersistenceException;

	public abstract void dumpStatistics(Output out)
		throws PersistenceException;
	
	/**
	 * Returns a WikiWordConceptReference for a random concept from the top-n 
	 * concepts with repect to in-degree.
	 * @param top the maximum rank of the concept to be returned. If top is 0, 
	 *        any concept from the full range may be returned. If it is negative,
	 *        it's interpreted as a percentage of the total number of concepts.
	 * @return a random concept from the range specified by the top argument.
	 */
	public R pickRandomConcept(int top) 
		throws PersistenceException;
	
}
