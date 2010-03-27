package de.brightbyte.wikiword.store;

import java.util.List;

import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.ConceptFeatures;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.model.WikiWordConceptReference;

public interface FeatureTopologyStore<T extends WikiWordConcept, R extends WikiWordConceptReference<T>, K> extends FeatureStore<T, K> {

	public DataSet<? extends R> getNeighbours(int concept) throws PersistenceException;

	public List<Integer> getNeighbourList(int concept) throws PersistenceException;

	public DataSet<ConceptFeatures<T, K>> getNeighbourhoodFeatures(int concept) throws PersistenceException;	
}
