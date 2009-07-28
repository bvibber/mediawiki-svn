package de.brightbyte.wikiword.integrator.data;

import java.sql.Blob;
import java.sql.Clob;
import java.util.Collections;
import java.util.HashMap;
import java.util.Map;

import de.brightbyte.abstraction.PropertyAccessor;
import de.brightbyte.db.DatabaseUtil;

public class DefaultPropertyMapping<R> implements PropertyMapping<R>{
	protected Map<String, PropertyAccessor<R, ?>> accessors = new HashMap<String, PropertyAccessor<R, ?>>();
	
	public DefaultPropertyMapping() {
		
	}
	
	public String toString() {
		return accessors.toString();
	}
	
	public void addMapping(String field, PropertyAccessor<R, ?> accessor) {
		accessors.put(field, accessor);
	}

	public void assertAccessor(String field) {
		if (!hasAccessor(field)) throw new IllegalArgumentException("Mapping must provide a feature name for "+field);
	}
	
	public boolean hasAccessor(String field) {
		return accessors.containsKey(field);
	}
	
	public PropertyAccessor<R, ?> getAccessor(String field) {
		return accessors.get(field);
	}
	
	public <T> T requireValue(R row, String field, Class<? extends T> type) {
		T v = getValue(row, field, type);
		
		if (v==null) {
			if (!hasAccessor(field)) throw new IllegalArgumentException("no accessor for "+field);
			else throw new IllegalArgumentException("no value for "+field+" using "+getAccessor(field));
		}
		
		return v;
	}
	
	public <T> T getValue(R row, String field, Class<? extends T> type) {
		return getValue(row, field, type, null);
	}
	
	public <T> T getValue(R row, String field, Class<? extends T> type, T def) {
		
		PropertyAccessor<R, ?> accessor = getAccessor(field);
		if (accessor==null) throw new IllegalArgumentException("no accessor defined for field "+field);
		
		Object v = accessor.getValue(row); 
		if (v==null) return def;
		
		if (type==null) {
				if (v instanceof byte[] || v instanceof char[] || v instanceof Clob || v instanceof Blob) { //XXX: UGLY HACK!
					type = (Class<? extends T>)String.class;
				} else {
					type = ((PropertyAccessor<R, T>)accessor).getType();
				}
		}
		
		return DatabaseUtil.as(v, type);  //NOTE: convert if necessary //XXX: charset...
	}

	public Iterable<String> fields() {
		return Collections.unmodifiableSet(accessors.keySet());
	}
}
