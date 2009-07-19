package de.brightbyte.wikiword.integrator.data;

import java.util.Collection;
import java.util.List;

import de.brightbyte.abstraction.AbstractedAccessor;
import de.brightbyte.abstraction.Abstractor;
import de.brightbyte.abstraction.ConvertingAccessor;
import de.brightbyte.abstraction.MultiMapAbstractor;
import de.brightbyte.abstraction.PropertyAccessor;
import de.brightbyte.data.Functor;
import de.brightbyte.data.Functors;
import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.MapLabeledVector;
import de.brightbyte.data.MultiMap;
import de.brightbyte.wikiword.integrator.data.FeatureSet.Feature;

public class FeatureSets {
		public static FeatureSet merge(FeatureSet... sets) {
			if (sets.length==0) return new DefaultFeatureSet();
			if (sets.length==1) return sets[0];
			
			FeatureSet f = new DefaultFeatureSet();
			
			for (int i = 0; i<sets.length; i++) {
				f.addAll(sets[i]);
			}
			
			return f;
		}
		
		public static <T>LabeledVector<T> histogram(Collection<? extends Feature<? extends T>> list) {
			LabeledVector<T> v = new MapLabeledVector<T>();
			
			for (FeatureSet.Feature<? extends T> f: list) {
				T obj = f.getValue();
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
		
		public static final Abstractor<? extends MultiMap<String, Object, List<Object>>> abstractor = new MultiMapAbstractor<Object, List<Object>>();
		
		public static class  FirstValue<V> implements Functor<V, List<Object>> {
			public V apply(List<Object> obj) {
				if (obj==null || obj.isEmpty()) return null;
				Object v = obj.get(0);
				return (V)v;
			}
		}
		
		public static <V>PropertyAccessor<FeatureSet, V>  fieldAccessor(String field, Class<V> type) {
			if (field.startsWith("=")) { //HACK: force constant! //DOC
				return (PropertyAccessor<FeatureSet, V>)(Object)constantAccessor(field.substring(1)); //X: if V is not String, this sucks!
			}
			
			AbstractedAccessor<FeatureSet, List<Object>> accessor = 
				new AbstractedAccessor<FeatureSet, List<Object>>(field, (Abstractor<FeatureSet>)abstractor);

			Functor<V, List<Object>> aggregator;
			if (type==Integer.class) aggregator = (Functor<V, List<Object>>)(Object)Functors.Integer.sum;
			else if (type==Double.class) aggregator = (Functor<V, List<Object>>)(Object)Functors.Double.sum;
			else if (type==Number.class) aggregator = (Functor<V, List<Object>>)(Object)Functors.Double.sum;
			else aggregator = new FirstValue<V>();
			
			return new ConvertingAccessor<FeatureSet, List<Object>, V>(accessor, aggregator, type);
		}

		public static <T>PropertyAccessor<FeatureSet, T> constantAccessor(T value) {
			return (PropertyAccessor<FeatureSet, T>)(Object)new PropertyAccessor.Constant<T>(value); 
		}
}
