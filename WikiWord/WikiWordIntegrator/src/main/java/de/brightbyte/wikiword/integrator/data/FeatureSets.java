package de.brightbyte.wikiword.integrator.data;

import java.util.List;

import de.brightbyte.abstraction.AbstractedAccessor;
import de.brightbyte.abstraction.Abstractor;
import de.brightbyte.abstraction.ConvertingAccessor;
import de.brightbyte.abstraction.MultiMapAbstractor;
import de.brightbyte.abstraction.PropertyAccessor;
import de.brightbyte.data.Functor;
import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.MapLabeledVector;
import de.brightbyte.data.MultiMap;

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
		
		public static final Abstractor<MultiMap<String, Object, List<Object>>> abstractor = new MultiMapAbstractor<Object, List<Object>>();
		
		public static class  FirstValue<V> implements Functor<V, List<Object>> {
			public V apply(List<Object> obj) {
				if (obj==null || obj.isEmpty()) return null;
				Object v = obj.get(0);
				return (V)v;
			}
		}
		
		public static <V>PropertyAccessor<FeatureSet, V>  fieldAccessor(String field, Class<V> type) {
			if (field.startsWith("=")) { //HACK: force constant! //DOC
				return (PropertyAccessor<FeatureSet, V>)(Object)new PropertyAccessor.Constant<String>(field.substring(1));
			}
			
			AbstractedAccessor<MultiMap<String, Object, List<Object>>, List<Object>> accessor = 
				new AbstractedAccessor<MultiMap<String, Object, List<Object>>, List<Object>>(field, abstractor);

			return new ConvertingAccessor<FeatureSet, List<Object>, V>(accessor, new FirstValue<V>(), type);
		}
		
}
