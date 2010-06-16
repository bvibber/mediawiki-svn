package de.brightbyte.wikiword.model;

import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.HashSet;
import java.util.Iterator;
import java.util.List;
import java.util.ListIterator;
import java.util.Set;
import java.util.regex.Matcher;

public class PhraseOccuranceSet implements List<PhraseOccurance> {

	public static class AggregatePhraseBuilder extends PhraseNode.Walker<PhraseOccurance> {
		protected Collection<PhraseOccurance> aggregated;
		protected double minWeight;
		protected double maxWeight;
		protected Matcher phraseBreak;
		
		public AggregatePhraseBuilder( double minWeight, double maxWeight, Matcher phraseBreak ) {
			aggregated = new HashSet<PhraseOccurance>();
			this.minWeight  = minWeight; 
			this.maxWeight  = maxWeight ; 
			this.phraseBreak = phraseBreak;
		}
		
		public boolean onNode(PhraseNode<? extends PhraseOccurance> node, List<? extends PhraseOccurance> sequence, double weight, boolean terminal) {
			if (weight>=minWeight && !sequence.isEmpty()) { 
				PhraseOccurance p = aggregatePhrase( sequence, minWeight, maxWeight );
				if (p!=null) aggregated.add(p);
				
				PhraseOccurance last = sequence.get( sequence.size()-1);
				
				if (phraseBreak!=null) {
					phraseBreak.reset(last.getTerm());
					if (phraseBreak.matches()) 
						return false; //phrase terminates here, don't dig deeper.
				}
				
				if (p==null) return weight <= maxWeight; //XXX: something is wrong here
				else return p.getWeight() <= maxWeight; //XXX: can we do that?
			} else {
				return weight <= maxWeight; //XXX: not sure...
			}
		}

		public Collection<PhraseOccurance> getAggregatedPhrases() {
			return aggregated;
		}
	}

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

	private static PhraseOccurance aggregatePhrase(List<? extends PhraseOccurance> sequence, double minWeight, double maxWeight) {
		if (sequence.isEmpty()) return null;
		
		int i = 0;
		while ( i<sequence.size() && sequence.get(i).getWeight() < minWeight ) i++;
		
		int j = sequence.size()-1;
		while ( j>i && sequence.get(j).getWeight() < minWeight ) j--;
		
		if ( j<i ) return null;
		
		double weight = 0;
		int ofs = -1;
		int start = -1;
		StringBuilder s = new StringBuilder();
		
		for (int n=i; n<=j; n++) {
			PhraseOccurance p = sequence.get(n);
			
			double w = p.getWeight();
			if (w<0) w = 0;
			if (weight+w > maxWeight) break;
			
			if ( start < 0 ) {
				start = p.getOffset();
				ofs = p.getOffset();
			} else {
				if (p.getOffset()>ofs) s.append(" ");
			}
			
			ofs = p.getEndOffset();
			
			weight += w;
			s.append(p.getTerm());
		}
		
		if (start < 0) return null;
		
		return new PhraseOccurance(s.toString(), (int)weight, start, ofs - start);
	}
	
	public PhraseOccurance get(int index) {
		return phrases.get(index);
	}

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

	public boolean hasPhrasesAt(int at) {
		for ( PhraseOccurance p: phrases ) {
			if ( p.getOffset() == at) return true;
			else if ( p.getOffset() > at) return false;
		}

		return false;
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
	
	public void prune( double minWeight ) {
		Iterator<PhraseOccurance> it = phrases.iterator();
		while (it.hasNext()) {
			PhraseOccurance t = it.next();
			if ( t.getWeight() < minWeight ) it.remove();
		}
	}

	public void buildAggregatePhrases( int start, double minWeight, double maxWeight, Matcher phraseBreak ) {
		AggregatePhraseBuilder builder = new AggregatePhraseBuilder( minWeight, maxWeight, phraseBreak );
		
		if (isEmpty()) return;
		PhraseOccurance last = phrases.get(phrases.size()-1);
		int end = last.getEndOffset();
		
		for (int i=start; i<end; i++) {
				if (hasPhrasesAt(i)) {
					builder.walk(getRootNodeAt(i), 0, null, Integer.MAX_VALUE, maxWeight);
				}
		}
 
		Collection<PhraseOccurance> phrases = builder.getAggregatedPhrases();
		addAll(  phrases );
	}

	public String toString() {
		return phrases.toString();
	}
	
	public Collection<PhraseOccurance> getTerms(PhraseNode<PhraseOccurance> root, int depth) {
		PhraseNode.TermSetBuilder<PhraseOccurance> builder = new PhraseNode.TermSetBuilder<PhraseOccurance>();
		builder.walk(root, 0, null, depth, Double.MAX_VALUE);
		return builder.getTerms();
	}
	
	public Collection<List<PhraseOccurance>> getSequences(PhraseNode<PhraseOccurance> root, int depth) {
		PhraseNode.SequenceSetBuilder<PhraseOccurance> builder = new PhraseNode.SequenceSetBuilder<PhraseOccurance>();
		builder.walk(root, 0, null, depth, Double.MAX_VALUE);
		return builder.getSequences();
	}

	public void add(int index, PhraseOccurance element) {
		add(element);
	}

	public boolean add(PhraseOccurance e) {
		int i = Collections.binarySearch(phrases, e);
		
		if (i<0) i = -i-1;
		else {
			PhraseOccurance old = get(i);
			if (old.equals(e)) return false;
		}
		
		phrases.add(i, e);
		
		return true;
	}

	public boolean addAll(Collection<? extends PhraseOccurance> c) {
		int count = 0;
		for (PhraseOccurance p: c) {
			if ( add(p) ) count++;
		}
		
		return count>0;
	}

	public boolean addAll(int index, Collection<? extends PhraseOccurance> c) {
		return addAll(c);
	}

	public int hashCode() {
		return phrases.hashCode();
	}

	public int lastIndexOf(Object o) {
		return phrases.lastIndexOf(o);
	}

	public PhraseOccurance set(int index, PhraseOccurance element) {
		throw new UnsupportedOperationException();
	}

	public PhraseOccuranceSet subList(int fromIndex, int toIndex) {
		return new PhraseOccuranceSet(text, phrases.subList(fromIndex, toIndex)); 
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
