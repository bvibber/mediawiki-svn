package org.wikimedia.lsearch.test;

import java.net.URL;
import java.util.ArrayList;
import java.util.HashSet;

import junit.framework.TestCase;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.SimpleAnalyzer;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.search.BooleanQuery;
import org.apache.lucene.search.Query;
import org.wikimedia.lsearch.analyzers.Analyzers;
import org.wikimedia.lsearch.analyzers.FieldBuilder;
import org.wikimedia.lsearch.analyzers.FieldNameFactory;
import org.wikimedia.lsearch.analyzers.WikiQueryParser;
import org.wikimedia.lsearch.analyzers.WikiQueryParser.NamespacePolicy;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.index.WikiIndexModifier;
import org.wikimedia.lsearch.search.NamespaceFilter;

/**
 * Query Parser tests. 
 * 
 * NOTE: setup global configuration url in test-data/mwsearch.conf.test
 * before running the test. 
 * 
 * @author rainman
 *
 */
public class WikiQueryParserTest extends TestCase {

	public void testParser() {
		Configuration.setConfigFile(System.getProperty("user.dir")+"/test-data/mwsearch.conf.test");
		Configuration.open();
		WikiQueryParser.TITLE_BOOST = 2;
		WikiQueryParser.ALT_TITLE_BOOST = 6;
		WikiQueryParser.KEYWORD_BOOST = 0.05f;
		WikiQueryParser.ADD_TITLE_PHRASES = false;
		WikiIndexModifier.ALT_TITLES = 3;		
		FieldBuilder.BuilderSet bs = new FieldBuilder(IndexId.get("enwiki")).getBuilder();
		FieldNameFactory ff = new FieldNameFactory();
		try{
			WikiQueryParser parser = new WikiQueryParser(bs.getFields().contents(),new SimpleAnalyzer(),bs,null);
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

			q = parser.parseRaw("+eggs incategory:breakfast");
			assertEquals("+contents:eggs +category:breakfast",q.toString());
			
			q = parser.parseRaw("+eggs incategory:\"two_words\"");
			assertEquals("+contents:eggs +category:\"two words\"",q.toString());
			
			q = parser.parseRaw("+eggs incategory:\"two words\"");
			assertEquals("+contents:eggs +category:\"two words\"",q.toString());

			q = parser.parseRaw("help:making breakfast");
			assertEquals("+help:making +help:breakfast",q.toString());

			q = parser.parseRaw("incategory:(help AND pleh)");
			assertEquals("+category:help +category:pleh",q.toString());

			q = parser.parseRaw("incategory:(help AND (pleh -ping))");
			assertEquals("+category:help +(+category:pleh -category:ping)",q.toString());

			q = parser.parseRaw("(\"something is\" OR \"something else\") AND \"very important\"");
			assertEquals("+(contents:\"something is\" contents:\"something else\") +contents:\"very important\"",q.toString());

			q = parser.parseRaw("incategory:(\"\" help AND () pleh)");
			assertEquals("+category:help +category:pleh",q.toString());

			q = parser.parseRaw("šđčćždzñ");
			assertEquals("contents:šđčćždzñ",q.toString());

			q = parser.parseRaw("help:making breakfast incategory:food");
			assertEquals("+help:making +help:breakfast +category:food",q.toString());

			q = parser.parseRaw("(help:making breakfast) food");
			assertEquals("+(+help:making +help:breakfast) +contents:food",q.toString());

			q = parser.parseRaw("(help:making (breakfast food");
			assertEquals("+help:making +(+help:breakfast +help:food)",q.toString());

			q = parser.parseRaw("(help:making (breakfast) food");
			assertEquals("+help:making +help:breakfast +help:food",q.toString());
			
			// extracting fields
			fields = parser.getFields("(help:making breakfast) food");
			assertEquals(2,fields.size());
			assertTrue(fields.contains("help"));
			assertTrue(fields.contains("contents"));
			
			fields = parser.getFields("help:making breakfast");
			assertEquals(1,fields.size());
			assertTrue(fields.contains("help"));
			
			fields = parser.getFields("contents:making +breakfast incategory:food");
			assertEquals(2,fields.size());
			assertTrue(fields.contains("incategory"));
			assertTrue(fields.contains("contents"));
			
			fields = parser.getFields("contents:making +breakfast \"incategory:food\"");
			assertEquals(1,fields.size());
			assertTrue(fields.contains("contents"));

			// extraction of phrases
			ArrayList<String> stopWords = new ArrayList<String>();
			stopWords.add("the"); stopWords.add("who");
			stopWords.add("is"); stopWords.add("a");	
			IndexId enwiki = IndexId.get("enwiki");
			Analyzer analyzer = Analyzers.getSearcherAnalyzer(enwiki);
			bs = new FieldBuilder(enwiki).getBuilder();
			parser = new WikiQueryParser(bs.getFields().title(),"0",analyzer,bs,NamespacePolicy.IGNORE,stopWords);
			assertEquals("[how, do, you, do]",parser.extractWords(parser.parseRaw("how do you do")).toString());
			assertEquals("[making, something, rest]",parser.extractWords(parser.parseRaw("(help:making something incategory:blah) OR (rest incategory:crest)")).toString());
			assertEquals("[godel, theorem]",parser.extractWords(parser.parseRaw("gödel theorem")).toString());
			assertEquals("[some, text, and, some, phrase]",parser.extractWords(parser.parseRaw("some_text and \"some phrase\"")).toString());
			
			//ArrayList<String> words = parser.extractWords(parser.parseRaw("the who band is something nobody knows about"));
			//assertEquals("contents:\"the who band\"~10 contents:\"band is something\"~10 contents:\"something nobody\"~10 contents:\"nobody knows\"~10 contents:\"knows about\"~10",parser.makePhraseQueries(words,"contents",10,1).toString());
						
			// namespace policies
			parser = new WikiQueryParser(ff.contents(),"0",new SimpleAnalyzer(), bs, WikiQueryParser.NamespacePolicy.IGNORE);
			q = parser.parseRaw("help:making breakfast incategory:food");
			assertEquals("+contents:making +contents:breakfast +category:food",q.toString());
			
			parser = new WikiQueryParser(ff.contents(),"0",new SimpleAnalyzer(), bs, WikiQueryParser.NamespacePolicy.REWRITE);
			q = parser.parseRaw("help:making breakfast incategory:food");
			assertEquals("+namespace:12 +(+contents:making +contents:breakfast +category:food)",q.toString());
			
			q = parser.parseRaw("help:making main_talk:something");
			assertEquals("(+namespace:12 +contents:making) (+namespace:1 +contents:something)",q.toString());
			
			q = parser.parseRaw("making something");
			assertEquals("+namespace:0 +(+contents:making +contents:something)",q.toString());
			
			q = parser.parseRaw("making something incategory:blah");
			assertEquals("+namespace:0 +(+contents:making +contents:something +category:blah)",q.toString());
			
			q = parser.parseRaw("(help:making something incategory:blah) (rest incategory:crest)");
			assertEquals("+(+namespace:12 +(+contents:making +contents:something +category:blah)) +(+namespace:0 +(+contents:rest +category:crest))",q.toString());
			
			//TODO: fix
			//q = parser.parse("(help:making something category:blah) (rest category:crest)");
			//assertEquals("+(+namespace:12 +(+contents:making +contents:something +category:blah (+title:making^2.0 +title:something^2.0))) (+namespace:0 +(+contents:rest +category:crest (+title:rest^2.0)))",q.toString());

			// ====== English Analyzer ========
			
			parser = new WikiQueryParser(ff.contents(),"0",new EnglishAnalyzer(), bs, WikiQueryParser.NamespacePolicy.REWRITE);
			q = parser.parseRaw("main_talk:laziness");
			assertEquals("+namespace:1 +(contents:laziness contents:lazi^0.5)",q.toString());
						
			q = parser.parse("main_talk:laziness");
			assertEquals("+namespace:1 +(+(contents:laziness contents:lazi^0.5) title:laziness^2.0)",q.toString());
			
			q = parser.parseRaw("(help:beans AND incategory:food) OR (help_talk:orchid AND incategory:\"some flowers\")");
			assertEquals("(+namespace:12 +(+(contents:beans contents:bean^0.5) +category:food)) (+namespace:13 +(+contents:orchid +category:\"some flowers\"))",q.toString());
			
			q = parser.parse("(help:beans AND incategory:food) OR (help_talk:orchid AND incategory:\"some flowers\")");
			assertEquals("(+namespace:12 +(+(+(contents:beans contents:bean^0.5) title:beans^2.0) +category:food)) (+namespace:13 +(+(+contents:orchid +category:\"some flowers\") title:orchid^2.0))",q.toString());

			q = parser.parse("(help:making something incategory:blah) OR (rest incategory:crest)");
			assertEquals("(+namespace:12 +(+(+(contents:making contents:make^0.5) title:making^2.0) +(+(contents:something contents:someth^0.5) title:something^2.0) +category:blah)) (+namespace:0 +(+(+contents:rest +category:crest) title:rest^2.0))",q.toString());
			
			parser = new WikiQueryParser(ff.contents(),new EnglishAnalyzer(),bs,null);

			q = parser.parseRaw("laziness");
			assertEquals("contents:laziness contents:lazi^0.5",q.toString());

			q = parser.parseRaw("laziness beans");
			assertEquals("+(contents:laziness contents:lazi^0.5) +(contents:beans contents:bean^0.5)",q.toString());

			q = parser.parse("laziness");
			assertEquals("+(contents:laziness contents:lazi^0.5) title:laziness^2.0",q.toString());

			q = parser.parseRaw("(beans AND incategory:food) (orchid AND incategory:\"some flowers\")");
			assertEquals("+(+(contents:beans contents:bean^0.5) +category:food) +(+contents:orchid +category:\"some flowers\")",q.toString());

			q = parser.parseRaw("(Beans AND incategory:FOod) (orchID AND incategory:\"some FLOWERS\")");
			assertEquals("+(+(contents:beans contents:bean^0.5) +category:food) +(+contents:orchid +category:\"some flowers\")",q.toString());

			q = parser.parse("(beans AND incategory:food) (orchid AND incategory:\"some flowers\")");
			assertEquals("+(+(+(contents:beans contents:bean^0.5) title:beans^2.0) +category:food) +(+(+contents:orchid +category:\"some flowers\") title:orchid^2.0)",q.toString());

			q = parser.parse("(beans AND incategory:food) +(orchid AND incategory:\"some flowers\")");
			assertEquals("+(+(+(contents:beans contents:bean^0.5) title:beans^2.0) +category:food) +(+(+contents:orchid +category:\"some flowers\") title:orchid^2.0)",q.toString());
						
			fields = parser.getFields("(help:making breakfast) food");
			assertEquals(2,fields.size());
			assertTrue(fields.contains("help"));
			assertTrue(fields.contains("contents"));
			
			fields = parser.getFields("help:making breakfast");
			assertEquals(1,fields.size());
			assertTrue(fields.contains("help"));
			
			fields = parser.getFields("contents:making +breakfast incategory:food");
			assertEquals(2,fields.size());
			assertTrue(fields.contains("incategory"));
			assertTrue(fields.contains("contents"));
			
			fields = parser.getFields("contents:making +breakfast \"incategory:food\"");
			assertEquals(1,fields.size());
			assertTrue(fields.contains("contents"));
			
			fields = parser.getFields("contents:making +[2]:breakfast \"incategory:food\"");
			assertEquals(2,fields.size());
			assertTrue(fields.contains("contents"));
			assertTrue(fields.contains("[2]"));
			
			// ==================================
			// Tests with actual params :)
			// ==================================
			analyzer = Analyzers.getSearcherAnalyzer(enwiki);
			bs = new FieldBuilder(enwiki).getBuilder();
			parser = new WikiQueryParser(bs.getFields().contents(),"0",analyzer,bs,NamespacePolicy.LEAVE);
			WikiQueryParser.ADD_STEM_TITLE = false;
			WikiQueryParser.STEM_TITLE_BOOST = 0;
			q = parser.parseTwoPass("beans everyone",null);
			assertEquals("(+(contents:beans contents:bean^0.5) +(contents:everyone contents:everyon^0.5)) (+title:beans^2.0 +title:everyone^2.0)",q.toString());
			
			q = parser.parseTwoPass("(beans incategory:plants) OR (orchid incategory:flowers)",null);
			assertEquals("((+(contents:beans contents:bean^0.5) +category:plants) (+contents:orchid +category:flowers)) ((+title:beans^2.0 +category:plants) (+title:orchid^2.0 +category:flowers))",q.toString());

			q = parser.parseTwoPass("main:beans everyone",NamespacePolicy.IGNORE);
			assertEquals("(+(contents:beans contents:bean^0.5) +(contents:everyone contents:everyon^0.5)) (+title:beans^2.0 +title:everyone^2.0)",q.toString());
			
			q = parser.parseTwoPass("beans everyone",NamespacePolicy.REWRITE);
			assertEquals("(+namespace:0 +(+(contents:beans contents:bean^0.5) +(contents:everyone contents:everyon^0.5))) (+namespace:0 +(+title:beans^2.0 +title:everyone^2.0))",q.toString());
			
			q = parser.parseTwoPass("(beans incategory:plants) OR (orchid incategory:flowers)",NamespacePolicy.REWRITE);
			assertEquals("((+namespace:0 +(+(contents:beans contents:bean^0.5) +category:plants)) (+namespace:0 +(+contents:orchid +category:flowers))) ((+namespace:0 +(+title:beans^2.0 +category:plants)) (+namespace:0 +(+title:orchid^2.0 +category:flowers)))",q.toString());
			
			q = parser.parseTwoPass("main:beans main:everyone",NamespacePolicy.REWRITE);
			assertEquals("(+namespace:0 +(+(contents:beans contents:bean^0.5) +(contents:everyone contents:everyon^0.5))) (+namespace:0 +(+title:beans^2.0 +title:everyone^2.0))",q.toString());
			
			q = parser.parseTwoPass("beans everyone (incategory:people OR incategory:food)",NamespacePolicy.REWRITE);
			assertEquals("(+namespace:0 +(+(contents:beans contents:bean^0.5) +(contents:everyone contents:everyon^0.5) +(category:people category:food))) (+namespace:0 +(+title:beans^2.0 +title:everyone^2.0 +(category:people category:food)))",q.toString());
			
			q = parser.parseTwoPass("all:beans everyone",NamespacePolicy.REWRITE);
			assertEquals("(+(contents:beans contents:bean^0.5) +(contents:everyone contents:everyon^0.5)) (+title:beans^2.0 +title:everyone^2.0)",q.toString());
			
			q = parser.parseTwoPass("all:beans everyone",NamespacePolicy.IGNORE);
			assertEquals("(+(contents:beans contents:bean^0.5) +(contents:everyone contents:everyon^0.5)) (+title:beans^2.0 +title:everyone^2.0)",q.toString());			
			
			q = parser.parseTwoPass("incategory:\"french actresses\" incategory:\"born 1920\"",NamespacePolicy.REWRITE);
			assertEquals("+category:\"french actresses\" +category:\"born 1920\"",q.toString());
			
			q = parser.parseTwoPass("(all:beans everyone) OR (something else) OR (help:editing) AND (all:foo)",NamespacePolicy.REWRITE);
			assertEquals("((+(contents:beans contents:bean^0.5) +(contents:everyone contents:everyon^0.5)) (+namespace:0 +(+(contents:something contents:someth^0.5) +(contents:else contents:els^0.5))) (+namespace:12 +(contents:editing contents:edit^0.5)) +contents:foo) ((+title:beans^2.0 +title:everyone^2.0) (+namespace:0 +(+title:something^2.0 +title:else^2.0)) (+namespace:12 +title:editing^2.0) +title:foo^2.0)",q.toString());
			
			q = parser.parseTwoPass("all:beans -everyone",NamespacePolicy.IGNORE);
			assertEquals("(+(contents:beans contents:bean^0.5) -(contents:everyone)) (+title:beans^2.0 -title:everyone^2.0)",q.toString());
			
			q = parser.parseTwoPass("all:(beans -everyone)",NamespacePolicy.IGNORE);
			assertEquals("(+(contents:beans contents:bean^0.5) -(contents:everyone)) (+title:beans^2.0 -title:everyone^2.0)",q.toString());
			
			q = parser.parseTwoPass("all:anti-hero",NamespacePolicy.IGNORE);
			assertEquals("(+contents:anti +contents:hero) (+title:anti^2.0 +title:hero^2.0)",q.toString());
			
			q = parser.parseTwoPass("main:1991 incategory:\"olympic cities\" OR all:1990",NamespacePolicy.REWRITE);
			assertEquals("(+(+namespace:0 +(+contents:1991 +category:\"olympic cities\")) contents:1990) (+(+namespace:0 +(+title:1991^2.0 +category:\"olympic cities\")) title:1990^2.0)",q.toString());
			
			q = parser.parseTwoPass("main:1991 incategory:\"olympic cities\" -all:1990",NamespacePolicy.REWRITE);
			assertEquals("(+(+namespace:0 +(+contents:1991 +category:\"olympic cities\")) -contents:1990) (+(+namespace:0 +(+title:1991^2.0 +category:\"olympic cities\")) -title:1990^2.0)",q.toString());
			
			q = parser.parseTwoPass("main:ba*",NamespacePolicy.IGNORE);
			assertEquals("contents:ba title:ba*^2.0",q.toString());
			
			q = parser.parseTwoPass("main:ba* all:lele",NamespacePolicy.REWRITE);
			assertEquals("(+(+namespace:0 +contents:ba) +contents:lele) (+(+namespace:0 +title:ba*^2.0) +title:lele^2.0)",q.toString());
			
			q = parser.parseTwoPass("main:ba*beans",NamespacePolicy.IGNORE);
			assertEquals("(+contents:ba +(contents:beans contents:bean^0.5)) (+title:ba^2.0 +title:beans^2.0)",q.toString());
			
			q = parser.parseTwoPass("*kuta",NamespacePolicy.IGNORE);
			assertEquals("contents:kuta title:kuta^2.0",q.toString());
			
			q = parser.parseTwoPass("category:beans",NamespacePolicy.IGNORE);
			assertEquals("(contents:beans contents:bean^0.5) title:beans^2.0",q.toString());
			
			q = parser.parseTwoPass("bzvz:beans",NamespacePolicy.IGNORE); // if it's unrecognized prefix, add to query
			assertEquals("(+contents:bzvz +(contents:beans contents:bean^0.5)) (+title:bzvz^2.0 +title:beans^2.0)",q.toString());
			
			// Generic namespace prefixes
			q = parser.parseTwoPass("[12]:beans",NamespacePolicy.IGNORE);
			assertEquals("(contents:beans contents:bean^0.5) title:beans^2.0",q.toString());
			
			q = parser.parseTwoPass("[1,12]:beans",NamespacePolicy.REWRITE);
			assertEquals("(+(namespace:1 namespace:12) +(contents:beans contents:bean^0.5)) (+(namespace:1 namespace:12) +title:beans^2.0)",q.toString());
			
			q = parser.parseTwoPass("[1,12]:beans and others incategory:food",NamespacePolicy.REWRITE);
			assertEquals("(+(namespace:1 namespace:12) +(+(contents:beans contents:bean^0.5) +contents:and +(contents:others contents:other^0.5) +category:food)) (+(namespace:1 namespace:12) +(+title:beans^2.0 +title:and^2.0 +title:others^2.0 +category:food))",q.toString());
			
			q = parser.parseTwoPass("[1,a12]:beans",NamespacePolicy.IGNORE);
			assertEquals("(+contents:1 +contents:a12 +(contents:beans contents:bean^0.5)) (+title:1^2.0 +title:a12^2.0 +title:beans^2.0)",q.toString());
			
			// Redirect third/forth pass tests
			q = parser.parseFourPass("beans",NamespacePolicy.IGNORE,true);
			assertEquals("(contents:beans contents:bean^0.5) title:beans^2.0 (alttitle1:beans^6.0 alttitle2:beans^6.0 alttitle3:beans^6.0) (keyword1:beans^0.05 keyword2:beans^0.025 keyword3:beans^0.016666668 keyword4:beans^0.0125 keyword5:beans^0.01)",q.toString());
			
			q = parser.parseFourPass("beans everyone",NamespacePolicy.IGNORE,true);			
			assertEquals("(+(contents:beans contents:bean^0.5) +(contents:everyone contents:everyon^0.5)) (+title:beans^2.0 +title:everyone^2.0) ((+alttitle1:beans^6.0 +alttitle1:everyone^6.0) (+alttitle2:beans^6.0 +alttitle2:everyone^6.0) (+alttitle3:beans^6.0 +alttitle3:everyone^6.0)) (spanNear([keyword1:beans, keyword1:everyone], 100, false)^0.05 spanNear([keyword2:beans, keyword2:everyone], 100, false)^0.025 spanNear([keyword3:beans, keyword3:everyone], 100, false)^0.016666668 spanNear([keyword4:beans, keyword4:everyone], 100, false)^0.0125 spanNear([keyword5:beans, keyword5:everyone], 100, false)^0.01)",q.toString());
			
			// TODO: check if this query will be optimized by lucene (categories)
			q = parser.parseFourPass("beans everyone incategory:mouse",NamespacePolicy.IGNORE,true);
			assertEquals("(+(contents:beans contents:bean^0.5) +(contents:everyone contents:everyon^0.5) +category:mouse) (+title:beans^2.0 +title:everyone^2.0 +category:mouse) ((+alttitle1:beans^6.0 +alttitle1:everyone^6.0 +category:mouse) (+alttitle2:beans^6.0 +alttitle2:everyone^6.0 +category:mouse) (+alttitle3:beans^6.0 +alttitle3:everyone^6.0 +category:mouse)) ((+spanNear([keyword1:beans, keyword1:everyone], 100, false)^0.05 +category:mouse) (+spanNear([keyword2:beans, keyword2:everyone], 100, false)^0.025 +category:mouse) (+spanNear([keyword3:beans, keyword3:everyone], 100, false)^0.016666668 +category:mouse) (+spanNear([keyword4:beans, keyword4:everyone], 100, false)^0.0125 +category:mouse) (+spanNear([keyword5:beans, keyword5:everyone], 100, false)^0.01 +category:mouse))",q.toString());
			
			q = parser.parseFourPass("beans OR everyone",NamespacePolicy.IGNORE,true);
			assertEquals("((contents:beans contents:bean^0.5) (contents:everyone contents:everyon^0.5)) (title:beans^2.0 title:everyone^2.0) ((alttitle1:beans^6.0 alttitle1:everyone^6.0) (alttitle2:beans^6.0 alttitle2:everyone^6.0) (alttitle3:beans^6.0 alttitle3:everyone^6.0))",q.toString());
			
			q = parser.parseFourPass("beans -everyone",NamespacePolicy.IGNORE,true);
			assertEquals("(+(contents:beans contents:bean^0.5) -(contents:everyone)) (+title:beans^2.0 -title:everyone^2.0) ((+alttitle1:beans^6.0 -alttitle1:everyone^6.0) (+alttitle2:beans^6.0 -alttitle2:everyone^6.0) (+alttitle3:beans^6.0 -alttitle3:everyone^6.0))",q.toString());
			
			q = parser.parseFourPass("[0,1,2]:beans everyone",NamespacePolicy.REWRITE,true);
			assertEquals("(+(namespace:0 namespace:1 namespace:2) +(+(contents:beans contents:bean^0.5) +(contents:everyone contents:everyon^0.5))) (+(namespace:0 namespace:1 namespace:2) +(+title:beans^2.0 +title:everyone^2.0)) ((+(namespace:0 namespace:1 namespace:2) +(+alttitle1:beans^6.0 +alttitle1:everyone^6.0)) (+(namespace:0 namespace:1 namespace:2) +(+alttitle2:beans^6.0 +alttitle2:everyone^6.0)) (+(namespace:0 namespace:1 namespace:2) +(+alttitle3:beans^6.0 +alttitle3:everyone^6.0))) ((+(namespace:0 namespace:1 namespace:2) +spanNear([keyword1:beans, keyword1:everyone], 100, false)^0.05) (+(namespace:0 namespace:1 namespace:2) +spanNear([keyword2:beans, keyword2:everyone], 100, false)^0.025) (+(namespace:0 namespace:1 namespace:2) +spanNear([keyword3:beans, keyword3:everyone], 100, false)^0.016666668) (+(namespace:0 namespace:1 namespace:2) +spanNear([keyword4:beans, keyword4:everyone], 100, false)^0.0125) (+(namespace:0 namespace:1 namespace:2) +spanNear([keyword5:beans, keyword5:everyone], 100, false)^0.01))",q.toString());
			
			q = parser.parseFourPass("[0,1,2]:beans everyone [0]:mainly",NamespacePolicy.REWRITE,true);
			assertEquals("((+(namespace:0 namespace:1 namespace:2) +(+(contents:beans contents:bean^0.5) +(contents:everyone contents:everyon^0.5))) (+namespace:0 +(contents:mainly contents:main^0.5))) ((+(namespace:0 namespace:1 namespace:2) +(+title:beans^2.0 +title:everyone^2.0)) (+namespace:0 +title:mainly^2.0)) (((+(namespace:0 namespace:1 namespace:2) +(+alttitle1:beans^6.0 +alttitle1:everyone^6.0)) (+namespace:0 +alttitle1:mainly^6.0)) ((+(namespace:0 namespace:1 namespace:2) +(+alttitle2:beans^6.0 +alttitle2:everyone^6.0)) (+namespace:0 +alttitle2:mainly^6.0)) ((+(namespace:0 namespace:1 namespace:2) +(+alttitle3:beans^6.0 +alttitle3:everyone^6.0)) (+namespace:0 +alttitle3:mainly^6.0)))",q.toString());
			
			q = parser.parseFourPass("Israeli-Palestinian conflict",NamespacePolicy.IGNORE,true);
			assertEquals("(+(+(contents:israeli contents:isra^0.5) +contents:palestinian) +contents:conflict) (+(+title:israeli^2.0 +title:palestinian^2.0) +title:conflict^2.0) ((+(+alttitle1:israeli^6.0 +alttitle1:palestinian^6.0) +alttitle1:conflict^6.0) (+(+alttitle2:israeli^6.0 +alttitle2:palestinian^6.0) +alttitle2:conflict^6.0) (+(+alttitle3:israeli^6.0 +alttitle3:palestinian^6.0) +alttitle3:conflict^6.0))",q.toString());
			
			// title phrases
			WikiQueryParser.ADD_TITLE_PHRASES = true;
			q = parser.parseFourPass("Israeli Palestinian conflict",NamespacePolicy.IGNORE,true);
			assertEquals("(+(contents:israeli contents:isra^0.5) +contents:palestinian +contents:conflict) (+title:israeli^2.0 +title:palestinian^2.0 +title:conflict^2.0) ((+alttitle1:israeli^6.0 +alttitle1:palestinian^6.0 +alttitle1:conflict^6.0) (+alttitle2:israeli^6.0 +alttitle2:palestinian^6.0 +alttitle2:conflict^6.0) (+alttitle3:israeli^6.0 +alttitle3:palestinian^6.0 +alttitle3:conflict^6.0)) (spanNear([keyword1:israeli, keyword1:palestinian, keyword1:conflict], 100, false)^0.05 spanNear([keyword2:israeli, keyword2:palestinian, keyword2:conflict], 100, false)^0.025 spanNear([keyword3:israeli, keyword3:palestinian, keyword3:conflict], 100, false)^0.016666668 spanNear([keyword4:israeli, keyword4:palestinian, keyword4:conflict], 100, false)^0.0125 spanNear([keyword5:israeli, keyword5:palestinian, keyword5:conflict], 100, false)^0.01)",q.toString());
			WikiQueryParser.ADD_TITLE_PHRASES = false;
			
			// alternative transliterations
			q = parser.parseFourPass("Something for Gödels",NamespacePolicy.IGNORE,true);
			assertEquals("(+(contents:something contents:someth^0.5) +contents:for +(+(contents:godels contents:godel^0.5) (contents:goedels contents:goedel^0.5))) (+title:something^2.0 +title:for^2.0 +(title:godels^2.0 title:goedels^2.0)) ((+alttitle1:something^6.0 +alttitle1:for^6.0 +(alttitle1:godels^6.0 alttitle1:goedels^6.0)) (+alttitle2:something^6.0 +alttitle2:for^6.0 +(alttitle2:godels^6.0 alttitle2:goedels^6.0)) (+alttitle3:something^6.0 +alttitle3:for^6.0 +(alttitle3:godels^6.0 alttitle3:goedels^6.0)))",q.toString());
			
			q = parser.parseFourPass("Something for Gödel",NamespacePolicy.IGNORE,true);
			assertEquals("(+(contents:something contents:someth^0.5) +contents:for +(contents:godel contents:goedel)) (+title:something^2.0 +title:for^2.0 +(title:godel^2.0 title:goedel^2.0)) ((+alttitle1:something^6.0 +alttitle1:for^6.0 +(alttitle1:godel^6.0 alttitle1:goedel^6.0)) (+alttitle2:something^6.0 +alttitle2:for^6.0 +(alttitle2:godel^6.0 alttitle2:goedel^6.0)) (+alttitle3:something^6.0 +alttitle3:for^6.0 +(alttitle3:godel^6.0 alttitle3:goedel^6.0)))",q.toString());

			// Backward compatiblity for complex filters
			analyzer = Analyzers.getSearcherAnalyzer(enwiki);
			bs = new FieldBuilder(enwiki).getBuilder();
			parser = new WikiQueryParser(bs.getFields().contents(),"0,1,4,12",analyzer,bs,NamespacePolicy.IGNORE);
			
			q = parser.parseTwoPass("beans everyone",NamespacePolicy.REWRITE);
			assertEquals("(+(namespace:0 namespace:1 namespace:4 namespace:12) +(+(contents:beans contents:bean^0.5) +(contents:everyone contents:everyon^0.5))) (+(namespace:0 namespace:1 namespace:4 namespace:12) +(+title:beans^2.0 +title:everyone^2.0))",q.toString());
			
			q = parser.parseTwoPass("beans main:everyone",NamespacePolicy.REWRITE);
			assertEquals("((+(namespace:0 namespace:1 namespace:4 namespace:12) +(contents:beans contents:bean^0.5)) (+namespace:0 +(contents:everyone contents:everyon^0.5))) ((+(namespace:0 namespace:1 namespace:4 namespace:12) +title:beans^2.0) (+namespace:0 +title:everyone^2.0))",q.toString());
			
			q = parser.parseTwoPass("beans everyone incategory:cheeses",NamespacePolicy.REWRITE);
			assertEquals("(+(namespace:0 namespace:1 namespace:4 namespace:12) +(+(contents:beans contents:bean^0.5) +(contents:everyone contents:everyon^0.5) +category:cheeses)) (+(namespace:0 namespace:1 namespace:4 namespace:12) +(+title:beans^2.0 +title:everyone^2.0 +category:cheeses))",q.toString());
			
			q = parser.parseTwoPass("all_talk: beans everyone",NamespacePolicy.REWRITE);
			assertEquals("(+(namespace:1 namespace:3 namespace:5 namespace:7 namespace:9 namespace:11 namespace:13 namespace:15) +(+(contents:beans contents:bean^0.5) +(contents:everyone contents:everyon^0.5))) (+(namespace:1 namespace:3 namespace:5 namespace:7 namespace:9 namespace:11 namespace:13 namespace:15) +(+title:beans^2.0 +title:everyone^2.0))",q.toString());
			
			// tokenization test
			ArrayList<Token> t = parser.tokenizeBareText("beans everyone");
			assertEquals("[(beans,0,5), (everyone,6,14)]",t.toString());
			
			t = parser.tokenizeBareText("hey \"are you\" [0]:gl ho");
			assertEquals("[(hey,0,3), (are,5,8,type=phrase), (you,9,12,type=phrase), (gl,18,20), (ho,21,23)]",t.toString());
			
			t = parser.tokenizeBareText("+many -more AND has OR some -g");
			assertEquals("[(many,1,5), (more,7,11), (has,16,19), (some,23,27), (g,29,30)]",t.toString());
			
			// German
			analyzer = Analyzers.getSearcherAnalyzer(IndexId.get("dewiki"));
			bs = new FieldBuilder(IndexId.get("dewiki")).getBuilder();
			parser = new WikiQueryParser(bs.getFields().contents(),"0",analyzer,bs,NamespacePolicy.IGNORE);
			q = parser.parseTwoPass("welche rolle spielen Mineralstoffe in der Ernährung?",NamespacePolicy.IGNORE);
			assertEquals("(+(contents:welche contents:welch^0.5) +(contents:rolle contents:roll^0.5) +(contents:spielen contents:spiel^0.5) +(contents:mineralstoffe contents:mineralstoff^0.5) +contents:in +contents:der +(+(contents:ernahrung contents:ernahr^0.5) (contents:ernaehrung contents:ernaehr^0.5))) (+title:welche^2.0 +title:rolle^2.0 +title:spielen^2.0 +title:mineralstoffe^2.0 +title:in^2.0 +title:der^2.0 +(title:ernahrung^2.0 title:ernaehrung^2.0))",q.toString());			
			
			// CJK
			analyzer = Analyzers.getSearcherAnalyzer(IndexId.get("jawiki"));
			bs = new FieldBuilder(IndexId.get("jawiki")).getBuilder();
			parser = new WikiQueryParser(bs.getFields().contents(),"0",analyzer,bs,NamespacePolicy.IGNORE);
			q = parser.parseFourPass("うろパン",NamespacePolicy.IGNORE,false);
			assertEquals("contents:\"うろ ろハ ハン\" title:\"うろ ろハ ハン\"^2.0 (alttitle1:\"うろ ろハ ハン\"^6.0 alttitle2:\"うろ ろハ ハン\"^6.0 alttitle3:\"うろ ろハ ハン\"^6.0)",q.toString());
			
			q = parser.parseFourPass("ナイロン100C other ャポン! ",NamespacePolicy.IGNORE,false);
			assertEquals("(+contents:\"ナイ イロ ロン\" +(+contents:100 +contents:c) +contents:other +contents:\"ャホ ホン\") (+title:\"ナイ イロ ロン\"^2.0 +(+title:100^2.0 +title:c^2.0) +title:other^2.0 +title:\"ャホ ホン\"^2.0) ((+alttitle1:\"ナイ イロ ロン\"^6.0 +(+alttitle1:100^6.0 +alttitle1:c^6.0) +alttitle1:other^6.0 +alttitle1:\"ャホ ホン\"^6.0) (+alttitle2:\"ナイ イロ ロン\"^6.0 +(+alttitle2:100^6.0 +alttitle2:c^6.0) +alttitle2:other^6.0 +alttitle2:\"ャホ ホン\"^6.0) (+alttitle3:\"ナイ イロ ロン\"^6.0 +(+alttitle3:100^6.0 +alttitle3:c^6.0) +alttitle3:other^6.0 +alttitle3:\"ャホ ホン\"^6.0))",q.toString());
			
			q = parser.parseFourPass("\"うろパン\"",NamespacePolicy.IGNORE,false);
			assertEquals("contents:\"うろ ろハ ハン\" title:\"うろ ろハ ハン\"^2.0 (alttitle1:\"うろ ろハ ハン\"^6.0 alttitle2:\"うろ ろハ ハン\"^6.0 alttitle3:\"うろ ろハ ハン\"^6.0)",q.toString());
			
			
			// Malayalam
			analyzer = Analyzers.getSearcherAnalyzer(IndexId.get("mlwiki"));
			bs = new FieldBuilder(IndexId.get("mlwiki")).getBuilder();
			parser = new WikiQueryParser(bs.getFields().contents(),"0",analyzer,bs,NamespacePolicy.IGNORE);
			q = parser.parseFourPass("കൊറിയ ",NamespacePolicy.IGNORE,false);
			assertEquals("contents:കറയ title:കറയ^2.0 (alttitle1:കറയ^6.0 alttitle2:കറയ^6.0 alttitle3:കറയ^6.0)",q.toString());
			
			
			// Test field extraction
			HashSet<NamespaceFilter> fs = parser.getFieldNamespaces("main:something [1]:else all:oh []:nja");
			assertEquals(3,fs.size());
			assertTrue(fs.contains(new NamespaceFilter("0")));
			assertTrue(fs.contains(new NamespaceFilter("1")));
			assertTrue(fs.contains(new NamespaceFilter()));

			WikiQueryParser.ADD_STEM_TITLE = true;
			WikiQueryParser.STEM_TITLE_BOOST = 1;
			
			// Localization tests
			analyzer = Analyzers.getSearcherAnalyzer(IndexId.get("srwiki"));
			bs = new FieldBuilder(IndexId.get("srwiki")).getBuilder();
			parser = new WikiQueryParser(bs.getFields().contents(),"0",analyzer,bs,NamespacePolicy.LEAVE);
			
			q = parser.parseTwoPass("all:добродошли на википедију",NamespacePolicy.IGNORE);
			assertEquals("(+(contents:добродошли contents:dobrodosli^0.5) +(contents:на contents:na^0.5) +(contents:википедију contents:vikipediju^0.5)) (+(title:добродошли^3.0 title:dobrodosli^0.6) +(title:на^3.0 title:na^0.6) +(title:википедију^3.0 title:vikipediju^0.6))",q.toString());
			
			q = parser.parseTwoPass("all:dobrodošli na šđčćž",NamespacePolicy.IGNORE);
			assertEquals("(+contents:dobrodosli +contents:na +contents:sdjccz) (+title:dobrodosli^3.0 +title:na^3.0 +title:sdjccz^3.0)",q.toString());
			
			analyzer = Analyzers.getSearcherAnalyzer(IndexId.get("thwiki"));
			bs = new FieldBuilder(IndexId.get("thwiki")).getBuilder();
			parser = new WikiQueryParser(bs.getFields().contents(),"0",analyzer,bs,NamespacePolicy.LEAVE);
			
			q = parser.parseTwoPass("ภาษาไทย",NamespacePolicy.IGNORE);
			assertEquals("(+contents:ภาษา +contents:ไทย) (+title:ภาษา^3.0 +title:ไทย^3.0)",q.toString());
			
			q = parser.parseTwoPass("help:ภาษาไทย",NamespacePolicy.REWRITE);
			assertEquals("(+namespace:12 +(+contents:ภาษา +contents:ไทย)) (+namespace:12 +(+title:ภาษา^3.0 +title:ไทย^3.0))",q.toString());
			
			// vietnamese
			analyzer = Analyzers.getSearcherAnalyzer(IndexId.get("viwiki"));
			bs = new FieldBuilder(IndexId.get("viwiki")).getBuilder();
			parser = new WikiQueryParser(bs.getFields().contents(),"0",analyzer,bs,NamespacePolicy.LEAVE);
			
			q = parser.parseTwoPass("Gánh nước đêm trăng",NamespacePolicy.IGNORE);
			assertEquals("(+contents:ganh +contents:nuoc +(contents:đem contents:dem^0.5) +contents:trang) (+title:ganh^3.0 +title:nuoc^3.0 +(title:đem^3.0 title:dem^0.6) +title:trang^3.0)",q.toString());			
			
		} catch(Exception e){
			e.printStackTrace();
		}

	}

}
