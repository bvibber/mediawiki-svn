package de.brightbyte.wikiword.integrator.data.filter;

import de.brightbyte.abstraction.PropertyAccessor;
import de.brightbyte.wikiword.integrator.data.FeatureSet;
import de.brightbyte.wikiword.integrator.data.FeatureSets;

public class MappingCandidatePropertyScorer implements MappingCandidateScorer {

	protected PropertyAccessor<FeatureSet, ? extends Number> accessor; 
	
	public MappingCandidatePropertyScorer(String field) {
		this(FeatureSets.fieldAccessor(field, Number.class));
	}
	
	public MappingCandidatePropertyScorer(PropertyAccessor<FeatureSet, ? extends Number> accessor) {
		if (accessor==null) throw new NullPointerException();
		this.accessor = accessor;
	}

	public int getCandidateScore(FeatureSet subject, FeatureSet candidate) {
		Number score = accessor.getValue(candidate);
		return score.intValue();
	}

}
