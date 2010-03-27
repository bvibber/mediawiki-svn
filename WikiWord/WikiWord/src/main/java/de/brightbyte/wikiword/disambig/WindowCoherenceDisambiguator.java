package de.brightbyte.wikiword.disambig;

import java.util.Collections;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import de.brightbyte.data.LabeledMatrix;
import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.MapLabeledMatrix;
import de.brightbyte.data.measure.Measure;
import de.brightbyte.data.measure.Similarity;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.LocalConcept;

public class WindowCoherenceDisambiguator<K> extends CoherenceDisambiguator<K> {

	protected int window = 2; 
	
	public WindowCoherenceDisambiguator(MeaningFetcher<LocalConcept> meaningFetcher, FeatureFetcher<LocalConcept, K> featureFetcher, Measure<LocalConcept> popularityMeasure, Similarity<LabeledVector<K>> sim, int window) {
		super(meaningFetcher, featureFetcher, popularityMeasure, sim);
		
		this.window = window;
	}

	/* (non-Javadoc)
	 * @see de.brightbyte.wikiword.disambig.Disambiguator#disambiguate(java.util.List)
	 */
	public Result disambiguate(List<String> terms, Map<String, List<LocalConcept>> meanings) throws PersistenceException {
		Map<String, LocalConcept> disambig = new HashMap<String, LocalConcept>(meanings.size()); 
		
		LabeledMatrix<LocalConcept, LocalConcept> similarities = new MapLabeledMatrix<LocalConcept, LocalConcept>(true);
		FeatureCache<LocalConcept, K> features = new FeatureCache<LocalConcept, K>(featureFetcher); //TODO: keep a chain of n caches, resulting in LRU logic.

		for (int i=0; i<terms.size(); i++) {
			int from = i-window+1;
			int to = i+1;
			
			if (from<0) from = 0;
			if (to>terms.size()) to = terms.size();
			
			String t = terms.get(i);
			LocalConcept m;
			
			if (to-from < 2) {
				Result r = popularityDisambiguator.disambiguate(terms.subList(from, to), meanings);
				m = (LocalConcept)r.getMeanings().get(t); //UGLY cast
			} else {
				List<Map<String, LocalConcept>> interpretations = getInterpretations(from, to, terms,  disambig, meanings);
				Result r = getBestInterpretation(terms, meanings, interpretations, similarities, features);
				m = (LocalConcept)r.getMeanings().get(t); //UGLY cast
			}
			
			disambig.put(t, m);
		}
		
		return getScore(disambig, similarities, features); //FIXME: this is unnecessarily expensive, we usually don't need the scores this calculates. 
	}

	protected List<Map<String, LocalConcept>> getInterpretations(int from, int to, List<String> terms,  Map<String, LocalConcept> known, Map<String, List<LocalConcept>> meanings) {
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
