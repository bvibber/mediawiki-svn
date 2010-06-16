package de.brightbyte.wikiword.analyzer.mangler;

import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.HashSet;
import java.util.List;

import de.brightbyte.wikiword.disambig.Term;

public class SpellingAlternator {
	protected List<Mangler> manglers = new ArrayList<Mangler>();
	protected double weightFactor;
	
	public SpellingAlternator(List<Mangler> manglers, double weightFactor) {
		if (manglers==null) throw new NullPointerException();
		if (weightFactor<=0 || weightFactor>1) throw new IllegalArgumentException("weightFactor must be > 0 and <= 1");
		
		this.manglers = manglers;
		this.weightFactor = weightFactor;
	}

	public Collection<Term> getAlternatives(String term, Collection<Term> into) {
		if (manglers.isEmpty() && into==null) {
			return Collections.singleton(new Term(term));
		}
		
		if (into==null) into = new HashSet<Term>();
		into.add(new Term(term));
		
		if (!manglers.isEmpty()) collectAlternatives(term, 0, 1, into);
		return into;
	}
	
	private void collectAlternatives(String term, int index, double weight, Collection<Term>  alternatives) {
		if (index>=manglers.size()) return;
		if (!alternatives.add(new Term(term, weight))) return;

		Mangler mangler= manglers.get(index);
		CharSequence t = mangler.mangle(term);
		
		if (t!=null) {
			collectAlternatives(t.toString(), index+1, weight*weightFactor, alternatives); //branch recursion. NOTE: use index=0 to cover all combinations
		}
		
		collectAlternatives(term, index+1, weight, alternatives); //primitive recursion
	}
	
	public List<Collection<Term>> getAlternatives(List<String> terms, List<Collection<Term>> into) {
		if (into == null) into = new ArrayList<Collection<Term>>(terms.size());
		
		for (String t: terms) {
			Collection<Term> alt = getAlternatives(t, null);
			into.add(alt);
		}
		
		return into;
	}
}
