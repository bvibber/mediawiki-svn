package de.brightbyte.wikiword.model;

import de.brightbyte.data.LabeledVector;

public class WikiWordConceptFeatures implements WikiWordRanking {
	protected WikiWordConceptReference reference;
	protected LabeledVector<Integer> features;
	
	public WikiWordConceptFeatures(WikiWordConceptReference reference, LabeledVector<Integer> features) {
		if (features==null) throw new NullPointerException();
		
		this.features = features;
		this.reference = reference;
	}

	public LabeledVector<Integer> getFeatures() {
		return features;
	}
	
	public int getId() {
		return reference.getId();
	}

	public String getName() {
		return reference.getName();
	}
	
	public WikiWordConceptReference getReference() {
		return reference;
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
		final WikiWordConceptFeatures other = (WikiWordConceptFeatures) obj;
		
		return reference.equals(other.reference);
	}	

	
	@Override
	public String toString() {
		return reference.toString();
	}
	
}