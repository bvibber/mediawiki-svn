package org.wikimedia.lsearch.test;

import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;

import org.apache.lucene.analysis.Analyzer;
import org.wikimedia.lsearch.analyzers.Analyzers;
import org.wikimedia.lsearch.analyzers.ExtToken;
import org.wikimedia.lsearch.analyzers.ExtToken.Position;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.util.Utf8Set;

public class ExtTokenTest {
	public static void main(String[] args) throws Exception {
		Configuration.open();
		
		Analyzer analyzer = Analyzers.getHighlightAnalyzer(IndexId.get("enwiki"));
		
		String text = "Some extremely [[simple]] example text. With two sentences, by Šostakovič.";
		byte[] serialized = ExtToken.serialize(analyzer.tokenStream("",text));
		HashMap<Integer,Position> posMap = new HashMap<Integer,Position>();
		for(Position p : Position.values())
			posMap.put(p.ordinal(),p);
		ArrayList<ExtToken> tokens = ExtToken.deserialize(serialized,new Utf8Set(new HashSet<String>()),posMap);
		System.out.println("TOKENS: "+tokens);
		
		timeTest(analyzer);
	}
	
	public static void timeTest(Analyzer analyzer) throws IOException{
		ArticlesParser ap = new ArticlesParser("./test-data/indexing-articles.test");
		ArrayList<TestArticle> articles = ap.getArticles();		
		int total = 5000;
		int size = 0;
		TestArticle article = null;
		for(TestArticle a : articles){
			if(a.title.equals("Aaliyah")){
				article = a;
				break;
			}
		}
		byte[] serialized = ExtToken.serialize(analyzer.tokenStream("",article.content));
		long start = System.currentTimeMillis();
		HashMap<Integer,Position> posMap = new HashMap<Integer,Position>();
		for(Position p : Position.values())
			posMap.put(p.ordinal(),p);
		for(int i = 0 ; i<total; i++ ){
			size += ExtToken.deserialize(serialized,new Utf8Set(new HashSet<String>()),posMap).size();
		}
		long delta = System.currentTimeMillis() - start;
		System.out.println(delta+"ms ["+delta/(double)total+"ms/ar], size="+size/total);
	}
}
