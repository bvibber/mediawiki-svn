package de.brightbyte.wikiword.analyzer.matcher;

import java.util.regex.Pattern;

import junit.framework.TestCase;

public class PatternNameMatcherTest extends TestCase {

	public void testGetRegularExpression() {
		PatternNameMatcher matcher = new PatternNameMatcher("x|y", 0, false);
		assertEquals( "(?m:x|y)", matcher.getRegularExpression() );

		matcher = new PatternNameMatcher("x|y", Pattern.CASE_INSENSITIVE, false);
		assertEquals( "(?im:x|y)", matcher.getRegularExpression() );

		matcher = new PatternNameMatcher("x|y", Pattern.CASE_INSENSITIVE, true);
		assertEquals( "(?im:^(?:x|y)$)", matcher.getRegularExpression() );
	}

	public void testMatchesCharSequence() {
		PatternNameMatcher matcher = new PatternNameMatcher("x|y", 0, false);
		assertTrue( matcher.matches("axb") );
		assertFalse( matcher.matches("aXb") );
		assertFalse( matcher.matches("ab") );

		matcher = new PatternNameMatcher("x|y", Pattern.CASE_INSENSITIVE, false);
		assertTrue( matcher.matches("axb") );
		assertTrue( matcher.matches("aXb") );
		assertFalse( matcher.matches("ab") );

		matcher = new PatternNameMatcher("x|y", Pattern.CASE_INSENSITIVE, true);
		assertFalse( matcher.matches("axb") );
		assertFalse( matcher.matches("aXb") );
		assertFalse( matcher.matches("ab") );
		assertTrue( matcher.matches("X") );
		assertTrue( matcher.matches("a\nX\nb") );
	}

	public void testMatchesLine() {
		PatternNameMatcher matcher = new PatternNameMatcher("x|y", 0, false);
		assertTrue( matcher.matchesLine("ssss\naxb\nssssss") );
		assertFalse( matcher.matchesLine("ssss\naXb\nssssss") );
		assertFalse( matcher.matchesLine("ssss\nab\nssssss") );

		matcher = new PatternNameMatcher("x|y", Pattern.CASE_INSENSITIVE, false);
		assertTrue( matcher.matchesLine("ssss\naxb\nssssss") );
		assertTrue( matcher.matchesLine("ssss\naXb\nssssss") );
		assertFalse( matcher.matchesLine("ssss\nab\nssssss") );

		matcher = new PatternNameMatcher("x|y", Pattern.CASE_INSENSITIVE, true);
		assertFalse( matcher.matchesLine("ssss\naxb\nssssss") );
		assertFalse( matcher.matchesLine("ssss\naXb\nssssss") );
		assertFalse( matcher.matchesLine("ssss\nab\nssssss") );
		assertTrue( matcher.matchesLine("ssss\nX\nssssss") );
		assertTrue( matcher.matchesLine("ssss\nX\nssssss") );
	}

}
