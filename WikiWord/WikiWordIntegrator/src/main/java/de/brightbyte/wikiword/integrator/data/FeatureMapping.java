package de.brightbyte.wikiword.integrator.data;

import java.util.HashMap;
import java.util.Map;

import de.brightbyte.abstraction.PropertyAccessor;
import de.brightbyte.wikiword.integrator.FeatureSetSourceDescriptor;

public class FeatureMapping {
	protected Map<String, PropertyAccessor<FeatureSet, ?>> accessors = new HashMap<String, PropertyAccessor<FeatureSet, ?>>();
	
	public FeatureMapping() {
		
	}
	
	public void addMapping(String field, PropertyAccessor<FeatureSet, ?> accessor) {
		accessors.put(field, accessor);
	}

	public <T>void addMapping(String field, String feature, Class<T> type) {
		PropertyAccessor<FeatureSet, T> accessor = FeatureSets.fieldAccessor(feature, type);
		addMapping(field, accessor);
	}

	public <T>void addMapping(String field, FeatureSetSourceDescriptor source, String option, String defaultFeature, Class<T> type) {
		String feature = source.getTweak(option, defaultFeature);		
		if (feature!=null) addMapping(field, feature, type);
	}

	public void assertAccessor(String field) {
		if (!hasAccessor(field)) throw new IllegalArgumentException("Mapping must provide a feature name for "+field);
	}
	
	public boolean hasAccessor(String field) {
		return accessors.containsKey(field);
	}
	
	public PropertyAccessor<FeatureSet, ?> getAccessor(String field) {
		return accessors.get(field);
	}
	
	public <T> T requireValue(FeatureSet features, String field, Class<T> type) {
		T v = getValue(features, field, type);
		
		if (v==null) {
			if (!hasAccessor(field)) throw new IllegalArgumentException("no accessor for "+field);
			else throw new IllegalArgumentException("no value for "+field+" using "+getAccessor(field));
		}
		
		return v;
	}
	
	public <T> T getValue(FeatureSet features, String field, Class<T> type) {
		PropertyAccessor<FeatureSet, ?> accessor = getAccessor(field);
		if (!type.isAssignableFrom(accessor.getType())) throw new IllegalArgumentException("incompatible value type: accessor provides "+accessor.getType()+", caller requested "+type);
		
		if (accessor==null) return null; 
		
		T v = (T)accessor.getValue(features); //NOTE: this is actually safe, provided accessor.getType() isn't lying
		return v; 
	}
}
