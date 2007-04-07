package org.mediawiki.scavenger.search;

import java.io.IOException;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.standard.StandardAnalyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.IndexWriter;
import org.apache.lucene.queryParser.ParseException;
import org.apache.lucene.queryParser.QueryParser;
import org.apache.lucene.search.BooleanClause;
import org.apache.lucene.search.BooleanQuery;
import org.apache.lucene.search.Hits;
import org.apache.lucene.search.IndexSearcher;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.Searcher;
import org.mediawiki.scavenger.Revision;
import org.mediawiki.scavenger.Title;
import org.mediawiki.scavenger.Wiki;

public class SearchIndex {
	Wiki wiki;
	String idxpath;
	
	public SearchIndex(Wiki w, String p) {
		wiki = w;
		idxpath = p;		
	}
	
	public void indexRevision(Revision r) throws SQLException, IOException {
		Title t = r.getPage().getTitle();
		IndexWriter writer = new IndexWriter(idxpath, new StandardAnalyzer(), true);
		Document doc = new Document();
		doc.add(new Field("key", t.getText(), Field.Store.YES, Field.Index.UN_TOKENIZED));
		doc.add(new Field("title", t.getText(), Field.Store.YES, Field.Index.TOKENIZED));
		doc.add(new Field("text", r.getText(), Field.Store.YES, Field.Index.TOKENIZED));
		writer.addDocument(doc);
		writer.close();
	}
	
	public List<SearchResult> search(String text, int limit) 
		throws IOException, ParseException {
		IndexReader reader = IndexReader.open(idxpath);
		Searcher searcher = new IndexSearcher(reader);
		Analyzer analyzer = new StandardAnalyzer();
		
		BooleanQuery query;
		Query bodyquery, titlequery;
		bodyquery = new QueryParser("text", analyzer).parse(text);
		bodyquery = bodyquery.rewrite(reader);
		titlequery = new QueryParser("title", analyzer).parse(text);
		titlequery = titlequery.rewrite(reader);
		query = new BooleanQuery();
		query.add(bodyquery, BooleanClause.Occur.SHOULD);
		query.add(titlequery, BooleanClause.Occur.SHOULD);
		
		Hits hits = searcher.search(query);
		int total = hits.length();
		List<SearchResult> results = new ArrayList<SearchResult>();
		
		for (int i = 0, end = Math.min(total, limit); i < end; ++i) {
			Document d = hits.doc(i);
			results.add(new SearchResult(d));
		}
		
		return results;
	}
}
