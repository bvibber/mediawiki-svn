package de.brightbyte.wikiword.integrator.data;

import java.util.Collection;

import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.MultiMap;
import de.brightbyte.data.ValueSetMultiMap;

public class DefaultFeatureSet implements FeatureSet {

	public static class DefaultFeature<V> implements  FeatureSet.Feature {
		protected V value;
		protected Record qualifiers;
		
		public DefaultFeature(V value, Record qualifiers) {
			super();
			this.value = value;
			this.qualifiers = qualifiers;
		}
		
		public String toString() {
			return String.valueOf(value);
		}
		
		@Override
		public int hashCode() {
			final int PRIME = 31;
			int result = 1;
			result = PRIME * result + ((qualifiers == null) ? 0 : qualifiers.hashCode());
			result = PRIME * result + ((value == null) ? 0 : value.hashCode());
			return result;
		}
		
		@Override
		public boolean equals(Object obj) {
			if (this == obj)
				return true;
			if (obj == null)
				return false;
			if (getClass() != obj.getClass())
				return false;
			final DefaultFeature other = (DefaultFeature) obj;
			if (qualifiers == null) {
				if (other.qualifiers != null)
					return false;
			} else if (!qualifiers.equals(other.qualifiers))
				return false;
			if (value == null) {
				if (other.value != null)
					return false;
			} else if (!value.equals(other.value))
				return false;
			return true;
		}

		public Record getQualifiers() {
			return qualifiers;
		}

		public V getValue() {
			return value;
		}

	
	}
	
		protected MultiMap<String, FeatureSet.Feature<?>, ? extends Collection<? extends FeatureSet.Feature<?>>> data;
		
		protected DefaultFeatureSet() {
			this(null);
		}

		public DefaultFeatureSet(MultiMap<String, FeatureSet.Feature<?>, ? extends Collection<? extends FeatureSet.Feature<?>>> data) {
			if (data==null) data = new ValueSetMultiMap<String, FeatureSet.Feature<?>>();
			this.data = data;
		}
		
		public String toString() {
			return data.toString();
		}

		public boolean overlaps(FeatureSet item, String feature) {
			Collection<? extends FeatureSet.Feature<?>> a = getFeatures(feature);
			Collection<? extends FeatureSet.Feature<?>> b = item.getFeatures(feature);
			
			for (Object obj: a) {
				if (b.contains(obj)) return true;
			}
			
			return false;
		}
		
		public LabeledVector<Object> getHistogram(String key) {
			Collection<? extends Feature<? extends Object>> list = this.<Object>getFeatures(key);
			return FeatureSets.<Object>histogram(list);
		}

		public <V>void addFeature(String key, V value, Record qualifiers) {
			if (value instanceof Object[]) {
				for(Object w: (Object[])value) {
					addFeature(key, w, qualifiers);
				}
			} if (value instanceof Iterable) {
				for(Object w: (Iterable)value) {
					addFeature(key, w, qualifiers);
				}
			} else {
				Feature f = new DefaultFeature<V>(value, qualifiers);
				data.put(key, f);
			}
		}

		public void addAll(FeatureSet other) {
			for (String k: other.keys()) {
				Collection<? extends Feature<? extends Object>> features = other.getFeatures(k);
				for (Feature<? extends Object> f: features) {
					addFeature(k, f.getValue(), f.getQualifiers());
				}
			}
		}

		public void addAll(Record rec, Record qualifiers) {
			for (String k: rec.keys()) {
				Object v = rec.get(k);
				
				if (v instanceof Collection) {
					for (Object w: (Collection)v) {
						addFeature(k, w, qualifiers);
					}
				} else {
					addFeature(k, v, qualifiers);
				}
			}
		}

		public <V> Collection<? extends Feature<? extends V>> getFeatures(String key) {
			Collection<FeatureSet.Feature<?>> features = data.get(key);
			return (Collection<? extends Feature<? extends V>>)features; //XXX: unmodifiable?!
		}

		public Iterable<String> keys() {
			return data.keySet();
		}

		@Override
		public int hashCode() {
			final int PRIME = 31;
			int result = 1;
			result = PRIME * result + ((data == null) ? 0 : data.hashCode());
			return result;
		}

		@Override
		public boolean equals(Object obj) {
			if (this == obj)
				return true;
			if (obj == null)
				return false;
			if (getClass() != obj.getClass())
				return false;
			final DefaultFeatureSet other = (DefaultFeatureSet) obj;
			if (data == null) {
				if (other.data != null)
					return false;
			} else if (!data.equals(other.data))
				return false;
			return true;
		}

		public boolean hasFeature(String key) {
			return data.containsKey(key);
		}
		
		
}
