package de.brightbyte.wikiword.integrator.data.filter;

import java.util.Collection;

import de.brightbyte.wikiword.integrator.data.FeatureSet;
import de.brightbyte.wikiword.integrator.data.MappingCandidates;

public class UniqueMappingCandidateSelector implements MappingCandidateSelector {

	public FeatureSet selectCandidate(MappingCandidates candidates) {
		Collection<FeatureSet> cand = candidates.getCandidates();
		
		if (cand.size()==1) return cand.iterator().next();
		else return null;
	}

}
