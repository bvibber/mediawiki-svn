package org.wikimedia.lsearch.ranks;

import org.wikimedia.lsearch.analyzers.ArticlesParser;
import org.wikimedia.lsearch.analyzers.FastWikiTokenizerEngine;
import org.wikimedia.lsearch.analyzers.TestArticle;
import org.wikimedia.lsearch.ranks.ContextParser;

public class ContextParserTest {
	
	public static void main(String[] args){
		ArticlesParser ap = new ArticlesParser("./test-data/indexing-articles.test");
		for(TestArticle a : ap.getArticles()){
			ContextParser p = new ContextParser(a.content,null,null,null);
			System.out.println("ORIGINAL ARTICLE:");
			System.out.println(a.content);
			System.out.println();
			for(ContextParser.Context c : p.getContexts()){
				System.out.println(FastWikiTokenizerEngine.stripTitle(c.get(a.content)));
				System.out.println("----------------------------------------------------------------------------------------------");
			}
		}
	}
}
