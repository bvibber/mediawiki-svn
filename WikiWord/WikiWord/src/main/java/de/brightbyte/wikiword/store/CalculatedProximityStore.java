package de.brightbyte.wikiword.store;

import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

import de.brightbyte.data.Functor2;
import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.MapLabeledVector;
import de.brightbyte.data.cursor.CursorIterator;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.util.UncheckedPersistenceException;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.model.WikiWordConceptFeatures;
import de.brightbyte.wikiword.model.WikiWordConceptReference;
import de.brightbyte.wikiword.model.WikiWordReference;

public class CalculatedProximityStore<T extends WikiWordConcept, R extends WikiWordConceptReference<T>>  
		implements ProximityStore<T, R> {

	protected class EnvironmentDataCursor implements DataCursor<R> {
		private DataCursor<WikiWordConceptFeatures> neighbours;
		private LabeledVector<Integer> centerFeatures;
		private double minProximity;

		public EnvironmentDataCursor(DataCursor<WikiWordConceptFeatures> neighbours, LabeledVector<Integer> centerFeatures, double minProximity) {
			this.neighbours = neighbours; 
			this.centerFeatures = centerFeatures; 
			this.minProximity = minProximity; 
		}

		public void close() {
			this.neighbours.close();
		}

		public R next() throws PersistenceException {
			WikiWordConceptFeatures f;
			while((f = neighbours.next()) != null) {
				double prox = getProximity(centerFeatures, f.getFeatures());
				if (prox<minProximity) continue;
				
				return newReference(f.getId(), f.getName(), 1, prox);
			} ;
			
			return null;
		}
	}
	
	protected class EnvironmentDataSet implements DataSet<R> {
		private DataSet<WikiWordConceptFeatures> neighbours;
		private LabeledVector<Integer> centerFeatures;
		private double minProximity;
	
		public EnvironmentDataSet(DataSet<WikiWordConceptFeatures> neighbours, LabeledVector<Integer> centerFeatures, double minProximity) {
			this.neighbours = neighbours; 
			this.centerFeatures = centerFeatures; 
			this.minProximity = minProximity; 
		}

		public DataCursor<R> cursor() throws PersistenceException {
			return new EnvironmentDataCursor(neighbours.cursor(), centerFeatures, minProximity);
		}

		public Iterator<R> iterator() throws UncheckedPersistenceException {
			try {
				return new CursorIterator<R>(cursor());
			} catch (PersistenceException e) {
				throw new UncheckedPersistenceException(e);
			}
		}

		public List<R> load() throws PersistenceException {
				ArrayList<R> r = new ArrayList<R>();
				for (R x: this) r.add(x);
				return r;
		}

	}

	protected WikiWordReference.Factory<R> referenceFactory;
	protected FeatureTopologyStore<T, R> featureStore;
	protected Functor2<Double, LabeledVector<Integer>, LabeledVector<Integer>>  proximityMeasure;
	
	public CalculatedProximityStore(FeatureTopologyStore<T, R> featureStore, 
						Functor2<Double, LabeledVector<Integer>, LabeledVector<Integer>> proximityMeasure, 
						WikiWordReference.Factory<R> referenceFactory) {
		this.featureStore = featureStore;
		this.proximityMeasure = proximityMeasure;
		this.referenceFactory = referenceFactory;
	}

	public DataSet<? extends R> getEnvironment(int concept, double minProximity)
			throws PersistenceException {
		LabeledVector<Integer> c = getFeatureVector(concept);
		DataSet<WikiWordConceptFeatures> n = featureStore.getNeighbourhoodFeatures(concept);
		
		return new EnvironmentDataSet(n, c, minProximity);
	}

	protected R newReference(int id, String name, int cardinality, double relevance) {
		return referenceFactory.newInstance(id, name, cardinality, relevance);
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
