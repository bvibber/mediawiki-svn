package de.brightbyte.wikiword.model;

import java.util.Set;

public class ConceptResources<C extends WikiWordConcept> {
	protected Set<WikiWordResource> resources;
	protected C concept;
	
	public ConceptResources(C concept, Set<WikiWordResource> resources) {
		if (resources==null) throw new NullPointerException();
		if (concept==null) throw new NullPointerException();
		this.resources = resources;
		this.concept = concept;
	}
	
	public String toString() {
		return concept+ ":"+resources;
	}

	public Set<WikiWordResource> getResources() {
		return resources;
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
		final ConceptResources other = (ConceptResources) obj;
		
		return concept.equals(other.concept);
	}	
	
}
