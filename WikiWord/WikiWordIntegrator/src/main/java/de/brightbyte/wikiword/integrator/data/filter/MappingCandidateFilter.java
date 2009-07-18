package de.brightbyte.wikiword.integrator.data.filter;

import java.util.Collection;

import de.brightbyte.wikiword.integrator.data.ConceptEntityRecord;
import de.brightbyte.wikiword.integrator.data.MappingCandidates;

/**
 * Interface for filters that apply to the set of candidates in a MappingCandidates object.
 * Can be used to reduce the number of possible mappings for a given subject. 
 * 
 * @author daniel
 */
public interface MappingCandidateFilter {
	public Collection<ConceptEntityRecord> filterCandidates(MappingCandidates candidates);
}
