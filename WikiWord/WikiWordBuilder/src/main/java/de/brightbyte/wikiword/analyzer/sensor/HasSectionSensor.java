/**
 * 
 */
package de.brightbyte.wikiword.analyzer.sensor;

import de.brightbyte.wikiword.analyzer.matcher.ExactNameMatcher;

public class HasSectionSensor<V> extends HasSectionLikeSensor<V> {
	
	public HasSectionSensor(V value, String section) {
		super(value, new ExactNameMatcher(section));
	}

}