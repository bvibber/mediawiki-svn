package de.brightbyte.wikiword.model;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.DatasetIdentifier;

public abstract class WikiWordConcept implements WikiWordRanking {
	/*
	public static interface Factory<T extends WikiWordConcept> {
		public T newInstance(Map<String, Object> data);
	}
    */
	
	protected DatasetIdentifier dataset;
	
	protected ConceptType type;
	protected WikiWordConceptReference reference;
	
	public WikiWordConcept(WikiWordConceptReference reference, DatasetIdentifier dataset, ConceptType type) {
		if (type==null) throw new NullPointerException();
		
		this.dataset = dataset;
		this.type = type;
		this.reference = reference;
	}

	public DatasetIdentifier getDatasetIdentifier() {
		return dataset;
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

	public ConceptType getType() {
		return type;
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
		final WikiWordConcept other = (WikiWordConcept) obj;
		
		return reference.equals(other.reference);
	}	

	
	@Override
	public String toString() {
		return reference.toString();
	}
	
	public abstract WikiWordConceptReference[] getSimilar();

	public abstract WikiWordConceptReference[] getRelated();	

	public abstract WikiWordConceptReference[] getBroader();

	public abstract WikiWordConceptReference[] getNarrower();	

	public abstract WikiWordConceptReference[] getInLinks();

	public abstract WikiWordConceptReference[] getOutLinks();
	
	public abstract TranslationReference[] getLanglinks();
	
}