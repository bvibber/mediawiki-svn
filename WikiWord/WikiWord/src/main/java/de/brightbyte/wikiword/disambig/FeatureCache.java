package de.brightbyte.wikiword.disambig;

import java.util.ArrayList;
import java.util.Collection;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.ConceptFeatures;
import de.brightbyte.wikiword.model.WikiWordConcept;

public class FeatureCache<C extends WikiWordConcept, K> implements FeatureFetcher<C, K> {
	
	protected static class Manager<C extends WikiWordConcept, K> {
		protected int maxDepth;
		
		protected FeatureFetcher<C, K> root;
		protected List<FeatureCache<C, K>> stack;
		
		public Manager(FeatureFetcher<C, K> root, int maxDepth) {
			if (root==null) throw new NullPointerException();
			this.stack = new ArrayList<FeatureCache<C, K>>(maxDepth+1);
			this.maxDepth = maxDepth;
			this.root = root;
		}
		
		private FeatureFetcher<C, K> getTop() {
			if (stack.isEmpty()) return root;
			else return stack.get(stack.size()-1);
		}
		
		public synchronized FeatureCache<C, K> newCache() {
			FeatureCache<C, K> cache = new FeatureCache<C, K>( getTop() );
			stack.add(cache);
			
			if (stack.size()>maxDepth) {
				FeatureCache<C, K> old = stack.remove(0);
				old.dispose();
			}
			
			if (!stack.isEmpty()) stack.get(0).setParent(root);
			
			return cache;
		}

		public boolean getFeaturesAreNormalized() {
			return root.getFeaturesAreNormalized();
		}
	}

	protected FeatureFetcher<C, K> parent;
	
	protected Map<Integer, ConceptFeatures<C, K>> cache;
	
	public FeatureCache(FeatureFetcher<C, K> parent) {
		if (parent==null) throw new NullPointerException();
		this.parent = parent;
		this.cache = new HashMap<Integer, ConceptFeatures<C,K>>();
	}

	protected void dispose() {
		this.cache.clear();
		this.cache = null;
		this.parent = null;
	}
	
	public ConceptFeatures<C, K> getFeatures(C c)
			throws PersistenceException {
		 
		ConceptFeatures<C, K> f = cache.get(c.getId());
		if (f!=null) return f;
		
		f = parent.getFeatures(c);
		cache.put(c.getId(), f);
		
		return f;
	}

	public Map<Integer, ConceptFeatures<C, K>> getFeatures(Collection<? extends C> concepts) throws PersistenceException {
		Map<Integer, ConceptFeatures<C, K>> features = new HashMap<Integer, ConceptFeatures<C, K>> ();
		List<C> todo = new ArrayList<C>(concepts.size());
		for (C c: concepts) {
			   if (c==null) continue;
			   
				ConceptFeatures<C, K> f = cache.get(c.getId());
				if (f!=null) {
					features.put(c.getId(), f);
					continue;
				} else {
					todo.add(c);
				}
		}
		
		if (!todo.isEmpty()) {
			Map<Integer, ConceptFeatures<C, K>> parentFeatures = parent.getFeatures(todo);
			features.putAll(parentFeatures);
		}
		
		cache.putAll(features);
		return features;
	}
	
	public FeatureFetcher<C, K> getParent() {
		return parent;
	}
	
	public void setParent(FeatureFetcher<C, K> parent) { 
		if (parent == null) throw new NullPointerException();
		if (parent == this) throw new IllegalArgumentException("can't be my own parent");
		//TODO: prevent cycles
		
		this.parent = parent;
	}
	
	public void clear() {
		cache.clear();
	}

	public boolean getFeaturesAreNormalized() {
		return parent.getFeaturesAreNormalized();
	}

}
