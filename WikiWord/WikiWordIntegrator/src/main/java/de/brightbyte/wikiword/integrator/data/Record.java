package de.brightbyte.wikiword.integrator.data;

import java.util.Collection;
import java.util.Collections;
import java.util.List;
import java.util.Set;

import de.brightbyte.abstraction.AbstractAccessor;
import de.brightbyte.data.Aggregator;
import de.brightbyte.data.StrictAggregator;
import de.brightbyte.data.Functors;
import de.brightbyte.db.DatabaseUtil;

public interface Record extends Cloneable {

	public static class Accessor<V> extends AbstractAccessor<Record, V> {
		

		public Accessor(String property, Class<V> type) {
			super(property, type);
		}

		public V getValue(Record obj) {
			Object v = obj.get(property);
			Class t = getType();
			
			if (Collection.class.isAssignableFrom(t)) {
				if (!(v instanceof Collection)) {
					if (t.isAssignableFrom(Set.class)) v = Collections.singleton(v);
					else if (t.isAssignableFrom(List.class)) v = Collections.singletonList(v);
					else ; //TODO...
				} ; //TODO...
			}
			else  {
				if (v instanceof Collection) {
					v = collapseValue((Collection)v);
				}
				
				v = DatabaseUtil.as(v, getType());
			}
				
			return (V)v;
		}

		protected V collapseValue(Collection collection) {
			return (V)Functors.firstElement().apply((Collection<V>)collection);
		}

		public boolean isMutable() {
			return true;
		}

		public void setValue(Record obj, V value) {
			obj.set(property, value);
		}
		
	}
		
	public static class AggregatingAccessor<U, V extends U> extends Accessor<V> {
		protected Aggregator<U, V> aggregator;
		
		public AggregatingAccessor(String property, Class<V> type, Aggregator<U, V> aggregator) {
			super(property, type);
			this.aggregator = aggregator;
		}
		
		protected V collapseValue(Collection collection) {
			if (aggregator!=null) return aggregator.apply(collection);
			else return (V)Functors.firstElement().apply(collection);
		}
	}
	
	public Object set(String key, Object value);	
	public boolean add(String key, Object value);
	public int addAll(Record rec);

	public Object get(String key);
	public Object getPrimitive(String key);
	
	public Iterable<String> keys();
	
	public Record clone();
}
