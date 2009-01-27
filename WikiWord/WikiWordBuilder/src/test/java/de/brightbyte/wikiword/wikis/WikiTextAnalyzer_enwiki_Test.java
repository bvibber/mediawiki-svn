package de.brightbyte.wikiword.wikis;

import java.util.HashSet;
import java.util.Set;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzerTestBase;


public class WikiTextAnalyzer_enwiki_Test extends WikiTextAnalyzerTestBase {

	public WikiTextAnalyzer_enwiki_Test() {
		super("enwiki");
	}
	
	public void testCase_Hill() throws Throwable {
		WikiTextAnalyzer.WikiPage page = makeTestPage("Hill");
		assertTestCase(page, "resourceType", ResourceType.ARTICLE);
		assertTestCase(page, "conceptType", ConceptType.OTHER);
		assertTestCase(page, "firstSentence", "A hill is a landform that extends above the surrounding terrain, in a limited area.");
		
		Set<String> categories = new HashSet<String>();
		categories.add("Hills");
		categories.add("Mountains");
		assertTestCase(page, "categories", categories);
		
		String txt = page.getPlainText(false).toString();
		
		assertTrue("plain text contains 'South'", txt.indexOf("South")>=0);
		assertTrue("plain text contains 'volcanic'", txt.indexOf("volcanic")>=0);
		assertTrue("plain text contains 'Historical'", txt.indexOf("Historical")>=0);
		assertTrue("plain text dosn't contain 'An example of a'", txt.indexOf("An example of a")<0);
		assertTrue("plain text dosn't contain 'refimprove'", txt.indexOf("refimprove")<0);
		assertTrue("plain text dosn't contain 'AAAAMAA'", txt.indexOf("AAAAMAA")<0);
		assertTrue("plain text dosn't contain 'moorland'", txt.indexOf("moorland")<0);
		
		//TODO: check more...
	}

	public static void main(String[] args) {
		run(WikiTextAnalyzer_enwiki_Test.class, args); 
	}

}
