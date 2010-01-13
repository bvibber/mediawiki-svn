/**
 * 
 */
package de.brightbyte.wikiword.analyzer.sensor;

import de.brightbyte.wikiword.analyzer.WikiPage;

public class NamespaceSensor<V> extends AbstractSensor<V> {
	
	private int namespace;

	public NamespaceSensor(V value, int ns) {
		super(value);
		this.namespace = ns;
	}

	@Override
	public boolean sense(WikiPage page) {
		return page.getNamespace() == namespace;
	}
	
	@Override
	public String toString() {
		return getClass().getName() + "(" + namespace + ")";
	}
}