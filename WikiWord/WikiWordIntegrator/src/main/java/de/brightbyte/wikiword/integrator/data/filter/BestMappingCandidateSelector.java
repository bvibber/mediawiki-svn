package de.brightbyte.wikiword.integrator.data.filter;

import de.brightbyte.wikiword.integrator.data.FeatureSet;
import de.brightbyte.wikiword.integrator.data.MappingCandidates;

public class BestMappingCandidateSelector implements MappingCandidateSelector {

	protected MappingCandidateScorer scorer; 
	
	public BestMappingCandidateSelector(MappingCandidateScorer scorer) {
		if (scorer==null) throw new NullPointerException();
		this.scorer = scorer;
	}

	public FeatureSet selectCandidate(MappingCandidates candidates) {
		int bestScore = 0;
		FeatureSet best =null;
		
		for (FeatureSet candidate: candidates.getCandidates()) {
			int score = scorer.getCandidateScore(candidates.getSubject(), candidate);

			if (best==null) {
				best = candidate;
				bestScore = score;
			}
			else if (score>bestScore) {
					best = candidate;
					bestScore = score;
			}
		}
		
		return best;
	}

}
