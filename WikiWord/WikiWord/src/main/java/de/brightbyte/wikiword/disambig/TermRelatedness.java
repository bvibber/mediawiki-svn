package de.brightbyte.wikiword.disambig;

import java.util.Collection;

import de.brightbyte.data.measure.Similarity;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.util.UncheckedPersistenceException;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordConcept;

public class TermRelatedness<C extends WikiWordConcept> implements Similarity<String> {

	public static class Relatedness {
		public final double relatedness;
		public final WikiWordConcept a;
		public final WikiWordConcept b;
		
		public Relatedness(final double relatedness, final WikiWordConcept a, final WikiWordConcept b) {
			super();
			this.relatedness = relatedness;
			this.a = a;
			this.b = b;
		}
		
		@Override
		public String toString() {
			return relatedness + " ("+a+" / "+b+")";
		}
	}
		
	protected Similarity<WikiWordConcept> relatedness;
	protected Disambiguator<TermReference, C> disambig;
	private Collection<C> context;

	public TermRelatedness(Disambiguator<TermReference,C> disambig) {
		this(disambig, null, null);
	}
	
	public TermRelatedness(Disambiguator<TermReference, C> disambig, Similarity<WikiWordConcept> relatedness, Collection<C> context) {
		this.relatedness = relatedness;
		this.disambig = disambig;
		this.context = context;
	}

	public double similarity(String a, String b) {
		Relatedness r = relatedness(a, b);
		if (r==null) return 0;
		
		return r.relatedness;
	}
	
	public Relatedness relatedness(String a, String b) {
		try {
			Disambiguator.Disambiguation<Term, ? extends WikiWordConcept> r = disambig.<Term>disambiguate(Term.asTerms(a, b), context);
			if (r==null || r.getMeanings().size()!=2) return null;
			
			double d;
			
			WikiWordConcept ca = r.getMeanings().get(a);
			WikiWordConcept cb = r.getMeanings().get(b);

			if (relatedness!=null) {
				d = relatedness.similarity(ca, cb);
			}
			else {
				d = r.getScore();
				if (d<0) throw new RuntimeException("disambiguator did not provide a score, and no concept similarity measure was defined!");
			}
			
			return new Relatedness(d, ca, cb);
		} catch (PersistenceException e) {
			throw new UncheckedPersistenceException(e);
		}
	}

}
