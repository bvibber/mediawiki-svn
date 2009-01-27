package de.brightbyte.wikiword.wikis;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;
import java.util.List;
import java.util.Map;
import java.util.Set;

import de.brightbyte.data.MultiMap;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzerTestBase;
import de.brightbyte.wikiword.analyzer.TemplateExtractor.TemplateData;


public class WikiTextAnalyzer_dewiki_Test extends WikiTextAnalyzerTestBase {

	public WikiTextAnalyzer_dewiki_Test() {
		super("dewiki");
	}
	
	public void testCase_Mont_Blanc() throws Throwable {
		WikiTextAnalyzer.WikiPage page = makeTestPage("Mont_Blanc");
		
		assertTestCase(page, "resourceType", ResourceType.REDIRECT);
		
		WikiTextAnalyzer.WikiLink r = page.getRedirect(); 
		assertEquals(r.getTarget(), "Montblanc");		
	}
	
	public void testCase_Kilauea() throws Throwable {
		String definition = "K\u012blauea ist ein aktiver Schildvulkan auf den Hawai\u02bbi-Inseln und einer der f\u00fcnf Vulkane, die zusammen die Hauptinsel Hawai\u02bbi bilden.";
		
		WikiTextAnalyzer.WikiPage page = makeTestPage("Kilauea");
		assertTestCase(page, "firstSentence", definition);

		Set<String> categories = new HashSet<String>();
		categories.add("Vulkan");
		categories.add("Berg_in_Australien_und_Ozeanien");
		categories.add("Berg_in_Hawaii");
		assertTestCase(page, "categories", categories);
		
		TemplateData infobox = new TemplateData();
		infobox.setParameter("NAME", "K\u012blauea");
		infobox.setParameter("BILD", "Kilauea map.gif");
		infobox.setParameter("BILDBESCHREIBUNG", "Lage des K\u012blauea, schematischer \u00dcberblick mit Caldera (Krater) und Riftzonen");
		infobox.setParameter("H\u00d6HE", "1247");
		infobox.setParameter("LAGE", "Hawai\u02bbi");
		infobox.setParameter("GEBIRGE", "Hawai\u02bbi-Inseln");
		infobox.setParameter("GEO-LAGE", "");
		infobox.setParameter("TYP", "Schildvulkan");
		infobox.setParameter("GESTEIN", "");
		infobox.setParameter("ALTER", "");
		infobox.setParameter("ERSTBESTEIGUNG", "");
		infobox.setParameter("BESONDERHEITEN", "");
		
		List<TemplateData> list = new ArrayList<TemplateData>();
		list.add(infobox);

		MultiMap<String, TemplateData, List<TemplateData>> templates = page.getTemplates();
		assertEquals(list, templates.get("Infobox_Berg"));

		assertTestCase(page, "resourceType", ResourceType.ARTICLE);
		assertTestCase(page, "conceptType", ConceptType.PLACE);
	}

	public void testCase_Herrenhausen_Stoecken() throws Throwable {
		String definition = "Herrenhausen-Stöcken ist der 12. Stadtbezirk in Hannover.";
		
		WikiTextAnalyzer.WikiPage page = makeTestPage("Herrenhausen-Stoecken");
		assertTestCase(page, "firstSentence", definition);
		
		assertTestCase(page, "resourceType", ResourceType.ARTICLE);
		assertTestCase(page, "conceptType", ConceptType.PLACE);

		//TODO: check more...
	}

	public void testCase_Hofstetten_Gruenau() throws Throwable {
		String definition = "Hofstetten-Grünau ist eine Marktgemeinde mit 2.622 Einwohnern im Bezirk Sankt Pölten-Land in Niederösterreich.";
		
		WikiTextAnalyzer.WikiPage page = makeTestPage("Hofstetten-Gruenau");
		assertTestCase(page, "firstSentence", definition);
		
		assertTestCase(page, "resourceType", ResourceType.ARTICLE);
		assertTestCase(page, "conceptType", ConceptType.PLACE);
		
		//TODO: check more...
	}

	public static void main(String[] args) {
		run(WikiTextAnalyzer_dewiki_Test.class, args); 
	}

}
