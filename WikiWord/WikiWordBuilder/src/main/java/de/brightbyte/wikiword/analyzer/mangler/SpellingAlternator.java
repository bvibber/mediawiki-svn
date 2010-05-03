package de.brightbyte.wikiword.analyzer.mangler;

import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.HashSet;
import java.util.List;
import java.util.Set;

public class SpellingAlternator {
	protected List<Mangler> manglers = new ArrayList<Mangler>();
	
	public Collection<String> getAlternatives(String term) {
		if (manglers.isEmpty()) return Collections.singleton(term);
		
		Set<String>  alternatives = new HashSet<String>();
		alternatives.add(term);
	
		collectAlternatives(term, 0, alternatives);
		return alternatives;
	}
	
	private void collectAlternatives(String term, int index, Set<String>  alternatives) {
		if (index>=manglers.size()) return;
		
		Mangler mangler= manglers.get(index);
		CharSequence t = mangler.mangle(term);
		
		if (t!=null && alternatives.add(t.toString())) {
			collectAlternatives(t.toString(), index+1, alternatives); //branch recursion. NOTE: use index=0 to cover all combinations
		}
		
		collectAlternatives(term, index+1, alternatives); //primitive recursion
	}
	
	public List<Collection<String>> getAlternatives(List<String> terms) {
		List<Collection<String>> alternatives = new ArrayList<Collection<String>>(terms.size());
		
		for (String t: terms) {
			Collection<String> alt = getAlternatives(t);
			alternatives.add(alt);
		}
		
		return alternatives;
	}
}
