package de.brightbyte.wikiword.integrator.data;

import java.util.List;

import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.ValueListMultiMap;

public class DefaultFeatureSet implements FeatureSet {

	public static class DefaultFeature<V> implements  FeatureSet.Feature {
		protected V value;
		protected Record qualifiers;
		
		public DefaultFeature(V value, Record qualifiers) {
			super();
			this.value = value;
			this.qualifiers = qualifiers;
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
	
		protected ValueListMultiMap<String, FeatureSet.Feature<?>> data = new ValueListMultiMap<String, FeatureSet.Feature<?>>();
		
		public DefaultFeatureSet() {
		}
		
		public String toString() {
			return data.toString();
		}

		public boolean overlaps(FeatureSet item, String feature) {
			List<? extends FeatureSet.Feature<?>> a = getFeatures(feature);
			List<? extends FeatureSet.Feature<?>> b = item.getFeatures(feature);
			
			for (Object obj: a) {
				if (b.contains(obj)) return true;
			}
			
			return false;
		}
		
		public LabeledVector<Object> getHistogram(String key) {
			List<? extends Feature<? extends Object>> list = this.<Object>getFeatures(key);
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
			// TODO Auto-generated method stub
			
		}

		public <V> List<? extends Feature<? extends V>> getFeatures(String key) {
			List<FeatureSet.Feature<?>> features = data.get(key);
			return (List<? extends Feature<? extends V>>)features; //XXX: unmodifiable?!
		}

		public Iterable<String> keys() {
			return data.keySet();
		}
		
		
}
