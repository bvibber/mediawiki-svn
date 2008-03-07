package org.wikimedia.lsearch.test;

import java.util.Arrays;
import java.util.HashSet;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.search.Query;
import org.wikimedia.lsearch.analyzers.Analyzers;
import org.wikimedia.lsearch.analyzers.FieldBuilder;
import org.wikimedia.lsearch.analyzers.FieldNameFactory;
import org.wikimedia.lsearch.analyzers.WikiQueryParser;
import org.wikimedia.lsearch.analyzers.WikiQueryParser.NamespacePolicy;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.index.WikiIndexModifier;

import junit.framework.TestCase;

public class WikiQueryParserTest extends TestCase {
	public void testParser() {
		Configuration.setConfigFile(System.getProperty("user.dir")+"/test-data/mwsearch.conf.test");
		Configuration.open();
		WikiQueryParser.TITLE_BOOST = 2;
		WikiQueryParser.ALT_TITLE_BOOST = 6;
		WikiQueryParser.CONTENTS_BOOST = 1;
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
			assertEquals("+contents:egg +category:\"two words\"",q.toString());
			
			q = parser.parseRaw("+egg incategory:\"two words\"");
			assertEquals("+contents:egg +category:\"two words\"",q.toString());

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
			assertEquals("+(contents:c# contents:c^0.5) +(+(contents:good- contents:good^0.5) +(contents:thomas contents:thoma^0.5))",q.toString());
			
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
			
			
			/* =================== MISC  ================= */
			
			q = parser.parse("douglas adams OR qian zhongshu OR (ibanez guitars)");
			assertEquals("[douglas, adams, qian, zhongshu, ibanez, guitars]",parser.getWordsClean().toString());
			
			assertEquals("[(douglas,0,7), (adam*,8,13,type=wildcard)]",parser.tokenizeBareText("douglas adam*").toString());
			
			assertEquals("[(douglas,0,7)]",parser.tokenizeBareText("douglas -adams").toString());
			
			assertEquals("[(box,0,3), (ven,4,7,type=fuzzy), (i'll,9,13)]",parser.tokenizeBareText("box ven~ i'll").toString());
			
			q = parser.parse("douglas adams -guides");
			assertEquals("[contents:adams, contents:dougla, contents:douglas, contents:adam]", Arrays.toString(parser.getHighlightTerms()));

		} catch(Exception e){
			e.printStackTrace();
		}
	}
}
