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
import de.brightbyte.data.measure.Measure;
import de.brightbyte.data.measure.Similarity;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.ConceptFeatures;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.model.PhraseNode;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordConcept;

public class SlidingCoherenceDisambiguator extends CoherenceDisambiguator {

	protected int window;
	protected int initialWindow; 
	
	public SlidingCoherenceDisambiguator(MeaningFetcher<LocalConcept> meaningFetcher, FeatureFetcher<LocalConcept, Integer> featureFetcher, int cacheCapacity) {
		this(meaningFetcher, featureFetcher, cacheCapacity, null, null, 5, 5);
	}
	
	public SlidingCoherenceDisambiguator(MeaningFetcher<LocalConcept> meaningFetcher, FeatureFetcher<LocalConcept, Integer> featureFetcher, int cacheCapacity, Measure<WikiWordConcept> popularityMeasure, Similarity<LabeledVector<Integer>> sim, int window, int initialWindow) {
		super(meaningFetcher, featureFetcher, cacheCapacity, popularityMeasure, sim);
		
 		this.window = window;
 		this.initialWindow = initialWindow;
	}

	public <X extends TermReference>Disambiguation<X, LocalConcept> evalStep(List<X> baseSequence, Map<X, LocalConcept> interpretation, PhraseNode<X> node, 
			Map<X, List<? extends LocalConcept>> meanings, Collection<? extends LocalConcept> context, 
			LabeledMatrix<LocalConcept, LocalConcept> similarities, FeatureFetcher<LocalConcept, Integer> features) throws PersistenceException {
		X term = node.getTermReference();
		
		List<X> sequence = new ArrayList<X>(baseSequence);
		sequence.add(term);

		int to = sequence.size();
		int from = to - window;
		if (from<0) from = 0;

		List<X> frame = sequence.subList(from, to);
		
		Disambiguation<X, LocalConcept> r ;
		
		if (to-from < 2) {
			r = popularityDisambiguator.disambiguate(frame, meanings, context);
		} else {
			Collection<Disambiguator.Interpretation<X, LocalConcept>> interpretations = getInterpretations(frame,  interpretation, meanings);
			r = getBestInterpretation(node, meanings, context, interpretations, similarities, features);
		}
		
		return r;
	}
	
	/* (non-Javadoc)
	 * @see de.brightbyte.wikiword.disambig.Disambiguator#disambiguate(java.util.List)
	 */
	public <X extends TermReference>CoherenceDisambiguation<X, LocalConcept> disambiguate(PhraseNode<X> root, Map<X, List<? extends LocalConcept>> meanings, Collection<? extends LocalConcept> context) throws PersistenceException {
		if (meanings.isEmpty()) return new CoherenceDisambiguation<X, LocalConcept>(Collections.<X, LocalConcept>emptyMap(), Collections.<X>emptyList(), Collections.<Integer, ConceptFeatures<LocalConcept, Integer>>emptyMap(), ConceptFeatures.newIntFeaturVector(1), 0.0, "no terms or meanings");

		int sz = meanings.size();
		if (context!=null) sz += context.size();
		
		//CAVEAT: because the map disambig can contain only one meaning per term, the same term can not occur with two meanings within the same term sequence.
		
		LabeledMatrix<LocalConcept, LocalConcept> similarities = new MapLabeledMatrix<LocalConcept, LocalConcept>(true);
		FeatureFetcher<LocalConcept, Integer> features = getFeatureCache(meanings, context); 

		if (window < 2 || sz<2) { 
			Disambiguation<X, LocalConcept> r = popularityDisambiguator.disambiguate(root, meanings, context);
			return getScore(r.getInterpretation(), context, similarities, features); 
		}
		
		pruneMeanings(meanings);
		
		sz = meanings.size();
		if (context!=null) sz += context.size();

		if (sz<2) { 
			Disambiguation<X, LocalConcept> r = popularityDisambiguator.disambiguate(root, meanings, context);
			return getScore(r.getInterpretation(), context, similarities, features); 
		}

		Map<X, LocalConcept> disambig = new HashMap<X, LocalConcept>(meanings.size()); 
		PhraseNode<X> currentNode = root;
		List<X> sequence = new ArrayList<X>();
		
		if (initialWindow > 0) { //apply full coherence disambig to initial window size. initialWindow == 1 will trigger a popularity disambig.
			Collection<List<X>> sequences = getSequences(root, initialWindow);
			Disambiguation<X, LocalConcept> r;
			
			if (initialWindow == 1) r = popularityDisambiguator.disambiguate(sequences, root, meanings, context);
			else r = super.disambiguate(sequences, root, meanings, context);
			
			sequence.addAll(r.getSequence());
			currentNode = getLastNode(root, sequence);
			disambig.putAll(r.getMeanings());
		}
		
		while (true) {
			Collection<? extends PhraseNode<X>> successors = currentNode.getSuccessors();
			if (successors==null || successors.isEmpty()) break;
			
			Disambiguation<X, LocalConcept> best = null;
			PhraseNode<X>  bestNode = null;
			
			for (PhraseNode<X> n: successors) {
				Disambiguation<X, LocalConcept> r = evalStep(sequence, disambig, n, meanings, context, similarities, features); //empty sequence will trigger popularity disambig
				trace("evalStep("+n+"): " + r.toString());
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

	protected <X extends TermReference>Collection<Disambiguator.Interpretation<X, LocalConcept>> getInterpretations(List<X> frame,  Map<X, ? extends LocalConcept> known, Map<? extends TermReference, List<? extends LocalConcept>> meanings) {
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

	public int getInitialWindow() {
		return initialWindow;
	}

	public void setInitialWindow(int initialWindow) {
		this.initialWindow = initialWindow;
	}

	public int getWindow() {
		return window;
	}

	public void setWindow(int window) {
		this.window = window;
	}

}
