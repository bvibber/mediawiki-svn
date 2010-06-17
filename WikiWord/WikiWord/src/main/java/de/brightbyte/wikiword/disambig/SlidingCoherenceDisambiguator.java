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
import de.brightbyte.wikiword.model.PhraseNode;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordConcept;

public class SlidingCoherenceDisambiguator<T extends TermReference, C extends WikiWordConcept> extends CoherenceDisambiguator<T, C> {

	protected int window;
	protected int initialWindow; 
	
	public SlidingCoherenceDisambiguator(MeaningFetcher<? extends C> meaningFetcher, FeatureFetcher<C, Integer> featureFetcher, int cacheCapacity) {
		this(meaningFetcher, featureFetcher, cacheCapacity, null, null, 5, 5);
	}
	
	public SlidingCoherenceDisambiguator(MeaningFetcher<? extends C> meaningFetcher, FeatureFetcher<C, Integer> featureFetcher, int cacheCapacity, Measure<C> popularityMeasure, Similarity<LabeledVector<Integer>> sim, int window, int initialWindow) {
		super(meaningFetcher, featureFetcher, cacheCapacity, popularityMeasure, sim);
		
 		this.window = window;
 		this.initialWindow = initialWindow;
	}

	public <X extends T>Disambiguation<X, C> evalStep(List<X> baseSequence, Map<X, C> interpretation, PhraseNode<X> node, 
			Map<X, List<? extends C>> meanings, Collection<? extends C> context, 
			LabeledMatrix<C, C> similarities, FeatureFetcher<C, Integer> features) throws PersistenceException {
			X term = node.getTermReference();
		
		List<X> sequence = new ArrayList<X>(baseSequence);
		sequence.add(term);

		int to = sequence.size();
		int from = to - window;
		if (from<0) from = 0;

		List<X> frame = sequence.subList(from, to);
		
		Disambiguation<X, C> r ;
		
		if (to-from < 2) {
			r = popularityDisambiguator.disambiguate(frame, meanings, context);
		} else {
			Collection<Disambiguator.Interpretation<X, C>> interpretations = getInterpretations(frame,  interpretation, meanings);
			r = getBestInterpretation(node, meanings, context, interpretations, similarities, features);
		}
		
		return r;
	}
	
	/* (non-Javadoc)
	 * @see de.brightbyte.wikiword.disambig.Disambiguator#disambiguate(java.util.List)
	 */
	public <X extends T>CoherenceDisambiguation<X, C> disambiguate(PhraseNode<X> root, Map<X, List<? extends C>> meanings, Collection<? extends C> context) throws PersistenceException {
		if (meanings.isEmpty()) return new CoherenceDisambiguation<X, C>(Collections.<X, C>emptyMap(), Collections.<X>emptyList(), Collections.<Integer, ConceptFeatures<C, Integer>>emptyMap(), ConceptFeatures.newIntFeaturVector(1), 0.0, "no terms or meanings");

		int sz = meanings.size();
		if (context!=null) sz += context.size();
		
		//CAVEAT: because the map disambig can contain only one meaning per term, the same term can not occur with two meanings within the same term sequence.
		
		LabeledMatrix<C, C> similarities = new MapLabeledMatrix<C, C>(true);
		FeatureFetcher<C, Integer> features = getFeatureCache(meanings, context); 

		if (window < 2 || sz<2) { 
			Disambiguation<X, C> r = popularityDisambiguator.disambiguate(root, meanings, context);
			return getScore(r.getInterpretation(), context, similarities, features); 
		}
		
		pruneMeanings(meanings);
		
		sz = meanings.size();
		if (context!=null) sz += context.size();

		if (sz<2) { 
			Disambiguation<X, C> r = popularityDisambiguator.disambiguate(root, meanings, context);
			return getScore(r.getInterpretation(), context, similarities, features); 
		}

		Map<X, C> disambig = new HashMap<X, C>(meanings.size()); 
		PhraseNode<X> currentNode = root;
		List<X> sequence = new ArrayList<X>();
		
		if (initialWindow > 0) { //apply full coherence disambig to initial window size. initialWindow == 1 will trigger a popularity disambig.
			Collection<List<X>> sequences = getSequences(root, initialWindow);
			Disambiguation<X, C> r;
			
			if (initialWindow == 1) r = popularityDisambiguator.disambiguate(sequences, root, meanings, context);
			else r = super.disambiguate(sequences, root, meanings, context);
			
			sequence.addAll(r.getSequence());
			currentNode = getLastNode(root, sequence);
			disambig.putAll(r.getMeanings());
		}
		
		while (true) {
			Collection<? extends PhraseNode<X>> successors = currentNode.getSuccessors();
			if (successors==null || successors.isEmpty()) break;
			
			Disambiguation<X, C> best = null;
			PhraseNode<X>  bestNode = null;
			
			for (PhraseNode<X> n: successors) {
				Disambiguation<X, C> r = evalStep(sequence, disambig, n, meanings, context, similarities, features); //empty sequence will trigger popularity disambig
				trace("evalStep("+n+"): " + r.toString());
				if (best == null || best.getScore() < r.getScore()) {
					best = r;
					bestNode = n;
				}
			}
			
			X term = bestNode.getTermReference();
			currentNode = bestNode;
			sequence.add(term);
			
			C meaning = best.getMeanings().get(term);
			disambig.put(term, meaning);
		}
		
		return getScore(new Disambiguator.Interpretation<X, C>(disambig, sequence), context, similarities, features); //FIXME: this is unnecessarily expensive, we usually don't need the scores this calculates. 
	}

	protected <X extends T>Collection<Disambiguator.Interpretation<X, C>> getInterpretations(List<X> frame,  Map<X, ? extends C> known, Map<? extends T, List<? extends C>> meanings) {
		//strip out all terms with no known meaning
		if (meanings.keySet().size() != frame.size()) {
			List<X> t = new ArrayList<X>(frame.size());
			t.addAll(frame);
			t.retainAll(meanings.keySet());
			frame = t;
		}
		
		Map<X, List<? extends C>> mset = new HashMap<X, List<? extends C>>();
		
		for (X t: frame) {
			List<? extends C> m;
			
			C c = known.get(t);

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
