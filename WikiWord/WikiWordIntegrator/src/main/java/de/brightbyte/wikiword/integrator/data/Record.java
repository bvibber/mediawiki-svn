package de.brightbyte.wikiword.integrator.data;

import de.brightbyte.abstraction.AbstractAccessor;

public interface Record {
	
	public static class Accessor<V> extends AbstractAccessor<Record, V> {

		public Accessor(String property, Class<V> type) {
			super(property, type);
		}

		public V getValue(Record obj) {
			return (V)obj.get(property);
		}

		public boolean isMutable() {
			return true;
		}

		public void setValue(Record obj, V value) {
			obj.set(property, value);
		}
		
	}
	
	public Object set(String key, Object value);	
	public boolean add(String key, Object value);
	public int addAll(Record rec);

	public Object get(String key);
	public Object getPrimitive(String key);
	
	public Iterable<String> keys();
}
