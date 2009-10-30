package de.brightbyte.wikiword.analyzer;

import java.util.AbstractList;
import java.util.ArrayList;
import java.util.Collections;
import java.util.List;
import java.util.RandomAccess;

import de.brightbyte.data.filter.Filter;

public class PhraseOccuranceSequence extends AbstractList<PhraseOccurance> implements RandomAccess {

	protected List<PhraseOccurance> phrases;
	protected String text;
		
	public PhraseOccuranceSequence(String text, List<PhraseOccurance> phrases) {
		this.text = text;

		this.phrases =  phrases;
		Collections.sort(this.phrases); //essential!
	}

	@Override
	public PhraseOccurance get(int index) {
		return phrases.get(index);
	}

	@Override
	public int size() {
		return phrases.size();
	}

	public String getText() {
		return text;
	}
	
	public List<PhraseOccurance> getPhrasesAt(int offs) {
		int i = 0;
		while (i<size()) {
			PhraseOccurance p = get(i);
			if (p.getOffset() >= offs) {
				offs = p.getOffset();
				break;
			}
			
			i++;
		}
		
		if (i>=size()) return null;
		
		int j = i;
		while (j<size()) {
			PhraseOccurance p = get(j);
			if (p.getOffset() > offs) break;
			j++;
		}

		return subList(i, j); //NOTE: Phraseoccurrance.compareTo assures that longest phrases come first.
	}

	public List<PhraseOccurance> getDisjointPhraseSequence(Filter<String> filter) {
		List<PhraseOccurance> phrases = new ArrayList<PhraseOccurance>();
		
		int i = 0;
		
		outer: 
		while (i<size()) {
			List<PhraseOccurance> candidates = getPhrasesAt(i);
			if (candidates == null) break;
			
			for (PhraseOccurance p: candidates) {
				i = p.getEndOffset();
				if (filter.matches(p.getPhrase())) {
					phrases.add(p);
					continue outer;
				}
			}
		}
		
		return phrases;
	}
}
