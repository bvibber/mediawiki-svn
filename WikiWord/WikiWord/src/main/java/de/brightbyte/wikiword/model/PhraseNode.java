package de.brightbyte.wikiword.model;

import java.util.ArrayList;
import java.util.Collection;
import java.util.HashSet;
import java.util.List;
import java.util.Set;

public interface PhraseNode<T extends TermReference>  {
	public abstract class Walker<T extends TermReference> {
		public abstract boolean onNode(PhraseNode<? extends T> node, List<? extends T> sequence, double weight, boolean terminal);
		
		public void walk(PhraseNode<T> root, int depth) {
			walk(root, 0, null, depth, Double.MAX_VALUE);
		}

		public void walk(PhraseNode<T> root, double baseWeight, List<T> intoSeqence, int depth, double maxWeight) {
			if (depth<1) return;
			if (intoSeqence == null) intoSeqence = new ArrayList<T>();
			
			T t = root.getTermReference();
			if (t.getTerm().length()>0) intoSeqence.add(t); //push
			else if (depth<Integer.MAX_VALUE) depth += 1; //XXX: ugly hack for blank root nodes.
			
			boolean terminal = (depth<=1) || (baseWeight>=maxWeight);
				
			Collection<? extends PhraseNode<T>> successors = terminal ? null : root.getSuccessors();
			if (successors==null || successors.isEmpty()) terminal = true;
			
			double w = root.getTermReference().getWeight();
			if (w<0) w = 0;
			if ( !onNode(root, intoSeqence, w, terminal) ) terminal = true;
			
			//System.out.println( "  - walk: "+intoSeqence+" " );
		
			if (!terminal) {
				for (PhraseNode<T> n: successors) {
					w = n.getTermReference().getWeight();
					if (w<0) w = 0;
					walk(n, baseWeight + w, intoSeqence, depth-1, maxWeight);
				}
			}
			
			if (t.getTerm().length()>0) intoSeqence.remove(t); //pop
		}
	}

	public static class SequenceSetBuilder <T extends TermReference> extends Walker<T> {
		protected List<List<T>> sequences;
		
		public SequenceSetBuilder() {
			sequences = new ArrayList<List<T>>();
		}
		
		public boolean onNode(PhraseNode<? extends T> node, List<? extends T> sequence, double weight, boolean terminal) {
			if (terminal) { 
				List<T> p = new ArrayList<T>(sequence);  //clone
				sequences.add(p);
			}
			
			return !terminal;
		}
		
		public List<List<T>> getSequences() {
			return sequences;
		}
	}

	public static class TermSetBuilder <T extends TermReference> extends Walker<T> {
		protected Set<T> terms;
		
		public TermSetBuilder() {
			terms = new HashSet<T>();
		}
		
		public boolean onNode(PhraseNode<? extends T> node, List<? extends T> sequence, double weight, boolean terminal) {
			T t = node.getTermReference();
			if (t.getTerm().length()>0) terms.add(t);
			
			return !terminal;
		}
		
		public Collection<T> getTerms() {
			return terms;
		}
	}

	public T getTermReference();
	
	public Collection<? extends PhraseNode<T>> getSuccessors();

}