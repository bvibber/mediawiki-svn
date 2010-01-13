/**
 * 
 */
package de.brightbyte.wikiword.analyzer.matcher;

import java.util.Collection;
import java.util.Map;
import java.util.Set;

import de.brightbyte.data.MultiMap;

public class AnyNameMatcher implements NameMatcher {
	
	public static final AnyNameMatcher instance = new AnyNameMatcher();
	
	public AnyNameMatcher() {
		//noop
	}

	public String getRegularExpression() {
		return ".*";
	}

	public boolean matches(CharSequence s) {
		return true;
	}

	public boolean matchesLine(String s) {
		return true;
	}

	public <V> Iterable<V> matches(Map<? extends CharSequence, V> m) {
		return m.values();
	}

	public <V, C extends Collection<V>> Iterable<C> matches(MultiMap<? extends CharSequence, V, C> m) {
		return m.values();
	}

	public <V extends CharSequence> Iterable<V> matches(Set<V> values) {
		return values;
	}
	
	@Override
	public String toString() {
		return getClass().getName() + "(*)";
	}
}