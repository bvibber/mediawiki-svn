package de.brightbyte.wikiword.integrator.data.filter;

import de.brightbyte.wikiword.integrator.data.FeatureSet;

/**
 * Determines a score value for a mapping between two given FeatureSets, the
 * subject and the candidate (object).
 * 
 * @author daniel
 */
public interface MappingCandidateScorer {
	public int getCandidateScore(FeatureSet subject, FeatureSet candidate);
}
