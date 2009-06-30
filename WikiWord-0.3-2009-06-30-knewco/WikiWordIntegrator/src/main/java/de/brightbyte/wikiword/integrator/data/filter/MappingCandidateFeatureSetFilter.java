package de.brightbyte.wikiword.integrator.data.filter;

import java.util.ArrayList;
import java.util.Collection;

import de.brightbyte.data.filter.Filter;
import de.brightbyte.wikiword.integrator.data.FeatureSet;
import de.brightbyte.wikiword.integrator.data.MappingCandidates;

/**
 *  A MappingCandidateFilter relying on a Filter&lt;FeatureSet&gt; to determin which condidates should be included.
 *  This is a generic implementation for any MappingCandidateFilter that relies on the candidates' features alone 
 *  and does not consider the properties of the mapping's subject. 
 *  
 * @author daniel
 */
public class MappingCandidateFeatureSetFilter implements MappingCandidateFilter {

	protected Filter<FeatureSet> filter;
	
	public MappingCandidateFeatureSetFilter(Filter<FeatureSet> filter) {
		if (filter==null) throw new NullPointerException();
		this.filter = filter;
	}

	public Collection<FeatureSet> filterCandidates(MappingCandidates candidates) {
		ArrayList<FeatureSet> matches = null;
		
		for (FeatureSet candidate: candidates.getCandidates()) {
			if (filter.matches(candidate)) {
				if (matches==null) matches = new ArrayList<FeatureSet>();
				matches.add(candidate);
			}
		}
		
		return matches;
	}

}
