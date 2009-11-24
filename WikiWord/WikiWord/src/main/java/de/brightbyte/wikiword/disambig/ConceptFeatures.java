package de.brightbyte.wikiword.disambig;

import de.brightbyte.data.LabeledVector;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.model.WikiWordConceptReference;

public class ConceptFeatures<C extends WikiWordConcept, K> {
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
	
	public int getConceptId() {
		return reference.getId();
	}
	
	
}
