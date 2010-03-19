package org.wikimedia.lsearch.analyzers;
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.ObjectInput;
import java.io.ObjectInputStream;
import java.io.ObjectOutputStream;
import java.io.StringReader;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Map.Entry;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.LowerCaseTokenizer;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenStream;
import org.wikimedia.lsearch.analyzers.ExtToken;
import org.wikimedia.lsearch.analyzers.FastWikiTokenizerEngine;
import org.wikimedia.lsearch.analyzers.TokenizerOptions;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.index.WikiIndexModifier;
import org.wikimedia.lsearch.test.WikiTestCase;

public class FastWikiTokenizerTest extends WikiTestCase {
	IndexId iid;
	TokenizerOptions options;

	public void testIndex(){
		this.iid = IndexId.get("enwiki");
		this.options = new TokenizerOptions.ContentOptions(false);

		assertEquals("1 [link] 1 [text]",
				tokens("[[link text]]"));

		assertEquals("1 [anchor] 1 [text]",
				tokens("[[some link|anchor text]]"));

		assertEquals("1 [italic] 2 [see]",
				tokens("''italic''<nowiki><!-- see --></nowiki><!-- nosee -->"));

		assertEquals("1 [http] 2 [en] 1 [wikipedia] 1 [org/] 0 [org] 1 [english] 1 [wikipedia]",
				tokens("[http://en.wikipedia.org/ english wikipedia]"));

		assertEquals("500 [image] 1 [argishti] 1 [monument] 1 [jpg] 1 [king] 1 [argishti] 1 [of] 1 [urartu]",
				tokens("[[Image:Argishti monument.JPG|thumb|King Argishti of Urartu]]"));

		assertEquals("500 [image] 1 [argishti] 1 [monument] 1 [jpg] 1 [king] 1 [argishti] 1 [of] 1 [urartu]",
				tokens("[[Image:Argishti monument.JPG|thumb|King [[link target|Argishti]] of Urartu]]"));

		assertEquals("500 [image] 1 [frizbi] 1 [jpg] 1 [frizbi] 1 [za] 1 [ultimate] 1 [28] 1 [cm] 1 [175] 1 [g]",
				tokens("[[Image:frizbi.jpg|десно|мини|240п|Frizbi za ultimate, 28cm, 175g]]"));

		assertEquals("1 [image] 3 [argishti] 1 [monument] 1 [jpg] 1 [thumb] 1 [king] 1 [argishti] 1 [of] 1 [urartu]",
				tokens("[[Image:Argishti monument.JPG|thumb|King Argishti of Urartu"));

		assertEquals("1 [clinton] 1 [comets]",
				tokens("{| style=\"margin:0px 5px 10px 10px; border:1px solid #8888AA;\" align=right cellpadding=3 cellspacing=3 width=360\n|- align=\"center\" bgcolor=\"#dddddd\"\n|colspan=\"3\"| '''Clinton Comets'''"));

		assertEquals("2 [or] 1 [ا] 500 [lɒs] 1 [ˈændʒəˌlɪs] 0 [ˈaendʒəˌlɪs]",
				tokens("{{IPA|[l&#594;s &#712;&aelig;nd&#658;&#601;&#716;l&#618;s]}} &lt; or &#60; &copy; &#169;&#1575;"));

		assertEquals("500 [text1] 1 [text2] 1 [text3]",
				tokens("{{template|text1}} {{template|text2|text3}}"));

		assertEquals("",
				tokens("[[sr:Naslov]]"));

		assertEquals("500 [some] 1 [category] 1 [name]",
				tokens("[[Category:Some category name]]"));

		assertEquals("[Some category name]",
				categories("[[Category:Some category name]]"));

		assertEquals("500 [param1] 1 [param2] 1 [value2]",
				tokens("{{template|param1 = {{value1}}|param2 = value2}}"));

		assertEquals("500 [param1] 1 [value1] 1 [param2] 1 [value2]",
				tokens("{{template|param1 = [[target|value1]]|param2 = value2}}"));

		assertEquals("1 [wikipedia] 1 [is] 1 [accurate] 2 [and] 1 [it's] 0 [its] 1 [not] 1 [a] 1 [lie] 20 [see] 1 [kurir]",
				tokens("Wikipedia is accurate<ref>see Kurir</ref>, and it's not a lie."));

		assertEquals("1 [this] 1 [column] 1 [is] 1 [100] 1 [points] 1 [wide] 1 [this] 1 [column] 1 [is] 1 [200] 1 [points] 1 [wide] 1 [this] 1 [column] 1 [is] 1 [300] 1 [points] 1 [wide] 1 [blah] 1 [blah] 1 [blah]",
				tokens("{| border=\"1\" cellpadding=\"2\"\n|-\n|width=\"100pt\"|This column is 100 points wide\n|width=\"200pt\"|This column is 200 points wide\n|width=\"300pt\"|This column is 300 points wide\n|-\n|blah || blah || blah\n|}"));

		assertEquals("1 [first] 10 [second]",
				tokens("first\n\nsecond"));

		assertEquals("1 [u2] 1 [heading1]",
				tokens("u2 heading1"));

		assertEquals("1 [test] 1 [apostrophe's] 0 [apostrophes] 1 [and] 1 [other’s] 0 [others]",
				tokens("Test apostrophe's and other\u2019s."));

		assertEquals("1 [ａｂｃｄｅｆ] 0 [abcdef]",
				tokens("ＡＢＣＤＥＦ"));

		assertEquals("1 [１２３４５６７８９] 0 [123456789]",
				tokens("１２３４５６７８９"));


	}

	public void testHighlight(){
		this.iid = IndexId.get("enwiki");
		this.options = new TokenizerOptions.Highlight(false);

		assertEquals("1 [' ' GLUE FIRST_SECTION] 1 ['link' TEXT FIRST_SECTION] 1 [' ' GLUE FIRST_SECTION] 1 ['text' TEXT FIRST_SECTION] 1 [' ' GLUE FIRST_SECTION]",
				tokens("[[link text]]"));

		assertEquals("1 [' ' GLUE BULLETINS] 10 ['bullet1' TEXT BULLETINS] 1 [' ' SENTENCE_BREAK BULLETINS] 1 ['bullet2' TEXT BULLETINS]",
				tokens("* bullet1\n* bullet2"));

		assertEquals("1 [' ' GLUE FIRST_SECTION] 1 ['http' TEXT FIRST_SECTION] 1 ['://' MINOR_BREAK FIRST_SECTION] 1 ['en' TEXT FIRST_SECTION] 1 ['.' SENTENCE_BREAK FIRST_SECTION] 1 ['wikipedia' TEXT FIRST_SECTION] 1 ['.' SENTENCE_BREAK FIRST_SECTION] 1 ['org/' TEXT FIRST_SECTION] 0 ['org' TEXT FIRST_SECTION] 1 ['wiki' TEXT FIRST_SECTION] 1 [' ' GLUE FIRST_SECTION] 1 ['english' TEXT FIRST_SECTION] 1 [' ' GLUE FIRST_SECTION] 1 ['wiki' TEXT FIRST_SECTION] 1 [' ' GLUE FIRST_SECTION]",
				tokens("[http://en.wikipedia.org/wiki english wiki]"));

		assertEquals("1 [' ' GLUE IMAGE_CAT_IW] 1 ['image' TEXT IMAGE_CAT_IW] 1 [':' MINOR_BREAK IMAGE_CAT_IW] 1 ['argishti' TEXT IMAGE_CAT_IW] 1 [' ' GLUE IMAGE_CAT_IW] 1 ['monument' TEXT IMAGE_CAT_IW] 1 ['.' SENTENCE_BREAK IMAGE_CAT_IW] 1 ['jpg' TEXT IMAGE_CAT_IW] 1 [' | ' GLUE IMAGE_CAT_IW] 1 ['king' TEXT IMAGE_CAT_IW] 1 [' ' GLUE IMAGE_CAT_IW] 1 ['argishti' TEXT IMAGE_CAT_IW] 1 [' ' GLUE IMAGE_CAT_IW] 1 ['of' TEXT IMAGE_CAT_IW] 1 [' ' GLUE IMAGE_CAT_IW] 1 ['urartu' TEXT IMAGE_CAT_IW] 1 [' ' GLUE IMAGE_CAT_IW] 1 [' ' SENTENCE_BREAK FIRST_SECTION] 1 ['main' TEXT FIRST_SECTION] 1 [' ' GLUE FIRST_SECTION] 1 ['text' TEXT FIRST_SECTION]",
				tokens("[[Image:Argishti monument.JPG|thumb|King Argishti of Urartu]]\n\nMain text"));

		assertEquals("1 [' ' GLUE IMAGE_CAT_IW] 1 ['category' TEXT IMAGE_CAT_IW] 1 [':' MINOR_BREAK IMAGE_CAT_IW] 1 ['name' TEXT IMAGE_CAT_IW] 1 [' ' GLUE IMAGE_CAT_IW]",
				tokens("[[Category:Name|sort key]]"));

		assertEquals("1 [' ' GLUE TEMPLATE] 1 ['param1' TEXT TEMPLATE] 1 [' ' GLUE TEMPLATE] 1 ['value1' TEXT TEMPLATE] 1 [' ' GLUE TEMPLATE] 1 [' | ' GLUE TEMPLATE] 1 ['param2' TEXT TEMPLATE] 1 [' ' GLUE TEMPLATE] 1 ['value2' TEXT TEMPLATE] 1 [' ' GLUE FIRST_SECTION]",
				tokens("{{template|param1 = [[value1]]|param2 = value2}}"));

		assertEquals("1 [' ' GLUE TEMPLATE] 1 ['param1' TEXT TEMPLATE] 1 ['  | ' GLUE TEMPLATE] 1 ['param2' TEXT TEMPLATE] 1 [' ' GLUE TEMPLATE] 1 ['value2' TEXT TEMPLATE] 1 [' ' GLUE FIRST_SECTION]",
				tokens("{{template|param1 = {{value1}}|param2 = value2}}"));

		assertEquals("1 [' ' GLUE HEADING] 1 ['heading' TEXT HEADING] 1 [' ' GLUE HEADING] 1 ['1' TEXT HEADING] 1 [' ' GLUE HEADING] 1 [' ' GLUE NORMAL]",
				tokens("== Heading 1 ==\n"));

		assertEquals("1 ['wikipedia' TEXT FIRST_SECTION] 1 [' ' GLUE FIRST_SECTION] 1 ['is' TEXT FIRST_SECTION] 1 [' ' GLUE FIRST_SECTION] 1 ['accurate' TEXT FIRST_SECTION] 1 [', ' MINOR_BREAK FIRST_SECTION] 1 ['and' TEXT FIRST_SECTION] 1 [' ' GLUE FIRST_SECTION] 1 ['it's' TEXT FIRST_SECTION] 0 ['its' TEXT FIRST_SECTION] 1 [' ' GLUE FIRST_SECTION] 1 ['not' TEXT FIRST_SECTION] 1 [' ' GLUE FIRST_SECTION] 1 ['a' TEXT FIRST_SECTION] 1 [' ' GLUE FIRST_SECTION] 1 ['lie' TEXT FIRST_SECTION] 1 ['.' SENTENCE_BREAK FIRST_SECTION] 20 [' ' GLUE REFERENCE] 1 ['see' TEXT REFERENCE] 1 [' ' GLUE REFERENCE] 1 ['kurir' TEXT REFERENCE] 1 [' ' GLUE REFERENCE]",
				tokens("Wikipedia is accurate<ref>see Kurir</ref>, and it's not a lie."));

		assertEquals("1 [' | ' GLUE TABLE] 1 ['this' TEXT TABLE] 1 [' ' GLUE TABLE] 1 ['column' TEXT TABLE] 1 [' ' GLUE TABLE] 1 ['is' TEXT TABLE] 1 [' ' GLUE TABLE] 1 ['100' TEXT TABLE] 1 [' ' GLUE TABLE] 1 ['points' TEXT TABLE] 1 [' ' GLUE TABLE] 1 ['wide' TEXT TABLE] 1 [' ' GLUE TABLE] 1 ['this' TEXT TABLE] 1 [' ' GLUE TABLE] 1 ['column' TEXT TABLE] 1 [' ' GLUE TABLE] 1 ['is' TEXT TABLE] 1 [' ' GLUE TABLE] 1 ['200' TEXT TABLE] 1 [' ' GLUE TABLE] 1 ['points' TEXT TABLE] 1 [' ' GLUE TABLE] 1 ['wide' TEXT TABLE] 1 [' ' GLUE TABLE] 1 ['this' TEXT TABLE] 1 [' ' GLUE TABLE] 1 ['column' TEXT TABLE] 1 [' ' GLUE TABLE] 1 ['is' TEXT TABLE] 1 [' ' GLUE TABLE] 1 ['300' TEXT TABLE] 1 [' ' GLUE TABLE] 1 ['points' TEXT TABLE] 1 [' ' GLUE TABLE] 1 ['wide' TEXT TABLE] 1 [' | ' SENTENCE_BREAK TABLE] 1 ['blah' TEXT TABLE] 1 [' | ' GLUE TABLE] 1 ['blah' TEXT TABLE] 1 [' | ' GLUE TABLE] 1 ['blah' TEXT TABLE] 1 [' | ' GLUE FIRST_SECTION]",
				tokens("{| border=\"1\" cellpadding=\"2\"\n|-\n|width=\"100pt\"|This column is 100 points wide\n|width=\"200pt\"|This column is 200 points wide\n|width=\"300pt\"|This column is 300 points wide\n|-\n|blah || blah || blah\n|}"));

	}



	public String tokens(String text){
		StringBuilder sb = new StringBuilder();
		FastWikiTokenizerEngine parser = new FastWikiTokenizerEngine(text,iid,options);
		Token[] tokens = parser.parse().toArray(new Token[] {});
		for (int i = 0; i < tokens.length; i++) {
			Token token = tokens[i];
			sb.append(" "+token.getPositionIncrement()+" ");
			if(token instanceof ExtToken)
				sb.append("['" + token.termText() + "' "+ ((ExtToken)token).getType() + " " + ((ExtToken)token).getPosition() + "]");
			else
				sb.append("[" + token.termText() + "]");
		}
		return sb.toString().trim();
	}

	public String categories(String text){
		FastWikiTokenizerEngine parser = new FastWikiTokenizerEngine(text,iid,options);
		parser.parse();
		return parser.getCategories().toString();
	}


	public static void displayTokensForParser(String text) {
		FastWikiTokenizerEngine parser = new FastWikiTokenizerEngine(text,IndexId.get("enwiki"),new TokenizerOptions.Highlight(false));
		Token[] tokens = parser.parse().toArray(new Token[] {});
		for (int i = 0; i < tokens.length; i++) {
			Token token = tokens[i];
			System.out.println("[" + token.toString() + "] ");
		}
		System.out.println();
		String[] cats = parser.getCategories().toArray(new String[] {});
		if(cats.length!=0){
			System.out.print("CATEGORIES: ");
		}
		for (int i = 0; i < cats.length; i++) {
			String cat = cats[i];
			System.out.print("[" + cat + "] ");
		}
		if(cats.length!=0) System.out.println();
		HashMap<String,String> iw = parser.getInterwikis();
		if(iw.size()!=0){
			System.out.print("INTERWIKI: ");
		}
		for(Entry<String,String> t : iw.entrySet()){
			System.out.print("["+t.getKey()+"] => ["+t.getValue()+"] ");
		}
		if(iw.size()!=0) System.out.println();

		HashSet<String> keywords = parser.getKeywords();
		if(keywords.size()!=0){
			System.out.print("KEYWORDS: ");
		}
		for(String t : keywords){
			System.out.print("["+t+"] ");
		}
		if(keywords.size()!=0) System.out.println();

		System.out.println();
	}

	static void showTokens(String text){
		System.out.println("TEXT: "+text);
		System.out.flush();
		displayTokensForParser(text);
		System.out.flush();
	}

	public static void main(String args[]) throws Exception{
		Configuration.open();
		String text = "ATA, [[:link]] [[zh-min-nan:Something]] [[zh-min-nana:Something]] str_replace";
		showTokens(text);
		text = "''italic'' text bre! <nowiki><!-- see--></nowiki> <!-- nosee --> (ant) and some. it's stupid it's something and 5\"6' or more, links abacus";
		showTokens(text);
		text = ":''This article is about the humorist. For the [[Indo-Europeanist]] see [[Douglas Q. Adams]].''\n{{Infobox writer <!-- for more information see [[:Template:Infobox writer]] -->\n| name = Douglas Adams\n| image = Douglas adams cropped.jpg\n| caption = Douglas Adams signing books at ApacheCon 2000\n| birthdate = {{birth date|1952|3|11|df=yes}}\n| birthplace = [[Cambridge]], [[England]]\n| deathdate = {{Death date and age|2001|5|11|1952|3|11|df=yes}}\n| deathplace = [[Santa Barbara, California]], [[United States|U.S.]]\n| occupation = comedy writer, novelist, dramatist, fantasist\n| genre = [[Science fiction]], [[Comedy]]\n| movement =\n| influences = [[Richard Dawkins]] <ref>[http://www.bbc.co.uk/cult/hitchhikers/metaguide/radio.shtml Interview extract (in RealAudio format)] where Adams states the influences on his work.</ref>, [[Monty Python]], [[Neil Gaiman]], [[Robert Sheckley]], [[Kurt Vonnegut]], <br/>[[P. G. Wodehouse]]\n| influenced =\n| website = http://www.douglasadams.com/\n}} And now text";
		showTokens(text);
		text = "メインページ klarinet3.jpg Also, I think that the syntax could be changed to\n <nowiki>[[category:''category_name''|''sort_key''|''display_text'']]</nowiki>\nwith ''sort_key'' and ''display_text'' defaulting to ''category_name''.";
		showTokens(text);
		text = "[[meta:jao]] L.A. W. B.M.W and This. is a '''list of [[African]] countries and dependencies by [[population]]'''.\n\n{| border=\"1\" cellpadding=\"2\" cellspacing=\"0\" style=\"border-collapse:collapse; text-align:right;\"\n|- style=\"text-align:center; background:#efefef\"\n!Pos !! Country !! Population\n|-\n| align=\"left\" |-\n| align=\"left\" |'''Africa''' || 934,283,426\n|-\n";
		showTokens(text);
		text = "u.s. {{template|text}} {{template|text2|text3}} [http://ls2.wiki link]";
		showTokens(text);
		text = "Good-Thomas C# C++ and so on.. ";
		showTokens(text);
		text = "[[Image:Argishti monument.JPG|thumb|King Argishti of Urartu riding a chariot with two horses in Yerevan, Armenia in front of the Erebuni Museum.]]'''Urartu''' (Assyrian ''Urarṭu'', [[Urartian language|Urartian]] ''Biainili'') was an ancient [[kingdom (politics)|kingdom]] of [[Armenia]]&lt;ref&gt;&quot;Urartu.&quot; Columbia Electronic Encyclopedia. Columbia University Press.&lt;/ref&gt; located in the mountainous plateau between [[Asia Minor]], [[Mesopotamia]], and [[Caucasus mountains]], later known as the [[Armenian Highland]], and it centered around [[Lake Van]] (present-day eastern [[Turkey]]). The kingdom existed from ca. [[860s BC|860 BC]], emerging from Late Bronze Age [[Nairi]] polities, until [[585 BC]]. The name corresponds to the [[Bible|Biblical]] '''[[Mount Ararat|Ararat]]'''.";
		showTokens(text);
		text = "<!--uncomment if needed ''For the current|defunct federal|provincial electoral district, see [[Burnaby—Richmond (electoral district)]]'' --->\n'''Burnaby—Richmond''' (also known as '''Burnaby—Richmond—Delta''') was a federal [[electoral district (Canada)|electoral district]] in [[British Columbia]], [[Canada]], that was  represented in the [[Canadian House of Commons]] from 1949 to 1979.\nThis [[Riding (division)|riding]] was created as \"Burnaby—Richmond\" in 1947 from parts of [[New Westminster (electoral district)|New Westminster]] and [[Vancouver North]] ridings.\nThe name of the electoral district was changed in 1970 to \"Burnaby—Richmond—Delta\".\n\nIt was abolished in 1976 when it was redistributed into [[Burnaby (electoral district)|Burnaby]] and [[Richmond—South Delta]] ridings.\n\n==Election results==\n\n{{CanElec1|1949}}\n|-\n{{Canadian elections/Liberals}}\n|GOODE, Tom ||align=right|12,848\n|-\n{{Canadian elections/CCF}}\n|STEEVES, Dorothy Gretchen ||align=right|12,553\n";
		showTokens(text);
		text = "; list\n;list2.\n;list3";
		showTokens(text);
		text = "[http://ls2.wikimedia.org test inteface] for ls2";
		showTokens(text);
		text = "{{Infobox Country\n| fullcountryname = République française\n| image_flag = Flag_of_France.svg\n| image_coa = France_coa.png\n| image_location = Europe location FRA.png\n| nationalmotto = <small>''National [[motto]]: [[Liberty, Equality, Brotherhood]]<br>([[French language|French]]: Liberté, Egalité, Fraternité)''</small>\n| nationalsong = ''[[La Marseillaise]]''\n| officiallanguages = [[French language|French]]\n| populationtotal = 63,044,000<ref>Whole territory of the French Republic, including all the overseas departments and territories, but excluding the French territory of Terre Adélie in Antarctica where sovereignty is suspended since the signing of the [[Antarctic Treaty]] in 1959</ref>\n* [[Metropolitan France]]: 60,560,000<ref name=\"met euro\">Metropolitan (i.e. European) France only</ref>\n| populationrank = 42\n| populationdensity = 111/km&sup2\n| countrycapital = [[Paris]]\n| countrylargestcity = [[Paris]]\n| areatotal = 674,843 km²\n| arearank = 40\n| areawater =\n| areawaterpercent =\n| establishedin =\n| leadertitlename = [[President of France|President]]:\n:[[Nicolas Sarkozy]]\n[[List of Prime Ministers of France|Prime Minister]]:\n:[[François Fillon]]\n| currency = [[Euro]] (€)<ref>Whole of the French Republic except the overseas territories in the Pacific Ocean</ref>, [[CFP Franc]]<ref name=\"French over\">French overseas territories in the Pacific Ocean only</ref>\n| utcoffset = in [[European Summer Time|summer]]\n:[[Central European Time|CET]] ([[Coordinated Universal Time|UTC]]+1)<ref name=\"met euro\" />\n:[[Central European Summer Time|CEST]] ([[Coordinated Universal Time|UTC]]+2)<ref name=\"met euro\" />\n| dialingcode = 33\n| internettld = [[.fr]]\n}}";
		showTokens(text);
		text = "|something|else| is| there| to | see";
		showTokens(text);
		text = "-test 3.14 and U.S.A and more, .test more";
		showTokens(text);
		text = "{{IPA|[l&#594;s &#712;&aelig;nd&#658;&#601;&#716;l&#618;s]}} &lt; or &#60; &copy; &#169;&#1575; or &#x627;  ";
		showTokens(text);
		text = "| Unseen\n|-\n| \"Junior\"\n|\n| Goa'uld larva\n|} something";
		showTokens(text);
		text = "Peđa Stojaković";
		showTokens(text);
		text = "{{template|param|param2}} [bad link] and [http://single] and other text and [http://com good link] and after.";
		showTokens(text);
		text = "In 1921, Tagore and an agricultural economist [[Leonard K. Elmhirst set up the Institute for Rural Reconstruction in a village named Surul near his ashram at Shriniketan. An English language translation of Shriniketan would mean an abode (place) of peace. He recruited many scholars";
		showTokens(text);
		text = "after this blank: [[Category:states in India]]";
		showTokens(text);
		text = "|N/A\n|[[William Franklyn]]\n|[[Stephen Fry]]\n| colspan=\"2\" | [[:de:Rolf Boysen|Rolf Boysen]]\n|-\n|[[Arthur Dent]]\n|[[Simon Jones (actor)|Simon Jones]]\n|[[Chris Langham]]</br>\nScience Fiction Theatre of Liverpool production<ref name=\"SFTL\"/>\n| colspan=\"2\" | Simon Jones\n|[[Jonathan Lermit]]\n|Simon Jones\n|[[Martin Freeman]]\n| colspan=\"2\" | [[Felix von Manteuffel]]\n|-\n|[[Minor characters from The Hitchhiker's Guide to the Galaxy#Mr. Prosser|Prosser]]\n|[[Bill Wallis]]\n|";
		showTokens(text);
		text = "|-\n{{Canadian elections/Liberals}}\n|GOODE, Tom ||align=right|12,848\n|-| something || param | something2 || something3 || something4\n";
		showTokens(text);
		text = "{{sprotected2}}\n{{2otheruses|the corporation|the search engine|Google search}}\n{{Infobox_Company\n| company_name  = Google Inc.\n| company_logo  = [[Image:Google.png|240px]] <!-- FAIR USE of Google.png: see image description page at http://en.wikipedia.org/wiki/Image:Google.png for rationale -->\n| company_type  = [[public company|Public]] ({{nasdaq|GOOG}}), ({{lse|GGEA}})\n| foundation    = <!--Creation: {{flagicon|California}}[[Stanford University]], [[California]] (January 1996</br>Incorporation:-->{{flagicon|California}}[[Menlo Park, California]] ([[September 7]] [[1998]]<ref>{{cite journal |url=http://www.usatoday.com/money/industries/technology/2004-04-29-google-timeline_x.htm |title=The Rise of Google |journal=[[USA Today]] |date=[[April 29]], [[2004]] |accessdate=2007-08-01}}</ref>)\n| location_city = [[Mountain View, California]]\n| location_country = USA\n| key_people    = [[Eric E. Schmidt]], CEO/Director<br />[[Sergey Brin]], Co-Founder, Technology President<br />[[Lawrence E. Page|Larry E. Page]], Co-Founder, Products President <br />[[George Reyes]], CFO\n| industry      = [[Internet]], [[Computer software]]\n| products      = See [[list of Google products]]\n| revenue       = {{profit}}10.604 Billion [[U.S. Dollar|USD]] (2006)<ref name=\"financialtables\">{{cite web |url=http://investor.google.com/fin_data.html |title=Financial Tables |publisher=Google Investor Relations |accessdate=2007-02-23}}</ref>\n| net_income    = {{profit}}3.077 Billion [[U.S. dollar|USD]] (2006)<ref name=\"financialtables\"/>\n| num_employees = 15,916 ([[September 30]] [[2007]])\n| company_slogan = [[Don't be evil]]\n| homepage      = [http://www.google.com www.google.com]}}\n'''Google Inc.''' ({{nasdaq|GOOG}} and {{lse|GGEA}}) is an [[United States|American]]";
		showTokens(text);
		text = "*[[Jacques Offenbach]], ([[1819]]–[[1880]]), cellist, composer, initiator of the genre '[[operetta]]'\n*[[Joseph Goebbels]], ([[1897]]-[[1945]]), [[Adolf Hitler|Hitler]]'s minister of propaganda";
		showTokens(text);
		text = "{| border=1 align=right cellpadding=4 cellspacing=0 width=300 style=\"margin: 0.5em 0 1em 1em; background: #f9f9f9; border: 1px #aaaaaa solid; border-collapse: collapse; font-size: 95%;\"\n|+<big><big>'''Argentina'''</big></big>\n|-\n| style=\"background:#efefef;\" align=\"center\" colspan=\"2\" |\n{| border=\"0\" cellpadding=\"2\" cellspacing=\"0\"\n|-\n| align=\"center\" width=\"140px\" | [[Image:Argentina topo blank.jpg|250px]]\n|}\n|-\n| '''[[Continent]]''' || [[South America]]\n|-\n| '''[[Subregion]]''' || [[Southern Cone]]\n|-\n| '''[[Geographic coordinates]]''' || {{coor dm|34|00|S|64|00|W|type:country}}\n|-\n| '''[[Area]]'''<br>&nbsp;- Total <br>&nbsp;- Water\n| [[List of countries by area|Ranked 8th]]<br>[[1 E12 m²|2,766,890 km²]]<br> 30,200 km² (1.09%)\n|-\n| '''Coastline''' || 4,989 km\n|-\n| '''Land boundaries''' || 9,861 km\n|-\n| '''Countries bordered''' || [[Chile]] 5,308 km <br> [[Paraguay]] 1,880 km <br> [[Brazil]] 1,261 km <br> [[Bolivia]] 832 km <br> [[Uruguay]] 580 km\n|-\n| '''Highest point''' || [[Cerro Aconcagua]], 6,960 m\n|-\n| '''Lowest point''' || [[Laguna del Carbón]], -105 m\n|-\n| '''Longest river''' || [[Parana River]], 4,700 km\n|-\n| '''Largest inland body of water''' || [[Lake Buenos Aires]] 1,850 km²\n|-\n| '''Land Use'''<br>&nbsp;- Arable land<br><br>&nbsp;- Permanent<br>&nbsp;&nbsp;&nbsp;crops<br><br>&nbsp;-  Other ||<br>10.03 %<br><br>0.36 %<br><br>89.61 %  (2005 est.)\n|-\n| '''Irrigated Land''' || 15,500km²\n|-\n| '''[[Climate]]''': || [[Temperate]] to [[arid]] to [[subantarctic]]\n|-\n| '''[[Terrain]]''': || [[plain]]s, [[plateau]], [[mountain]]s\n|-\n| '''Natural resources''' ||  fertile [[plain]]s, [[lead]], [[zinc]], [[tin]], [[copper]], [[iron]] ore, [[manganese]], [[petroleum]], [[uranium]]\n|-\n| '''Natural hazards''' || [[earthquake]]s, [[windstorm]]s, heavy [[flood]]ing\n|-\n| '''Environmental issues''' || [[deforestation]], [[soil degradation]], [[desertification]], [[air]] and [[water pollution]]\n|}\n\n{{Argentina main topics}}\n[[Argentina]] is a country in southern [[South America]], situated between the [[Andes]] in the west and the southern [[Atlantic Ocean]] in the east. It is bordered by [[Paraguay]] and [[Bolivia]] in the north, [[Brazil]] and [[Uruguay]] in the northeast and [[Chile]] in the west.";
		showTokens(text);
		text = "The first line of Chapter One—\"Call me [[Ishmael (Moby-Dick)|Ishmael]].\"—is one of the most famous in literature.";
		showTokens(text);
		text = "{| style=\"margin:0px 5px 10px 10px; border:1px solid #8888AA;\" align=right cellpadding=3 cellspacing=3 width=360\n|- align=\"center\" bgcolor=\"#dddddd\"\n|colspan=\"3\"| '''Clinton Comets'''";
		showTokens(text);
		text = "The '''Moon''' ({{lang-la|Luna}}) is [[Earth]]'s only [[natural satellite]],";
		showTokens(text);
		text = "=== Adl - Ado... ===\n*[[Ada Adler|Adler, Ada]], (1878-1946)\n*[[Alfred Adler|Adler, Alfred]], (1870-1937), father of Individual Psychology\n";
		showTokens(text);
		text = "{{Portal|Hitchhiker's}}\n:''This page is about the franchise. You may be looking for the [[The Hitchhiker's Guide to the Galaxy (book)|book]], [[The Hitchhiker's Guide to the Galaxy (film)|film]], [[The Hitchhiker's Guide to the Galaxy (computer game)|computer game]], [[The Hitchhiker's Guide to the Galaxy (radio series)|radio series]] or [[The Hitchhiker's Guide to the Galaxy (TV series)|TV series]].\n[[Image:Hitchhiker's Guide (book cover).jpg|frame|The cover of the [[The Hitchhiker's Guide to the Galaxy (book)|first novel]] in the Hitchhiker's series, from a late 1990s printing. The cover features the [[42 Puzzle]] devised by [[Douglas Adams]].]] '''''The Hitchhiker's Guide to the Galaxy''''' is a [[Comic science fiction|science fiction comedy]]";
		showTokens(text);
		text = "{{Infobox Writer\n| name        = Douglas Adams\n| image       = Douglas adams cropped.jpg\n| caption     = <small>Douglas Adams signing books at ApacheCon 2000</small>\n| birth_date  = {{birth date|1952|3|11|df=yes}}\n| birth_place = [[Cambridge]], [[England]]\n| death_date  = {{Death date and age|2001|5|11|1952|3|11|df=yes}}\n| death_place = [[Santa Barbara, California]], [[United States|U.S.]]\n| occupation  = comedy writer, novelist, dramatist, fantasist\n| genre       = [[Science fiction]], [[Comedy]]\n| movement    = \n| magnum_opus = ''[[The Hitchhiker's Guide to the Galaxy]]'' series\n| influences  = [[Monty Python]], [[Kurt Vonnegut]], <br/>[[P. G. Wodehouse]]<ref>[http://www.bbc.co.uk/cult/hitchhikers/metaguide/radio.shtml Interview extract (in RealAudio format)] where Adams states the influences on his work.</ref>\n| influenced  = \n| website     = [http://www.douglasadams.com/ douglasadams.com]\n| footnotes   =\n}}";
		showTokens(text);
		text = ":''This page is about the franchise. You may be looking for the [[The Hitchhiker's Guide to the Galaxy (book)|book]], [[The Hitchhiker's Guide to the Galaxy (film)|film]], [[The Hitchhiker's Guide to the Galaxy (computer game)|computer game]], [[The Hitchhiker's Guide to the Galaxy (radio series)|radio series]] or [[The Hitchhiker's Guide to the Galaxy (TV series)|TV series]].\n[[Image:Hitchhiker's Guide (book cover).jpg|frame|The cover of the [[The Hitchhiker's Guide to the Galaxy (book)|first novel]] in the Hitchhiker's series, from a late 1990s printing. The cover features the [[42 Puzzle]] devised by [[Douglas Adams]].]] First section";
		showTokens(text);
		text = "*[http://www.google.com/gwt/n?u=http%3A%2F%2Fwww.digitaljournal.com%2Farticle%2F92325%2FSuicides_Imitating_Saddam_Hussein_s_Hanging_&_gwt_pg=0&hl=en&mrestrict=xhtml&q=+children+hang+themselves++imitating+saddam+hussein+video+blamed&source=m&output=xhtml&site=search Over dozens of children Suicides Imitating Saddam Hussein's Hanging]";
		showTokens(text);
		text = "{| border=\"1\" cellpadding=\"2\"\n|-\n|width=\"100pt\"|This column is 100 points wide\n|width=\"200pt\"|This column is 200 points wide\n|width=\"300pt\"|This column is 300 points wide\n|-\n|blah || blah || blah\n|}";
		showTokens(text);
		text = "Æ (ď), l' (ľ), תּפר ä, ö, ü; for instance, Ø ÓóÒò Goedel for Gödel; ĳ čakšire תפר   ";
		showTokens(text);
		text = "Dž (Dž), dž (dž), d' (ď), l' (ľ), t' (ť), IJ (Ĳ), ij (ĳ), LJ (Ǉ), Lj (ǈ), lj (ǉ). NJ (Ǌ), Nj (ǋ), nj (ǌ). All characters in parentheses are the single-unicode form; those not in parentheses are component character forms. There's also the issue of searching for AE (Æ), ae (æ), OE (Œ), & oe (œ).";
		showTokens(text);
		text = "ça Алекса́ндр Серге́евич Пу́шкин Đ đViệt Nam Đ/đ ↔ D/d  contains רוּחַ should be treated as though it contained ";
		showTokens(text);
		text = "[[Category:Blah Blah?!|Caption]], and [[:Category:Link to category]]";
		showTokens(text);
		text = "{{IPstack}} '''[[Hypertext]] Transfer [[communications protocol|Protocol]]''' ('''HTTP''') is a method used to transfer or convey information on the [[World Wide Web]]. Its original purpose was to provide a way to publish and retrieve [[HTML]] pages.";
		showTokens(text);
		text = "[[Slika:frizbi.jpg|десно|мини|240п|Frizbi za -{ultimate}-, 28cm, 175g]]";
		showTokens(text);
		text = "[[Image:frizbi.jpg|десно|мини|240п|Frizbi za -{ultimate}-, 28cm, 175g]]";
		showTokens(text);
		text = "===Остале везе===\n*[http://www.paganello.com Светски алтимет куп на плажи]";
		showTokens(text);
		text = "[http://www.google.com/] with [http://www.google.com/ search engine] i onda tekst";
		showTokens(text);
		text = "[[Bad link], [[Another bad link|With caption]";
		showTokens(text);
		text = "Before the bad link [[Unclosed link";
		showTokens(text);
		text = "This is <!-- Comment is this!! --> a text";
		showTokens(text);
		text = "This is <!-- Unclosed";
		showTokens(text);
		text = "This are [[bean]]s and more [[bla]]njah also Großmann";
		showTokens(text);
		text = "[[Category:Blah Blah?!]], and [[:Category:Link to something]] [[Category:Mathematics|Name]]";
		showTokens(text);
		text = "[[sr:Glavna stranica]], and [[:Category:Link to category]]";
		showTokens(text);
		text = "{{IPstack|name = Hundai}} '''[[Hypertext]] Transfer [[communications protocol|Protocol]]''' ('''HTTP''') is a method used to transfer or convey information on the [[World Wide Web]]. Its original purpose was to provide a way to publish and retrieve [[HTML]] pages.";
		showTokens(text);
		text = "Aaliyah was to have had a supporting role as Zee, the wife of [[Harold Perrineau Jr.]]'s character,";
		showTokens(text);
		text = "[[Image:Aaliyah-age-aint-94.jpg|right|200px|thumb|Cover of ''[[Age Ain't Nothing but a Number]]''.]] ";
		showTokens(text);

		// test keyword extraction
		FastWikiTokenizerEngine.KEYWORD_TOKEN_LIMIT = 10;
		text = "[[First link]]\n== Some caption ==\n[[Other link]]";
		showTokens(text);
		text = "[[First]] second third fourth and so on goes the ... [[last link]]";
		showTokens(text);
		text = "{{Something| param = {{another}}[[First]]  } }} }} }} [[first good]]s {{name| [[many]] many many tokens }} second third fourth and so on goes the ... [[good keyword]]";
		showTokens(text);
		text = "{| style=\"float: right; clear: right; background-color: transparent\"\n|-\n|{{Infobox Military Conflict|\n|conflict=1982 Lebanon War <br>([[Israel-Lebanon conflict]])\n|image=[[Image:Map of Lebanon.png|300px]]\n|caption=Map of modern Lebanon\n|date=June - September 1982\n|place=Southern [[Lebanon]]\n|casus=Two main causes:\n*Terrorist raids on northern Israel by [[PLO]] [[guerrilla]] based in Lebanon\n*the [[Shlomo Argov|shooting of Israel's ambassador]] by the [[Abu Nidal Organization]]<ref>[http://www.usatoday.com/graphics/news/gra/gisrael2/flash.htm The Middle East conflict], ''[[USA Today]]'' (sourced guardian.co.uk, Facts on File, AP) \"Israel invades Lebanon in response to terrorist attacks by PLO guerrillas based there.\"</ref><ref>{{cite book\n|author = Mark C. Carnes, John A. Garraty\n|title = The American Nation\n|publisher = Pearson Education, Inc.\n|date = 2006\n|location = USA\n|pages = 903\n|id = ISBN 0-321-42606-1\n}}</ref><ref>{{cite book\n|author= ''[[Time (magazine)|Time]]''\n|title = The Year in Review\n|publisher = Time Books\n|date = 2006\n|location = 1271 Avenue of the Americs, New York, NY 10020\n|id = ISSN: 1097-5721\n}} \"For decades now, Arab terrorists operating out of southern Lebanon have staged raids and fired mortar shells into northern Israel, denying the Israelis peace of mind. In the early 1980s, the terrorists operating out of Lebanon were controlled by Yasser Arafat's Palestine Liberation Organization (P.L.O.). After Israel's ambassador to Britain, Shlomo Argov, was shot in cold blood and seriously wounded by the Palestinian terror group Abu Nidal in London in 1982, fed-up Israelis sent tanks and troops rolling into Lebanon to disperse the guerrillas.\" (pg. 44-45)</ref><ref>\"The Palestine Liberation Organization (PLO) had been launching guerrilla attacks against Israel since the 1960s (see Palestine Liberation Organization). After the PLO was driven from Jordan in 1971, the organization established bases in southern Lebanon, from which it continued to attack Israel. In 1981 heavy PLO rocket fire on Israeli settlements led Israel to conduct air strikes in Lebanon. The Israelis also destroyed Iraq's nuclear reactor at Daura near Baghdad.";
		showTokens(text);



		ArticlesParser ap1 = new ArticlesParser("./test-data/indexing-articles.test");
		ArrayList<TestArticle> articles1 = ap1.getArticles();
		showTokens(articles1.get(articles1.size()-1).content);

		//if(true)
		//return;

		ArticlesParser ap = new ArticlesParser("./test-data/indexing-articles.test");
		ArrayList<TestArticle> articles = ap.getArticles();
		serializationTest(articles);
		//timeTest(articles);
	}

	static void timeTest(ArrayList<TestArticle> articles) throws IOException{
		long now = System.currentTimeMillis();
		int count = 0;
		for(int i=0;i<10000;i++){
			for(TestArticle article : articles){
				count++;
				String text = article.content;
				FastWikiTokenizerEngine parser = new FastWikiTokenizerEngine(text,IndexId.get("enwiki"),new TokenizerOptions(false));
				parser.parse();
			}
		}
		long delta = (System.currentTimeMillis()-now);
		System.out.println("Parser elapsed: "+delta+"ms, per article: "+((double)delta/count)+"ms");

		for(int i=0;i<10000;i++){
			for(TestArticle article : articles){
				String text = article.content;
				TokenStream strm = new LowerCaseTokenizer(new StringReader(text));
				while(strm.next()!=null);
			}
		}
		System.out.println("Tokenizer elapsed: "+(System.currentTimeMillis()-now)+"ms");
	}

	static void serializationTest(ArrayList<TestArticle> articles) throws IOException, ClassNotFoundException{
		ArrayList<ExtToken> tokens = new ArrayList<ExtToken>();
		for(TestArticle article : articles){
			String text = article.content;
			FastWikiTokenizerEngine parser = new FastWikiTokenizerEngine(text,IndexId.get("enwiki"),new TokenizerOptions.Highlight(false));
			for(Token t : parser.parse())
				tokens.add((ExtToken)t);
		}
		long now = System.currentTimeMillis();
		int total = 1000;
		long size = 0;
		System.out.println("Running "+total+" tests on "+tokens.size()+" tokens");
		for(int i=0;i<total;i++){
			/* ByteArrayOutputStream ba = new ByteArrayOutputStream();
				ObjectOutputStream out = new ObjectOutputStream(ba);
				out.writeObject(tokens);
				size += ba.size(); */
			//byte[] b = ExtToken.serializetokens);
			//size += b.length;
			//ObjectInputStream in = new ObjectInputStream(new ByteArrayInputStream(ba.toByteArray()));
			//ArrayList<ExtToken> some = (ArrayList<ExtToken>) in.readObject();
			//size += some.size();
		}
		long delta = (System.currentTimeMillis()-now);
		System.out.println("Parser elapsed: "+delta+"ms, per serialization: "+((double)delta/total)+"ms, size:"+size/total);

	}

	public void testVowels(){
		assertEquals("zdrv", FastWikiTokenizerEngine.deleteVowels("zdravo"));
		assertEquals("v g mlrd", FastWikiTokenizerEngine.deleteVowels("eve ga milorad"));
	}

}
