package de.brightbyte.wikiword.analyzer;

import java.io.IOException;
import java.net.URISyntaxException;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashSet;
import java.util.List;
import java.util.Set;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import de.brightbyte.data.MultiMap;
import de.brightbyte.data.ValueListMultiMap;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.Namespace;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.analyzer.mangler.Armorer;
import de.brightbyte.wikiword.analyzer.mangler.TextArmor;
import de.brightbyte.wikiword.analyzer.sensor.HasCategoryLikeSensor;
import de.brightbyte.wikiword.analyzer.sensor.HasSectionSensor;
import de.brightbyte.wikiword.analyzer.sensor.HasTemplateSensor;
import de.brightbyte.wikiword.analyzer.sensor.TitleSensor;
import de.brightbyte.wikiword.analyzer.template.TemplateData;

/**
 * Unit tests for WikiTextAnalyzer
 */
public class WikiTextAnalyzerTest extends WikiTextAnalyzerTestBase {
	
	public WikiTextAnalyzerTest() {
		super("test");
	}

	//TODO: tests for all sensors (and manglers) !
	
	protected class TestWikiTextAnalyzer extends WikiTextAnalyzer {
		//TODO: check coverage!

		public TestWikiTextAnalyzer(PlainTextAnalyzer language) throws IOException {
			super(language);
		}
		
		protected WikiPage makeTestPage(String title, String text) {
			return makePage(0, title, text, false);
		}
		
		public void testUnarmor() {
			TextArmor armor = new TextArmor();
			armor.put(Armorer.ARMOR_MARKER_CHAR+"[1]", "one");
			armor.put(Armorer.ARMOR_MARKER_CHAR+"[2]", "two");
			armor.put(Armorer.ARMOR_MARKER_CHAR+"[3]", "three");
			
			CharSequence s = armor.unarmor("and "+Armorer.ARMOR_MARKER_CHAR+"[1] and "+Armorer.ARMOR_MARKER_CHAR+"[2] and "+Armorer.ARMOR_MARKER_CHAR+"[3] and "+Armorer.ARMOR_MARKER_CHAR+"[4]!");
			assertEquals("simple unarmor", "and one and two and three and "+Armorer.ARMOR_MARKER_CHAR+"[4]!", s.toString());
			
			armor = new TextArmor();
			armor.put(""+Armorer.ARMOR_MARKER_CHAR+"[1]", "foo");
			armor.put(""+Armorer.ARMOR_MARKER_CHAR+"[2]", "bar");
			armor.put(""+Armorer.ARMOR_MARKER_CHAR+"[3]", ""+Armorer.ARMOR_MARKER_CHAR+"[1]"+Armorer.ARMOR_MARKER_CHAR+"[2]");

			s = armor.unarmor("it's "+Armorer.ARMOR_MARKER_CHAR+"[1], "+Armorer.ARMOR_MARKER_CHAR+"[2], and "+Armorer.ARMOR_MARKER_CHAR+"[3]!");
			assertEquals("nested unarmor", "it's foo, bar, and foobar!", s.toString());			
		}

		public void testStripClutter() {
			TextArmor armor = new TextArmor();
			String text = "Hello [[Image:Hello.jpg|23px]] World {{FULLPAGENAME}}";
			
			CharSequence s = stripClutter(text, 0, "TEST", armor);
			assertEquals("inline image", "Hello  World TEST", s.toString());
			
  			text = "Hello\n\n[[Image:Cutaway view east rift zone Kilauea.gif|thumb|240px|Schnittzeichnung von der [[Caldera (Krater)]] des K\u012blauea \u00fcber die \u00f6stliche [[Grabenbruch|Riftzone]] mit dem [[Pu\u02bbu \u02bb\u014c\u02bb\u014d]] und durch die ''P\u016blama Pali'']]\nWorld\n{{FULLPAGENAME}}";
			s = stripClutter(text, 1, "TEST", armor);
			assertEquals("complex image with links in description", "Hello\n\n\nWorld\nTalk:TEST", s.toString());
			
  			text = "Hello<ref>some reference here</ref> World";
			s = stripClutter(text, 1, "TEST", armor);
			assertEquals("make sure ref tags are not damaged", "Hello<ref>some reference here</ref> World", s.toString());
			
			//TODO: comment, nowiki, pre, gallery, magic ...
			//TODO: nested links in images, also odd like [[Image:Hello.jpg|bla [[ foo]]
		}

		public void testStripBoxes() {
			String text = "Hello\n"
				+"{{test|foo=bar}}\n"
				+"World";
			
			String expected = "Hello\n\nWorld";
			
			CharSequence s = stripBoxes(text);
			assertEquals("lone template", expected, s.toString());
			
			text = "'''Wayne Roberts''' manages the [[Toronto Food Policy Council| Toronto Food Policy Council (TFPC)]], " +
					"a citizen body of 30 food activists and experts that is widely recognized for its innovative approach to " +
					"food security.<ref>See, for example, Harriet Friedmann,\"Bringing public institutions and food service " +
					"companies into the project for a local, sustainable food system in Ontario,\" Agriculture and Human Values, " +
					"24,3, March, 2007</ref> As a leading member of the [[Municipal government of Toronto|City of Toronto]]'s " +
					"Environmental Task Force, he helped develop a number of official plans for the city, including the " +
					"''Environmental Plan'' and ''Food Charter,'' adopted by [[Toronto City Council]] in 2000 and 2001 respectively." +
					"<ref>Clean, Green and Healthy: A Plan for an Environmentally Sustainable Toronto (City of Toronto, February 2000) " +
					"[http://www.toronto.ca/council/etfepfin.pdf], Toronto's Food Charter (City of Toronto, February 2000) " +
					"[http://www.toronto.ca/food_hunger/pdf/food_charter.pdf]</ref> Many ideas and projects of the [[Toronto Food " +
					"Policy Council|TFPC]] are featured in Roberts' book ''The No-Nonsense Guide to World Food'' (2008)." +
					"<ref>No-Nonsense Guide to World Food, by Wayne Roberts, New Internationalist Publications, 2008</ref> " +
					"In April 2009, under Roberts' leadership, the [[Toronto Food Policy Council|TFPC]] received the Bob Hunter " +
					"Environmental Achievement Award, given to a City of Toronto agency with a record of outstanding leadership, " +
					"for its efforts to make food an action item on the environmental agenda." +
					"<ref>http://greengta.ca/positive-news/2009-green-toronto-awards-winners</ref> The [[Toronto Food Policy " +
					"Council|TFPC]] also won honorary mention for a major award from the [[Community Food Security Coalition]] " +
					"that honors exceptional work to promote food sovereignty in October, 2009.";
			
			expected = "'''Wayne Roberts''' manages the [[Toronto Food Policy Council| Toronto Food Policy Council (TFPC)]], a " +
					"citizen body of 30 food activists and experts that is widely recognized for its innovative approach to food security. " +
					"As a leading member of the [[Municipal government of Toronto|City of Toronto]]'s Environmental Task Force, he helped " +
					"develop a number of official plans for the city, including the ''Environmental Plan'' and ''Food Charter,'' adopted by " +
					"[[Toronto City Council]] in 2000 and 2001 respectively. Many ideas and projects of the [[Toronto Food Policy Council|TFPC]] " +
					"are featured in Roberts' book ''The No-Nonsense Guide to World Food'' (2008). In April 2009, under Roberts' leadership, the " +
					"[[Toronto Food Policy Council|TFPC]] received the Bob Hunter Environmental Achievement Award, given to a City of Toronto " +
					"agency with a record of outstanding leadership, for its efforts to make food an action item on the environmental agenda. " +
					"The [[Toronto Food Policy Council|TFPC]] also won honorary mention for a major award from the [[Community Food Security " +
					"Coalition]] that honors exceptional work to promote food sovereignty in October, 2009.";
			
			s = stripBoxes(text);
			assertEquals("ref tags", expected, s.toString());
			
			//TODO: tables, div, center, nesting, complex, broken
			//TODO: tables, div, center, nesting, complex, broken
		}

		public void testStripMarkup() {
			String text;
			CharSequence s;
			String t;
						
			text = "* Interwiki:-[[w:Foo]]-\n" +
			"* Langlink:-[[fr:Foo]]-\n" +
			"* Namespace:-[[User:Foo|blubb]]-\n" +
			"* Colon:-[[Say: what?]]-\n" +
			"* Category:-[[Category:Foo]]-\n" +
			"* Plain:-[[Foo|blah]]-\nx";
			s = stripMarkup(text);

			t = "* Interwiki:--\n" +
			"* Langlink:--\n" +
			"* Namespace:--\n" +
			"* Colon:-Say: what?-\n" +
			"* Category:--\n" +
			"* Plain:-blah-\nx";
			assertEquals("qualified links", t, s.toString());
			
			text = "Hello [[World]]!";
			s = stripMarkup(text);
			assertEquals("simple link", "Hello World!", s.toString());
			
			text = "Foo &amp; Bar &stuff!";
			s = stripMarkup(text);
			assertEquals("html entities", "Foo & Bar &stuff!", s.toString());
			
			text = "Check '''''this''' out'' dude!";
			s = stripMarkup(text);
			assertEquals("emphasis", "Check this out dude!", s.toString());
			
			text = "Some <b>bold</b> stuff!";
			s = stripMarkup(text);
			assertEquals("html tags", "Some bold stuff!", s.toString());
			
			text = "Entities: &Uuml;, &#64;, &#x0040; &&;!";
			s = stripMarkup(text);
			assertEquals("html entities", "Entities: \u00dc, @, @ &&;!", s.toString());
			
			//TODO: hr, indent, bullets, etc
		}

		public void testDetermineResourceType() {
			WikiPage page = testAnalyzer.makeTestPage("Foo", "#REDIRECT [[Bar]]<!-- go there! -->");
			ResourceType pt = determineResourceType(page);
			assertSame("redirect", ResourceType.REDIRECT, pt);

			page = testAnalyzer.makeTestPage("Foo (Disambiguation)", "yadda yadda");
			pt = determineResourceType(page);
			assertSame("disambig (title)", ResourceType.DISAMBIG, pt);

			page = testAnalyzer.makeTestPage("Foo", "{{disambiguation}}\nyadda");
			pt = determineResourceType(page);
			assertSame("disambig (template)", ResourceType.DISAMBIG, pt);

			page = testAnalyzer.makeTestPage("Foo", "{{delete}}\nyadda");
			pt = determineResourceType(page);
			assertSame("bad (delete)", ResourceType.BAD, pt);

			page = testAnalyzer.makeTestPage("Foo", "yadda yadda");
			pt = determineResourceType(page);
			assertSame("plain article", ResourceType.ARTICLE, pt);

			page = testAnalyzer.makeTestPage("Foo", "yadda\n#REDIRECT\nyadda");
			pt = determineResourceType(page);
			assertSame("article like redirect", ResourceType.ARTICLE, pt);
		}

		public void testDetermineConceptType() {
			WikiPage page = testAnalyzer.makeTestPage("Foo", "yadda [[Category:Died 1956]]");
			ConceptType ct = determineConceptType(page);
			assertSame("person", ConceptType.PERSON, ct);

			page = testAnalyzer.makeTestPage("Foo", "yadda [[Category:Town in Hubbub]]");
			ct = determineConceptType(page);
			assertSame("place", ConceptType.PLACE, ct);

			page = testAnalyzer.makeTestPage("Foo", "yadda {{Taxobox\n|foo=bar}}");
			ct = determineConceptType(page);
			assertSame("lifeform", ConceptType.LIFEFORM, ct);

			page = testAnalyzer.makeTestPage("1866", "bla bla");
			ct = determineConceptType(page);
			assertSame("time", ConceptType.TIME, ct);

			page = testAnalyzer.makeTestPage("Foobar", "bla bla");
			ct = determineConceptType(page);
			assertSame("time", ConceptType.OTHER, ct);
		}

		public void testDetermineTitleSuffix() {
			String title = "Talk:Foo (Bar)"; 
			CharSequence s = determineTitleSuffix(title);
			
			assertEquals("Bar", s.toString());
		}

		public void testDetermineTitleTerms() {
			Set<String> exp = new HashSet<String>();
			exp.add("Foo");
			exp.add("foo");
			
			WikiPage page = testAnalyzer.makeTestPage("Foo", "{{DISPLAYTITLE:foo}} yadda yadda");
			Set<CharSequence> terms = determineTitleTerms(page);
			assertEquals("plain title", exp, terms);
		}

		public void testExtractRedirectLink() {
			WikiPage page = testAnalyzer.makeTestPage("Foo", "#REDIREcT [[bar]][[Category:Orf]]");
			WikiTextAnalyzer.WikiLink link = extractRedirectLink(page);

			assertEquals("Bar", link.getTarget());
		}

		public void testIsInterlanguagePrefix() {
			assertEquals("nl", true, isInterlanguagePrefix("nl"));
			assertEquals("zh-yue", true, isInterlanguagePrefix("zh-yue"));
			assertEquals("talk", false, isInterlanguagePrefix("talk"));
			assertEquals("project_talk", false, isInterlanguagePrefix("project_talk"));
			assertEquals("s", false, isInterlanguagePrefix("s"));
			assertEquals("meta", false, isInterlanguagePrefix("meta"));
		}

		public void testIsInterwikiPrefix() {
			assertEquals("nl", true, isInterwikiPrefix("nl"));
			//assertEquals("talk", false, isInterwikiPrefix("talk")); //NOTE: can't be detected here, namespace index must be checked first.
			assertEquals("project_talk", false, isInterwikiPrefix("project_talk"));
			assertEquals("s", true, isInterwikiPrefix("s"));
			assertEquals("meta", true, isInterwikiPrefix("meta"));
		}

		public void testGetNamespaceId() {
			//TODO: custom namespaces!
			assertEquals("(main)", Namespace.MAIN, getNamespaceId(""));
			assertEquals("talk", Namespace.TALK, getNamespaceId("talk"));
			assertEquals("User", Namespace.USER, getNamespaceId("User"));
			assertEquals("Template_talk", Namespace.TEMPLATE_TALK, getNamespaceId("Template_talk"));
			assertEquals("project talk", Namespace.PROJECT_TALK, getNamespaceId("project talk"));
		}

		public void testNormalizeTitle() {
			String title = " a&b c";
			CharSequence s = normalizeTitle(title);
			assertEquals("A&b_c", s.toString());
			
			//TODO: more...
		}

		public void testIsBadLinkTarget() {
			assertEquals("foo xyzzy", false, isBadLinkTarget("foo xyzzy"));
			assertEquals("(empty)", true, isBadLinkTarget(""));
			assertEquals("{{foo}}", true, isBadLinkTarget("{{foo}}"));
			assertEquals("{{{foo}}}", true, isBadLinkTarget("barf_{{{foo}}}"));
			assertEquals("barf <magic>foo</magic>", true, isBadLinkTarget("barf <magic>foo</magic>"));
			assertEquals("x|y", true, isBadLinkTarget("x|y"));
			assertEquals("..", true, isBadLinkTarget(".."));
			assertEquals("http://dummy.org/link", true, isBadLinkTarget("http://dummy.org/link"));
			assertEquals("[[bla]]x", true, isBadLinkTarget("[[bla]]x"));
			assertEquals("(tab)", true, isBadLinkTarget("bla\tblubb"));
			assertEquals("(null)", true, isBadLinkTarget("bla\0blubb"));
			assertEquals("(newline)", true, isBadLinkTarget("bla\nblubb"));
			assertEquals("(space)", false, isBadLinkTarget("bla blubb"));
			assertEquals("(umlaut)", false, isBadLinkTarget("bl\u00f6h"));
			//assertEquals("foo:bar", true, isBadLinkTarget("foo:bar"));
			//assertEquals("foo: bar", false, isBadLinkTarget("foo: bar"));
			assertEquals("..", true, isBadLinkTarget(".."));
		}

		public void testIsBadTitle() {
			assertEquals("foo xyzzy", false, isBadTitle("foo xyzzy"));
			assertEquals("(empty)", true, isBadTitle(""));
			assertEquals("{{foo}}", true, isBadTitle("{{foo}}"));
			assertEquals("{{{foo}}}", true, isBadTitle("barf_{{{foo}}}"));
			assertEquals("barf <magic>foo</magic>", true, isBadTitle("barf <magic>foo</magic>"));
			assertEquals("x|y", true, isBadTitle("x|y"));
			assertEquals("..", true, isBadTitle(".."));
			assertEquals("http://dummy.org/link", true, isBadTitle("http://dummy.org/link"));
			assertEquals("[[bla]]x", true, isBadTitle("[[bla]]x"));
			assertEquals("(tab)", true, isBadTitle("bla\tblubb"));
			assertEquals("(null)", true, isBadTitle("bla\0blubb"));
			assertEquals("(newline)", true, isBadTitle("bla\nblubb"));
			assertEquals("(space)", false, isBadTitle("bla blubb"));
			assertEquals("(umlaut)", false, isBadTitle("bl\u00f6h"));
			assertEquals("foo:bar", false, isBadTitle("foo:bar"));
			assertEquals("foo: bar", false, isBadTitle("foo: bar"));
			assertEquals("..", false, isBadTitle(".."));
		}

		public void testIsBadTerm() {
			assertEquals("(empty)", true, isBadTerm(""));
			assertEquals("(tab)", true, isBadTerm("bla\tblubb"));
			assertEquals("(null)", true, isBadTerm("bla\0blubb"));
			assertEquals("(newline)", true, isBadTerm("bla\nblubb"));
			assertEquals("(space)", false, isBadTerm("bla blubb"));
			assertEquals("(umlaut)", false, isBadTerm("bl\u00f6h"));
		}

		public void testExtractFirstParagraph() {
			String text;
			CharSequence s;
			
			text = "{{eeuwbox|eeuw=24}}\n\n" +
					"{{eeuwoverzicht|eeuw=24}}\n\n" +
					"[[Categorie:24e eeuw| ]]\n\n" +
					"[[stq:24. Jierhunnert]]";
			
			s = extractFirstParagraph(text);
			assertNull("no text, just cruft", s);
			
			text = "\n"
				+"First thing\n"
				+"\n"
				+"end.\n";
			
			s = extractFirstParagraph(text);
			assertEquals("simple paragraph", "First thing", s.toString());
			
			text = "== section ==\n"
				+"no intro\n"
				+"end.\n";
			
			s = extractFirstParagraph(text);
			assertEquals("no paragraph", "", s.toString());
			
			text = "{{head}}\n"
				//+"[[Image:something.png|thumb]]\n" //NOTE: usually stripped as clutter
				+"<div>boxy thingy</div>\n"
				+"This is [[it]],\n"
				+"you know.\n"
				+"\n"
				+"end.\n";
			
			s = extractFirstParagraph(text);
			assertEquals("hidden paragraph", "This is it,\nyou know.", s.toString());
		}

		public void testExtractFirstSentence() {
			String text = "Foo (also abc. cde.) is the 2. [[Quux]] in [[Xyzzy]]. Its not a [[Barf]].\n";
			
			CharSequence s = language.extractFirstSentence( stripMarkup( text ) );
			assertEquals("simple sentence", "Foo is the 2. Quux in Xyzzy.", s.toString());
			
			//TODO: all the nasty stuff...
		}

		public void testExtractDisambigLinks() {
			String text = "\n{{Disambiguation}}"
				+"'''Foo''' [[may]] be:\n"
				+"* A [[bla]]\n"
				+"* A [[Yoxo]] or [[foxo]] in [[baar]]\n"
				+"== Other ==\n"
				+"* [[Math]]:\n"
				+"*# A [[quux]]\n"
				+"*# A barf: see [[info:barf]]\n"
				+"* Internal [[Project:Stuff|stuff]]\n"
				+"== See Also ==\n"
				+"* see [[xxx]]\n"
				+"* and [[xxx|xyz]]\n"
				+"[[Category:TLA]]\n"
				+"end\n";
			
			List<WikiLink> exp = new ArrayList<WikiLink>();
			exp.add(new WikiTextAnalyzer.WikiLink(null, "Bla", Namespace.MAIN, "Bla", null, "bla", true, LinkMagic.NONE));
			exp.add(new WikiTextAnalyzer.WikiLink(null, "Foxo", Namespace.MAIN, "Foxo", null, "foxo", true, LinkMagic.NONE));
			exp.add(new WikiTextAnalyzer.WikiLink(null, "Quux", Namespace.MAIN, "Quux", null, "quux", true, LinkMagic.NONE));

			WikiPage page = testAnalyzer.makeTestPage("Foo", text);
			List<WikiLink> links = extractDisambigLinks(page.getTitle(), page.getCleanedText(true));
			assertEquals("simple disambig", exp, links);
		}

		public void testExtractLinks() {
			String text;
			List<WikiLink> exp;
			WikiPage page;
			List<WikiLink> links;

			text = "";
			exp = new ArrayList<WikiLink>();
			text += "Foo [[bar]]s!\n";
			text += "check [[this|that]] out, [[simple thing]]\n";
			text += "this [[pipe | pipes]], this [[ blow|blows ]]\n";
			exp.add(new WikiTextAnalyzer.WikiLink(null, "Bar", Namespace.MAIN, "Bar", null, "bars", true, LinkMagic.NONE));
			exp.add(new WikiTextAnalyzer.WikiLink(null, "This", Namespace.MAIN, "This", null, "that", false, LinkMagic.NONE));
			exp.add(new WikiTextAnalyzer.WikiLink(null, "Simple_thing", Namespace.MAIN, "Simple_thing", null, "simple thing", true, LinkMagic.NONE));
			exp.add(new WikiTextAnalyzer.WikiLink(null, "Pipe", Namespace.MAIN, "Pipe", null, "pipes", false, LinkMagic.NONE));
			exp.add(new WikiTextAnalyzer.WikiLink(null, "Blow", Namespace.MAIN, "Blow", null, "blows", false, LinkMagic.NONE));
			page = testAnalyzer.makeTestPage("Foo", text);
			links = extractLinks(page.getTitle(), page.getCleanedText(true));
			assertEquals(exp, links);

			text = "";
			exp = new ArrayList<WikiLink>();
			text += "12[[inch|\"]] or more\n";
			text += "[[first]] and [[:last]]\n";
			text += "[[give me some space|some space| and time]]\n";
			text += "[[odd#|stuff>]]\n";
			exp.add(new WikiTextAnalyzer.WikiLink(null, "Inch", Namespace.MAIN, "Inch", null, "\"", false, LinkMagic.NONE));
			exp.add(new WikiTextAnalyzer.WikiLink(null, "First", Namespace.MAIN, "First", null, "first", true, LinkMagic.NONE));
			exp.add(new WikiTextAnalyzer.WikiLink(null, "Last", Namespace.MAIN, "Last", null, "last", true, LinkMagic.NONE));
			exp.add(new WikiTextAnalyzer.WikiLink(null, "Give_me_some_space", Namespace.MAIN, "Give_me_some_space", null, "some space| and time", false, LinkMagic.NONE));
			exp.add(new WikiTextAnalyzer.WikiLink(null,  "Odd", Namespace.MAIN, "Odd", null, "stuff>", false, LinkMagic.NONE));
			page = testAnalyzer.makeTestPage("Foo", text);
			links = extractLinks(page.getTitle(), page.getCleanedText(true));
			assertEquals(exp, links);

			text = "";
			exp = new ArrayList<WikiLink>();
			text += "[[{{bad}}]]\n";
			text += "[[bad{bar]]\n";
			text += "[[bad}bar]]\n";
			text += "[[too ''bad'']]\n";
			text += "[[bad[bar]]\n";
			text += "[[bad]bar]]\n";
			text += "[[bad<bar]]\n";
			text += "[[bad>bar]]\n";
			text += "[[broken\nlink]]\n";			
			text += "[[this|''works'' {{too}}]]\n";
			text += "[[quite'ok']]\n";
			text += "[[section# link thing...]]\n";
			exp.add(new WikiTextAnalyzer.WikiLink(null, "This", Namespace.MAIN, "This", null, "works {{too}}", false, LinkMagic.NONE));
			exp.add(new WikiTextAnalyzer.WikiLink(null, "Quite'ok'", Namespace.MAIN, "Quite'ok'", null, "quite'ok'", true, LinkMagic.NONE));
			exp.add(new WikiTextAnalyzer.WikiLink(null, "Section", Namespace.MAIN, "Section", "link_thing...", "section# link thing...", true, LinkMagic.NONE));
			page = testAnalyzer.makeTestPage("Foo", text);
			links = extractLinks(page.getTitle(), page.getCleanedText(true));
			assertEquals(exp, links);

			text = "";
			exp = new ArrayList<WikiLink>();
			text += "[[section#.C3.84.C.ASX.Y.0B.26.05.4]]\n"; //encoded special chars in section (including .05 and .0B which should get dropped)
			text += "[[%C3%84%C%ASX%Y%0b%26%05%4]]\n"; //encoded special chars in target (including %05 and %0b which should get dropped)
			text += "[[URL%23Encoding]]\n"; //url-encoded link (yes the # may also be encoded, this does not act as an escape)
			text += "[[HTML&amp;entities]]\n"; //html-entities
			text += "[[no%special&stuff]]\n"; //no special stuff
			exp.add(new WikiTextAnalyzer.WikiLink(null, "Section", Namespace.MAIN, "Section", "\u00c4.C.ASX.Y&.4", "section#.C3.84.C.ASX.Y.0B.26.05.4", true, LinkMagic.NONE)); 
			exp.add(new WikiTextAnalyzer.WikiLink(null, "\u00c4%C%ASX%Y&%4", Namespace.MAIN, "\u00c4%C%ASX%Y&%4", null, "\u00c4%C%ASX%Y&%4", true, LinkMagic.NONE)); 
			exp.add(new WikiTextAnalyzer.WikiLink(null, "URL", Namespace.MAIN, "URL", "Encoding", "URL#Encoding", true, LinkMagic.NONE)); 
			exp.add(new WikiTextAnalyzer.WikiLink(null, "HTML&entities", Namespace.MAIN, "HTML&entities", null, "HTML&entities", true, LinkMagic.NONE)); 
			exp.add(new WikiTextAnalyzer.WikiLink(null, "No%special&stuff", Namespace.MAIN, "No%special&stuff", null, "no%special&stuff", true, LinkMagic.NONE)); 
			page = testAnalyzer.makeTestPage("Foo", text);
			links = extractLinks(page.getTitle(), page.getCleanedText(true));
			assertEquals(exp, links);

			exp = new ArrayList<WikiLink>();
			text = "";
			text += "\nimage: [[Image:test.jpg]], [[Image:test.jpg|thumb]], [[Image:test.jpg|the [[test]] image]], [[Image:test.jpg|the {{test}} image]];"; //NOTE: stripped as clutter
			text += "namespace: [[User:foo]], [[user talk :foo|talk]], [[:User:foo]]bar;\n";			
			exp.add(new WikiLink(null, "User:Foo", Namespace.USER, "Foo", null, "User:foo", true, LinkMagic.NONE));
			exp.add(new WikiLink(null, "User_talk:Foo", Namespace.USER_TALK, "Foo", null, "talk", false, LinkMagic.NONE));
			exp.add(new WikiLink(null, "User:Foo", Namespace.USER, "Foo", null, "User:foobar", true, LinkMagic.NONE));
			page = testAnalyzer.makeTestPage("Foo", text);
			links = extractLinks(page.getTitle(), page.getCleanedText(true));
			assertEquals(exp, links);

			text = "";
			exp = new ArrayList<WikiLink>();
			text += "[[Category:Foo| ]]\n"; //category blank sortkey
			text += "[[Category:Foo]]\n"; //category
			text += "[[:Category:Foo|Bar]]\n"; //category link
			text += "[[Category:Foo|Bar]]\n"; //category sortkey
			exp.add(new WikiTextAnalyzer.WikiLink(null, "Category:Foo", Namespace.CATEGORY, "Foo", null, "", false, LinkMagic.CATEGORY)); 
			exp.add(new WikiTextAnalyzer.WikiLink(null, "Category:Foo", Namespace.CATEGORY, "Foo", null, "Foo", true, LinkMagic.CATEGORY)); 
			exp.add(new WikiTextAnalyzer.WikiLink(null, "Category:Foo", Namespace.CATEGORY, "Foo", null, "Bar", false, LinkMagic.NONE)); 
			exp.add(new WikiTextAnalyzer.WikiLink(null, "Category:Foo", Namespace.CATEGORY, "Foo", null, "Bar", false, LinkMagic.CATEGORY)); 
			page = testAnalyzer.makeTestPage("Foo", text);
			links = extractLinks(page.getTitle(), page.getCleanedText(true));
			assertEquals(exp, links);
			
			exp = new ArrayList<WikiLink>();
			text = "category: [[Category: Z]], [[category: z|zz]], [[:Category: z]], [[:Category: z|z]];\n";
			exp.add(new WikiLink(null, "Category:Z", Namespace.CATEGORY, "Z", null, "Foo", true, LinkMagic.CATEGORY));
			exp.add(new WikiLink(null, "Category:Z", Namespace.CATEGORY, "Z", null, "zz", false, LinkMagic.CATEGORY));
			exp.add(new WikiLink(null, "Category:Z", Namespace.CATEGORY, "Z", null, "Category: z", true, LinkMagic.NONE));
			exp.add(new WikiLink(null, "Category:Z", Namespace.CATEGORY, "Z", null, "z", false, LinkMagic.NONE));
			page = testAnalyzer.makeTestPage("Foo", text);
			links = extractLinks(page.getTitle(), page.getCleanedText(true));
			assertEquals(exp, links);

			text = "";
			exp = new ArrayList<WikiLink>();
			text += "[[..]]\n"; //bad (should be caught by badLinkTarget)
			text += "[[xyz:zeug|zeug]]\n"; //interwiki
			text += "[[de:Zeug]]\n"; //interlanguage
			text += "[[:de:Zeug]]\n"; //interwiki
			exp.add(new WikiTextAnalyzer.WikiLink("xyz", "Zeug", Namespace.MAIN, "Zeug", null, "zeug", false, LinkMagic.NONE)); 
			exp.add(new WikiTextAnalyzer.WikiLink("de", "Zeug", Namespace.MAIN, "Zeug", null, "de:Zeug", true, LinkMagic.LANGUAGE)); 
			exp.add(new WikiTextAnalyzer.WikiLink("de", "Zeug", Namespace.MAIN, "Zeug", null, "de:Zeug", true, LinkMagic.NONE)); 
			page = testAnalyzer.makeTestPage("Foo", text);
			links = extractLinks(page.getTitle(), page.getCleanedText(true));
			assertEquals(exp, links);
			
			exp = new ArrayList<WikiLink>();
			text = "language: [[nl: z]], [[zh-yue: z|z]], [[:de: z|z]];\n";
			exp.add(new WikiLink("nl", "Z", Namespace.MAIN, "Z", null, "nl: z", true, LinkMagic.LANGUAGE));
			exp.add(new WikiLink("zh-yue", "Z", Namespace.MAIN, "Z", null, "z", false, LinkMagic.LANGUAGE));
			exp.add(new WikiLink("de", "Z", Namespace.MAIN, "Z", null, "z", false, LinkMagic.NONE));
			page = testAnalyzer.makeTestPage("Foo", text);
			links = extractLinks(page.getTitle(), page.getCleanedText(true));
			assertEquals(exp, links);
			
			exp = new ArrayList<WikiLink>();
			text = "interwiki: [[ixy: z]], [[ixy: z|z]], [[:ixy: z|z]];\n";
			exp.add(new WikiLink("ixy", "Z", Namespace.MAIN, "Z", null, "ixy: z", true, LinkMagic.NONE));
			exp.add(new WikiLink("ixy", "Z", Namespace.MAIN, "Z", null, "z", false, LinkMagic.NONE));
			exp.add(new WikiLink("ixy", "Z", Namespace.MAIN, "Z", null, "z", false, LinkMagic.NONE));
			page = testAnalyzer.makeTestPage("Foo", text);
			links = extractLinks(page.getTitle(), page.getCleanedText(true));
			assertEquals(exp, links);
			
			exp = new ArrayList<WikiLink>();
			text = "prefix: [[x y: z]], [[x y: z|z ]];\n";
			exp.add(new WikiLink(null, "X_y:_z", Namespace.MAIN, "X_y:_z", null, "x y: z", true, LinkMagic.NONE));
			exp.add(new WikiLink(null, "X_y:_z", Namespace.MAIN, "X_y:_z", null, "z", false, LinkMagic.NONE));
			page = testAnalyzer.makeTestPage("Foo", text);
			links = extractLinks(page.getTitle(), page.getCleanedText(true));
			assertEquals(exp, links);
		}

		public void testExtractTemplates() {
			String text = "something {{simple thing}} or {{trivial |x y}} or {{easy | y z | foo=23}}.\n"
				+"now {{nasty stuff|\n"
				+"dummy|\n"
				+"foo {{bar}} x|\n"
				+"foo {{bar{{!}}x=y}}|\n"
				+"one=<nowiki>1</nowiki>|\n"
				+"|two=2|three=3\n"
				+"|ugly={{thingy{{!}}3}} |\n"
				+"|{{x}}fugly= barf |\n"
				+"1=yummy\n"
				+"}}{{ping}}\n"
				+"plus {{#if:cruft|then|that}}\n"
				+"plus {{DEFAULTSORTKEY:x}}\n"
				+"end.\n";
			MultiMap<String, TemplateData, List<TemplateData>> exp = new ValueListMultiMap<String, TemplateData>();
			TemplateData params;
			
			params = new TemplateData("TEST");
			exp.put("Simple_thing", params );
			
			params = new TemplateData("TEST");
			params.setParameter("1", "x y");
			exp.put("Trivial", params);

			params = new TemplateData("TEST");
			params.setParameter("1", "y z");
			params.setParameter("foo", "23");
			exp.put("Easy", params);

			params = new TemplateData("TEST");
			params.setParameter("1", "yummy");
			params.setParameter("2", "foo  x");
			params.setParameter("3", "foo");
			params.setParameter("one", "1");
			params.setParameter("4", "");
			params.setParameter("two", "2");
			params.setParameter("three", "3");
			params.setParameter("ugly", "");
			params.setParameter("5", "");
			params.setParameter("fugly", "barf");
			exp.put("Nasty_stuff", params);

			params = new TemplateData("TEST");
			exp.put("Ping", params);

			params = new TemplateData("TEST");
			params.setParameter("0", "x"); 
			exp.put("DEFAULTSORT", params);

			WikiPage page = testAnalyzer.makeTestPage("Foo", text);
			MultiMap<String, TemplateData, List<TemplateData>> templates = page.getTemplates();
			assertEquals("templates", exp, templates);
		}

		public void testExtractSections() {
			Set<String> sections = extractSections("");
			assertEquals(theSet(), sections);			
			
			sections = extractSections("== foo ==");
			assertEquals(theSet("foo"), sections);			

			sections = extractSections("== foo ==\n\n== bar ==\n== baz ==\n== zoosh ==\n== orf ==");
			assertEquals(theSet("foo", "bar", "baz", "zoosh", "orf"), sections);			

			sections = extractSections("xxx\n== foo ==\nxxx\n== bar ==\nxxx");
			assertEquals(theSet("foo", "bar"), sections);			

			sections = extractSections("== baz\nxxx\n== foo ==\nxxx\n=== bar ===\nxxx\n=== frob==");
			assertEquals(theSet("foo", "bar", "= frob"), sections);			
		}
		
		public void testStripSections() {
			Matcher strip = WikiConfiguration.sectionPattern("see also|external links|references", Pattern.CASE_INSENSITIVE).matcher(""); 

			String text = "";
			String expected = text;
			CharSequence stripped = stripSections(text, strip);
			assertEquals(expected, stripped.toString());			
			
			text = "foo\n\nbar\n\n== xxx ==\n\nxxx";
			expected = text;
			stripped = stripSections(text, strip);
			assertEquals(expected, stripped.toString());			
			
			text = "foo\n\nbar\n\n== xxx ==\n\nxxx";
			expected = text;
			stripped = stripSections(text, strip);
			assertEquals(expected, stripped.toString());			
			
			text = "foo\n\n" +
					"== see also ==\n\n" +
					"aaa\n\n" +
					"bbb";
			expected = "foo\n";
			stripped = stripSections(text, strip);
			assertEquals(expected, stripped.toString());			
			
			text = "foo\n\n" +
					"== xxx ==\n\n" +
					"xxx\n\n" +
					"== see also ==\n\n" +
					"aaa\n\n" +
					"=== boof ===\n\n" +
					"bbb\n\n" +
					"= zzzz =\n\n" +
					"zzz\n\n" +
					"== external links ==\n\n" +
					"eeee\n\n" +
					"== yyy ==\n\n" +
					"yyy";
			expected = "foo\n\n" +
					"== xxx ==\n\n" +
					"xxx\n\n" +
					"= zzzz =\n\n" +
					"zzz\n\n" +
					"== yyy ==\n\n" +
					"yyy";
			stripped = stripSections(text, strip);
			assertEquals(expected, stripped.toString());			
		}
		
	} 
	
	protected static <T> List<T> theList(T... x) {
		return Arrays.asList(x);
	}
	
	protected static <T> Set<T> theSet(T... x) {
		return new HashSet<T>( Arrays.asList(x) );
	}
	
	protected TestWikiTextAnalyzer testAnalyzer;
	
	@Override
	public void setUp() throws URISyntaxException, IOException {		
		LanguageConfiguration lconfig = new LanguageConfiguration();

		corpus = new Corpus("TEST", "en", "en", "en", "en", "en", "wikipedia", null);
		PlainTextAnalyzer language = new PlainTextAnalyzer(corpus);
		language.configure(lconfig, tweaks);
		language.initialize();

		WikiConfiguration config = new WikiConfiguration();
		config.resourceTypeSensors.add( new TitleSensor<ResourceType>(ResourceType.DISAMBIG, ".*_\\(Disambiguation\\)", 0) );
		config.resourceTypeSensors.add( new HasTemplateSensor<ResourceType>(ResourceType.DISAMBIG, "Disambiguation") );
		config.resourceTypeSensors.add( new HasTemplateSensor<ResourceType>(ResourceType.BAD, "Delete") );

		config.conceptTypeSensors.add( new HasCategoryLikeSensor<ConceptType>(ConceptType.PERSON, "^Died_|^Born_", 0) );
		config.conceptTypeSensors.add( new HasCategoryLikeSensor<ConceptType>(ConceptType.NAME, "(^N|_n)ames?$", 0) );
		config.conceptTypeSensors.add( new HasCategoryLikeSensor<ConceptType>(ConceptType.PLACE, "^Town_in_|^Country_in_|^Region_in_", 0) );
		config.conceptTypeSensors.add( new HasTemplateSensor<ConceptType>(ConceptType.LIFEFORM, "Taxobox") );
		config.conceptTypeSensors.add( new TitleSensor<ConceptType>(ConceptType.TIME, "-?\\d+", 0) );
		config.conceptTypeSensors.add( new HasSectionSensor<ConceptType>(ConceptType.PERSON, "Life") );
		
		config.disambigStripSectionPattern = WikiConfiguration.sectionPattern("see also|external links", Pattern.CASE_INSENSITIVE);
		
		testAnalyzer = new TestWikiTextAnalyzer(language);
		testAnalyzer.addExtraTemplateUser(Pattern.compile(".*"), true);
		testAnalyzer.configure(config, tweaks);
		testAnalyzer.initialize(namespaces, titleCase);
		
		analyzer = testAnalyzer;
	}

	public void testDetermineConceptType() {
		testAnalyzer.testDetermineConceptType();
	}

	public void testDetermineTitleTerms() {
		testAnalyzer.testDetermineTitleTerms();
	}

	public void testDetermineResourceType() {
		testAnalyzer.testDetermineResourceType();
	}

	public void testDetermineTitleSuffix() {
		testAnalyzer.testDetermineTitleSuffix();
	}

	public void testExtractDisambigLinks() {
		testAnalyzer.testExtractDisambigLinks();
	}

	public void testExtractFirstParagraph() {
		testAnalyzer.testExtractFirstParagraph();
	}

	public void testExtractFirstSentence() {
		testAnalyzer.testExtractFirstSentence();
	}

	public void testExtractLinks() {
		testAnalyzer.testExtractLinks();
	}

	public void testExtractRedirectLink() {
		testAnalyzer.testExtractRedirectLink();
	}

	public void testExtractTemplates() {
		testAnalyzer.testExtractTemplates();
	}

	public void testGetNamespaceId() {
		testAnalyzer.testGetNamespaceId();
	}

	public void testIsBadLinkTarget() {
		testAnalyzer.testIsBadLinkTarget();
	}

	public void testIsBadTerm() {
		testAnalyzer.testIsBadTerm();
	}

	public void testIsInterlanguagePrefix() {
		testAnalyzer.testIsInterlanguagePrefix();
	}

	public void testIsInterwikiPrefix() {
		testAnalyzer.testIsInterwikiPrefix();
	}

	public void testNormalizeTitle() {
		testAnalyzer.testNormalizeTitle();
	}

	public void testStripBoxes() {
		testAnalyzer.testStripBoxes();
	}

	public void testStripClutter() {
		testAnalyzer.testStripClutter();
	}

	public void testStripMarkup() {
		testAnalyzer.testStripMarkup();
	}

	public void testUnarmor() {
		testAnalyzer.testUnarmor();
	}

	public void testExtractSections() {
		testAnalyzer.testExtractSections();
	}

	public void testStripSections() {
		testAnalyzer.testStripSections();
	}

	public static void main(String[] args) {
		run(WikiTextAnalyzerTest.class, args); 
	}
}
