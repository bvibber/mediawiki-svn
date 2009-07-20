package de.brightbyte.wikiword.integrator.data.filter;

import java.util.ArrayList;
import java.util.Collection;

import de.brightbyte.wikiword.integrator.data.ConceptEntityRecord;
import de.brightbyte.wikiword.integrator.data.MappingCandidates;

/**
 * MappingCandidateFilter using a MappingCandidateScorer to reduce the set of 
 * candidate mappings to those who's score pass a given threshold. 
 * 
 * @author daniel
 */
public class MappingCandidateThresholdFilter implements MappingCandidateFilter {

	protected MappingCandidateScorer scorer;
	protected int threshold;
	
	public MappingCandidateThresholdFilter(MappingCandidateScorer scorer, int threshold) {
		if (scorer==null) throw new NullPointerException();
		this.scorer = scorer;
		this.threshold = threshold;
	}

	public Collection<ConceptEntityRecord> filterCandidates(MappingCandidates candidates) {
		ArrayList<ConceptEntityRecord> res = new ArrayList<ConceptEntityRecord>();
		
		for (ConceptEntityRecord candidate: candidates.getCandidates()) {
			int score = scorer.getCandidateScore(candidates.getSubject(), candidate);
			if (score>=threshold) res.add(candidate);
		}
		
		return res;
	}

}
