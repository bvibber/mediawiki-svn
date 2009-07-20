package de.brightbyte.wikiword.integrator.data;

import de.brightbyte.abstraction.AbstractAccessor;
import de.brightbyte.db.DatabaseUtil;

public interface Record extends Cloneable {
	
	public static class Accessor<V> extends AbstractAccessor<Record, V> {

		public Accessor(String property, Class<V> type) {
			super(property, type);
		}

		public V getValue(Record obj) {
			Object v = obj.get(property);
			return (V)DatabaseUtil.as(v, getType());
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
	
	public Record clone();
}
