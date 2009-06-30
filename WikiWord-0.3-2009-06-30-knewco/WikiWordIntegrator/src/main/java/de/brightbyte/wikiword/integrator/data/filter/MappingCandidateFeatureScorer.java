package de.brightbyte.wikiword.integrator.data.filter;

import de.brightbyte.abstraction.PropertyAccessor;
import de.brightbyte.wikiword.integrator.data.FeatureSet;
import de.brightbyte.wikiword.integrator.data.FeatureSets;

/**
 * MappingCandidateScorer that determins the score directly from each FeatureSet's features,
 * using a given accessor.
 * 
 * @author daniel
 */
public class MappingCandidateFeatureScorer implements MappingCandidateScorer {

	protected PropertyAccessor<FeatureSet, ? extends Number> accessor; 
	
	/**
	 * Creates a MappingCandidateFeatureScorer that uses the givenfeature's value as the 
	 * candidate's score.
	 * 
	 * @param field feature
	 */
	public MappingCandidateFeatureScorer(String feature) {
		this(FeatureSets.fieldAccessor(feature, Number.class));
	}
	
	/**
	 * Creates a MappingCandidateFeatureScorer that determins the score using the given accessor.
	 * @param field accessor
	 */
	public MappingCandidateFeatureScorer(PropertyAccessor<FeatureSet, ? extends Number> accessor) {
		if (accessor==null) throw new NullPointerException();
		this.accessor = accessor;
	}

	public int getCandidateScore(FeatureSet subject, FeatureSet candidate) {
		Number score = accessor.getValue(candidate);
		return score.intValue();
	}

}
