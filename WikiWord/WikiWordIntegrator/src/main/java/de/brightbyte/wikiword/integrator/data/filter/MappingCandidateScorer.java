package de.brightbyte.wikiword.integrator.data.filter;

import de.brightbyte.wikiword.integrator.data.FeatureSet;

public interface MappingCandidateScorer {
	public int getCandidateScore(FeatureSet subject, FeatureSet candidate);
}
