package de.brightbyte.wikiword.model;

import java.util.AbstractList;
import java.util.ArrayList;
import java.util.Collections;
import java.util.List;
import java.util.RandomAccess;

import de.brightbyte.wikiword.disambig.Term;

public class PhraseOccuranceSet extends AbstractList<PhraseOccurance> implements RandomAccess {

	protected class Node implements PhraseNode<PhraseOccurance> {
		protected PhraseOccurance phrase;
		
		public Node(PhraseOccurance phrase) {
			super();
			this.phrase = phrase;
		}

		public List<? extends PhraseNode<PhraseOccurance>> getSuccessors() {
			return PhraseOccuranceSet.this.getPhraseNodesAt(phrase.getEndOffset());
		}

		public PhraseOccurance getTermReference() {
			return phrase;
		}
		
		public String toString() {
			return phrase.toString();
		}
	}
	
	protected List<PhraseOccurance> phrases;
	protected String text;
		
	public PhraseOccuranceSet(String text, List<PhraseOccurance> phrases) {
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
	
	public PhraseNode<PhraseOccurance> getRootNode() {
		return getRootNodeAt(0);
	}
	
	public PhraseNode<PhraseOccurance> getRootNodeAt(final int ofs) {
		return new PhraseNode<PhraseOccurance>(){
			public PhraseOccurance getTermReference() {
				return new PhraseOccurance("", 0, ofs, 0);
			}
		
			public List<? extends PhraseNode<PhraseOccurance>> getSuccessors() {
				return getPhraseNodesAt(ofs);
			}
			
			public String toString() {
				return "(root#"+ofs+")";
			}
		}; 
	}
	
	public List<? extends PhraseNode<PhraseOccurance>> getPhraseNodesAt(int offs) {
		List<PhraseOccurance> phrases = getPhrasesAt(offs);
		List<Node> nodes = new ArrayList<Node>(phrases.size());
		
		for (PhraseOccurance p: phrases) {
			nodes.add(new Node(p));
		}
		
		return nodes;
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

	/*
	public List<PhraseOccurance> getDisjointPhraseSequence(Filter<String> filter) {
		List<PhraseOccurance> phrases = new ArrayList<PhraseOccurance>();
		
		int i = 0;
		
		outer: 
		while (i<size()) {
			List<PhraseOccurance> candidates = getPhrasesAt(i);
			if (candidates == null) break;
			
			for (PhraseOccurance p: candidates) {
				i = p.getEndOffset();
				if (filter==null || filter.matches(p.getPhrase())) {
					phrases.add(p);
					continue outer;
				}
			}
		}
		
		return phrases;
	}
	*/
}
