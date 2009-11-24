package de.brightbyte.wikiword.disambig;

import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.measure.Similarity;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.util.UncheckedPersistenceException;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.model.WikiWordConceptReference;

public class ConceptRelatedness<C extends WikiWordConcept, K> implements Similarity<C> {

	public static class Relatedness<C extends WikiWordConcept> {
		public final double relatedness;
		public final WikiWordConceptReference<C> a;
		public final WikiWordConceptReference<C> b;
		
		public Relatedness(final double relatedness, final WikiWordConceptReference<C> a, final WikiWordConceptReference<C> b) {
			super();
			this.relatedness = relatedness;
			this.a = a;
			this.b = b;
		}
		
		@Override
		public String toString() {
			return relatedness + " ("+a+" / "+b+")";
		}
	}

	protected Similarity<LabeledVector<K>> similarityMeasure;
	protected FeatureFetcher<C, K> featureFetcher;

	public ConceptRelatedness(Similarity<LabeledVector<K>> similarityMeasure, FeatureFetcher<C, K> featureFetcher) {
		this.similarityMeasure = similarityMeasure;
		this.featureFetcher = featureFetcher;
	}

	public Relatedness relatedness(C a, C b) {
		double d = similarity(a, b);
		return new Relatedness<C>(d, a.getReference(), b.getReference());		
	}
	
	public double similarity(C a, C b) {
		try {
			ConceptFeatures<C, K>  fa = featureFetcher.getFeatures(a);
			ConceptFeatures<C, K>  fb = featureFetcher.getFeatures(b);
			
			double d = similarityMeasure.similarity(fa.getFeatureVector(), fb.getFeatureVector());
			return d;
		} catch (PersistenceException e) {
			throw new UncheckedPersistenceException(e);
		}
	}

}
