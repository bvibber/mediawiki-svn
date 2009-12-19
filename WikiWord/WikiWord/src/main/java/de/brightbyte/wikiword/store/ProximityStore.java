package de.brightbyte.wikiword.store;

import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.model.WikiWordConceptReference;

public interface ProximityStore<T extends WikiWordConcept, R extends WikiWordConceptReference<T>> {

	public LabeledVector<Integer> getFeatureVector(int concept) throws PersistenceException;

	public double getProximity(int concept1, int concept2) throws PersistenceException;

	public DataSet<? extends R> getEnvironment(int concept, double minProximity) throws PersistenceException;

	public LabeledVector<Integer> getEnvironmentVector(int concept, double minProximity) throws PersistenceException;
	
}
