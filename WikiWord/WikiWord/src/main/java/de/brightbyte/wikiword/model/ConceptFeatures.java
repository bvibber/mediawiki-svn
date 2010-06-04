package de.brightbyte.wikiword.model;

import gnu.trove.impl.Constants;
import de.brightbyte.data.IntLabeledVector;
import de.brightbyte.data.LabeledVector;

public class ConceptFeatures<C extends WikiWordConcept, K> {
	protected LabeledVector<K> features;
	protected WikiWordConcept concept;
	
	public ConceptFeatures(WikiWordConcept concept, LabeledVector<K> features) {
		if (features==null) throw new NullPointerException();
		if (concept==null) throw new NullPointerException();
		this.features = features;
		this.concept = concept;
	}
	
	public String toString() {
		return concept+ ":"+features;
	}

	public LabeledVector<K> getFeatureVector() {
		return features;
	}
	
	public WikiWordConcept getConcept() {
		return concept;
	}
	
	public int getId() {
		return concept.getId();
	}
	

	@Override
	public int hashCode() {
		return concept.hashCode();
	}

	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		final ConceptFeatures other = (ConceptFeatures) obj;
		
		return concept.equals(other.concept);
	}	
	
	public static LabeledVector<Integer>newIntFeaturVector() {
		return newIntFeaturVector( -1 );
	}
	
	public static LabeledVector<Integer>newIntFeaturVector(int capacity) {
		if ( capacity <= 0 ) capacity = Constants.DEFAULT_CAPACITY;
		return new IntLabeledVector(capacity);
	}

}
