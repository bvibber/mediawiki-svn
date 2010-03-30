package de.brightbyte.wikiword.model;

import java.io.Serializable;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.WikiWordConcept.Factory;
import de.brightbyte.wikiword.schema.ConceptInfoStoreSchema.ConceptListEntrySpec;



public class TermMeaning implements TermReference, Serializable  {

	private String term;
	private double score;
	private WikiWordConcept concept;

	public TermMeaning(String term, WikiWordConcept concept, double score) {
		this.term = term;
		this.concept = concept;
		this.score = score;
	}

	public WikiWordConcept getConcept() {
		return concept;
	}

	public double getScore() {
		return score;
	}

	public String getTerm() {
		return term;
	}
	
	public String toString() {
		return "\""+term+"\" -> "+getConcept();
	}

	/*
	public static TermMeaning[] parseList(String s, WikiWordConcept.ListFormatSpec spec, WikiWordConcept.Factory factory) {
		return WikiWordConcept.parseList(s, factory, spec);
	}
	*/
	
	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + ((concept == null) ? 0 : concept.hashCode());
		result = PRIME * result + ((term == null) ? 0 : term.hashCode());
		return result;
	}

	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		final TermMeaning other = (TermMeaning) obj;
		if (concept == null) {
			if (other.concept != null)
				return false;
		} else if (!concept.equals(other.concept))
			return false;
		if (term == null) {
			if (other.term != null)
				return false;
		} else if (!term.equals(other.term))
			return false;
		return true;
	}

	public static TermReference[] parseList(String s, Factory<LocalConcept> factory, ConceptListEntrySpec spec) throws PersistenceException {
		LocalConcept[] concepts = WikiWordConcept.parseList(s, factory, spec); //XXX: this is a terrible, terrible hack.
		TermReference[] terms = new TermReference[concepts.length];
		
		for (int i=0; i<terms.length; i++) {
			WikiWordConcept dummy = concepts[i];
			
			String term = dummy.getName(); //UGHA!
			double score = dummy.getCardinality();
			
			WikiWordConcept target = factory.newInstance(dummy.getId(), null, dummy.getType());
			terms[i] = new TermMeaning(term, target, score);
		}
		
		return terms;
	}

}
