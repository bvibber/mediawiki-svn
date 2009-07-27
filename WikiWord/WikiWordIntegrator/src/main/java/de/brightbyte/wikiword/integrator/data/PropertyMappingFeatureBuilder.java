package de.brightbyte.wikiword.integrator.data;

import java.util.HashMap;
import java.util.Map;

import de.brightbyte.abstraction.PropertyAccessor;

public class PropertyMappingFeatureBuilder<R> implements FeatureBuilder<R>  {

	protected String authorityField;
	protected String idField;
	
	protected PropertyMapping<R> dataMapping;
	protected Map<String, PropertyMapping<R>> qualifierMappings = new HashMap<String, PropertyMapping<R>>();
	
	public PropertyMappingFeatureBuilder(String authorityField, String idField) {
		this(authorityField, idField, new DefaultPropertyMapping<R>());
	}

	public PropertyMappingFeatureBuilder(String authorityField, String idField, PropertyMapping<R> dataMapping) {
		if (idField==null) throw new NullPointerException();
		if (dataMapping==null) throw new NullPointerException();
		
		this.authorityField = authorityField;
		this.idField = idField;
		this.dataMapping = dataMapping;
	}

	public PropertyMapping<R> getQualifierMappings(String field) {
		return qualifierMappings.get(field);
	}
	
	public void addMapping(String field, PropertyAccessor<R, ?> accessor, PropertyMapping<R> qualifiers) {
		dataMapping.addMapping(field, accessor);
		if (qualifiers!=null) qualifierMappings.put(field, qualifiers);
	}
	
	public void addFeatures(R rec, FeatureSet features) {
		for(String field: this.fields()) {
			addFeature(rec, field, features);
		}
	}
	
	public void addFeature(R rec, String field, FeatureSet features) {
		PropertyAccessor<R, ?> accessor = getAccessor(field);
		if (accessor==null) throw new IllegalArgumentException("unknown field: "+field);
		
		PropertyMapping<R> qm = getQualifierMappings(field);
		DefaultRecord qualifiers = null;
		
		Object v = accessor.getValue(rec);
		if (v==null) return; //XXX: always?!

		if (qm != null) {
			qualifiers = new DefaultRecord();
			addFields(rec, qm, qualifiers);
		}
		
		features.addFeature(field, v, qualifiers);
	}

	protected static <R>void addFields(R rec, PropertyMapping<R> qualifierMappings, Record qualifiers) {
		for(String q: qualifierMappings.fields()) {
			PropertyAccessor<R, ?> accessor = qualifierMappings.getAccessor(q);
			Object v = accessor.getValue(rec);
			qualifiers.add(q, v);
		}
	}

	public void assertAccessor(String field) {
		dataMapping.assertAccessor(field);
	}

	public Iterable<String> fields() {
		return dataMapping.fields();
	}

	public PropertyAccessor<R, ?> getAccessor(String field) {
		return dataMapping.getAccessor(field);
	}

	public <T> T getValue(R row, String field, Class<T> type, T def) {
		return dataMapping.getValue(row, field, type, def);
	}

	public <T> T getValue(R row, String field, Class<T> type) {
		return dataMapping.getValue(row, field, type);
	}

	public boolean hasAccessor(String field) {
		return dataMapping.hasAccessor(field);
	}

	public <T> T requireValue(R row, String field, Class<T> type) {
		return dataMapping.requireValue(row, field, type);
	}

	public boolean hasSameSubject(R prev, R next) {
		if (prev==next) return true;
		if (prev==null || next==null) return false;
		
		if (authorityField!=null) {
			Object a = requireValue(prev, authorityField, Object.class);
			Object b = requireValue(next, authorityField, Object.class);
			
			if (!a.equals(b)) return false;
		}

		Object x = requireValue(prev, idField, Object.class);
		Object y = requireValue(next, idField, Object.class);
		
		if (!x.equals(y)) return false;
		
		return true;
	}
	
}
