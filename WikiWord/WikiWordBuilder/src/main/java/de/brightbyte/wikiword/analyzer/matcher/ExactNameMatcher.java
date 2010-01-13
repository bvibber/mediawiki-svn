/**
 * 
 */
package de.brightbyte.wikiword.analyzer.matcher;

import java.util.Collection;
import java.util.Collections;
import java.util.Map;
import java.util.Set;
import java.util.regex.Pattern;

import de.brightbyte.data.MultiMap;
import de.brightbyte.util.StringUtils;

public class ExactNameMatcher implements NameMatcher {
	private String name;
	
	public ExactNameMatcher(String name) {
		if (name==null) throw new NullPointerException();
		this.name = name;
	}

	public String getRegularExpression() {
		return "^" + Pattern.quote(name) + "$";
	}

	public boolean matches(CharSequence s) {
		return StringUtils.equals(name, s);
	}

	public boolean matchesLine(String s) {
		int i = s.indexOf(name);
		if (i<0) return false;
		if (i>0 && s.charAt(i-1)!='\n') return false;
		
		int c = name.length();
		int j = i+c;
		
		if (j<s.length()-1 && s.charAt(j+1)!='\n') return false;
		
		return true;
	}

	public <V> Iterable<V> matches(Map<? extends CharSequence, V> m) {
		V v = m.get(name);
		if (v==null) return Collections.emptySet();
		else return Collections.singleton(v);
	}

	public <V, C extends Collection<V>> Iterable<C> matches(MultiMap<? extends CharSequence, V, C> m) {
		C c = m.get(name);
		if (c==null) return Collections.emptySet();
		else return Collections.singleton(c);
	}

	@SuppressWarnings("unchecked")
	public <V extends CharSequence> Iterable<V> matches(Set<V> values) {
		if (values.contains(name)) return Collections.singleton((V)name);
		else return Collections.emptySet();
	}
	
	public String toString() {
		return getClass().getName() + "(" + name + ")";
	}
}