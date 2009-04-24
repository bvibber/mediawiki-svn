/**
 * 
 */
package de.brightbyte.wikiword.analyzer.sensor;

import de.brightbyte.wikiword.analyzer.matcher.ExactNameMatcher;

public class HasCategorySensor<V> extends HasCategoryLikeSensor<V> {
	
	public HasCategorySensor(V value, String category) {
		super(value, new ExactNameMatcher(category));
	}
}