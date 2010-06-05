package de.brightbyte.wikiword.model;

import gnu.trove.impl.Constants;

import java.util.Collection;

import de.brightbyte.data.IntLabeledVector;
import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.MultiMap;

public class ConceptProperties<C extends WikiWordConcept> {
	protected MultiMap<String, String, ? extends Collection<String>> properties;
	protected C concept;
	
	public ConceptProperties(C concept, MultiMap<String, String, ? extends Collection<String>> properties) {
		if (properties==null) throw new NullPointerException();
		if (concept==null) throw new NullPointerException();
		this.properties = properties;
		this.concept = concept;
	}
	
	public String toString() {
		return concept+ ":"+properties;
	}

	public MultiMap<String, String, ? extends Collection<String>> getProperties() {
		return properties;
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
		final ConceptProperties other = (ConceptProperties) obj;
		
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
