package de.brightbyte.wikiword.integrator.data.filter;

import java.util.Collection;
import java.util.regex.Pattern;

import de.brightbyte.data.filter.Filter;
import de.brightbyte.wikiword.integrator.data.FeatureSet;

/**
 *  Filter that matches any FeatureSet with a given feature, where one of the feature's values 
 *  matches a given value filter. Some default filters for feature values are given as static
 *  inner classes of FeatureSetFeatureValueFilter.
 *  
 * @author daniel
 *
 * @param <T> The type of the feature values
 */
public class FeatureSetFeatureValueFilter<T> implements Filter<FeatureSet> {
	
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

	protected String feature;
	protected Filter<T> filter;
	
	public FeatureSetFeatureValueFilter(String feature, Filter<T> filter) {
		if (feature==null) throw new NullPointerException();
		if (filter==null) throw new NullPointerException();
		this.feature = feature;
		this.filter = filter;
	}

	public boolean matches(FeatureSet fs) {
		Collection<Object> values = fs.get(feature);
		if (values==null) return false;
		
		for (Object v: values) {
			if (filter.matches((T)v)) return true;
		}
		
		return false;
	}

}
