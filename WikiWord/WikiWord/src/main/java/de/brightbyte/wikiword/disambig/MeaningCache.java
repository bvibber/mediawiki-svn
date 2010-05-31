package de.brightbyte.wikiword.disambig;

import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordConcept;

public class MeaningCache<C extends WikiWordConcept> implements MeaningFetcher<C> {

	protected static class Manager<C extends WikiWordConcept> {
		protected int maxDepth;
		
		protected MeaningFetcher<? extends C> root;
		protected List<MeaningCache<C>> stack;
		
		public Manager(MeaningFetcher<? extends C> root, int maxDepth) {
			if (root==null) throw new NullPointerException();
			this.stack = new ArrayList<MeaningCache<C>>(maxDepth+1);
			this.maxDepth = maxDepth;
			this.root = root;
		}
		
		private MeaningFetcher<? extends C> getTop() {
			if (stack.isEmpty()) return root;
			else return stack.get(stack.size()-1);
		}
		
		public synchronized MeaningCache<C> newCache() {
			MeaningCache<C> cache = new MeaningCache<C>( getTop() );
			stack.add(cache);
			
			if (stack.size()>maxDepth) {
				MeaningCache<C> old = stack.remove(0);
				old.dispose();
			}
			if (!stack.isEmpty()) stack.get(0).setParent(root);
			
			return cache;
		}
	}

	protected MeaningFetcher<C> parent;
	
	protected Map<String, List<? extends C>> cache;
	
	public MeaningCache(MeaningFetcher<? extends C> parent) {
		if (parent==null) throw new NullPointerException();
		this.setParent(parent);
		this.cache = new HashMap<String, List<? extends C>>();
	}

	
	public MeaningFetcher<? extends C> getParent() {
		return parent;
	}
	
	public void setParent(MeaningFetcher<? extends C> parent) { 
		if (parent == null) throw new NullPointerException();
		if (parent == this) throw new IllegalArgumentException("can't be my own parent");
		//TODO: prevent cycles
		
		this.parent = (MeaningFetcher<C>)(Object)parent; //XXX: ugly scast. generics are a pain.
	}
	
	public void clear() {
		cache.clear();
	}


	public List<? extends C> getMeanings(String term) throws PersistenceException {
		List<? extends C> meanings = cache.get(term); 
		
		if (meanings==null) {
			meanings = parent.getMeanings(term);
			cache.put(term, meanings);
		}
		
		return meanings;
	}


	public <X extends TermReference> Map<X, List<? extends C>> getMeanings(Collection<X> terms) throws PersistenceException {
		Map<X, List<? extends C>> meanings= new HashMap<X, List<? extends C>>();
		List<X> todo = new ArrayList<X>(terms.size());
		
		for (X t: terms) {
				 List<? extends C> m = cache.get(t.getTerm());
				if (m!=null) {
					meanings.put(t, m);
					cache.put(t.getTerm(), m);
					continue;
				} else {
					todo.add(t);
				}
		}
		
		if (!todo.isEmpty()) {
			Map<X, List<? extends C>> parentMeanings = parent.getMeanings(todo); //XXX: ugly cast, generics are a pain
			meanings.putAll(parentMeanings);
			
			for (X t: todo) {
					List<? extends C> m = parentMeanings.get(t);
					if (m==null) m = Collections.emptyList();
					cache.put(t.getTerm(), m);
			}
		}
		
		return meanings;
	}

	protected void dispose() {
		this.cache.clear();
		this.cache = null;
		this.parent = null;
	}
	
}
