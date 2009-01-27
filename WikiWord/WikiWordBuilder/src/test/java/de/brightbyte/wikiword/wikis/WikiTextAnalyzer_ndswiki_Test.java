package de.brightbyte.wikiword.wikis;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzerTestBase;


public class WikiTextAnalyzer_ndswiki_Test extends WikiTextAnalyzerTestBase {

	public WikiTextAnalyzer_ndswiki_Test() {
		super("ndswiki");
	}
	
	public void testCase_Aachen() throws Throwable {
		WikiTextAnalyzer.WikiPage page = makeTestPage("Aachen");
		assertTestCase(page, "resourceType", ResourceType.REDIRECT);
	}
	
	public void testCase_Belgien() throws Throwable {
		WikiTextAnalyzer.WikiPage page = makeTestPage("Belgien");
		assertTestCase(page, "resourceType", ResourceType.ARTICLE);
		assertTestCase(page, "conceptType", ConceptType.PLACE);
		
		String definition = "Belgien, offiziell K\u00f6nigriek vun Belgien is een parlamentaarsche Monarkie, de in't Westen vun Europa liggt.";
		assertTestCase(page, "firstSentence", definition);
	}

	public static void main(String[] args) {
		run(WikiTextAnalyzer_ndswiki_Test.class, args); 
	}

}
