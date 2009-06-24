package de.brightbyte.wikiword.integrator.data.filter;

import java.util.ArrayList;
import java.util.Collection;

import de.brightbyte.data.Functor2;
import de.brightbyte.wikiword.integrator.data.FeatureSet;

public class MappingCandidateMultiScorer implements MappingCandidateScorer {

	protected Collection<MappingCandidateScorer> scorers = new ArrayList<MappingCandidateScorer>();
	protected Functor2<? extends Number, Number, Number> aggregator;
	
	public MappingCandidateMultiScorer(Functor2<? extends Number, Number, Number> aggregator, MappingCandidateScorer... scorers) {
		if (aggregator==null) throw new NullPointerException();
		this.aggregator = aggregator;
		
		for (MappingCandidateScorer scorer: scorers) {
			addScorer(scorer);
		}
	}
	
	public void addScorer(MappingCandidateScorer scorer) {
		if (scorer==null) throw new NullPointerException();
		scorers.add(scorer);
	}

	public int getCandidateScore(FeatureSet subject, FeatureSet candidate) {
		Number acc = null;
		for (MappingCandidateScorer scorer: scorers) {
			Number score = scorer.getCandidateScore(subject, candidate);
			if (acc==null) acc = score;
			else acc = aggregator.apply(acc, score);
		}
		
		return acc==null ? 0 : acc.intValue();
	}

}
