package de.brightbyte.wikiword.disambig;

import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.MapLabeledVector;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.model.WikiWordConceptReference;
import de.brightbyte.wikiword.store.WikiWordConceptStore;

public class LinkFeatureFetcher<C extends WikiWordConcept> implements FeatureFetcher<C> {
	protected boolean useRelevance;
	protected boolean useCardinality;
	
	protected WikiWordConceptStore  store; //NOTE: currently not used for anything. Keep for future use
	
	public LinkFeatureFetcher(WikiWordConceptStore  store, boolean useRelevance, boolean useCardinality) {
		if (store==null) throw new NullPointerException();
		
		this.store = store;
		this.useRelevance = useRelevance;
		this.useCardinality = useCardinality;
	}

	protected double getWeight(WikiWordConceptReference r) {
		double x = useRelevance ? r.getRelevance() : 1;
		int y = useCardinality ? r.getCardinality() : 1;
		
		if (x<0) x = 1;
		else if (x==0) x = 0.001; //FIXME: what's good here?
		
		if (y<=0) y = 1;
		
		return x*y;
	}
	
	public ConceptFeatures<C> getFeatures(WikiWordConcept c) throws PersistenceException {
		LabeledVector<Integer> features = new MapLabeledVector<Integer>();
		
		//XXX: magic numbers!
		
		features.add( getLabel(c.getReference()), getWeight(c.getReference()) * 2 ); 
		
		//match cohyperonyms (weak)
		for (WikiWordConceptReference r: c.getNarrower()) {
			features.add(getLabel(r), getWeight(r) * 1); 
		}
		
		//match cohyponyms (very strong!)
		for (WikiWordConceptReference r: c.getBroader()) {
			features.add(getLabel(r), getWeight(r) * 2);
		}
		
		//match coocurrances (strong!)
		for (WikiWordConceptReference r: c.getInLinks()) {
			features.add(getLabel(r), getWeight(r) * 2);
		}
		
		//match coreferences (weak)
		for (WikiWordConceptReference r: c.getOutLinks()) {
			features.add(getLabel(r), getWeight(r) * 1);
		}
		
		//match related (very strong, but already scores 4 from combined inlinks and outlinks)
		for (WikiWordConceptReference r: c.getRelated()) {
			features.add(getLabel(r), getWeight(r) * -1);
		}
		
		//match similars (very strong!) 
		for (WikiWordConceptReference r: c.getSimilar()) {
			features.add(getLabel(r), getWeight(r) * 2);
		}
		
		//XXX: compare cooccurrances (i.e. eval second level cooc)
		
		return new ConceptFeatures<C>(c.getReference(), features);
	}
	
	private Integer getLabel(WikiWordConceptReference r) {
		return r.getId();
	}
	
	/*
	protected Map<WikiWordReference<?>, String> getNames(WikiWordReference<?>[] rr) throws PersistenceException {
		if (rr==null) return null;
		if (rr.length==0) return Collections.emptyMap();
		
		Map<WikiWordReference<?>, String> names = new HashMap<WikiWordReference<?>, String>();
		
		if (rr[0].getName()==null) {
			int[] ids = new int[rr.length];
			
			int i = 0;
			for (WikiWordReference<?> r: rr) {
				ids[i++] = r.getId();
			}
			
			DataSet<? extends WikiWordConcept> concepts = store.getConcepts(ids);
			for (WikiWordConcept c: concepts) {
				names.put(c.getReference(), c.getName());
			}
		}
		else {
			
			for (WikiWordReference<?> r: rr) {
				names.put(r, r.getName());
			}
		}
		
		return names;
	}
	*/
}
