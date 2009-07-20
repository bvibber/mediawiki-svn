package de.brightbyte.wikiword.integrator.data.filter;

import java.util.ArrayList;
import java.util.Collection;

import de.brightbyte.data.Functor2;
import de.brightbyte.wikiword.integrator.data.ConceptEntityRecord;
import de.brightbyte.wikiword.integrator.data.ForeignEntityRecord;

/**
 * MappingCandidateScorer that accumulates the scores from multiple MappingCandidateScorers into a single score,
 * using a accumulator functor (usually the sum or maximum, as implemented by Functors.Integer.sum resp. Functors.Integer.max).
 * 
 * @author daniel
 */
public class MappingCandidateMultiScorer implements MappingCandidateScorer {

	protected Collection<MappingCandidateScorer> scorers = new ArrayList<MappingCandidateScorer>();
	protected Functor2<? extends Number, Number, Number> accumulator;
	
	public MappingCandidateMultiScorer(Functor2<? extends Number, Number, Number> accumulator, MappingCandidateScorer... scorers) {
		if (accumulator==null) throw new NullPointerException();
		this.accumulator = accumulator;
		
		for (MappingCandidateScorer scorer: scorers) {
			addScorer(scorer);
		}
	}
	
	public void addScorer(MappingCandidateScorer scorer) {
		if (scorer==null) throw new NullPointerException();
		scorers.add(scorer);
	}

	public int getCandidateScore(ForeignEntityRecord subject, ConceptEntityRecord candidate) {
		Number acc = null;
		for (MappingCandidateScorer scorer: scorers) {
			Number score = scorer.getCandidateScore(subject, candidate);
			if (acc==null) acc = score;
			else acc = accumulator.apply(acc, score);
		}
		
		return acc==null ? 0 : acc.intValue();
	}

}
