package de.brightbyte.wikiword.integrator.data;

import java.util.List;

import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.ValueListMultiMap;

public class DefaultFeatureSet extends ValueListMultiMap<String, Object> implements FeatureSet {

		protected String nameField;
		
		public DefaultFeatureSet() {
			this(null);
		}
		
		public DefaultFeatureSet(String nameField) {
			this.nameField = nameField;
		}
		
		public String toString() {
			/*if (nameField != null) return String.valueOf(get(nameField));
			else*/ return super.toString();
		}

		public boolean overlaps(FeatureSet item, String feature) {
			List<Object> a = get(feature);
			List<Object> b = item.get(feature);
			
			for (Object obj: a) {
				if (b.contains(obj)) return true;
			}
			
			return false;
		}
		
		public LabeledVector<Object> getHistogram(String key) {
			List<Object> list = get(key);
			return FeatureSets.histogram(list);
		}

		@Override
		public boolean put(String key, Object value) {
			boolean changed = false;
			if (value instanceof Object[]) {
				for(Object w: (Object[])value) {
					changed = put(key, w) | changed;
				}
			} if (value instanceof Iterable) {
				for(Object w: (Iterable)value) {
					changed = put(key, w) | changed;
				}
			} else {
				changed = super.put(key, value);
			}
			
			return changed;
		}
		
		
}
