package org.wikimedia.lsearch.test;
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

public class FastWikiTokenizerTest {		
		public static void displayTokensForParser(String text) {
			FastWikiTokenizerEngine parser = new FastWikiTokenizerEngine(text,IndexId.get("enwiki"),new TokenizerOptions.Highlight());
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
			String text = "(ant) and some. it's stupid it's something and 5\"6' or more";
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
				FastWikiTokenizerEngine parser = new FastWikiTokenizerEngine(text,IndexId.get("enwiki"),new TokenizerOptions.Highlight());
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

}
