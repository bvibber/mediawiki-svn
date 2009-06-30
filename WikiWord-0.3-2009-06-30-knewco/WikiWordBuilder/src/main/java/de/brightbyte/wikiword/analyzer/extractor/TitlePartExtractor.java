/**
 * 
 */
package de.brightbyte.wikiword.analyzer.extractor;

import java.util.ArrayList;
import java.util.List;
import java.util.Set;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import de.brightbyte.wikiword.Namespace;
import de.brightbyte.wikiword.analyzer.AnalyzerUtils;
import de.brightbyte.wikiword.analyzer.WikiPage;
import de.brightbyte.wikiword.analyzer.sensor.Sensor;

public class TitlePartExtractor implements ValueExtractor {
	protected Matcher matcher;
	protected int namespace;
	protected String replacement;
	protected List<Sensor<?>> conditions = null;
	protected boolean normalize= true;
	
	public TitlePartExtractor(String pattern, int flags, String replacement) {
		this(Namespace.NONE, pattern, flags, replacement);
	}
	
	public TitlePartExtractor(Pattern pattern, String replacement) {
		this(Namespace.NONE, pattern, replacement);
	}
	
	public TitlePartExtractor(int ns, String pattern, int flags, String replacement) {
		this(ns, Pattern.compile(pattern, flags), replacement);
	}
	
	public TitlePartExtractor(int ns, Pattern pattern, String replacement) {
		this.matcher = pattern.matcher("");
		this.namespace = ns;
		this.replacement = replacement;
	}

	public Set<CharSequence> extract(WikiPage page, Set<CharSequence> into) {
		if (namespace!=Namespace.NONE && namespace!=page.getNamespace()) return into;
		
		if (conditions!=null) {
			for(Sensor<?> sensor: conditions) {
				if (!sensor.sense(page)) return into;
			}
		}
		
		matcher.reset(page.getName());
		if (!matcher.matches()) return into;

		CharSequence v = matcher.replaceFirst(replacement);
		if (normalize) v = page.getAnalyzer().normalizeTitle(v);
		return AnalyzerUtils.addToSet(into, v);
	}
	
	public TitlePartExtractor addCondition(Sensor<?> sensor) { 
		if (conditions==null) conditions = new ArrayList<Sensor<?>>();
		conditions.add(sensor);
		return this;
	}
}