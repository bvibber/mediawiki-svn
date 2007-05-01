package org.wikimedia.lsearch.test;

import java.util.HashSet;

import junit.framework.TestCase;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.SimpleAnalyzer;
import org.apache.lucene.search.BooleanQuery;
import org.apache.lucene.search.Query;
import org.wikimedia.lsearch.analyzers.Analyzers;
import org.wikimedia.lsearch.analyzers.WikiQueryParser;
import org.wikimedia.lsearch.analyzers.WikiQueryParser.NamespacePolicy;

public class WikiQueryParserTest extends TestCase {

	public void testParser() {
		try{
			WikiQueryParser parser = new WikiQueryParser("contents",new SimpleAnalyzer());
			Query q;
			HashSet<String> fields;

			q = parser.parseRaw("1991");
			assertEquals("",q.toString());
			
			q = parser.parseRaw("\"eggs and bacon\" OR milk");
			assertEquals("contents:\"eggs and bacon\" contents:milk",q.toString());

			q = parser.parseRaw("+eggs milk");
			assertEquals("+contents:eggs +contents:milk",q.toString());
			
			q = parser.parse("foo bar");
			assertEquals("+(+contents:foo +contents:bar) (+title:foo^2.0 +title:bar^2.0)",q.toString());
			
			q = parser.parse("+eggs milk");
			assertEquals("+(+contents:eggs +contents:milk) (+title:eggs^2.0 +title:milk^2.0)",q.toString());

			q = parser.parseRaw("eggs AND milk");
			assertEquals("+contents:eggs +contents:milk",q.toString());

			q = parser.parseRaw("+eggs category:breakfast");
			assertEquals("+contents:eggs +category:breakfast",q.toString());

			q = parser.parseRaw("help: making breakfast");
			assertEquals("+help:making +help:breakfast",q.toString());

			q = parser.parseRaw("category:(help AND pleh)");
			assertEquals("+category:help +category:pleh",q.toString());

			q = parser.parseRaw("category:(help AND (pleh -ping))");
			assertEquals("+category:help +(+category:pleh -category:ping)",q.toString());

			q = parser.parseRaw("(\"something is\" OR \"something else\") AND \"very important\"");
			assertEquals("+(contents:\"something is\" contents:\"something else\") +contents:\"very important\"",q.toString());

			q = parser.parseRaw("category:(\"\" help AND () pleh)");
			assertEquals("+category:help +category:pleh",q.toString());

			q = parser.parseRaw("šđčćždzñ");
			assertEquals("contents:sđcczdzn",q.toString());

			q = parser.parseRaw("help:making breakfast category:food");
			assertEquals("+help:making +help:breakfast +category:food",q.toString());

			q = parser.parseRaw("(help:making breakfast) food");
			assertEquals("+(+help:making +help:breakfast) +contents:food",q.toString());

			q = parser.parseRaw("(help:making (breakfast food");
			assertEquals("+help:making +(+help:breakfast +help:food)",q.toString());

			q = parser.parseRaw("(help:making (breakfast) food");
			assertEquals("+help:making +help:breakfast +help:food",q.toString());

			q = parser.parse("contents:making +breakfast category:food");
			assertEquals("+(+contents:making +contents:breakfast +category:food) (+title:making^2.0 +title:breakfast^2.0)",q.toString());
			
			// extracting fields
			fields = parser.getFields("(help:making breakfast) food");
			assertEquals(2,fields.size());
			assertTrue(fields.contains("help"));
			assertTrue(fields.contains("contents"));
			
			fields = parser.getFields("help:making breakfast");
			assertEquals(1,fields.size());
			assertTrue(fields.contains("help"));
			
			fields = parser.getFields("contents:making +breakfast category:food");
			assertEquals(2,fields.size());
			assertTrue(fields.contains("category"));
			assertTrue(fields.contains("contents"));
			
			fields = parser.getFields("contents:making +breakfast \"category:food\"");
			assertEquals(1,fields.size());
			assertTrue(fields.contains("contents"));
			
			// namespace policies
			parser = new WikiQueryParser("contents","main",new SimpleAnalyzer(), WikiQueryParser.NamespacePolicy.IGNORE);
			q = parser.parseRaw("help:making breakfast category:food");
			assertEquals("+contents:making +contents:breakfast +category:food",q.toString());
			
			parser = new WikiQueryParser("contents","main",new SimpleAnalyzer(), WikiQueryParser.NamespacePolicy.REWRITE);
			q = parser.parseRaw("help:making breakfast category:food");
			assertEquals("+namespace:12 +(+contents:making +contents:breakfast +category:food)",q.toString());
			
			q = parser.parseRaw("help:making main_talk:something");
			assertEquals("(+namespace:12 +contents:making) (+namespace:1 +contents:something)",q.toString());
			
			q = parser.parseRaw("making something");
			assertEquals("+namespace:0 +(+contents:making +contents:something)",q.toString());
			
			q = parser.parseRaw("making something category:blah");
			assertEquals("+namespace:0 +(+contents:making +contents:something +category:blah)",q.toString());
			
			q = parser.parseRaw("(help:making something category:blah) (rest category:crest)");
			assertEquals("+(+namespace:12 +(+contents:making +contents:something +category:blah)) +(+namespace:0 +(+contents:rest +category:crest))",q.toString());
			
			//TODO: fix
			//q = parser.parse("(help:making something category:blah) (rest category:crest)");
			//assertEquals("+(+namespace:12 +(+contents:making +contents:something +category:blah (+title:making^2.0 +title:something^2.0))) (+namespace:0 +(+contents:rest +category:crest (+title:rest^2.0)))",q.toString());

			// ====== English Analyzer ========
			
			parser = new WikiQueryParser("contents","main",new EnglishAnalyzer(), WikiQueryParser.NamespacePolicy.REWRITE);
			q = parser.parseRaw("main_talk:laziness");
			assertEquals("+namespace:1 +(contents:laziness contents:lazi^0.5)",q.toString());
						
			q = parser.parse("main_talk:laziness");
			assertEquals("+namespace:1 +(+(contents:laziness contents:lazi^0.5) title:laziness^2.0)",q.toString());
			
			q = parser.parseRaw("(help:beans AND category:food) OR (help_talk:orchid AND category:\"some flowers\")");
			assertEquals("(+namespace:12 +(+(contents:beans contents:bean^0.5) +category:food)) (+namespace:13 +(+contents:orchid +category:\"some flowers\"))",q.toString());
			
			q = parser.parse("(help:beans AND category:food) OR (help_talk:orchid AND category:\"some flowers\")");
			assertEquals("(+namespace:12 +(+(+(contents:beans contents:bean^0.5) title:beans^2.0) +category:food)) (+namespace:13 +(+(+contents:orchid +category:\"some flowers\") title:orchid^2.0))",q.toString());

			q = parser.parse("(help:making something category:blah) OR (rest category:crest)");
			assertEquals("(+namespace:12 +(+(+(contents:making contents:make^0.5) title:making^2.0) +(+(contents:something contents:someth^0.5) title:something^2.0) +category:blah)) (+namespace:0 +(+(+contents:rest +category:crest) title:rest^2.0))",q.toString());
			
			parser = new WikiQueryParser("contents",new EnglishAnalyzer());

			q = parser.parseRaw("laziness");
			assertEquals("contents:laziness contents:lazi^0.5",q.toString());

			q = parser.parseRaw("laziness beans");
			assertEquals("+(contents:laziness contents:lazi^0.5) +(contents:beans contents:bean^0.5)",q.toString());

			q = parser.parse("laziness");
			assertEquals("+(contents:laziness contents:lazi^0.5) title:laziness^2.0",q.toString());

			q = parser.parseRaw("(beans AND category:food) (orchid AND category:\"some flowers\")");
			assertEquals("+(+(contents:beans contents:bean^0.5) +category:food) +(+contents:orchid +category:\"some flowers\")",q.toString());

			q = parser.parseRaw("(Beans AND category:FOod) (orchID AND category:\"some FLOWERS\")");
			assertEquals("+(+(contents:beans contents:bean^0.5) +category:food) +(+contents:orchid +category:\"some flowers\")",q.toString());

			q = parser.parse("(beans AND category:food) (orchid AND category:\"some flowers\")");
			assertEquals("+(+(+(contents:beans contents:bean^0.5) title:beans^2.0) +category:food) +(+(+contents:orchid +category:\"some flowers\") title:orchid^2.0)",q.toString());

			q = parser.parse("(beans AND category:food) +(orchid AND category:\"some flowers\")");
			assertEquals("+(+(+(contents:beans contents:bean^0.5) title:beans^2.0) +category:food) +(+(+contents:orchid +category:\"some flowers\") title:orchid^2.0)",q.toString());
			
			q = parser.parse("main:cheese contents:greek");
			assertEquals("+(+(main:cheese main:chees^0.5) +contents:greek) title:greek^2.0",q.toString());
			
			fields = parser.getFields("(help:making breakfast) food");
			assertEquals(2,fields.size());
			assertTrue(fields.contains("help"));
			assertTrue(fields.contains("contents"));
			
			fields = parser.getFields("help:making breakfast");
			assertEquals(1,fields.size());
			assertTrue(fields.contains("help"));
			
			fields = parser.getFields("contents:making +breakfast category:food");
			assertEquals(2,fields.size());
			assertTrue(fields.contains("category"));
			assertTrue(fields.contains("contents"));
			
			fields = parser.getFields("contents:making +breakfast \"category:food\"");
			assertEquals(1,fields.size());
			assertTrue(fields.contains("contents"));
			
			// ==================================
			// Tests with actual params :)
			// ==================================
			Analyzer analyzer = Analyzers.getSearcherAnalyzer("en");
			parser = new WikiQueryParser("contents","main",analyzer,NamespacePolicy.LEAVE);
			q = parser.parseTwoPass("beans everyone",null);
			assertEquals("(+(contents:beans contents:bean^0.5) +(contents:everyone contents:everyon^0.5)) (+title:beans^2.0 +title:everyone^2.0)",q.toString());
			
			q = parser.parseTwoPass("(beans category:plants) OR (orchid category:flowers)",null);
			assertEquals("((+(contents:beans contents:bean^0.5) +category:plants) (+contents:orchid +category:flowers)) ((+title:beans^2.0 +category:plants) (+title:orchid^2.0 +category:flowers))",q.toString());

			q = parser.parseTwoPass("main:beans everyone",NamespacePolicy.IGNORE);
			assertEquals("(+(contents:beans contents:bean^0.5) +(contents:everyone contents:everyon^0.5)) (+title:beans^2.0 +title:everyone^2.0)",q.toString());
			
			q = parser.parseTwoPass("beans everyone",NamespacePolicy.REWRITE);
			assertEquals("(+namespace:0 +(+(contents:beans contents:bean^0.5) +(contents:everyone contents:everyon^0.5))) (+namespace:0 +(+title:beans^2.0 +title:everyone^2.0))",q.toString());
			
			q = parser.parseTwoPass("(beans category:plants) OR (orchid category:flowers)",NamespacePolicy.REWRITE);
			assertEquals("((+namespace:0 +(+(contents:beans contents:bean^0.5) +category:plants)) (+namespace:0 +(+contents:orchid +category:flowers))) ((+namespace:0 +(+title:beans^2.0 +category:plants)) (+namespace:0 +(+title:orchid^2.0 +category:flowers)))",q.toString());
			
			q = parser.parseTwoPass("main:beans main:everyone",NamespacePolicy.REWRITE);
			assertEquals("(+namespace:0 +(+(contents:beans contents:bean^0.5) +(contents:everyone contents:everyon^0.5))) (+namespace:0 +(+title:beans^2.0 +title:everyone^2.0))",q.toString());
			
			q = parser.parseTwoPass("beans everyone (category:people OR category:food)",NamespacePolicy.REWRITE);
			assertEquals("(+namespace:0 +(+(contents:beans contents:bean^0.5) +(contents:everyone contents:everyon^0.5) +(category:people category:food))) (+namespace:0 +(+title:beans^2.0 +title:everyone^2.0 +(category:people category:food)))",q.toString());
			
			q = parser.parseTwoPass("all:beans everyone",NamespacePolicy.REWRITE);
			assertEquals("(+(contents:beans contents:bean^0.5) +(contents:everyone contents:everyon^0.5)) (+title:beans^2.0 +title:everyone^2.0)",q.toString());
			
			q = parser.parseTwoPass("all:beans everyone",NamespacePolicy.IGNORE);
			assertEquals("(+(contents:beans contents:bean^0.5) +(contents:everyone contents:everyon^0.5)) (+title:beans^2.0 +title:everyone^2.0)",q.toString());			
			
			q = parser.parseTwoPass("category:\"french actresses\" category:\"born 1920\"",NamespacePolicy.REWRITE);
			assertEquals("+category:\"french actresses\" +category:\"born 1920\"",q.toString());
			
			q = parser.parseTwoPass("(all:beans everyone) OR (something else) OR (help:editing) AND (all:foo)",NamespacePolicy.REWRITE);
			assertEquals("((+(contents:beans contents:bean^0.5) +(contents:everyone contents:everyon^0.5)) (+namespace:0 +(+(contents:something contents:someth^0.5) +(contents:else contents:els^0.5))) (+namespace:12 +(contents:editing contents:edit^0.5)) +contents:foo) ((+title:beans^2.0 +title:everyone^2.0) (+namespace:0 +(+title:something^2.0 +title:else^2.0)) (+namespace:12 +title:editing^2.0) +title:foo^2.0)",q.toString());
			
			q = parser.parseTwoPass("all:beans -everyone",NamespacePolicy.IGNORE);
			assertEquals("(+(contents:beans contents:bean^0.5) -(contents:everyone)) (+title:beans^2.0 -title:everyone^2.0)",q.toString());
			
			q = parser.parseTwoPass("all:(beans -everyone)",NamespacePolicy.IGNORE);
			assertEquals("(+(contents:beans contents:bean^0.5) -(contents:everyone)) (+title:beans^2.0 -title:everyone^2.0)",q.toString());
			
			q = parser.parseTwoPass("all:anti-hero",NamespacePolicy.IGNORE);
			assertEquals("(+contents:anti +contents:hero) (+title:anti^2.0 +title:hero^2.0)",q.toString());
			
			q = parser.parseTwoPass("main:1991 category:\"olympic cities\" OR all:1990",NamespacePolicy.REWRITE);
			assertEquals("(+(+namespace:0 +(+contents:1991 +category:\"olympic cities\")) contents:1990) (+(+namespace:0 +(+title:1991^2.0 +category:\"olympic cities\")) title:1990^2.0)",q.toString());
			
			q = parser.parseTwoPass("main:1991 category:\"olympic cities\" -all:1990",NamespacePolicy.REWRITE);
			assertEquals("(+(+namespace:0 +(+contents:1991 +category:\"olympic cities\")) -contents:1990) (+(+namespace:0 +(+title:1991^2.0 +category:\"olympic cities\")) -title:1990^2.0)",q.toString());
			
			// Localization tests
			analyzer = Analyzers.getSearcherAnalyzer("sr");
			parser = new WikiQueryParser("contents","main",analyzer,NamespacePolicy.LEAVE);
			
			q = parser.parseTwoPass("all:добродошли на википедију",NamespacePolicy.IGNORE);
			assertEquals("(+(contents:добродошли contents:dobrodosli^0.5) +(contents:на contents:na^0.5) +(contents:википедију contents:vikipediju^0.5)) (+(title:добродошли^2.0 title:dobrodosli) +(title:на^2.0 title:na) +(title:википедију^2.0 title:vikipediju))",q.toString());
			
			q = parser.parseTwoPass("all:dobrodošli na šđčćž",NamespacePolicy.IGNORE);
			assertEquals("(+contents:dobrodosli +contents:na +contents:sdjccz) (+title:dobrodosli^2.0 +title:na^2.0 +title:sdjccz^2.0)",q.toString());
			
			analyzer = Analyzers.getSearcherAnalyzer("th");
			parser = new WikiQueryParser("contents","main",analyzer,NamespacePolicy.LEAVE);
			
			q = parser.parseTwoPass("ภาษาไทย",NamespacePolicy.IGNORE);
			assertEquals("(+contents:ภาษา +contents:ไทย) (+title:ภาษา^2.0 +title:ไทย^2.0)",q.toString());
			
			q = parser.parseTwoPass("help:ภาษาไทย",NamespacePolicy.REWRITE);
			assertEquals("(+namespace:12 +(+contents:ภาษา +contents:ไทย)) (+namespace:12 +(+title:ภาษา^2.0 +title:ไทย^2.0))",q.toString());
			
		} catch(Exception e){
			e.printStackTrace();
		}

	}

}
