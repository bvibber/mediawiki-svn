package de.brightbyte.wikiword.disambig;

import java.util.Collection;
import java.util.List;
import java.util.Map;

import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordConcept;

public interface Disambiguator<T extends TermReference, C extends WikiWordConcept> {

	public static class Result<T extends TermReference, C extends WikiWordConcept> implements Comparable {
		private Map<? extends T, ? extends C> meanings;
		private double score;
		private String description;
		
		public Result(Map<? extends T, ? extends C> meanings, double score, String description) {
			super();
			this.meanings = meanings;
			this.score = score;
			this.description = description;
		}
		
		public Map<? extends T, ? extends C> getMeanings() {
			return meanings;
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

	public <X extends T>Result<X, C> disambiguate(List<X> terms, Collection<C> context) throws PersistenceException;

}