package de.brightbyte.wikiword.model;



public class TermReference  {

	private String term;
	private double score;
	private WikiWordConcept concept;

	public TermReference(String term, WikiWordConcept concept, double score) {
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
	public static TermReference[] parseList(String s, WikiWordConcept.ListFormatSpec spec, WikiWordConcept.Factory factory) {
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
		final TermReference other = (TermReference) obj;
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

}
