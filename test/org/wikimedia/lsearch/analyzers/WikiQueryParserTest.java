package org.wikimedia.lsearch.analyzers;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashSet;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.Token;
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
import org.wikimedia.lsearch.test.WikiTestCase;

import junit.framework.TestCase;

public class WikiQueryParserTest extends WikiTestCase {
	
	

	public void testEnglish() {
		IndexId enwiki = IndexId.get("enwiki");
		FieldBuilder.BuilderSet bs = new FieldBuilder(IndexId.get("enwiki")).getBuilder();
		Analyzer analyzer = Analyzers.getSearcherAnalyzer(enwiki);
		FieldNameFactory ff = new FieldNameFactory();
		try{
			WikiQueryParser parser = new WikiQueryParser("contents","0",analyzer,bs,NamespacePolicy.IGNORE);
			Query q;
			
			/* =================== BASICS ================= */ 
			q = parser.parseRaw("1991");
			assertEquals("contents:1991",q.toString());
			
			q = parser.parseRaw("\"eggs and bacon\" OR milk");
			assertEquals("contents:\"eggs and bacon\" contents:milk",q.toString());

			q = parser.parseRaw("+eggs milk -something");
			assertEquals("+(contents:eggs contents:egg^0.5) +contents:milk -contents:something",q.toString());			

			q = parser.parseRaw("eggs AND milk");
			assertEquals("+(contents:eggs contents:egg^0.5) +contents:milk",q.toString());

			q = parser.parseRaw("+egg incategory:breakfast");
			assertEquals("+contents:egg +category:breakfast",q.toString());
			
			q = parser.parseRaw("+egg incategory:\"two_words\"");
			assertEquals("+contents:egg +category:two words",q.toString());
			
			q = parser.parseRaw("+egg incategory:\"two words\"");
			assertEquals("+contents:egg +category:two words",q.toString());

			q = parser.parseRaw("incategory:(help AND pleh)");
			assertEquals("+category:help +category:pleh",q.toString());

			q = parser.parseRaw("incategory:(help AND (pleh -ping))");
			assertEquals("+category:help +(+category:pleh -category:ping)",q.toString());

			q = parser.parseRaw("(\"something is\" OR \"something else\") AND \"very important\"");
			assertEquals("+(contents:\"something is\" contents:\"something else\") +contents:\"very important\"",q.toString());

			q = parser.parseRaw("šđčćždzñ");
			assertEquals("contents:šđčćždzñ contents:sđcczdzn^0.5 contents:sđcczdznh^0.5",q.toString());
			
			q = parser.parseRaw(".test 3.14 and.. so");
			assertEquals("+(contents:.test contents:test^0.5) +contents:3.14 +contents:and +contents:so",q.toString());
			
			q = parser.parseRaw("i'll get");
			assertEquals("+(contents:i'll contents:ill^0.5) +contents:get",q.toString());
			
			q = parser.parseRaw("c# good-thomas");
			assertEquals("+(contents:c# contents:c^0.5) +(+(contents:good- contents:good^0.5 contents:goodthomas^0.5) +contents:thomas)",q.toString());
			
			q = parser.parseRaw("a8n sli");
			assertEquals("+contents:a8n +contents:sli",q.toString());
			
			q = parser.parseRaw("en.wikipedia.org");
			assertEquals("+contents:en +contents:wikipedia +contents:org",q.toString());
			assertEquals("[[contents:en, contents:wikipedia, contents:org]]",parser.getUrls().toString());
			
			q = parser.parseRaw("something prefix:[2]:Rainman/Archive");
			assertEquals("contents:something",q.toString());
			assertEquals("[2:rainman/archive]",Arrays.toString(parser.getPrefixFilters()));
			
			q = parser.parseRaw("something prefix:[2]:Rainman/Archive|Names|[4]:Help");
			assertEquals("contents:something",q.toString());
			assertEquals("[2:rainman/archive, 0:names, 4:help]",Arrays.toString(parser.getPrefixFilters()));
			
			q = parser.parseRaw("query incategory:Some_category_name");
			assertEquals("+contents:query +category:some category name",q.toString());
			
			q = parser.parseRaw("list of countries in Africa by population");
			assertEquals("+contents:list +contents:of +(contents:countries contents:country^0.5) +contents:in +contents:africa +contents:by +contents:population", q.toString());
			
			q = parser.parseRaw("list_of_countries_in_Africa_by_population");
			assertEquals("+contents:list +contents:of +(contents:countries contents:country^0.5) +contents:in +contents:africa +contents:by +contents:population", q.toString());
			
			// FIXME, some differences in alttitle
			//assertEquals(parser.parse("list of countries in Africa by population").toString(), parser.parse("list_of_countries_in_Africa_by_population").toString());

			
			/* =================== MISC  ================= */
			
			q = parser.parse("douglas adams OR qian zhongshu OR (ibanez guitars)");
			assertEquals("[douglas, adams, qian, zhongshu, ibanez, guitars]",parser.getWordsClean().toString());
			
			assertEquals("[(douglas,0,7), (adam,8,12,type=wildcard)]",parser.tokenizeForSpellCheck("douglas adam*").toString());
			
			assertEquals("[(douglas,0,7)]",parser.tokenizeForSpellCheck("douglas -adams").toString());
			assertEquals("[(douglas,4,11)]",parser.tokenizeForSpellCheck("[2]:douglas -adams").toString());
			
			assertEquals("[(box,0,3), (ven,4,7,type=fuzzy), (i'll,9,13)]",parser.tokenizeForSpellCheck("box ven~ i'll").toString());
			
			q = parser.parse("douglas -adams guides");
			assertEquals("[contents:guides, contents:douglas, contents:guide]", Arrays.toString(parser.getHighlightTerms()));
			
			/* ================== PREFIXES ============ */
			q = parser.parseRaw("intitle:tests");
			// FIXME: stemming for titles?
			assertEquals("title:tests title:test^0.5",q.toString());
			
			q = parser.parseRaw("intitle:multiple words in title");
			assertEquals("+title:multiple +title:words +title:in +title:title",q.toString());
			
			q = parser.parseRaw("intitle:[2]:tests");
			assertEquals("title:tests title:test^0.5",q.toString());
			
			q = parser.parseRaw("something (intitle:[2]:tests) out");
			assertEquals("+contents:something +(title:tests title:test^0.5) +contents:out",q.toString());
			
			ArrayList<Token> tokens = parser.tokenizeForSpellCheck("+incategory:\"zero\" a:b incategory:c +incategory:d [1]:20");
			assertEquals("[(a,19,20), (b,21,22), (c,34,35), (d,48,49), (20,54,56)]", tokens.toString());
			
			tokens = parser.tokenizeForSpellCheck("+incategory:\"Suspension bridges in the United States\"");
			assertEquals("[]", tokens.toString());
			
			/* ================== unicode decomposition stuffs ============ */
			q = parser.parseRaw("šta");
			assertEquals("contents:šta contents:sta^0.5",q.toString());
			
			q = parser.parseRaw("װאנט");
			assertEquals("contents:װאנט contents:וואנט^0.5",q.toString());
			
			q = parser.parseRaw("פּאריז");
			assertEquals("contents:פּאריז contents:פאריז^0.5",q.toString());
			
						
		} catch(Exception e){
		}
	}

	
	public void XtestEnglishFull() {
		IndexId enwiki = IndexId.get("enwiki");
		FieldBuilder.BuilderSet bs = new FieldBuilder(IndexId.get("enwiki")).getBuilder();
		Analyzer analyzer = Analyzers.getSearcherAnalyzer(enwiki);
		FieldNameFactory ff = new FieldNameFactory();
		try{
			WikiQueryParser parser = new WikiQueryParser("contents","0",analyzer,bs,NamespacePolicy.IGNORE);
			Query q;
			/* =================== FULL QUERIES ================= */
			
			q = parser.parse("simple query",new WikiQueryParser.ParsingOptions(true));
			assertEquals("(+(contents:simple contents:simpl^0.5) +(contents:query contents:queri^0.5)) ((+title:simple^2.0 +title:query^2.0) (+(stemtitle:simple^0.8 stemtitle:simpl^0.32000002) +(stemtitle:query^0.8 stemtitle:queri^0.32000002)))",q.toString());
			
			q = parser.parse("guitars",new WikiQueryParser.ParsingOptions(true));
			assertEquals("(contents:guitars contents:guitar^0.5) ((title:guitars^2.0 title:guitar^0.4) (stemtitle:guitars^0.8 stemtitle:guitar^0.32000002))",q.toString());
			assertEquals("[guitars]",parser.getWordsClean().toString());
			
			q = parser.parse("simple -query",new WikiQueryParser.ParsingOptions(true));
			assertEquals("(+(contents:simple contents:simpl^0.5) -contents:query) ((+title:simple^2.0 -title:query^2.0) (+(stemtitle:simple^0.8 stemtitle:simpl^0.32000002) -stemtitle:query^0.8)) -(contents:query title:query^2.0 stemtitle:query^0.8)",q.toString());
			assertEquals("[simple]",parser.getWordsClean().toString());
			
			q = parser.parse("the who",new WikiQueryParser.ParsingOptions(true));
			assertEquals("(+contents:the +contents:who) ((+title:the^2.0 +title:who^2.0) (+stemtitle:the^0.8 +stemtitle:who^0.8))",q.toString());
			
			q = parser.parse("the_who",new WikiQueryParser.ParsingOptions(true));
			assertEquals("(+contents:the +contents:who) ((+title:the^2.0 +title:who^2.0) (+stemtitle:the^0.8 +stemtitle:who^0.8))",q.toString());
			
			q = parser.parse("\"Ole von Beust\"",new WikiQueryParser.ParsingOptions(true));
			assertEquals("contents:\"ole von beust\" (title:\"ole von beust\"^2.0 stemtitle:\"ole von beust\"^0.8)",q.toString());
			
			q = parser.parse("who is president of u.s.",new WikiQueryParser.ParsingOptions(true));
			assertEquals("(+contents:who +contents:is +(contents:president contents:presid^0.5) +contents:of +(contents:u.s contents:us^0.5)) ((+title:who^2.0 +title:is^2.0 +title:president^2.0 +title:of^2.0 +(title:u.s^2.0 title:us^0.4)) (+stemtitle:who^0.8 +stemtitle:is^0.8 +(stemtitle:president^0.8 stemtitle:presid^0.32000002) +stemtitle:of^0.8 +(stemtitle:u.s^0.8 stemtitle:us^0.32000002)))",q.toString());
			assertEquals("[who, is, president, of, u.s]",parser.getWordsClean().toString());
		
		} catch(Exception e){
			e.printStackTrace();
		}
	}
	
	public void testExtractRawFields(){
		assertEquals("[something , 0:eh heh]", Arrays.toString(WikiQueryParser.extractRawField("something ondiscussionpage:eh heh", "ondiscussionpage:")));
		assertEquals("[something , 0:eh \"heh\"]", Arrays.toString(WikiQueryParser.extractRawField("something ondiscussionpage:eh \"heh\"", "ondiscussionpage:")));
	}
}
