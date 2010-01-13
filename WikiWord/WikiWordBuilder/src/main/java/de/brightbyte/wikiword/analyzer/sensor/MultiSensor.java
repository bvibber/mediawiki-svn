/**
 * 
 */
package de.brightbyte.wikiword.analyzer.sensor;

import de.brightbyte.wikiword.analyzer.WikiPage;

public class MultiSensor<V> extends AbstractSensor<V> {
	protected Sensor<V>[] sensors;

	public MultiSensor(V value, Sensor<V>... sensors) {
		super(value);
		this.sensors = sensors;
	}
	
	@Override
	public boolean sense(WikiPage page) {
		for(Sensor<V> s: sensors) {
			if (!s.sense(page)) return false;
		}
		
		return true;
	}
	
	public String toString() {
		return getClass().getName() + "(" + sensors.toString() + ")";
	}
}