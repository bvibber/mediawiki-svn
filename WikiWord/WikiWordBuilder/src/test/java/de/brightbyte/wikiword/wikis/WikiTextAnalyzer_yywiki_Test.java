package de.brightbyte.wikiword.wikis;

import java.util.ArrayList;
import java.util.HashSet;
import java.util.List;
import java.util.Set;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Namespace;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.analyzer.WikiPage;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzerTestBase;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer.LinkMagic;


public class WikiTextAnalyzer_yywiki_Test extends WikiTextAnalyzerTestBase {

	public WikiTextAnalyzer_yywiki_Test() {
		super("yywiki");
	}
	
	public void testCase_Yoo() throws Throwable {
		String definition = "Yoo is yoo.";
		
		WikiPage page = makeTestPage("Yoo");
		assertTestCase(page, "firstSentence", definition);
		assertTestCase(page, "resourceType", ResourceType.ARTICLE);
		assertTestCase(page, "conceptType", ConceptType.OTHER);

		List<WikiTextAnalyzer.WikiLink> links = new ArrayList<WikiTextAnalyzer.WikiLink>();
		links.add(analyzer.newLink(null, "Yar", Namespace.MAIN, "Yar", null, "Yar", true, LinkMagic.NONE));
		links.add(analyzer.newLink(null, "Category:Yoo", Namespace.CATEGORY, "Yoo", null, "*", false, LinkMagic.CATEGORY));
		links.add(analyzer.newLink(null, "Category:Yofos", Namespace.CATEGORY, "Yofos", null, "Yoo", true, LinkMagic.CATEGORY));
		links.add(analyzer.newLink("xx", "Xo", Namespace.MAIN, "Xo", null, "xx:Xo", true, LinkMagic.LANGUAGE));
		links.add(analyzer.newLink("zz", "Zoo", Namespace.MAIN, "Zoo", null, "zz:Zoo", true, LinkMagic.LANGUAGE));
		assertTestCase(page, "links", links);
		
		Set<String> categories = new HashSet<String>();
		categories.add("Yoo");
		categories.add("Yofos");
		assertTestCase(page, "categories", categories);
	}

	public static void main(String[] args) {
		run(WikiTextAnalyzer_yywiki_Test.class, args); 
	}

}
