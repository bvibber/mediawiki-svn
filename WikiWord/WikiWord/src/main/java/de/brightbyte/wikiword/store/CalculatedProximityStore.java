package de.brightbyte.wikiword.store;

import de.brightbyte.data.Functor2;
import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.MapLabeledVector;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.model.WikiWordConceptFeatures;
import de.brightbyte.wikiword.model.WikiWordConceptReference;

public class CalculatedProximityStore<T extends WikiWordConcept, R extends WikiWordConceptReference<T>>  
		implements ProximityStore<T, R> {

	protected FeatureTopologyStore<T, R> featureStore;
	protected Functor2<Double, LabeledVector<Integer>, LabeledVector<Integer>>  proximityMeasure;
	
	public CalculatedProximityStore(FeatureTopologyStore<T, R> featureStore, Functor2<Double, LabeledVector<Integer>, LabeledVector<Integer>> proximityMeasure) {
		this.featureStore = featureStore;
		this.proximityMeasure = proximityMeasure;
	}

	public DataSet<? extends R> getEnvironment(int concept, double minProximity)
			throws PersistenceException {
		LabeledVector<Integer> c = getFeatureVector(concept);
		DataSet<WikiWordConceptFeatures> n = featureStore.getNeighbourhoodFeatures(concept);
		
		return new EnvironmentDataSet(n);
	}

	public LabeledVector<Integer> getEnvironmentVector(int concept, double minProximity)
			throws PersistenceException {
		
		LabeledVector<Integer> env = new MapLabeledVector<Integer>();
		
		LabeledVector<Integer> c = getFeatureVector(concept);
		DataSet<WikiWordConceptFeatures> n = featureStore.getNeighbourhoodFeatures(concept);
		
		WikiWordConceptFeatures f;
		DataCursor<WikiWordConceptFeatures> cursor = n.cursor();
		while ((f = cursor.next()) != null) {
			double prox = getProximity(c, f.getFeatures());
			if (prox>=minProximity) env.set(f.getId(), prox);
		}
		
		return env;
	}

	public LabeledVector<Integer> getFeatureVector(int concept)
			throws PersistenceException {
		
		return featureStore.getFeatureVector(concept);
	}

	protected double getProximity(LabeledVector<Integer> v, LabeledVector<Integer> w) {
		return proximityMeasure.apply(v, w);
	}
	
	public double getProximity(int concept1, int concept2)
			throws PersistenceException {
		LabeledVector<Integer> v = getFeatureVector(concept1);
		LabeledVector<Integer> w = getFeatureVector(concept2);
		
		return getProximity(v, w);
	}

}
