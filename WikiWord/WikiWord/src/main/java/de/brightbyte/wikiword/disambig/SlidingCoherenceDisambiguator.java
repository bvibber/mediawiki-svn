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
import de.brightbyte.wikiword.model.PhraseNode;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordConcept;

public class SlidingCoherenceDisambiguator extends CoherenceDisambiguator {

	protected int window;
	protected int initialWindow; 
	
	public SlidingCoherenceDisambiguator(MeaningFetcher<LocalConcept> meaningFetcher, FeatureFetcher<LocalConcept, Integer> featureFetcher, boolean featuresAreNormalized) {
		this(meaningFetcher, featureFetcher, WikiWordConcept.theCardinality, 
					featuresAreNormalized ? ScalarVectorSimilarity.<Integer>getInstance() : CosineVectorSimilarity.<Integer>getInstance(),  //if pre-normalized, use scalar to calc cosin
					5, 5);
	}
	
	public SlidingCoherenceDisambiguator(MeaningFetcher<LocalConcept> meaningFetcher, FeatureFetcher<LocalConcept, Integer> featureFetcher, Measure<WikiWordConcept> popularityMeasure, Similarity<LabeledVector<Integer>> sim, int window, int initialWindow) {
		super(meaningFetcher, featureFetcher, popularityMeasure, sim);
		
 		this.window = window;
 		this.initialWindow = initialWindow;
	}

	public <X extends TermReference>Result<X, LocalConcept> evalStep(List<X> baseSequence, Map<X, LocalConcept> interpretation, PhraseNode<X> node, 
			Map<X, List<? extends LocalConcept>> meanings, Collection<LocalConcept> context, 
			LabeledMatrix<LocalConcept, LocalConcept> similarities, FeatureFetcher<LocalConcept, Integer> features) throws PersistenceException {
		X term = node.getTermReference();
		
		List<X> sequence = new ArrayList<X>(baseSequence);
		sequence.add(term);

		int to = sequence.size();
		int from = to - window;
		if (from<0) from = 0;

		List<X> frame = sequence.subList(from, to);
		
		Result<X, LocalConcept> r ;
		
		if (to-from < 2) {
			r = popularityDisambiguator.disambiguate(frame, meanings, context);
		} else {
			List<Disambiguator.Interpretation<X, LocalConcept>> interpretations = getInterpretations(frame,  interpretation, meanings);
			r = getBestInterpretation(node, meanings, context, interpretations, similarities, features);
		}
		
		return r;
	}
	
	/* (non-Javadoc)
	 * @see de.brightbyte.wikiword.disambig.Disambiguator#disambiguate(java.util.List)
	 */
	public <X extends TermReference>Result<X, LocalConcept> disambiguate(PhraseNode<X> root, Map<X, List<? extends LocalConcept>> meanings, Collection<LocalConcept> context) throws PersistenceException {
		if (meanings.isEmpty()) return new Disambiguator.Result<X, LocalConcept>(Collections.<X, LocalConcept>emptyMap(), Collections.<X>emptyList(), 0.0, "no terms or meanings");

		int sz = meanings.size();
		if (context!=null) sz += context.size();

		if (window < 2 || sz<2) { 
				return popularityDisambiguator.disambiguate(root, meanings, context);
		}
		
		pruneMeanings(meanings);
		
		sz = meanings.size();
		if (context!=null) sz += context.size();

		if (sz<2) { 
			return popularityDisambiguator.disambiguate(root, meanings, context);
		}
		
		//CAVEAT: because the map disambig can contain only one meaning per term, the same term can not occur with two meanings within the same term sequence.
		
		LabeledMatrix<LocalConcept, LocalConcept> similarities = new MapLabeledMatrix<LocalConcept, LocalConcept>(true);
		FeatureFetcher<LocalConcept, Integer> features = getFeatureCache(meanings, context); 

		Map<X, LocalConcept> disambig = new HashMap<X, LocalConcept>(meanings.size()); 
		PhraseNode<X> currentNode = root;
		List<X> sequence = new ArrayList<X>();
		
		if (initialWindow > 0) { //apply full coherence disambig to initial window size. initialWindow == 1 will trigger a popularity disambig.
			Collection<List<X>> sequences = getSequences(root, initialWindow);
			Result<X, LocalConcept> r = disambiguate(sequences, root, meanings, context);
			
			sequence.addAll(r.getSequence());
			currentNode = getLastNode(root, sequence);
			disambig.putAll(r.getMeanings());
		}
		
		while (true) {
			Collection<? extends PhraseNode<X>> successors = currentNode.getSuccessors();
			if (successors==null || successors.isEmpty()) break;
			
			Result<X, LocalConcept> best = null;
			PhraseNode<X>  bestNode = null;
			
			for (PhraseNode<X> n: successors) {
				Result<X, LocalConcept> r = evalStep(sequence, disambig, currentNode, meanings, context, similarities, features); //empty sequence will trigger popularity disambig
				if (best == null || best.getScore() < r.getScore()) {
					best = r;
					bestNode = n;
				}
			}
			
			X term = bestNode.getTermReference();
			currentNode = bestNode;
			sequence.add(term);
			
			LocalConcept meaning = best.getMeanings().get(term);
			disambig.put(term, meaning);
		}
		
		return getScore(new Disambiguator.Interpretation<X, LocalConcept>(disambig, sequence), context, similarities, features); //FIXME: this is unnecessarily expensive, we usually don't need the scores this calculates. 
	}

	protected <X extends TermReference>List<Disambiguator.Interpretation<X, LocalConcept>> getInterpretations(List<X> frame,  Map<X, ? extends LocalConcept> known, Map<? extends TermReference, List<? extends LocalConcept>> meanings) {
		//strip out all terms with no known meaning
		if (meanings.keySet().size() != frame.size()) {
			List<X> t = new ArrayList<X>(frame.size());
			t.addAll(frame);
			t.retainAll(meanings.keySet());
			frame = t;
		}
		
		Map<X, List<? extends LocalConcept>> mset = new HashMap<X, List<? extends LocalConcept>>();
		
		for (X t: frame) {
			List<? extends LocalConcept> m;
			
			LocalConcept c = known.get(t);

			if (c!=null) m = Collections.singletonList(c);
			else m = meanings.get(t);
			
			mset.put(t, m);
		}
		
		return getSequenceInterpretations(frame, mset);
	}

}
