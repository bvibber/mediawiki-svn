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
	//protected boolean anchored; //screws up line matches

	public PatternNameMatcher(String p, int flags, boolean anchored) {
		this(Pattern.compile(AnalyzerUtils.getRegularExpression(p, flags| Pattern.MULTILINE, anchored), flags | Pattern.MULTILINE));
	}
	
	public PatternNameMatcher(Pattern p) {
		this(p.matcher(""));
	}
	
	public PatternNameMatcher(Matcher matcher) {
		if (matcher==null) throw new NullPointerException();
		this.matcher = matcher;
		//this.anchored = anchored;
		
		/*
		if (anchored) { //screws up line matches
			String p = matcher.pattern().pattern();
			if (p.startsWith("^") || p.endsWith("$")) {
				PatternNameMatcher.logger.warn("Anchored pattern contains anchor mark: "+p);
			}
		}*/
	}

	public String getRegularExpression() {
		return matcher.pattern().pattern();
	}

	public boolean matchesLine(String s) {
		if ((matcher.pattern().flags() & Pattern.MULTILINE)==0) throw new UnsupportedOperationException();
		return matches(s);
	}
	
	public boolean matches(CharSequence s) {
		matcher.reset(s);
		
		//if (anchored) return matcher.matches(); //screws up multi-line matches
		//else 
		return matcher.find();
	}

	@Override
	public String toString() {
		return getClass().getName() + "(" + matcher.pattern().pattern() + ")";
	}
}