package de.brightbyte.wikiword.disambig;

import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.HashSet;
import java.util.List;
import java.util.Map;
import java.util.Set;

import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.PhraseNode;
import de.brightbyte.wikiword.model.TermListNode;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordConcept;

public abstract class AbstractDisambiguator<T extends TermReference, C extends WikiWordConcept> implements Disambiguator<T, C> {

	public interface NodeListener<T extends TermReference> {
		public void onNode(PhraseNode<? extends T> node, List<? extends T> seqence, boolean terminal);
	}

	public static class SequenceSetBuilder <T extends TermReference> implements NodeListener<T> {
		protected List<List<T>> seqencees;
		
		public SequenceSetBuilder() {
			seqencees = new ArrayList<List<T>>();
		}
		
		public void onNode(PhraseNode<? extends T> node, List<? extends T> seqence, boolean terminal) {
			if (terminal) { 
				List<T> p = new ArrayList<T>(seqence);  //clone
				seqencees.add(p);
			}
		}
		
		public List<List<T>> getSequences() {
			return seqencees;
		}
	}

	public static class TermSetBuilder <T extends TermReference> implements NodeListener<T> {
		protected Set<T> terms;
		
		public TermSetBuilder() {
			terms = new HashSet<T>();
		}
		
		public void onNode(PhraseNode<? extends T> node, List<? extends T> seqence, boolean terminal) {
			T t = node.getTermReference();
			if (t.getTerm().length()>0) terms.add(t);
		}
		
		public Collection<T> getTerms() {
			return terms;
		}
	}

	private MeaningFetcher<C> meaningFetcher;
	
	private Output trace;

	private Map<String, C> meaningOverrides;
	
	public AbstractDisambiguator(MeaningFetcher<C> meaningFetcher, int cacheCapacity) {
		if (meaningFetcher==null) throw new NullPointerException();
		
		if (cacheCapacity>0) meaningFetcher = new CachingMeaningFetcher<C>(meaningFetcher, cacheCapacity);
		this.meaningFetcher = meaningFetcher;
	}
	
	public MeaningFetcher<C> getMeaningFetcher() {
		return meaningFetcher;
	}

	public void setMeaningOverrides(Map<String, C> overrideMap) {
		this.meaningOverrides = overrideMap;
	}	
	
	protected <X extends T>Collection<X> getTerms(PhraseNode<X> root, int depth) {
		TermSetBuilder<X> builder = new TermSetBuilder<X>();
		walk(root, null, builder, depth);
		return builder.getTerms();
	}
	
	protected <X extends T>Collection<List<X>> getSequences(PhraseNode<X> root, int depth) {
		SequenceSetBuilder<X> builder = new SequenceSetBuilder<X>();
		walk(root, null, builder, depth);
		return builder.getSequences();
	}
	
	protected <X extends T>PhraseNode<X> getLastNode(PhraseNode<X> root, List<X> sequence) {
		PhraseNode<X> n = findLastNode(root, sequence);
		if (n==null) throw new IllegalArgumentException("sequence does not match node structure: "+sequence);
		return n;
	}
	
	private <X extends T>PhraseNode<X> findLastNode(PhraseNode<X> root, List<X> sequence) {
		if (root.getTermReference().getTerm().length()>0) {
			X t = sequence.get(0);
			if (!t.getTerm().equals(root.getTermReference().getTerm())) return null;
			sequence = sequence.subList(1, sequence.size());
		}
		
		terms: for (X t: sequence) {
			Collection<? extends PhraseNode<X>> successors = root.getSuccessors();
			if (successors==null || successors.isEmpty()) 
				return null;
			
			for (PhraseNode<X> n: successors) {
				if (n.getTermReference().getTerm().equals(t.getTerm())) {
					root = n;
					continue terms;
				}
			}
			
			for (PhraseNode<X> n: successors) {
				PhraseNode<X> m = findLastNode(n, sequence);
				if (m != null) return m;
			}
			
			return null;
		}
		
		return root;
	}
	
	protected <X extends T>void walk(PhraseNode<X> root, List<X> seqence, NodeListener<? super X> nodeListener, int depth) {
		if (depth<1) return;
		if (seqence == null) seqence = new ArrayList<X>();
		
		X t = root.getTermReference();
		if (t.getTerm().length()>0) seqence.add(t); //push
		else if (depth<Integer.MAX_VALUE) depth += 1; //XXX: ugly hack for blank root nodes.
		
		boolean terminal = (depth<=1);
			
		Collection<? extends PhraseNode<X>> successors = terminal ? null : root.getSuccessors();
		if (successors==null || successors.isEmpty()) terminal = true;
		
		if (nodeListener!=null) 
			nodeListener.onNode(root, seqence, terminal);
		
		if (!terminal) {
			for (PhraseNode<X> n: successors) {
				walk(n, seqence, nodeListener, depth-1);
			}
		}
		
		if (t.getTerm().length()>0) seqence.remove(t); //pop
	}
	
	protected <X extends T>Map<X, List<? extends C>> getMeanings(PhraseNode<X> root) throws PersistenceException {
		Collection<X> terms = getTerms(root, Integer.MAX_VALUE);
		return getMeanings(terms);
	}
	
	protected <X extends T>Map<X, List<? extends C>> getMeanings(Collection<X> terms) throws PersistenceException {
		Collection<X> todo = terms;
		
		if (meaningOverrides!=null) {
			todo = new ArrayList<X>();
			for (X t: terms) {
				if (!meaningOverrides.containsKey(t.getTerm())) todo.add(t);
			}
		}
		
		Map<X, List<? extends C>> meanings = meaningFetcher.getMeanings(todo);
		
		if (meaningOverrides!=null && todo.size()!=terms.size()) {
			for (X t: terms) {
				C c = meaningOverrides.get(t.getTerm());
				if (c!=null) meanings.put(t, Collections.singletonList(c));
			}
		}

		return meanings;
	}
	
	public <X extends T>Disambiguation<X, C> disambiguate(List<X> terms, Collection<? extends C> context) throws PersistenceException {
		return this.<X>disambiguate(new TermListNode<X>(terms, 0), context);
	}
	
	public <X extends T>Disambiguation<X, C> disambiguate(PhraseNode<X> root, Collection<? extends C> context) throws PersistenceException {
		Collection<X> terms = getTerms(root, Integer.MAX_VALUE);
		Map<X, List<? extends C>> meanings = getMeanings(terms);
		return disambiguate(root, meanings, context);
	}
	
	public abstract <X extends T>Disambiguation<X, C> disambiguate(PhraseNode<X> root, Map<X, List<? extends C>> meanings, Collection<? extends C> context) throws PersistenceException;

	public Output getTrace() {
		return trace;
	}

	public void setTrace(Output trace) {
		this.trace = trace;
	}

	protected void trace(String msg) {
		if (trace!=null) trace.println(msg);
	}

}