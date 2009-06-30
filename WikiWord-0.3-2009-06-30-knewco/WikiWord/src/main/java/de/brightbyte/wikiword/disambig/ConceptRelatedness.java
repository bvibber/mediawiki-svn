package de.brightbyte.wikiword.disambig;

import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.measure.Similarity;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.util.UncheckedPersistenceException;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.model.WikiWordConceptReference;

public class ConceptRelatedness<K> implements Similarity<WikiWordConcept> {

	public static class Relatedness {
		public final double relatedness;
		public final WikiWordConceptReference a;
		public final WikiWordConceptReference b;
		
		public Relatedness(final double relatedness, final WikiWordConceptReference a, final WikiWordConceptReference b) {
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
	protected FeatureFetcher<K> featureFetcher;

	public ConceptRelatedness(Similarity<LabeledVector<K>> similarityMeasure, FeatureFetcher<K> featureFetcher) {
		this.similarityMeasure = similarityMeasure;
		this.featureFetcher = featureFetcher;
	}

	public Relatedness relatedness(WikiWordConcept a, WikiWordConcept b) {
		double d = similarity(a, b);
		return new Relatedness(d, a.getReference(), b.getReference());		
	}
	
	public double similarity(WikiWordConcept a, WikiWordConcept b) {
		try {
			LabeledVector<K> fa = featureFetcher.getFeatures(a);
			LabeledVector<K> fb = featureFetcher.getFeatures(b);
			
			double d = similarityMeasure.similarity(fa, fb);
			return d;
		} catch (PersistenceException e) {
			throw new UncheckedPersistenceException(e);
		}
	}

}
