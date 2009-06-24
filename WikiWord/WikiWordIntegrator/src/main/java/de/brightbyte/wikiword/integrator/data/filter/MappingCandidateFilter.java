package de.brightbyte.wikiword.integrator.data.filter;

import java.util.Collection;

import de.brightbyte.wikiword.integrator.data.FeatureSet;
import de.brightbyte.wikiword.integrator.data.MappingCandidates;

public interface MappingCandidateFilter {
	public Collection<FeatureSet> filterCandidates(MappingCandidates candidates);
}
