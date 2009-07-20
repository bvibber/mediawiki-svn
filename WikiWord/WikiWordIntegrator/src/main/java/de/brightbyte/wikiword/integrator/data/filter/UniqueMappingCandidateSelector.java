package de.brightbyte.wikiword.integrator.data.filter;

import java.util.Collection;

import de.brightbyte.wikiword.integrator.data.ConceptEntityRecord;
import de.brightbyte.wikiword.integrator.data.MappingCandidates;

/**
 * MappingCandidateSelector that only returns unique mapping candidates - that is, it returns null
 * if there is more than once candidate. If there is only one, that candidate is returned. 
 * 
 * @author daniel
 */
public class UniqueMappingCandidateSelector implements MappingCandidateSelector {

	public ConceptEntityRecord selectCandidate(MappingCandidates candidates) {
		Collection<ConceptEntityRecord> cand = candidates.getCandidates();
		
		if (cand.size()==1) return cand.iterator().next();
		else return null;
	}

}
