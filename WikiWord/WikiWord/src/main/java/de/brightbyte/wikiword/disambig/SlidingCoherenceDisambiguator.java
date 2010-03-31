package de.brightbyte.wikiword.disambig;

import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import de.brightbyte.data.LabeledMatrix;
import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.MapLabeledMatrix;
import de.brightbyte.data.measure.CosineVectorSimilarity;
import de.brightbyte.data.measure.Measure;
import de.brightbyte.data.measure.ScalarVectorSimilarity;
import de.brightbyte.data.measure.Similarity;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordConcept;

public class SlidingCoherenceDisambiguator extends CoherenceDisambiguator {

	protected int window ; 
	
	public SlidingCoherenceDisambiguator(MeaningFetcher<LocalConcept> meaningFetcher, FeatureFetcher<LocalConcept, Integer> featureFetcher, boolean featuresAreNormalized) {
		this(meaningFetcher, featureFetcher, WikiWordConcept.theCardinality, 
					featuresAreNormalized ? ScalarVectorSimilarity.<Integer>getInstance() : CosineVectorSimilarity.<Integer>getInstance(),  //if pre-normalized, use scalar to calc cosin
					5);
	}
	
	public SlidingCoherenceDisambiguator(MeaningFetcher<LocalConcept> meaningFetcher, FeatureFetcher<LocalConcept, Integer> featureFetcher, Measure<WikiWordConcept> popularityMeasure, Similarity<LabeledVector<Integer>> sim, int window) {
		super(meaningFetcher, featureFetcher, popularityMeasure, sim);
		
		this.window = window;
	}

	/* (non-Javadoc)
	 * @see de.brightbyte.wikiword.disambig.Disambiguator#disambiguate(java.util.List)
	 */
	public <X extends TermReference>Result<X, LocalConcept> disambiguate(List<X> terms, Map<X, List<? extends LocalConcept>> meanings, Collection<LocalConcept> context) throws PersistenceException {
		if (terms.isEmpty() || meanings.isEmpty()) return new Disambiguator.Result<X, LocalConcept>(Collections.<X, LocalConcept>emptyMap(), 0.0, "no terms or meanings");

		int sz = Math.min(terms.size(), meanings.size());
		if (context!=null) sz += context.size();

		if (window < 2 || sz<2) { 
				return popularityDisambiguator.disambiguate(terms, meanings, context);
		}
		
		pruneMeanings(meanings);
		
		sz = Math.min(terms.size(), meanings.size());
		if (context!=null) sz += context.size();

		if (sz<2) { 
			return popularityDisambiguator.disambiguate(terms, meanings, context);
		}
		
		//CAVEAT: because the map disambig can contain only one meaning per term, the same term can not occur with two meanings within the same term sequence.

		Map<X, LocalConcept> disambig = new HashMap<X, LocalConcept>(meanings.size()); 
		
		LabeledMatrix<LocalConcept, LocalConcept> similarities = new MapLabeledMatrix<LocalConcept, LocalConcept>(true);
		FeatureFetcher<LocalConcept, Integer> features = getFeatureCache(meanings, context); 
		
		for (int i= window; ; i++) {
			int from = i-window;
			int to = i+1;
			
			if (from<0) from = 0;
			if (to>terms.size()) to = terms.size();
			
			Result r ;
			
			if (to-from < 2) {
				r = popularityDisambiguator.disambiguate(terms.subList(from, to), meanings, context);
			} else {
				List<Map<X, LocalConcept>> interpretations = getInterpretations(from, to, terms,  disambig, meanings);
				r = getBestInterpretation(terms, meanings, context, interpretations, similarities, features);
			}

			for (int j=from; j<to; j++) {
				X t = terms.get(j);
				if (disambig.containsKey(t)) continue;
				
				LocalConcept m;
				
				m = (LocalConcept)r.getMeanings().get(t); //UGLY cast
				if (m!=null) disambig.put(t, m);
			}
			
			if (to+1>terms.size()) break;
		}
		
		return getScore(disambig, context, similarities, features); //FIXME: this is unnecessarily expensive, we usually don't need the scores this calculates. 
	}

	protected <X extends TermReference>List<Map<X, LocalConcept>> getInterpretations(int from, int to, List<X> terms,  Map<X, ? extends LocalConcept> known, Map<? extends TermReference, List<? extends LocalConcept>> meanings) {
		//strip out all terms with no known meaning
		if (meanings.keySet().size() != terms.size()) {
			List<X> t = new ArrayList<X>(terms.size());
			t.addAll(terms);
			t.retainAll(meanings.keySet());
			terms = t;
		}
		
		Map<X, List<? extends LocalConcept>> mset = new HashMap<X, List<? extends LocalConcept>>();
		
		if (to>terms.size()) to = terms.size();
		
		for (int i=from; i<to; i++) {
			List<? extends LocalConcept> m;
			
			X t = terms.get(i);
			LocalConcept c = known.get(t);

			if (c!=null) m = Collections.singletonList(c);
			else m = meanings.get(t);
			
			mset.put(t, m);
		}
		
		return getInterpretations(terms.subList(from, to), mset);
	}	
}
