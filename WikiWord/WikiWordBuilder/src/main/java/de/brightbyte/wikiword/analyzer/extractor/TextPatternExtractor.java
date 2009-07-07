/**
 * 
 */
package de.brightbyte.wikiword.analyzer.extractor;

import java.util.Set;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import de.brightbyte.wikiword.Namespace;
import de.brightbyte.wikiword.analyzer.AnalyzerUtils;
import de.brightbyte.wikiword.analyzer.WikiPage;

public class TextPatternExtractor implements ValueExtractor {
	protected Matcher matcher;
	protected int namespace;
	protected boolean normalize= true;
	protected int group;
	
	public TextPatternExtractor(String pattern, int flags, int group) {
		this(Namespace.NONE, pattern, flags, group);
	}
	
	public TextPatternExtractor(Pattern pattern, int group) {
		this(Namespace.NONE, pattern, group);
	}
	
	public TextPatternExtractor(int ns, String pattern, int flags, int group) {
		this(ns, Pattern.compile(pattern, flags), group);
	}
	
	public TextPatternExtractor(int ns, Pattern pattern, int group) {
		this.matcher = pattern.matcher("");
		this.namespace = ns;
		this.group = group;
	}

	public Set<CharSequence> extract(WikiPage page, Set<CharSequence> into) {
		if (namespace!=Namespace.NONE && namespace!=page.getNamespace()) return into;

		matcher.reset(page.getCleanedText(true));
		
		while (matcher.find()) {
			into = AnalyzerUtils.addToSet(into, matcher.group(group));
		}
		
		return into;
	}
	
	public String toString() {
		return matcher.pattern().toString();
	}
}