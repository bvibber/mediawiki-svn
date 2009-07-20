package de.brightbyte.wikiword.integrator.data.filter;

import de.brightbyte.wikiword.integrator.data.ConceptEntityRecord;
import de.brightbyte.wikiword.integrator.data.MappingCandidates;

/**
 * Selects a candidate from a set of MappingCandidates. Used to determin the "best" mapping. 
 * 
 * @author daniel
 */
public interface MappingCandidateSelector {
	
	/**
	 * returns the "best" candidate from the given MappingCandidates instance, according to
	 * whatever logic the implementation chooses. May also return null to indicate that no 
	 * suitable candidate was found.
	 * 
	 * @param candidates
	 * @return the best candidate, or null to indicate that no sufficiently good candidate was found.
	 */
	public ConceptEntityRecord selectCandidate(MappingCandidates candidates);
}
