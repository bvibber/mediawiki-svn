package org.wikimedia.lsearch.test;

import java.io.IOException;
import java.io.StringReader;
import java.util.ArrayList;
import java.util.HashSet;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenStream;
import org.apache.lucene.analysis.cjk.CJKAnalyzer;
import org.apache.lucene.analysis.standard.StandardAnalyzer;
import org.apache.lucene.queryParser.ParseException;
import org.apache.lucene.queryParser.QueryParser;
import org.apache.lucene.search.Query;
import org.wikimedia.lsearch.analyzers.Aggregate;
import org.wikimedia.lsearch.analyzers.AggregateAnalyzer;
import org.wikimedia.lsearch.analyzers.Analyzers;
import org.wikimedia.lsearch.analyzers.ExtToken;
import org.wikimedia.lsearch.analyzers.FieldBuilder;
import org.wikimedia.lsearch.analyzers.FilterFactory;
import org.wikimedia.lsearch.analyzers.KeywordsAnalyzer;
import org.wikimedia.lsearch.analyzers.LanguageAnalyzer;
import org.wikimedia.lsearch.analyzers.ReusableLanguageAnalyzer;
import org.wikimedia.lsearch.analyzers.RelatedAnalyzer;
import org.wikimedia.lsearch.analyzers.SplitAnalyzer;
import org.wikimedia.lsearch.beans.Title;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.index.WikiIndexModifier;
import org.wikimedia.lsearch.ranks.StringList;
import org.wikimedia.lsearch.related.RelatedTitle;
import org.wikimedia.lsearch.util.MathFunc;

public class AnalysisTest {
	public static Token[] tokensFromAnalysis(Analyzer analyzer, String text, String field) throws IOException {
		TokenStream stream = analyzer.tokenStream(field, text);
		ArrayList tokenList = new ArrayList();
		while (true) {
			Token token = stream.next();
			if (token == null) break;
			tokenList.add(token);
		}
		return (Token[]) tokenList.toArray(new Token[0]);
	}
	
	public static void displayTokens(Analyzer analyzer, String text) throws IOException {		
		Token[] tokens = tokensFromAnalysis(analyzer, text, "contents");
		System.out.println(text);
		System.out.print(">> ");
		print(tokens);
		System.out.println();
	}
	
	protected static void print(Token[] tokens){
		for (int i = 0, j =0; i < tokens.length; i++, j++) {
			Token token = tokens[i];
			//System.out.print(token.getPositionIncrement()+" [" + token.termText() + "]("+token.startOffset()+","+token.endOffset()+") ");
			System.out.print(token.getPositionIncrement()+" [" + token.termText() + "] ");
			if(j > 50){
				System.out.println();
				j=0;
			}
				
		}		
	}
	
	public static void displayTokens2(Analyzer analyzer, String text) throws IOException {		
		Token[] tokens = tokensFromAnalysis(analyzer, text, "contents");
		System.out.println(text);
		System.out.print("contents >> ");	
		print(tokens);
		System.out.println();
		tokens = tokensFromAnalysis(analyzer, text, "stemmed");
		System.out.print("stemmed >> ");
		print(tokens);
		System.out.println();
		for(int i=1;i<=KeywordsAnalyzer.KEYWORD_LEVELS;i++){
			tokens = tokensFromAnalysis(analyzer, text, "redirect"+i);
			System.out.print("redirect"+i+" >> ");
			print(tokens);
			System.out.println();
		}
		for(int i=1;i<=RelatedAnalyzer.RELATED_GROUPS;i++){
			tokens = tokensFromAnalysis(analyzer, text, "related"+i);
			System.out.print("related"+i+" >> ");
			print(tokens);
			System.out.println();
		}
	}
	
	public static void main(String args[]) throws IOException, ParseException{
		Configuration.open();
		
		//serializeTest(Analyzers.getHighlightAnalyzer(IndexId.get("enwiki")));
		//testAnalyzer(Analyzers.getHighlightAnalyzer(IndexId.get("enwiki")),"Aaliyah");
		
		HashSet<String> stopWords = new HashSet<String>();
		stopWords.add("the"); stopWords.add("of"); stopWords.add("is"); stopWords.add("in"); stopWords.add("and"); stopWords.add("he") ;
		//Analyzer analyzer = Analyzers.getSpellCheckAnalyzer(IndexId.get("enwiki"),stopWords);
		//Analyzer analyzer = Analyzers.getSearcherAnalyzer(IndexId.get("enwiki"));
		Analyzer analyzer = Analyzers.getHighlightAnalyzer(IndexId.get("enwiki"));
		Analyzer old = new EnglishAnalyzer();
		String text = "a-b compatibly compatible Gödel; The who is a band. The who is Pascal's earliest work was in the natural and applied sciences where he made important contributions to the construction of mechanical calculators, the study of fluids, and clarified the concepts of pressure and vacuum by generalizing the work of Evangelista Torricelli. Pascal also wrote powerfully in defense of the scientific method.";
		displayTokens(analyzer,text);
		displayTokens(old,text);
		displayTokens(Analyzers.getSearcherAnalyzer(IndexId.get("zhwiki")),"末朝以來藩鎮割據and some plain english 和宦官亂政的現象 as well");
		displayTokens(analyzer,"Agreement reply readily Gödel;");
		displayTokens(old,"Agreement reply readily compatibly compatible");
		displayTokens(analyzer,"''' This is title ''' <i> italic</i>");
		displayTokens(analyzer,"{| border = 1\n| foo bar \n|}>");
		displayTokens(analyzer,"<math> x^2 = \\sin{2x-1}</math>");
		displayTokens(analyzer,"200000, 200.00, 20cm X2, b10, 10.b10");
		displayTokens(analyzer,"#REDIRECT [[Blahblah]]");
		displayTokens(analyzer,"Nešto šđčćžš, itd.. Ћирилично писмо");
		displayTokens(analyzer,"ضصثقفغعهخحشسيب لاتنمئءؤرﻻى");
		displayTokens(analyzer,"[[Image:Lawrence_Brainerd.jpg]], [[Image:Lawrence_Brainerd.jpg|thumb|300px|Lawrence Brainerd]]");
		displayTokens(analyzer,"{{Otheruses4|the Irish rock band|other uses|U2 (disambiguation)}}");
		displayTokens(analyzer,"{{Otheruses4|the Irish rock band|other uses|U2<ref>U2-ref</ref> (disambiguation)}} Let's see<ref>Seeing is...</ref> if template extraction works.\n==Some heading==\n And after that some text..\n\nAnd now? Not now. Then when?  ");
		
		ArrayList<String> l = new ArrayList<String>();
		l.add("0:Douglas Adams|0:Someone");
		l.add("0:Someone");
		l.add("0:Douglas Adams");
		l.add("");
		l.add("0:Heu");
		displayTokens(new SplitAnalyzer(10,true),new StringList(l).toString());
		
		analyzer = Analyzers.getSearcherAnalyzer(IndexId.get("viwiki"));
		displayTokens(analyzer,"ä, ö, ü; Đ đViệt Nam Đ/đ ↔ D/d lastone");
		
		analyzer = Analyzers.getSearcherAnalyzer(IndexId.get("dewiki"));
		displayTokens(analyzer," ä, ö, ü; for instance, Ø ÓóÒò Goedel for Gödel; čakšire");
		
		analyzer = Analyzers.getSearcherAnalyzer(IndexId.get("enwiki"));
		displayTokens(analyzer," ä, ö, ü; for instance, Ø ÓóÒò Goedel for Gödel; čakšire");
		
		analyzer = Analyzers.getSearcherAnalyzer(IndexId.get("srwiki"));
		displayTokens(analyzer," ä, ö, ü; for instance, Ø ÓóÒò Goedel for Gödel; čakšire");
		
		analyzer = Analyzers.getSearcherAnalyzer(IndexId.get("eswiki"));
		displayTokens(analyzer,"lógico y matemático");
		
		analyzer = Analyzers.getSearcherAnalyzer(IndexId.get("mlwiki"));
		displayTokens(analyzer,"കൊറിയ,“കൊറിയ”");
		
		printCodePoints("“കൊറിയ”");
		
		QueryParser parser = new QueryParser("contents",new CJKAnalyzer());
		Query q = parser.parse("プロサッカークラブをつくろう");
		System.out.println("Japanese in standard analyzer: "+q);
		displayTokens(new CJKAnalyzer(),"『パンツぱんくろう』というタイトルは、阪本牙城の漫画『タンクタンクロー』が元ネタになっているといわれる。ただし、このアニメと『タンクタンクロー』に内容的な直接の関係は全く無い。");
		displayTokens(Analyzers.getSearcherAnalyzer(IndexId.get("jawiki")),"『パンツぱんくろう』というタイトルは、阪本牙城の漫画『タンクタンクロー』が元ネタになっているといわれる。ただし、このアニメと『タンクタンクロー』に内容的な直接の関係は全く無い。");
		displayTokens(Analyzers.getSearcherAnalyzer(IndexId.get("jawiki")),"『パンツぱんくろう』というタjavaイトルはbalaton");
		displayTokens(Analyzers.getSearcherAnalyzer(IndexId.get("jawiki")),"パ ン");
		
		ArrayList<Aggregate> items = new ArrayList<Aggregate>();
		items.add(new Aggregate("douglas adams",10,IndexId.get("enwiki"),analyzer,"related",stopWords));
		items.add(new Aggregate("{{something|alpha|beta}} the selected works...",2.1f,IndexId.get("enwiki"),analyzer,"related",stopWords));
		items.add(new Aggregate("hurricane",3.22f,IndexId.get("enwiki"),analyzer,"related",stopWords));
		items.add(new Aggregate("and some other stuff",3.2f,IndexId.get("enwiki"),analyzer,"related",stopWords));
		displayTokens(new AggregateAnalyzer(items),"AGGREGATE TEST");
		
		// redirects?
		FieldBuilder builder = new FieldBuilder(IndexId.get("enwiki"));
		ArrayList<String> list = new ArrayList<String>();
		list.add("WikiWiki");
		list.add("What Is Wiki");
		list.add("A very long redirect indeed and dead");
		list.add("Short");
		list.add("Short is short");
		list.add("one two three four five");
		list.add("foo bar");
		/*analyzer = (Analyzer) Analyzers.getIndexerAnalyzer("Agreement reply readily",builder,list,null,null,null,null,null,null)[0];
		displayTokens2(analyzer,"");
		// related
		ArrayList<RelatedTitle> related = new ArrayList<RelatedTitle>();
		related.add(new RelatedTitle(new Title("0:Douglas Adams"),0.52));
		related.add(new RelatedTitle(new Title("0:March 8"),0.12));
		int p[] = MathFunc.partitionList(new double[] {0.52,0.12},5);
		analyzer = (Analyzer) Analyzers.getIndexerAnalyzer("Agreement reply readily",builder,null,null,related,p,null,null,null)[0];
		displayTokens2(analyzer,"");
		
		analyzer = (Analyzer) Analyzers.getIndexerAnalyzer("Pascal's earliest work was in the natural and applied sciences where he made important contributions to the construction of mechanical calculators, the study of fluids, and clarified the concepts of pressure and vacuum by generalizing the work of Evangelista Torricelli. Pascal also wrote powerfully in defense of the scientific method.",builder,null,null,null,null,null,null,null)[0];
		displayTokens2(analyzer,"");
		analyzer = (Analyzer) Analyzers.getIndexerAnalyzer("1,039/Smoothed Out Slappy Hours",new FieldBuilder(IndexId.get("itwiki")),null,null,null,null,null,null,null)[0];
		displayTokens2(analyzer,"");
		displayTokens(Analyzers.getSearcherAnalyzer(IndexId.get("itwiki")),"1,039/Smoothed Out Slappy Hours");
		
		ArrayList<Aggregate> items = new ArrayList<Aggregate>();
		items.add(new Aggregate("douglas adams",10,IndexId.get("enwiki"),false));
		items.add(new Aggregate("the selected works...",2.1f,IndexId.get("enwiki"),false));
		items.add(new Aggregate("hurricane",3.22f,IndexId.get("enwiki"),false));
		items.add(new Aggregate("and some other stuff",3.2f,IndexId.get("enwiki"),false));
		displayTokens(new AggregateAnalyzer(items),"AGGREGATE TEST"); */
		
		
		
		if(true)
			return;
		
		//testAnalyzer(new EnglishAnalyzer());
		testAnalyzer(Analyzers.getSearcherAnalyzer(IndexId.get("enwiki")));
		testAnalyzer(Analyzers.getSearcherAnalyzer(IndexId.get("dewiki")));
		testAnalyzer(Analyzers.getSearcherAnalyzer(IndexId.get("frwiki")));
		testAnalyzer(Analyzers.getSearcherAnalyzer(IndexId.get("srwiki")));
		testAnalyzer(Analyzers.getSearcherAnalyzer(IndexId.get("eswiki")));
		
		
	}
	
	private static void printCodePoints(String string) {
		char[] str = string.toCharArray();
		for(int i=0;i<str.length;i++){
			System.out.println(str[i]+"\t"+Integer.toHexString(Character.codePointAt(str,i)));
		}
	}

	public static void serializeTest(Analyzer analyzer) throws IOException{
		ArticlesParser ap = new ArticlesParser("./test-data/indexing-articles.test");
		ArrayList<TestArticle> articles = ap.getArticles();
		long start = System.currentTimeMillis();
		int total = 1000;
		double count=0;
		int size =0, size2 = 0;
		for(int i = 0 ; i<total; i++ ){
			for(TestArticle article : articles){
				count++;
				byte[] b = ExtToken.serialize(analyzer.tokenStream("",article.content));
				if(i == 0)
					size += b.length;
				else 
					size2 += b.length;
				tokensFromAnalysis(analyzer, article.content,"contents");
			}
		}
		long delta = System.currentTimeMillis() - start;
		System.out.println(delta+"ms ["+delta/count+"ms/ar] elapsed for analyzer "+analyzer+", size="+size+", size2="+size2);
	}
	
	public static void testAnalyzer(Analyzer analyzer) throws IOException{
		ArticlesParser ap = new ArticlesParser("./test-data/indexing-articles.test");
		ArrayList<TestArticle> articles = ap.getArticles();
		long start = System.currentTimeMillis();
		int total = 5000;
		double count=0;
		for(int i = 0 ; i<total; i++ ){
			for(TestArticle article : articles){
				count++;
				tokensFromAnalysis(analyzer, article.content,"contents");
			}
		}
		long delta = System.currentTimeMillis() - start;
		System.out.println(delta+"ms ["+delta/count+"ms/ar] elapsed for analyzer "+analyzer);
	}
	
	public static void testAnalyzer(Analyzer analyzer, String name) throws IOException{
		ArticlesParser ap = new ArticlesParser("./test-data/indexing-articles.test");
		ArrayList<TestArticle> articles = ap.getArticles();
		TestArticle article = null;
		for(TestArticle a : articles){
			if(a.title.equals(name)){
				article = a;
				break;
			}
		}
		long start = System.currentTimeMillis();
		int total = 5000;
		double count=0;
		int size =0;
		for(int i = 0 ; i<total; i++ ){
			count++;
			size += tokensFromAnalysis(analyzer, article.content,"contents").length;
		}
		long delta = System.currentTimeMillis() - start;
		System.out.println(delta+"ms ["+delta/count+"ms/ar] elapsed for analyzer "+analyzer+", size="+size/count);
	}
}
