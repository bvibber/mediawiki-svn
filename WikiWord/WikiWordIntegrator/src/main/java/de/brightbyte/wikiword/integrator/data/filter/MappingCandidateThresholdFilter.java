package de.brightbyte.wikiword.integrator.data.filter;

import java.util.ArrayList;
import java.util.Collection;

import de.brightbyte.wikiword.integrator.data.FeatureSet;
import de.brightbyte.wikiword.integrator.data.MappingCandidates;

public class MappingCandidateThresholdFilter implements MappingCandidateFilter {

	protected MappingCandidateScorer scorer;
	protected int threshold;
	
	public MappingCandidateThresholdFilter(MappingCandidateScorer scorer, int threshold) {
		if (scorer==null) throw new NullPointerException();
		this.scorer = scorer;
		this.threshold = threshold;
	}

	public Collection<FeatureSet> filterCandidates(MappingCandidates candidates) {
		ArrayList<FeatureSet> res = new ArrayList<FeatureSet>();
		
		for (FeatureSet candidate: candidates.getCandidates()) {
			int score = scorer.getCandidateScore(candidates.getSubject(), candidate);
			if (score>=threshold) res.add(candidate);
		}
		
		return res;
	}

}
