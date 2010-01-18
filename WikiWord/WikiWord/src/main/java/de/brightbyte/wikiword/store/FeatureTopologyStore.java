package de.brightbyte.wikiword.store;

import java.util.List;

import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.model.WikiWordConceptFeatures;
import de.brightbyte.wikiword.model.WikiWordConceptReference;

public interface FeatureTopologyStore<T extends WikiWordConcept, R extends WikiWordConceptReference<T>> {

	public LabeledVector<Integer> getFeatureVector(int concept) throws PersistenceException;

	public DataSet<? extends R> getNeighbours(int concept) throws PersistenceException;

	public List<Integer> getNeighbourList(int concept) throws PersistenceException;

	public DataSet<WikiWordConceptFeatures> getNeighbourhoodFeatures(int concept) throws PersistenceException;	
}
