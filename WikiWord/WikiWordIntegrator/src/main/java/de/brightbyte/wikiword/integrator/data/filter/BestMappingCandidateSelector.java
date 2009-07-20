package de.brightbyte.wikiword.integrator.data.filter;

import de.brightbyte.wikiword.integrator.data.ConceptEntityRecord;
import de.brightbyte.wikiword.integrator.data.FeatureSet;
import de.brightbyte.wikiword.integrator.data.MappingCandidates;

/**
 * MappingCandidateSelector selecting the candidate with the highest score, as determined
 * by a given MappingCandidateScorer.
 * 
 * @author daniel
 */
public class BestMappingCandidateSelector implements MappingCandidateSelector {

	protected MappingCandidateScorer scorer; 
	
	public BestMappingCandidateSelector(MappingCandidateScorer scorer) {
		if (scorer==null) throw new NullPointerException();
		this.scorer = scorer;
	}

	public ConceptEntityRecord selectCandidate(MappingCandidates candidates) {
		int bestScore = 0;
		ConceptEntityRecord best =null;
		
		for (ConceptEntityRecord candidate: candidates.getCandidates()) {
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
