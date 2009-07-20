package de.brightbyte.wikiword.integrator.data.filter;

import java.util.regex.Pattern;

import de.brightbyte.data.filter.Filter;
import de.brightbyte.wikiword.integrator.data.Record;

/**
 *  Filter that matches any FeatureSet with a given feature, where one of the feature's values 
 *  matches a given value filter. Some default filters for feature values are given as static
 *  inner classes of FeatureSetFeatureValueFilter.
 *  
 * @author daniel
 *
 * @param <T> The type of the feature values
 */
public class RecordFieldValueFilter<T> implements Filter<Record> {
	
	/**
	 * Filter matching one specific value.
	 * 
	 * @author daniel
	 *
	 * @param <V> The type of the values to filter
	 */
	public static class ValueFilter<V> implements Filter<V> {
		protected V value;
		
		public ValueFilter(V value) {
			this.value = value;
		}

		public boolean matches(V obj) {
			if (obj==value) return true;
			if (obj==null) return false;
			return obj.equals(value);
		}
	}

	/**
	 * Filter matching any String or other CharSequence that matches a given regular expression. 
	 *  
	 * @author daniel
	 */
	public static class PatternFilter implements Filter<CharSequence> {
		protected Pattern pattern;
		
		public PatternFilter(String pattern, int flags) {
			this(Pattern.compile(pattern, flags));
		}
		
		public PatternFilter(Pattern pattern) {
			if (pattern==null) throw new NullPointerException();
			this.pattern = pattern;
		}

		public boolean matches(CharSequence obj) {
			if (obj==null) return false;
			return pattern.matcher(obj).matches();
		}
	}

	protected String field;
	protected Filter<T> filter;
	
	public RecordFieldValueFilter(String field, Filter<T> filter) {
		if (field==null) throw new NullPointerException();
		if (filter==null) throw new NullPointerException();
		this.field = field;
		this.filter = filter;
	}

	public boolean matches(Record r) {
		Object value = r.get(field);
		if (value==null) return false;
		
		if (value instanceof Iterable) {
			for (Object v: (Iterable)value) {
				if (filter.matches((T)v)) return true;
			}

			return false;
		} else {
			return filter.matches((T)value);
		}
	}

}
