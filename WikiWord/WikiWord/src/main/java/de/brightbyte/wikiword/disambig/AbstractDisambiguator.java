package de.brightbyte.wikiword.disambig;

import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.List;
import java.util.Map;

import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.PhraseNode;
import de.brightbyte.wikiword.model.TermListNode;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordConcept;

public abstract class AbstractDisambiguator<T extends TermReference, C extends WikiWordConcept> implements Disambiguator<T, C> {

	public interface NodeListener<T extends TermReference> {
		public void onNode(PhraseNode<? extends T> node, List<? extends T> seqence);
	}

	public static class SequenceSetBuilder <T extends TermReference> implements NodeListener<T> {
		protected List<List<T>> seqencees;
		
		public SequenceSetBuilder() {
			seqencees = new ArrayList<List<T>>();
		}
		
		public void onNode(PhraseNode<? extends T> node, List<? extends T> seqence) {
			if (node.getSuccessors().isEmpty()) { //is leaf
				List<T> p = new ArrayList<T>(seqence);  //clone
				seqencees.add(p);
			}
		}
		
		public List<List<T>> getSequences() {
			return seqencees;
		}
	}

	public static class TermSetBuilder <T extends TermReference> implements NodeListener<T> {
		protected List<T> terms;
		
		public TermSetBuilder() {
			terms = new ArrayList<T>();
		}
		
		public void onNode(PhraseNode<? extends T> node, List<? extends T> seqence) {
			T t = node.getTermReference();
			if (t.getTerm().length()>0) terms.add(t);
		}
		
		public List<T> getTerms() {
			return terms;
		}
	}

	private MeaningCache.Manager<C> meaningCacheManager;
	
	private Output trace;

	private Map<? extends T, C> meaningOverrides;
	
	public AbstractDisambiguator(MeaningFetcher<? extends C> meaningFetcher) {
		if (meaningFetcher==null) throw new NullPointerException();
		this.meaningCacheManager = new MeaningCache.Manager<C>(meaningFetcher, 10);
	}

	public void setMeaningOverrides(Map<? extends T, C> overrideMap) {
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
	
	protected <X extends T>void walk(PhraseNode<X> root, List<X> seqence, NodeListener<? super X> nodeListener, int depth) {
		if (depth<1) return;
		if (seqence == null) seqence = new ArrayList<X>();
		
		X t = root.getTermReference();
		if (t.getTerm().length()>0) seqence.add(t); //push
		
		if (nodeListener!=null) 
			nodeListener.onNode(root, seqence);
		
		List<? extends PhraseNode<X>> successors = root.getSuccessors();
		
		if (depth>1) {
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
				if (!meaningOverrides.containsKey(t)) todo.add(t);
			}
		}
		
		MeaningCache<C> mcache = meaningCacheManager.newCache();
		Map<X, List<? extends C>> meanings = mcache.getMeanings(todo);
		
		if (meaningOverrides!=null && todo.size()!=terms.size()) {
			for (X t: terms) {
				C c = meaningOverrides.get(t);
				if (c!=null) meanings.put(t, Collections.singletonList(c));
			}
		}

		return meanings;
	}
	
	public <X extends T>Result<X, C> disambiguate(List<X> terms, Collection<C> context) throws PersistenceException {
		return this.<X>disambiguate(new TermListNode<X>(terms, 0), context);
	}
	
	public <X extends T>Result<X, C> disambiguate(PhraseNode<X> root, Collection<C> context) throws PersistenceException {
		Collection<X> terms = getTerms(root, Integer.MAX_VALUE);
		Map<X, List<? extends C>> meanings = getMeanings(terms);
		return disambiguate(root, terms, meanings, context);
	}
	
	public abstract <X extends T>Result<X, C> disambiguate(PhraseNode<X> root, Collection<X> terms, Map<X, List<? extends C>> meanings, Collection<C> context) throws PersistenceException;

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