/**
 * 
 */
package de.brightbyte.wikiword.analyzer.sensor;

import java.util.Set;
import java.util.regex.Pattern;

import de.brightbyte.wikiword.analyzer.WikiPage;
import de.brightbyte.wikiword.analyzer.matcher.NameMatcher;
import de.brightbyte.wikiword.analyzer.matcher.PatternNameMatcher;

public class HasPropertySensor<V> extends AbstractSensor<V> {
	protected String name; 
	protected NameMatcher matcher; 
	
	public HasPropertySensor(V value, String name) {
		this(value, name, null);
	}
	
	public HasPropertySensor(V value, String name, String pattern, int flags) {
		this(value, name, new PatternNameMatcher(pattern, flags | Pattern.MULTILINE, false));
	}
	
	public HasPropertySensor(V value, String name, NameMatcher matcher) {
		super(value);
		this.matcher = matcher;
		this.name = name;
	}

	@Override
	public boolean sense(WikiPage page) {
		Set<CharSequence> vv = page.getProperties().get(name);
		
		if (matcher==null) {
			return vv!=null && !vv.isEmpty();
		}
		else {
			return matcher.matches(vv).iterator().hasNext();
		}
	}
	
	public String toString() {
		return getClass().getName() + "(" + name + " ~ " + matcher.toString() + ")";
	}
}