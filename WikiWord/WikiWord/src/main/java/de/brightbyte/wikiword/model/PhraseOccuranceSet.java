package de.brightbyte.wikiword.model;

import java.util.AbstractList;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.HashSet;
import java.util.Iterator;
import java.util.List;
import java.util.ListIterator;
import java.util.RandomAccess;
import java.util.Set;

public class PhraseOccuranceSet extends AbstractList<PhraseOccurance> implements RandomAccess {

	protected class Node implements PhraseNode<PhraseOccurance> {
		protected PhraseOccurance phrase;
		
		public Node(PhraseOccurance phrase) {
			super();
			this.phrase = phrase;
		}

		public Collection<? extends PhraseNode<PhraseOccurance>> getSuccessors() {
			return getSuccessorsAt(phrase.getEndOffset());
		}

		public PhraseOccurance getTermReference() {
			return phrase;
		}
		
		public String toString() {
			return phrase.toString();
		}

		@Override
		public int hashCode() {
			final int PRIME = 31;
			int result = 1;
			result = PRIME * result + ((phrase == null) ? 0 : phrase.hashCode());
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
			final Node other = (Node) obj;
			if (phrase == null) {
				if (other.phrase != null)
					return false;
			} else if (!phrase.equals(other.phrase))
				return false;
			return true;
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
		
			public Collection<? extends PhraseNode<PhraseOccurance>> getSuccessors() {
				return getSuccessorsAt(ofs);
			}
			
			public String toString() {
				return "(root#"+ofs+")";
			}
		}; 
	}
	
	public Collection<? extends PhraseNode<PhraseOccurance>> getSuccessorsAt(int pos) {
		Set<PhraseNode<PhraseOccurance>> successors = new HashSet<PhraseNode<PhraseOccurance>>();
		
		int horizon = text.length();
		while (true) {
			    Collection<? extends PhraseNode<PhraseOccurance>> nodes = PhraseOccuranceSet.this.getPhraseNodesAt(pos);
				if (nodes != null && !nodes.isEmpty()) {
					successors.addAll(nodes);
					horizon = getHorizon(successors, horizon);
				}
				
				pos ++;
				if (pos>=horizon) break;
		}
		
		return successors;
	}
	
	private int getHorizon(Collection<? extends PhraseNode<PhraseOccurance>> successors, int horizon) {
		for (PhraseNode<PhraseOccurance> n: successors) {
			int end = n.getTermReference().getEndOffset();
			if (end < horizon) horizon = end;
		}
		
		return horizon;
	}

	
	public Collection<? extends PhraseNode<PhraseOccurance>> getPhraseNodesAt(int offs) {
		List<PhraseOccurance> phrases = getPhrasesAt(offs);
		return toNodeList(phrases);
	}
	
	public Collection<? extends PhraseNode<PhraseOccurance>> getPhraseNodesFrom(int offs) {
		List<PhraseOccurance> phrases = getPhrasesFrom(offs);
		return toNodeList(phrases);
	}
	
	protected List<Node> toNodeList(List<PhraseOccurance> phrases) {
		if (phrases==null) return null;
		List<Node> nodes = new ArrayList<Node>(phrases.size());
		
		for (PhraseOccurance p: phrases) {
			nodes.add(new Node(p));
		}
		
		return nodes;
	}
	
	public List<PhraseOccurance> getPhrasesAt(int at) {
		int i = 0;
		PhraseOccurance p = null;
		while (i<size()) {
			p = get(i);
			if (p.getOffset() >= at) {
				break;
			}
			
			i++;
		}
		
		if (p!=null && p.getOffset() > at) return null;
		if (i>=size()) return null;
		
		int j = i;
		while (j<size()) {
			p = get(j);
			if (p.getOffset() > at) break;
			j++;
		}

		return subList(i, j); //NOTE: Phraseoccurrance.compareTo assures that longest phrases come first.
	}

	public List<PhraseOccurance> getPhrasesFrom(int offs) {
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

	public void clear() {
		phrases.clear();
	}

	public boolean contains(Object o) {
		return phrases.contains(o);
	}

	public boolean containsAll(Collection<?> c) {
		return phrases.containsAll(c);
	}

	public boolean equals(Object o) {
		return phrases.equals(o);
	}

	public int indexOf(Object o) {
		return phrases.indexOf(o);
	}

	public boolean isEmpty() {
		return phrases.isEmpty();
	}

	public Iterator<PhraseOccurance> iterator() {
		return phrases.iterator();
	}

	public ListIterator<PhraseOccurance> listIterator() {
		return phrases.listIterator();
	}

	public ListIterator<PhraseOccurance> listIterator(int index) {
		return phrases.listIterator(index);
	}

	public PhraseOccurance remove(int index) {
		return phrases.remove(index);
	}

	public boolean remove(Object o) {
		return phrases.remove(o);
	}

	public boolean removeAll(Collection<?> c) {
		return phrases.removeAll(c);
	}

	public boolean retainAll(Collection<?> c) {
		return phrases.retainAll(c);
	}

	public Object[] toArray() {
		return phrases.toArray();
	}

	public <T> T[] toArray(T[] a) {
		return phrases.toArray(a);
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
