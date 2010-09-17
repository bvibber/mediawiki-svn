package de.brightbyte.wikiword.disambig;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collection;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import de.brightbyte.data.Pair;
import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.PhraseNode;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordConcept;

public interface Disambiguator<T extends TermReference, C extends WikiWordConcept> {
	
	public static class Interpretation<T extends TermReference, C extends WikiWordConcept> {
		private final Map<T, C> meanings; 		
		private final List<T> sequence;
		
		private static <T extends TermReference, C extends WikiWordConcept>Map<T, C> buildMeaningMap(List<Pair<T, C>> interpretation) {
			Map<T, C> sequence = new HashMap<T, C>(interpretation.size());
			for (Pair<T, C> p: interpretation) {
				if (p.getB()!=null) sequence.put(p.getA(), p.getB());
			}
			return sequence;
		}

		private static <T extends TermReference, C extends WikiWordConcept>List<T> buildTermSequence(List<Pair<T, C>> interpretation) {
			List<T> sequence = new ArrayList<T>(interpretation.size());
			for (Pair<T, C> p: interpretation) {
				sequence.add(p.getA());
			}
			return sequence;
		}

		public Interpretation(Pair<T, C>... interpretation) {
			this(Arrays.asList(interpretation));
		}
		
		public Interpretation(List<Pair<T, C>> interpretation) {
			this(buildMeaningMap(interpretation), buildTermSequence(interpretation));
		}

		public Interpretation(final Map<T, C> meanings, final List<T> sequence) {
			if (meanings==null) throw new NullPointerException();
			if (sequence==null) throw new NullPointerException();

			this.meanings = meanings;
			this.sequence = sequence;
		}
		
		public Map<T, C> getMeanings() {
			return meanings;
		}
		
		public List<T> getSequence() {
			return sequence;
		}
		
		public String toString() {
			if (sequence.isEmpty()) return "()";
			
			StringBuilder b = new StringBuilder();
			b.append("(");
			
			for (T t: sequence) {
				C c = meanings.get(t);
				b.append(t);
				b.append("=>");
				b.append(c);
				b.append("; ");
			}
			
			b.append(")");
			return b.toString();
		}

		@Override
		public int hashCode() {
			final int PRIME = 31;
			int result = 1;
			result = PRIME * result + ((meanings == null) ? 0 : meanings.hashCode());
			result = PRIME * result + ((sequence == null) ? 0 : sequence.hashCode());
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
			final Interpretation other = (Interpretation) obj;
			if (meanings == null) {
				if (other.meanings != null)
					return false;
			} else if (!meanings.equals(other.meanings))
				return false;
			if (sequence == null) {
				if (other.sequence != null)
					return false;
			} else if (!sequence.equals(other.sequence))
				return false;
			return true;
		}
		
		
	}
	
	public static class Disambiguation<T extends TermReference, C extends WikiWordConcept> implements Comparable {
		private Interpretation<T, C>  interpretation;
		private double score;
		private String description;
		
		public Disambiguation(Map<T, C> meanings, List<T> sequence, double score, String description) {
			this(new Interpretation<T, C>(meanings, sequence), score, description);
		}
		
		public Disambiguation(Interpretation<T, C> interpretation, double score, String description) {
			if (interpretation==null) throw new NullPointerException();
			this.interpretation = interpretation;
			this.score = score;
			this.description = description;
		}
		
		public Interpretation<T, C> getInterpretation() {
			return interpretation;
		}
		
		public Map<? extends T, ? extends C> getMeanings() {
			return getInterpretation().getMeanings();
		}
		
		public List<? extends T> getSequence() {
			return getInterpretation().getSequence();
		}

		public double getScore() {
			return score;
		}
		
		public String getDescription() {
			return description;
		}
		
		@Override
		public String toString() {
			return "("+score+"|"+description+") "+getMeanings();
		}
	
		public int compareTo(Object o) {
			Disambiguation r = (Disambiguation)o;
			double d = score - r.score;
			
			if (d==0) return 0;
			else if (d>0) return (int)d+1;
			else return (int)d-1;
		}
		
	}

	public void setTrace(Output trace);

	public <X extends T>Disambiguation<X, C> disambiguate(List<X> terms, Collection<? extends C> context) throws PersistenceException;
	public <X extends T>Disambiguation<X, C> disambiguate(PhraseNode<X> root, Collection<? extends C> context) throws PersistenceException;

	public boolean exploresAllSequences();

}