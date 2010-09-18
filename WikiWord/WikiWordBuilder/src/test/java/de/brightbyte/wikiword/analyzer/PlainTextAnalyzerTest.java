package de.brightbyte.wikiword.analyzer;

import java.io.IOException;
import java.net.URISyntaxException;
import java.text.ParsePosition;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collections;
import java.util.HashSet;
import java.util.List;
import java.util.Set;

import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.model.PhraseOccurance;
import de.brightbyte.wikiword.model.PhraseOccuranceSet;

/**
 * Unit tests for PlainTextAnalyzer
 */
public class PlainTextAnalyzerTest extends PlainTextAnalyzerTestBase {
	
	public PlainTextAnalyzerTest() {
		super("en");
	}

	//TODO: tests for all sensors (and manglers) !
	
	protected class TestPlainTextAnalyzer extends PlainTextAnalyzer {
		//TODO: check coverage!

		public TestPlainTextAnalyzer(Corpus corpus) throws IOException {
			super(corpus);
		}
		
		public void testExtractFirstSentence() {
			String text = "Hello World";
			
			CharSequence s = extractFirstSentence(text);
			assertEquals("simple sentence", "Hello World", s.toString());

			//-------------------
			text = "Hello World.";
			
			s = extractFirstSentence(text);
			assertEquals("simple sentence", "Hello World.", s.toString());

			//-------------------
			text = "Hello World.\n";
			
			s = extractFirstSentence(text);
			assertEquals("simple sentence", "Hello World.", s.toString());

			//-------------------
			text = "Foo (also abc. cde.) is the 2. Quux in Xyzzy. Its not a Barf.\n";
			
			s = extractFirstSentence(text);
			assertEquals("simple sentence", "Foo is the 2. Quux in Xyzzy.", s.toString());
			
			//TODO: all the nasty stuff...
		}

		public void testScanNextSentence() {
			String text = "Hello World";
			
			ParsePosition pos = new ParsePosition(0);
			CharSequence s = scanNextSentence(text, pos);
			assertEquals("Hello World", s.toString());

			// ----------------------
			text = "He's John Doe, I'm Jane. Alex is not here.";
			
			pos = new ParsePosition(0);
			s = scanNextSentence(text, pos);
			assertEquals("He's John Doe, I'm Jane. ", s.toString());
			
			s = scanNextSentence(text, pos);
			assertEquals("Alex is not here.", s.toString());
			
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
			assertEquals(theList( "foo-bar" ), words);

			words = extractWords("harald's 'schlaaand");
			assertEquals(theList( "harald's", "'schlaaand" ), words);

			words = extractWords("23-42");
			assertEquals(theList( "23-42" ), words);

			words = extractWords("23foo42");
			assertEquals(theList( "23", "foo", "42" ), words);
		}

		public void testExtractNames() {
			PhraseOccuranceSet names = extractNames("", 2);
			assertEquals(0, names.size());
			assertEquals(theList(), getWordList(names));

			names = extractNames("foo", 2);
			assertEquals(theList(), getWordList(names));

			names = extractNames("Foo", 2);
			assertEquals(theList("Foo"), getWordList(names));

			names = extractNames("The", 2);
			assertEquals(theList(), getWordList(names));

			names = extractNames("The Foo", 2);
			assertEquals(theList("Foo"), getWordList(names));

			names = extractNames("The Foo Bar Bear", 2);
			assertEquals(theList("Foo Bar", "Bear"), getWordList(names));

			names = extractNames("meet the Foo of Bar on tuesday", 2);
			assertEquals(theList("Foo of Bar"), getWordList(names));

			names = extractNames("He's John Doe, I'm Jane. Alex is not here.", 2);
			assertEquals(theList("John Doe", "Jane", "Alex"), getWordList(names));

			names = extractNames("Anne-Catrin Drinkwater, Joe; Jane.", 3);
			assertEquals(theList("Anne-Catrin Drinkwater", "Joe", "Jane"), getWordList(names));
		}

		public void testExtractPhrases() {
			PhraseOccuranceSet phrases = extractPhrases("", 3, 3);
			assertEquals(0, phrases.size());
			assertEquals(theList(), getWordList(phrases.getPhrasesAt(0)));
			
			phrases = extractPhrases("foo", 3, 3);
			assertEquals(theList( "foo" ), getWordList(phrases.getPhrasesAt(0)));

			phrases = extractPhrases(" foo ", 3, 3);
			assertEquals(theList(), getWordList(phrases.getPhrasesAt(0)));
			assertEquals(theList( "foo" ), getWordList(phrases.getPhrasesAt(1)));
			assertEquals(theList( "foo" ), getWordList(phrases.getPhrasesFrom(0)));
		}
		
		public void testExtractPhrases2() {
			PhraseOccuranceSet phrases = extractPhrases("red green blue yellow black", 3, 6);
			assertEquals(theList( "red green blue", "red green", "red" ), getWordList(phrases.getPhrasesAt(0)));
			assertEquals(theList( "green blue yellow", "green blue", "green" ), getWordList(phrases.getPhrasesAt(4)));

			phrases = extractPhrases("red green blue yellow black", 5, 10);
			assertEquals(theList( "red green blue yellow black", "red green blue yellow", "red green blue", "red green", "red" ), getWordList(phrases.getPhrasesAt(0)));
			assertEquals(theList( "green blue yellow black", "green blue yellow", "green blue", "green" ), getWordList(phrases.getPhrasesAt(4)));

			phrases = extractPhrases("and red and green and blue and yellow", 3, 12);
			assertEquals(theList( "and red and green and blue",
														"and red and green and",
														"and red and green",
														"and red and",
														"and red"
														), 
											getWordList(phrases.getPhrasesAt(0)));
			assertEquals(theList( "red and green and blue",
														"red and green and",
														"red and green",
														"red and",
														"red"
														), 
											getWordList(phrases.getPhrasesAt(4)));

			phrases = extractPhrases("red green blue. yellow black", 5, 10);
			assertEquals(theList( "red green blue", "red green", "red" ), getWordList(phrases.getPhrasesAt(0)));
			assertEquals(theList( "blue" ), getWordList(phrases.getPhrasesAt(10)));
			assertEquals(theList( "yellow black", "yellow" ), getWordList(phrases.getPhrasesAt(16)));
		}
		
		public void testExtractPhrases3() {
			PhraseOccuranceSet phrases = extractPhrases("Krababbel: l'Foo-Bar", 3, 6);
			assertEquals(theList( "Krababbel"), getWordList(phrases.getPhrasesAt(0)));

			assertEquals(theList( "l'Foo-Bar", 
														"l'Foo" 
													), 
										getWordList(phrases.getPhrasesAt(11)));

			assertEquals(theList( "Foo-Bar", 
														"Foo" 
													), 
										getWordList(phrases.getPhrasesAt(13)));

			assertEquals(theList( "Bar"), 
										getWordList(phrases.getPhrasesAt(17)));

			phrases = extractPhrases("harald's 'schlaaand", 3, 3);
			assertEquals(theList( "harald's 'schlaaand", 
														"harald's", 
														"harald" 
													), 
										getWordList(phrases.getPhrasesAt(0)));

			assertEquals(theList( "'schlaaand"),  getWordList(phrases.getPhrasesAt(9)));
			assertEquals(theList("schlaaand"), getWordList(phrases.getPhrasesAt(10)));
		}

		private List<String> getWordList(List<PhraseOccurance> phrases) {
			if (phrases==null) return Collections.emptyList();
			
			List<String> words = new ArrayList<String>(phrases.size());
			
			for (PhraseOccurance phrase: phrases) {
				String w = phrase.getTerm();
				words.add(w);
			}
			
			return words;
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
	public void setUp() throws URISyntaxException, IOException, InstantiationException {
		testAnalyzer = new TestPlainTextAnalyzer(corpus);
		testAnalyzer.configure(corpus, tweaks);
		testAnalyzer.initialize();
		
		analyzer = testAnalyzer;
	}

	public void testExtractFirstSentence() {
		testAnalyzer.testExtractFirstSentence();
	}

	public void testScanNextSentence() {
		testAnalyzer.testScanNextSentence();
	}

	public void testExtractWords() {
		testAnalyzer.testExtractWords();
	}

	public void testExtractNames() {
		testAnalyzer.testExtractNames();
	}

	public void testExtractPhrases() {
		testAnalyzer.testExtractPhrases();
	}

	public void testExtractPhrases2() {
		testAnalyzer.testExtractPhrases2();
	}

	public void testExtractPhrases3() {
		testAnalyzer.testExtractPhrases3();
	}

	public static void main(String[] args) {
		run(PlainTextAnalyzerTest.class, args); 
	}
}
