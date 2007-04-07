package org.mediawiki.scavenger.search;

import java.io.IOException;
import java.io.StringReader;

import org.apache.lucene.analysis.standard.StandardAnalyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.highlight.Highlighter;
import org.apache.lucene.search.highlight.QueryScorer;
import org.apache.lucene.search.highlight.SimpleHTMLEncoder;
import org.apache.lucene.search.highlight.SimpleHTMLFormatter;
import org.mediawiki.scavenger.Title;

public class SearchResult {
	Title t;
	String hi;
	
	public SearchResult(Query q, Document d) throws IOException {
		t = new Title(d.get("key"));
		
		Highlighter h = new Highlighter(
				new SimpleHTMLFormatter(
						"<span class=\"searchterm\">",
						"</span>"),
				new SimpleHTMLEncoder(),
				new QueryScorer(q, "text"));
		String text = d.get("text");
		hi = h.getBestFragments(
				new StandardAnalyzer().tokenStream("body", new StringReader(text)), 
				text, 3, "...");
	}
	
	public Title getTitle() {
		return t;
	}
	
	public String getContext() {
		return hi;
	}
}
