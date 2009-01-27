package de.brightbyte.wikiword.wikis;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzerTestBase;

public class WikiTextAnalyzer_simplewiki_Test extends WikiTextAnalyzerTestBase {

	public WikiTextAnalyzer_simplewiki_Test() {
		super("simplewiki");
	}
	
	public void testCase_Bending() throws Throwable {
		WikiTextAnalyzer.WikiPage page = makeTestPage("Bending_(disambiguation)");
		assertTestCase(page, "resourceType", ResourceType.DISAMBIG);
	}
	
	public void testCase_Dinosaur() throws Throwable {
		WikiTextAnalyzer.WikiPage page = makeTestPage("Dinosaur");
		assertTestCase(page, "resourceType", ResourceType.ARTICLE);
		assertTestCase(page, "conceptType", ConceptType.LIFEFORM);
		
		String definition = "Dinosaurs were reptiles that lived in past times.";
		assertTestCase(page, "firstSentence", definition);
	}
	
	public void testCase_Peanuts() throws Throwable {
		WikiTextAnalyzer.WikiPage page = makeTestPage("Peanuts");
		assertTestCase(page, "resourceType", ResourceType.ARTICLE);
		assertTestCase(page, "conceptType", ConceptType.OTHER);
		
		String definition = "Peanuts was a comic strip made by Charles M. Schulz.";
		assertTestCase(page, "firstSentence", definition);
	}
	
	public void testCase_Kielce() throws Throwable {
		WikiTextAnalyzer.WikiPage page = makeTestPage("Kielce");
		assertTestCase(page, "resourceType", ResourceType.ARTICLE);
		assertTestCase(page, "conceptType", ConceptType.PLACE);
		
		String definition = "Kielce is a city in Poland in \u015awi\u0119tokrzyskie voivodship.";
		assertTestCase(page, "firstSentence", definition);
	}
	
	public void testCase_Stuttgart() throws Throwable {
		WikiTextAnalyzer.WikiPage page = makeTestPage("Stuttgart");
		assertTestCase(page, "resourceType", ResourceType.ARTICLE);
		assertTestCase(page, "conceptType", ConceptType.PLACE);
		
		String definition = "Stuttgart is a city in Germany.";
		assertTestCase(page, "firstSentence", definition);
	}

	public void testCase_Aarhus() throws Throwable {
		WikiTextAnalyzer.WikiPage page = makeTestPage("Aarhus");
		assertTestCase(page, "resourceType", ResourceType.ARTICLE);
		assertTestCase(page, "conceptType", ConceptType.PLACE);
		
		String definition = "Aarhus is the second biggest city in Denmark.";
		assertTestCase(page, "firstSentence", definition);
	}

	public static void main(String[] args) {
		run(WikiTextAnalyzer_simplewiki_Test.class, args); 
	}

}
