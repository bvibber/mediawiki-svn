package de.brightbyte.wikiword.disambig;

import java.util.ArrayList;
import java.util.Collection;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import de.brightbyte.data.Functors;
import de.brightbyte.data.measure.Measure;
import de.brightbyte.wikiword.model.PhraseNode;
import de.brightbyte.wikiword.model.TermListNode;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordConcept;

public class ForwardPopularityDisambiguator<T extends TermReference, C extends WikiWordConcept> extends PopularityDisambiguator<T, C> {

	public ForwardPopularityDisambiguator(MeaningFetcher<? extends C> meaningFetcher,
			int cacheCapacity) {
		super(meaningFetcher, cacheCapacity);
	}

	public ForwardPopularityDisambiguator(MeaningFetcher<? extends C> meaningFetcher,
			int cacheCapacity, Measure<? super C> popularityMeasure) {
		super(meaningFetcher, cacheCapacity, popularityMeasure);
	}

	@Override
	public <X extends T> Disambiguator.Disambiguation<X, C> disambiguate(List<X> sequence, Map<X, List<? extends C>> meanings, Collection<? extends C> context) {
		PhraseNode<X> root = new TermListNode<X>( sequence, 0 );
		return disambiguate(root, meanings, context);
	}

	@Override
	public <X extends T> Disambiguator.Disambiguation<X, C> disambiguate(PhraseNode<X> root, Map<X, List<? extends C>> meanings, Collection<? extends C> context) {
		Map<X, C> disambig = new HashMap<X, C>();
		List<X> sequence  = new ArrayList<X>();
		
		double totalScore = 0;
		double totalPop = 0;
		
		PhraseNode<X> node = root; 
		while ( true ) {
			Collection<? extends PhraseNode<X>> terms = node.getSuccessors();
			if ( terms==null  || terms.isEmpty() ) break;
			
			// pick the combination of term/meaning with the best combined popularity-weight 
			double bestScore = 0;
			double bestPop = 0;
			C bestMeaning = null;
			
			for (PhraseNode<X> n: terms) {
				C m = getBestMeaning(n.getTermReference(), meanings, popularityMeasure);
				if ( m==null ) continue;
				
				double pop = popularityMeasure.measure(m);
				double score = weigthCombiner.apply(pop, n.getTermReference().getWeight());
				
				if ( bestMeaning == null || bestScore<score) {
					bestScore = score;
					bestPop = pop;
					bestMeaning = m;
					node = n;
				}
			}
			
			if ( bestMeaning == null ) {
				// if no term had a best meaning, pick the term with the smallest weight to skip, and bugger on.  
				double minWeight = Double.POSITIVE_INFINITY;
				for (PhraseNode<X> n: terms) {
					double w = n.getTermReference().getWeight();
					
					if ( minWeight>w ) {
						minWeight = w;
						node = n;
					}
				}
			} else  {
				totalPop += bestPop;
				totalScore += bestScore;
				disambig.put( node.getTermReference(), bestMeaning );
				sequence.add( node.getTermReference() );
			}
		}
		
		if (disambig.size()>0) totalScore = totalScore / sequence.size(); //NOTE: treat unknown terms as having pop = 0
		
		Disambiguation<X, C> r = new Disambiguation<X, C>(disambig, sequence, totalScore, "score="+totalScore+"; pop="+totalPop);
		return r;
	}

	public boolean exploresAllSequences() {
		return false;
	}

}
