package de.brightbyte.wikiword.integrator.data.filter;

import java.util.Collection;
import java.util.Collections;

import de.brightbyte.wikiword.integrator.data.FeatureSet;
import de.brightbyte.wikiword.integrator.data.MappingCandidates;

public class MappingCandidateSelectorFilter implements MappingCandidateFilter {

	protected MappingCandidateSelector selector;
	
	public MappingCandidateSelectorFilter(MappingCandidateSelector selector) {
		if (selector==null) throw new NullPointerException();
		this.selector = selector;
	}

	public Collection<FeatureSet> filterCandidates(MappingCandidates candidates) {
		FeatureSet selected = selector.selectCandidate(candidates);
		if (selected==null) return Collections.emptyList();
		else return Collections.singleton(selected);
	}

}
