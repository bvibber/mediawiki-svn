package de.brightbyte.wikiword.integrator.data;

import de.brightbyte.abstraction.PropertyAccessor;

public interface PropertyMapping<R> {
	public void addMapping(String field, PropertyAccessor<R, ?> accessor);

	public void assertAccessor(String field);
	
	public boolean hasAccessor(String field);
	
	public PropertyAccessor<R, ?> getAccessor(String field);
	
	public <T> T requireValue(R row, String field, Class<T> type);
	
	public <T> T getValue(R row, String field, Class<T> type);
	
	public <T> T getValue(R row, String field, Class<T> type, T def);

	public Iterable<String> fields();
}
