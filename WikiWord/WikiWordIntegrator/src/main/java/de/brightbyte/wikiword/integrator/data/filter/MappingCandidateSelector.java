package de.brightbyte.wikiword.integrator.data.filter;

import de.brightbyte.wikiword.integrator.data.FeatureSet;
import de.brightbyte.wikiword.integrator.data.MappingCandidates;

public interface MappingCandidateSelector {
	public FeatureSet selectCandidate(MappingCandidates candidates);
}
