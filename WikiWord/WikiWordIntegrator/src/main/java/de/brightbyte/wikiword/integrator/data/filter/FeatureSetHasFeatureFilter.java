package de.brightbyte.wikiword.integrator.data.filter;

import de.brightbyte.data.filter.Filter;
import de.brightbyte.wikiword.integrator.data.FeatureSet;

/**
 * Filter matching any FeatureSet which has a value for a given feature. That is, the condition for
 * matching is that a specific feature be defined.
 * 
 * @author daniel
 *
 */
public class FeatureSetHasFeatureFilter implements Filter<FeatureSet> {

	protected String feature;
	
	public FeatureSetHasFeatureFilter(String feature) {
		if (feature==null) throw new NullPointerException();
		this.feature = feature;
	}

	public boolean matches(FeatureSet fs) {
		return fs.getFeatures(feature) != null;
	}

}
