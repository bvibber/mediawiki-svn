package de.brightbyte.wikiword.disambig;

import java.util.ArrayList;
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
import de.brightbyte.wikiword.model.WikiWordConcept;

public class SlidingCoherenceDisambiguator<K> extends CoherenceDisambiguator<K> {

	protected int window ; 
	
	public SlidingCoherenceDisambiguator(MeaningFetcher<LocalConcept> meaningFetcher, FeatureFetcher<LocalConcept, K> featureFetcher, boolean featuresAreNormalized) {
		this(meaningFetcher, featureFetcher, WikiWordConcept.theCardinality, 
					featuresAreNormalized ? ScalarVectorSimilarity.<K>getInstance() : CosineVectorSimilarity.<K>getInstance(),  //if pre-normalized, use scalar to calc cosin
					5);
	}
	
	public SlidingCoherenceDisambiguator(MeaningFetcher<LocalConcept> meaningFetcher, FeatureFetcher<LocalConcept, K> featureFetcher, Measure<WikiWordConcept> popularityMeasure, Similarity<LabeledVector<K>> sim, int window) {
		super(meaningFetcher, featureFetcher, popularityMeasure, sim);
		
		this.window = window;
	}

	/* (non-Javadoc)
	 * @see de.brightbyte.wikiword.disambig.Disambiguator#disambiguate(java.util.List)
	 */
	public Result disambiguate(List<String> terms, Map<String, List<LocalConcept>> meanings) throws PersistenceException {
		if (window < 2 || terms.size()<2 || meanings.size()<2) 
				return popularityDisambiguator.disambiguate(terms, meanings);
		
		//CAVEAT: because the map disambig can contain only one meaning per term, the same term can not occur with two meanings within the same term sequence.

		Map<String, LocalConcept> disambig = new HashMap<String, LocalConcept>(meanings.size()); 
		
		LabeledMatrix<LocalConcept, LocalConcept> similarities = new MapLabeledMatrix<LocalConcept, LocalConcept>(true);
		FeatureCache<LocalConcept, K> features = new FeatureCache<LocalConcept, K>(featureFetcher); //TODO: keep a chain of n caches, resulting in LRU logic.
		
		for (int i= window; ; i++) {
			int from = i-window;
			int to = i+1;
			
			if (from<0) from = 0;
			if (to>terms.size()) to = terms.size();
			
			Result r ;
			
			if (to-from < 2) {
				r = popularityDisambiguator.disambiguate(terms.subList(from, to), meanings);
			} else {
				List<Map<String, LocalConcept>> interpretations = getInterpretations(from, to, terms,  disambig, meanings);
				r = getBestInterpretation(terms, meanings, interpretations, similarities, features);
			}

			for (int j=from; j<to; j++) {
				String t = terms.get(j);
				if (disambig.containsKey(t)) continue;
				
				LocalConcept m;
				
				m = (LocalConcept)r.getMeanings().get(t); //UGLY cast
				if (m!=null) disambig.put(t, m);
			}
			
			if (to+1>terms.size()) break;
		}
		
		return getScore(disambig, similarities, features); //FIXME: this is unnecessarily expensive, we usually don't need the scores this calculates. 
	}

	protected List<Map<String, LocalConcept>> getInterpretations(int from, int to, List<String> terms,  Map<String, LocalConcept> known, Map<String, List<LocalConcept>> meanings) {
		//strip out all terms with no known meaning
		if (meanings.keySet().size() != terms.size()) {
			List<String> t = new ArrayList<String>(terms.size());
			t.addAll(terms);
			t.retainAll(meanings.keySet());
			terms = t;
		}
		
		Map<String, List<LocalConcept>> mset = new HashMap<String, List<LocalConcept>>();
		
		if (to>terms.size()) to = terms.size();
		
		for (int i=from; i<to; i++) {
			List<LocalConcept> m;
			
			String t = terms.get(i);
			LocalConcept c = known.get(t);

			if (c!=null) m = Collections.singletonList(c);
			else m = meanings.get(t);
			
			mset.put(t, m);
		}
		
		return getInterpretations(terms.subList(from, to), mset);
	}	
}
