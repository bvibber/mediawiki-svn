package de.brightbyte.wikiword.store;

import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;
import java.util.Map;

import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.MapLabeledVector;
import de.brightbyte.data.cursor.CursorIterator;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.data.measure.ScalarVectorSimilarity;
import de.brightbyte.data.measure.Similarity;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.util.UncheckedPersistenceException;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.model.ConceptFeatures;
import de.brightbyte.wikiword.model.WikiWordConcept;

public class CalculatedProximityStore<T extends WikiWordConcept>  
		implements ProximityStore<T, Integer> {

	protected class EnvironmentDataCursor implements DataCursor<T> {
		private DataCursor<ConceptFeatures<T, Integer>> neighbours;
		private LabeledVector<Integer> centerFeatures;
		private double minProximity;

		public EnvironmentDataCursor(DataCursor<ConceptFeatures<T, Integer>> neighbours, LabeledVector<Integer> centerFeatures, double minProximity) {
			this.neighbours = neighbours; 
			this.centerFeatures = centerFeatures; 
			this.minProximity = minProximity; 
		}

		public void close() {
			this.neighbours.close();
		}

		public T next() throws PersistenceException {
			ConceptFeatures<T, Integer> f;
			while((f = neighbours.next()) != null) {
				double prox = getProximity(centerFeatures, f.getFeatureVector());
				if (prox<minProximity) continue;
				
				return newConcept(f.getId(), f.getConcept().getName(), f.getConcept().getType(), 1, prox);
			} ;
			
			return null;
		}
	}
	
	protected class EnvironmentDataSet implements DataSet<T> {
		private DataSet<ConceptFeatures<T, Integer>> neighbours;
		private LabeledVector<Integer> centerFeatures;
		private double minProximity;
	
		public EnvironmentDataSet(DataSet<ConceptFeatures<T, Integer>> neighbours, LabeledVector<Integer> centerFeatures, double minProximity) {
			this.neighbours = neighbours; 
			this.centerFeatures = centerFeatures; 
			this.minProximity = minProximity; 
		}

		public DataCursor<T> cursor() throws PersistenceException {
			return new EnvironmentDataCursor(neighbours.cursor(), centerFeatures, minProximity);
		}

		public Iterator<T> iterator() throws UncheckedPersistenceException {
			try {
				return new CursorIterator<T>(cursor());
			} catch (PersistenceException e) {
				throw new UncheckedPersistenceException(e);
			}
		}

		public List<T> load() throws PersistenceException {
				ArrayList<T> r = new ArrayList<T>();
				for (T x: this) r.add(x);
				return r;
		}

	}

	protected WikiWordConcept.Factory<T> conceptFactory;
	protected FeatureTopologyStore<T, Integer> featureStore;
	protected Similarity<LabeledVector<Integer>>  proximityMeasure;
	
	public CalculatedProximityStore(FeatureTopologyStore<T, Integer> featureStore, 
						WikiWordConcept.Factory<T> referenceFactory) {
		this.featureStore = featureStore;
		this.proximityMeasure = ScalarVectorSimilarity.<Integer>getInstance();
		this.conceptFactory = referenceFactory;
	}

	public DataSet<? extends T> getEnvironment(int concept, double minProximity)
			throws PersistenceException {
		ConceptFeatures<T, Integer> c = getConceptFeatures(concept);
		DataSet<ConceptFeatures<T, Integer>> n = featureStore.getNeighbourhoodFeatures(concept);
		
		return new EnvironmentDataSet(n, c.getFeatureVector(), minProximity);
	}

	protected T newConcept(int id, String name, ConceptType type, int cardinality, double relevance) throws PersistenceException {
		return conceptFactory.newInstance(id, name, type, cardinality, relevance);
	}
	
	public LabeledVector<Integer> getEnvironmentVector(int concept, double minProximity)
			throws PersistenceException {
		
		LabeledVector<Integer> env = ConceptFeatures.newIntFeaturVector();
		
		ConceptFeatures<T, Integer> c = getConceptFeatures(concept);
		DataSet<ConceptFeatures<T, Integer>> n = featureStore.getNeighbourhoodFeatures(concept);
		
		ConceptFeatures<T, Integer> f;
		DataCursor<ConceptFeatures<T, Integer>> cursor = n.cursor();
		while ((f = cursor.next()) != null) {
			double prox = getProximity(c.getFeatureVector(), f.getFeatureVector());
			if (prox>=minProximity) env.set(f.getId(), prox);
		}
		
		return env;
	}

	public ConceptFeatures<T, Integer> getConceptFeatures(int concept)
			throws PersistenceException {
		
		return featureStore.getConceptFeatures(concept);
	}

	protected double getProximity(LabeledVector<Integer> v, LabeledVector<Integer> w) {
		return proximityMeasure.similarity(v, w);
	}
	
	public double getProximity(int concept1, int concept2)
			throws PersistenceException {
		ConceptFeatures<T, Integer> v = getConceptFeatures(concept1);
		ConceptFeatures<T, Integer> w = getConceptFeatures(concept2);
		
		return getProximity(v.getFeatureVector(), w.getFeatureVector());
	}

	public Map<Integer, ConceptFeatures<T, Integer>> getConceptsFeatures(int[] concepts) throws PersistenceException {
		return featureStore.getConceptsFeatures(concepts);
	}

}
