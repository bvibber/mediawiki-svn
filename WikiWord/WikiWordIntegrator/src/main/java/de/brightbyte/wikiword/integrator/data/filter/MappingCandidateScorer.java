package de.brightbyte.wikiword.integrator.data.filter;

import de.brightbyte.wikiword.integrator.data.ConceptEntityRecord;
import de.brightbyte.wikiword.integrator.data.ForeignEntityRecord;

/**
 * Determines a score value for a mapping between two given FeatureSets, the
 * subject and the candidate (object).
 * 
 * @author daniel
 */
public interface MappingCandidateScorer {
	public int getCandidateScore(ForeignEntityRecord subject, ConceptEntityRecord candidate);
}
