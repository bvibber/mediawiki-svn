package de.brightbyte.wikiword.wikis;

import java.util.HashSet;
import java.util.Set;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzerTestBase;


public class WikiTextAnalyzer_nlwiki_Test extends WikiTextAnalyzerTestBase {

	public WikiTextAnalyzer_nlwiki_Test() {
		super("nlwiki");
	}
	
	public void testCase_Verenigde_Staten() throws Throwable {
		String definition = "De Verenigde Staten van Amerika, afgekort VS, zijn een federatie van 50 Noord-Amerikaanse staten en het district van Columbia.";
		
		WikiTextAnalyzer.WikiPage page = makeTestPage("Verenigde_Staten");
		assertTestCase(page, "firstSentence", definition);
		assertTestCase(page, "resourceType", ResourceType.ARTICLE);
		assertTestCase(page, "conceptType", ConceptType.PLACE);

		Set<String> categories = new HashSet<String>();
		categories.add("NAVO-lid");
		categories.add("Land");
		categories.add("Verenigde_Staten");
		assertTestCase(page, "categories", categories);
	}

	public void testCase_Mikolajki() throws Throwable {
        String definition = "Miko\u0142ajki is een stad in het Poolse woiwodschap Ermland-Mazuri\u00eb, gelegen in de powiat Mr\u0105gowski.";
		
		WikiTextAnalyzer.WikiPage page = makeTestPage("Mikolajki");
		assertTestCase(page, "firstSentence", definition);
		assertTestCase(page, "resourceType", ResourceType.ARTICLE);
		assertTestCase(page, "conceptType", ConceptType.PLACE);

		/*
		Set<String> categories = new HashSet<String>();
		categories.add("NAVO-lid");
		categories.add("Land");
		categories.add("Verenigde_Staten");
		assertTestCase(page, "categories", categories);
		*/
	}

	public static void main(String[] args) {
		run(WikiTextAnalyzer_nlwiki_Test.class, args); 
	}

}
