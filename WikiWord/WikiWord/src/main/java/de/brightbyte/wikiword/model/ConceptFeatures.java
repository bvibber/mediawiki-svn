package de.brightbyte.wikiword.model;

import de.brightbyte.data.LabeledVector;

public class ConceptFeatures<C extends WikiWordConcept, K> implements WikiWordRanking {
	protected LabeledVector<K> features;
	protected WikiWordConceptReference<C> reference;
	
	public ConceptFeatures(WikiWordConceptReference<C> reference, LabeledVector<K> features) {
		this.features = features;
		this.reference = reference;
	}
	
	public String toString() {
		return reference+ ":"+features;
	}

	public LabeledVector<K> getFeatureVector() {
		return features;
	}
	
	public WikiWordConceptReference<C> getConceptReference() {
		return reference;
	}
	
	public int getId() {
		return reference.getId();
	}
	
	public String getName() {
		return reference.getName();
	}
	
	public int getCardinality() {
		return reference==null ? 1 : reference.getCardinality();
	}

	public double getRelevance() {
		return reference==null ? 1 : reference.getRelevance();
	}
	
	public boolean hasRanking() {
		return reference != null && ( reference.getCardinality()>0 || reference.getRelevance()>0 );
	}

	@Override
	public int hashCode() {
		return reference.hashCode();
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
		
		return reference.equals(other.reference);
	}	

}
