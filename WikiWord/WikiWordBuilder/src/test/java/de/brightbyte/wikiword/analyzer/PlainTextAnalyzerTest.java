package de.brightbyte.wikiword.analyzer;

import java.io.IOException;
import java.net.URISyntaxException;
import java.util.Arrays;
import java.util.HashSet;
import java.util.List;
import java.util.Set;

import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.analyzer.LanguageConfiguration;
import de.brightbyte.wikiword.analyzer.PlainTextAnalyzer;

/**
 * Unit tests for PlainTextAnalyzer
 */
public class PlainTextAnalyzerTest extends PlainTextAnalyzerTestBase {
	
	public PlainTextAnalyzerTest() {
		super("test");
	}

	//TODO: tests for all sensors (and manglers) !
	
	protected class TestPlainTextAnalyzer extends PlainTextAnalyzer {
		//TODO: check coverage!

		public TestPlainTextAnalyzer(Corpus corpus) throws IOException {
			super(corpus);
		}
		
		public void testExtractFirstSentence() {
			String text = "Foo (also abc. cde.) is the 2. Quux in Xyzzy. Its not a Barf.\n";
			
			CharSequence s = extractFirstSentence(text);
			assertEquals("simple sentence", "Foo is the 2. Quux in Xyzzy.", s.toString());
			
			//TODO: all the nasty stuff...
		}

		public void testExtractWords() {
			List<String> words = extractWords("");
			assertEquals(theList(), words);
			
			words = extractWords("foo");
			assertEquals(theList( "foo" ), words);

			words = extractWords(" foo ");
			assertEquals(theList( "foo" ), words);

			words = extractWords("---foo---");
			assertEquals(theList( "foo" ), words);

			words = extractWords("foo bar");
			assertEquals(theList( "foo", "bar" ),words);

			words = extractWords("foo bar.\n");
			assertEquals(theList( "foo", "bar" ), words);

			words = extractWords("foo-bar");
			assertEquals(theList( "foo", "bar" ), words);

			words = extractWords("23-42");
			assertEquals(theList( "23", "42" ), words);

			words = extractWords("23foo42");
			assertEquals(theList( "23", "foo", "42" ), words);
		}
				
	} 
	
	protected static <T> List<T> theList(T... x) {
		return Arrays.asList(x);
	}
	
	protected static <T> Set<T> theSet(T... x) {
		return new HashSet<T>( Arrays.asList(x) );
	}
	
	protected TestPlainTextAnalyzer testAnalyzer;
	
	@Override
	public void setUp() throws URISyntaxException, IOException {
		LanguageConfiguration config = new LanguageConfiguration();
		
		corpus = new Corpus("TEST", "generic", "generic", "generic", "generic", "xx", "generic", null);
		testAnalyzer = new TestPlainTextAnalyzer(corpus);
		testAnalyzer.configure(config, tweaks);
		testAnalyzer.initialize();
		
		analyzer = testAnalyzer;
	}

	public void testExtractFirstSentence() {
		testAnalyzer.testExtractFirstSentence();
	}

	public void testExtractWords() {
		testAnalyzer.testExtractWords();
	}

	public static void main(String[] args) {
		run(PlainTextAnalyzerTest.class, args); 
	}
}
