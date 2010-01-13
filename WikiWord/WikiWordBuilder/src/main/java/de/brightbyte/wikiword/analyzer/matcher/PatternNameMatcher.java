/**
 * 
 */
package de.brightbyte.wikiword.analyzer.matcher;

import java.util.regex.Matcher;
import java.util.regex.Pattern;

import de.brightbyte.util.Logger;
import de.brightbyte.wikiword.analyzer.AnalyzerUtils;

public class PatternNameMatcher extends AbstractAttributeMatcher<CharSequence> implements NameMatcher {
	static final Logger logger = Logger.getLogger(PatternNameMatcher.class); 

	protected Matcher matcher;
	protected boolean anchored;

	public PatternNameMatcher(String p, int flags, boolean anchored) {
		this(Pattern.compile(p, flags | Pattern.MULTILINE), anchored);
	}
	
	public PatternNameMatcher(Pattern p, boolean anchored) {
		this(p.matcher(""), anchored);
	}
	
	public PatternNameMatcher(Matcher matcher, boolean anchored) {
		if (matcher==null) throw new NullPointerException();
		this.matcher = matcher;
		this.anchored = anchored;
		
		if (anchored) {
			String p = matcher.pattern().pattern();
			if (p.startsWith("^") || p.endsWith("$")) {
				PatternNameMatcher.logger.warn("Anchored pattern contains anchor mark: "+p);
			}
		}
	}

	public String getRegularExpression() {
		return AnalyzerUtils.getRegularExpression(matcher.pattern(), anchored);
	}

	public boolean matchesLine(String s) {
		if ((matcher.pattern().flags() & Pattern.MULTILINE)==0) throw new UnsupportedOperationException();
		return matches(s);
	}
	
	public boolean matches(CharSequence s) {
		matcher.reset(s);
		
		if (anchored) return matcher.matches();
		else return matcher.find();
	}

	@Override
	public String toString() {
		return getClass().getName() + "(" + matcher.pattern().pattern() + ")";
	}
}