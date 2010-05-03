package de.brightbyte.wikiword.disambig;

import java.util.Collection;
import java.util.List;
import java.util.Map;

import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.PhraseNode;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordConcept;

public interface Disambiguator<T extends TermReference, C extends WikiWordConcept> {

	public static class Interpretation<T extends TermReference, C extends WikiWordConcept> {
		private final Map<T, C> meanings; 		
		private final List<T> sequence;
		
		public Interpretation(final Map<T, C> meanings, final List<T> sequence) {
			this.meanings = meanings;
			this.sequence = sequence;
		}
		
		public Map<T, C> getMeanings() {
			return meanings;
		}
		public List<T> getSequence() {
			return sequence;
		}
	}
	
	public static class Result<T extends TermReference, C extends WikiWordConcept> implements Comparable {
		private Map<? extends T, ? extends C> meanings;
		private List<? extends T> sequence;
		private double score;
		private String description;
		
		public Result(Map<? extends T, ? extends C> meanings, List<? extends T> sequence, double score, String description) {
			super();
			this.meanings = meanings;
			this.sequence = sequence;
			this.score = score;
			this.description = description;
		}
		
		public Map<? extends T, ? extends C> getMeanings() {
			return meanings;
		}
		
		public List<? extends T> getSequence() {
			return sequence;
		}

		public double getScore() {
			return score;
		}
		
		public String getDescription() {
			return description;
		}
		
		@Override
		public String toString() {
			return "("+score+"|"+description+") "+meanings;
		}
	
		public int compareTo(Object o) {
			Result r = (Result)o;
			double d = score - r.score;
			
			if (d==0) return 0;
			else if (d>0) return (int)d+1;
			else return (int)d-1;
		}
		
	}

	public void setTrace(Output trace);

	public <X extends T>Result<X, C> disambiguate(List<X> terms, Collection<? extends C> context) throws PersistenceException;
	public <X extends T>Result<X, C> disambiguate(PhraseNode<X> root, Collection<? extends C> context) throws PersistenceException;

}