package de.brightbyte.wikiword.integrator.data.filter;

import java.util.ArrayList;
import java.util.Collection;

import de.brightbyte.data.filter.Filter;
import de.brightbyte.wikiword.integrator.data.ConceptEntityRecord;
import de.brightbyte.wikiword.integrator.data.MappingCandidates;

/**
 *  A MappingCandidateFilter relying on a Filter&lt;ConceptEntityRecord&gt; to determin which condidates should be included.
 *  This is a generic implementation for any MappingCandidateFilter that relies on the candidates' features alone 
 *  and does not consider the properties of the mapping's subject. 
 *  
 * @author daniel
 */
public class MappingCandidateFeatureSetFilter implements MappingCandidateFilter {

	protected Filter<ConceptEntityRecord> filter;
	
	public MappingCandidateFeatureSetFilter(Filter<ConceptEntityRecord> filter) {
		if (filter==null) throw new NullPointerException();
		this.filter = filter;
	}

	public Collection<ConceptEntityRecord> filterCandidates(MappingCandidates candidates) {
		ArrayList<ConceptEntityRecord> matches = null;
		
		for (ConceptEntityRecord candidate: candidates.getCandidates()) {
			if (filter.matches(candidate)) {
				if (matches==null) matches = new ArrayList<ConceptEntityRecord>();
				matches.add(candidate);
			}
		}
		
		return matches;
	}

}
