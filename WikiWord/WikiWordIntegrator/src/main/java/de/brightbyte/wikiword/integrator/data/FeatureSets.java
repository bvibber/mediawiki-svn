package de.brightbyte.wikiword.integrator.data;

import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.MapLabeledVector;

public class FeatureSets {
		public static FeatureSet merge(FeatureSet... sets) {
			if (sets.length==0) return new DefaultFeatureSet();
			if (sets.length==1) return sets[0];
			
			FeatureSet f = new DefaultFeatureSet();
			
			for (int i = 0; i<sets.length; i++) {
				f.putAll(sets[i]);
			}
			
			return f;
		}
		
		public static <T>LabeledVector<T> histogram(Iterable<T> list) {
			LabeledVector<T> v = new MapLabeledVector<T>();
			
			for (T obj: list) {
				v.add(obj, 1);
			}
			
			return v;
		}

		public static <T>int count(Iterable<T> list, T item) {
			int c = 0;
			for (T obj: list) {
				if (item.equals(obj)) c++; 
			}
			
			return c;
		}
}
