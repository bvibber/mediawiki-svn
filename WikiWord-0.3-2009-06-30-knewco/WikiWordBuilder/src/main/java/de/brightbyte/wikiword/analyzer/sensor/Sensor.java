package de.brightbyte.wikiword.analyzer.sensor;

import de.brightbyte.wikiword.analyzer.WikiPage;

/**
 * Sensor to detect if a WikiPage matches some condition. 
 * In addition, a Sensor defines a value that is associated
 * with matching the condition.
 * For example, if the Sensor matches a pattern in the page title, 
 * which indicates the type of page, then the value would represent
 * that type of page.
 */
public interface Sensor<V> {
	
	/** returns true iff the given WikiPage matches the condition
	 * this sensor represents.
	 **/
	public boolean sense(WikiPage page);
	
	/**
	 * returns the value of this sensor, that is, the value that is 
	 * associated with matching the condition this Sensor represents.
	 */
	public V getValue();
}