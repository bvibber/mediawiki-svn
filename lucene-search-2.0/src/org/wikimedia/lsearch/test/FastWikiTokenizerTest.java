package org.wikimedia.lsearch.test;
import java.io.IOException;
import java.io.StringReader;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map.Entry;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.LowerCaseTokenizer;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenStream;
import org.wikimedia.lsearch.analyzers.FastWikiTokenizerEngine;
import org.wikimedia.lsearch.index.WikiIndexModifier;

public class FastWikiTokenizerTest {		
		public static void displayTokensForParser(String text) {
			FastWikiTokenizerEngine parser = new FastWikiTokenizerEngine(text,"sr");
			Token[] tokens = parser.parse().toArray(new Token[] {});
			for (int i = 0; i < tokens.length; i++) {
				Token token = tokens[i];
				System.out.print("[" + token.termText() + "] ");
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
			System.out.println();
		}
		
		static void showTokens(String text){
			System.out.println("TEXT: "+text);
			System.out.flush();
			displayTokensForParser(text);	
			System.out.flush();
		}
		
		public static void main(String args[]) throws IOException{
			String text = "[[Category:Blah Blah?!|Caption]], and [[:Category:Link to category]]";
			showTokens(text);
			text = "{{IPstack}} '''[[Hypertext]] Transfer [[communications protocol|Protocol]]''' ('''HTTP''') is a method used to transfer or convey information on the [[World Wide Web]]. Its original purpose was to provide a way to publish and retrieve [[HTML]] pages.";
			showTokens(text);
			text = "[[Slika:frizbi.jpg|десно|мини|240п|Frizbi za -{ultimate}-, 28cm, 175g]]";
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
			text = "[[Category:Blah Blah?!]], and [[:Category:Link to category]]";
			showTokens(text);
			text = "[[sr:Glavna stranica]], and [[:Category:Link to category]]";
			showTokens(text);
			text = "{{IPstack|name = Hundai}} '''[[Hypertext]] Transfer [[communications protocol|Protocol]]''' ('''HTTP''') is a method used to transfer or convey information on the [[World Wide Web]]. Its original purpose was to provide a way to publish and retrieve [[HTML]] pages.";
			showTokens(text);
			
			//if(true)
			//	return;
			
			ArticlesParser ap = new ArticlesParser("./test-data/indexing-articles.test");
			ArrayList<TestArticle> articles = ap.getArticles();
			timeTest(articles);
		}
		
		static void timeTest(ArrayList<TestArticle> articles) throws IOException{
			long now = System.currentTimeMillis();
			for(int i=0;i<2000;i++){
				for(TestArticle article : articles){
					String text = article.content;
					FastWikiTokenizerEngine parser = new FastWikiTokenizerEngine(text);
					parser.parse();
				}
			}
			System.out.println("Parser elapsed: "+(System.currentTimeMillis()-now)+"ms");
						
			for(int i=0;i<2000;i++){
				for(TestArticle article : articles){
					String text = article.content;
					TokenStream strm = new LowerCaseTokenizer(new StringReader(text));
					while(strm.next()!=null);
				}
			}
			System.out.println("Tokenizer elapsed: "+(System.currentTimeMillis()-now)+"ms");
		}

}
