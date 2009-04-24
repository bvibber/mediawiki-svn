/**
 * 
 */
package de.brightbyte.wikiword.analyzer.matcher;

import java.util.Collection;
import java.util.Map;
import java.util.Set;

import de.brightbyte.data.MultiMap;
import de.brightbyte.data.filter.Filter;

public interface AttributeMatcher<T> extends Filter<T> {
	public boolean matches(T s);
	
	public <V> Iterable<V> matches(Map<? extends T, V> m);
	public <V, C extends Collection<V>> Iterable<C> matches(MultiMap<? extends T, V, C> m);
	public <V extends T> Iterable<V> matches(Set<V> values);
}