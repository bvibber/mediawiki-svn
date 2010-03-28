package de.brightbyte.wikiword.store;

import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.WikiWordConcept;

public interface ProximityStore<T extends WikiWordConcept, K> extends FeatureStore<T, K> {

	public double getProximity(int concept1, int concept2) throws PersistenceException;

	public DataSet<? extends T> getEnvironment(int concept, double minProximity) throws PersistenceException;

	public LabeledVector<Integer> getEnvironmentVector(int concept, double minProximity) throws PersistenceException;
	
}
