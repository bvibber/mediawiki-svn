/**
 * 
 */
package de.brightbyte.wikiword.analyzer.sensor;

import de.brightbyte.wikiword.analyzer.WikiPage;

public abstract class AbstractSensor<V> implements Sensor<V> {
	protected V value;
	
	public AbstractSensor(V value) {
		if (value==null) throw new IllegalArgumentException("value must not be null");
		this.value = value;
	}

	public abstract boolean sense(WikiPage page);
	
	public V getValue() {
		return value;
	}
	
	/*
	public AbstractSensor setValue(Object v) {
		if (value!=null) throw new IllegalStateException("value already set");
		value = v;
		return this;
	}
	*/
}