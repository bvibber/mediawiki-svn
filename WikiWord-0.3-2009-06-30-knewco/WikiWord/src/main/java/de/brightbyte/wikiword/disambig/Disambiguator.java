package de.brightbyte.wikiword.disambig;

import java.util.List;
import java.util.Map;

import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.WikiWordConcept;

public interface Disambiguator {

	public static class Result implements Comparable {
		private Map<String, ? extends WikiWordConcept> meanings;
		private double score;
		private double coherence;
		private double popularity;
		
		public Result(Map<String, ? extends WikiWordConcept> meanings, double score, double coherence, double popularity) {
			super();
			this.meanings = meanings;
			this.score = score;
			this.coherence = coherence;
			this.popularity = popularity;
		}
		
		public Map<String, ? extends WikiWordConcept> getMeanings() {
			return meanings;
		}
		
		public double getScore() {
			return score;
		}
		
		public double getCoherence() {
			return coherence;
		}
		
		public double getPopularity() {
			return popularity;
		}
		
		@Override
		public String toString() {
			return "("+score+"|"+coherence+"&"+popularity+") "+meanings;
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

	public Result disambiguate(List<String> terms) throws PersistenceException;

}