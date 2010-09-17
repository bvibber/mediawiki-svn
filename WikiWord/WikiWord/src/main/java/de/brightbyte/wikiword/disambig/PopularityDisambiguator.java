package de.brightbyte.wikiword.disambig;

import java.util.Collection;
import java.util.Collections;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import de.brightbyte.data.Functor;
import de.brightbyte.data.Functor2;
import de.brightbyte.data.measure.Measure;
import de.brightbyte.wikiword.model.PhraseNode;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordConcept;

public class PopularityDisambiguator<T extends TermReference, C extends WikiWordConcept> extends AbstractDisambiguator<T, C> {
	
	protected Measure<? super C> popularityMeasure;
	
	protected Functor.Double weightBooster = SquareBooster.instance; 
	protected Functor2.Double weigthCombiner = new ProductCombiner(); //NOTE: pop and weight are not in the same scale.
	
	public PopularityDisambiguator(MeaningFetcher<? extends C> meaningFetcher, int cacheCapacity) {
		this(meaningFetcher, cacheCapacity, WikiWordConcept.theCardinality);
	}
	
	public PopularityDisambiguator(MeaningFetcher<? extends C> meaningFetcher, int cacheCapacity, Measure<? super C> popularityMeasure) {
		super(meaningFetcher, cacheCapacity);
		
		this.setPopularityMeasure(popularityMeasure);
	}
	
	public Measure<? super C> getPopularityMeasure() {
		return popularityMeasure;
	}

	public void setPopularityMeasure(Measure<? super C> popularityMeasure) {
		this.popularityMeasure = popularityMeasure;
	}

	public void setWeightCombiner(Functor2.Double weightCombiner) {
		this.weigthCombiner = weightCombiner;
	}

	public Functor.Double getWeightBooster() {
		return weightBooster;
	}

	public void setWeightBooster(Functor.Double weightBooster) {
		this.weightBooster = weightBooster;
	}

	public Functor2.Double getWeigthCombiner() {
		return weigthCombiner;
	}

	public void setWeigthCombiner(Functor2.Double weigthCombiner) {
		this.weigthCombiner = weigthCombiner;
	}

	public <X extends T>Disambiguation<X, C> disambiguate(PhraseNode<X> root, Map<X, List<? extends C>> meanings, Collection<? extends C> context) {
		Collection<List<X>> sequences = getSequences(root, Integer.MAX_VALUE);
		return disambiguate(sequences, root, meanings, context);
	}
	
	protected <X extends T>Disambiguation<X, C> disambiguate(Collection<List<X>> sequences, PhraseNode<X> root, Map<X, List<? extends C>> meanings, Collection<? extends C> context) {
		Disambiguation<X, C> best = null;
		
		pruneMeaninglessSequences( sequences, meanings );
		
		for (List<X> sequence: sequences) {
			Disambiguation<X, C> r = disambiguate(sequence, meanings, context);
			trace(r.toString());
			if (best == null || best.getScore() < r.getScore()) {
				best = r;
			}
		}
		
		trace("best:" + best);
		return best;
	}
	
	protected <X extends T> C getBestMeaning(X term, Map<X, List<? extends C>> meanings, Measure<? super C> measure) {
		List<? extends C> m = meanings.get(term);
		if (m==null || m.size()==0) return null;
		
		C best = null;
		double bestPop = 0;
		
		for (C c: m) {
			double pop = measure.measure(c);
			if ( best==null || pop>bestPop ) {
				bestPop = pop;
				best = c;
			}
		}
		
		C c = m.get(0);
		return c;
	}
	
	public <X extends T>Disambiguation<X, C> disambiguate(List<X> sequence, Map<X, List<? extends C>> meanings, Collection<? extends C> context) {
		if (sequence.isEmpty() || meanings.isEmpty()) return new Disambiguator.Disambiguation<X, C>(Collections.<X, C>emptyMap(), Collections.<X>emptyList(), 0.0, "no terms or meanings");

		Map<X, C> disambig = new HashMap<X, C>();
		double score = 0;
		int totalPop = 0;
		
		for (X t: sequence) {
			C c = getBestMeaning(t, meanings, popularityMeasure);
			if ( c==null ) continue;
			
			disambig.put(t, c);

			double pop = popularityMeasure.measure(c);
			totalPop += pop;
			
			double w = weightBooster.apply(t.getWeight());
			double sc = weigthCombiner.apply(pop, w); 
			score += sc;
		}

		if (disambig.size()>0) score = score / sequence.size(); //NOTE: treat unknown terms as having pop = 0
		
		Disambiguation<X, C> r = new Disambiguation<X, C>(disambig, sequence, score, "score="+score+"; pop="+totalPop);
		return r;
	}

	public boolean exploresAllSequences() {
		return true;
	}

}
