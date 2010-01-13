/**
 * 
 */
package de.brightbyte.wikiword.analyzer.sensor;

import de.brightbyte.wikiword.Namespace;
import de.brightbyte.wikiword.analyzer.WikiPage;
import de.brightbyte.wikiword.analyzer.matcher.NameMatcher;
import de.brightbyte.wikiword.analyzer.matcher.PatternNameMatcher;

public class TitleSensor<V> extends AbstractSensor<V> {
	protected NameMatcher matcher;
	protected int namespace;
	
	public TitleSensor(V value, String pattern, int flags) {
		this(value, Namespace.NONE, pattern, flags);
	}
	
	public TitleSensor(V value, NameMatcher matcher) {
		this(value, Namespace.NONE, matcher);
	}
	
	public TitleSensor(V value, int ns, String pattern, int flags) {
		this(value, ns, new PatternNameMatcher(pattern, flags, true));
	}
	
	public TitleSensor(V value, int ns, NameMatcher matcher) {
		super(value);
		this.matcher = matcher;
		this.namespace = ns;
	}

	@Override
	public boolean sense(WikiPage page) {
		if (namespace!=Namespace.NONE && namespace!=page.getNamespace()) return false;
		return matcher.matches(page.getName());
	}
	
	@Override
	public String toString() {
		return getClass().getName() + "(" + matcher.toString() + ")";
	}
}