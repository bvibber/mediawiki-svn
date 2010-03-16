package org.wikimedia.lsearch.highlight;

import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Iterator;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.Term;
import org.apache.lucene.search.IndexSearcher;
import org.apache.lucene.search.Query;
import org.wikimedia.lsearch.analyzers.Analyzers;
import org.wikimedia.lsearch.analyzers.FieldBuilder;
import org.wikimedia.lsearch.analyzers.StopWords;
import org.wikimedia.lsearch.analyzers.WikiQueryParser;
import org.wikimedia.lsearch.analyzers.WikiQueryParser.NamespacePolicy;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.config.IndexRegistry;
import org.wikimedia.lsearch.highlight.Highlight;
import org.wikimedia.lsearch.highlight.HighlightResult;
import org.wikimedia.lsearch.search.NamespaceFilter;
import org.wikimedia.lsearch.search.SearcherCache;

public class HighlightTest {

	public static void testTokenizer() throws Exception {
		ArrayList<String> hits = new ArrayList<String>();
		hits.add("0:Douglas Adams");
		hits.add("0:The Hitchhiker's Guide to the Galaxy");
		hits.add("0:2001");
		hits.add("0:Boot");
		IndexId iid = IndexId.get("wikilucene.nspart1.sub1");
		FieldBuilder.BuilderSet bs = new FieldBuilder(iid).getBuilder();
		Analyzer analyzer = Analyzers.getSearcherAnalyzer(iid);
		ArrayList<String> stopWords = StopWords.getCached(iid);
		WikiQueryParser parser = new WikiQueryParser(bs.getFields().contents(),new NamespaceFilter(0),analyzer,bs,NamespacePolicy.IGNORE,stopWords);
		Query q = parser.parse("miniseries");
		HashSet<Term> termSet = new HashSet<Term>();
		q.extractTerms(termSet);
		Iterator<Term> it = termSet.iterator();
		while(it.hasNext()){
			if(!(it.next().field().equals("contents")))
				it.remove();
		}
		Term[] terms = termSet.toArray(new Term[] {});
		IndexSearcher searcher = SearcherCache.getInstance().getLocalSearcher(iid);
		int[] df = searcher.docFreqs(terms);
		Highlight.highlight(hits,iid,terms,df,searcher.maxDoc(),parser.getWordsClean(),StopWords.getPredefinedSet(iid),false,null,false,false);
	}
	
	public static void timeTest(String dbname, String dbnameSrc) throws Exception {
		IndexId src = IndexId.get(dbnameSrc);
		IndexId iid = IndexId.get(dbname).getHighlight();
		IndexReader reader = IndexReader.open(IndexRegistry.getInstance().getCurrentSearch(src).path);
		int maxDoc = reader.maxDoc();
		
		ArrayList<String> words = new ArrayList<String>();
		words.add("in");
		words.add("the");
		words.add("some");
		words.add("book");
		Term[] terms = new Term[5];
		terms[0] = new Term("contents","in");
		terms[1] = new Term("contents","the");
		terms[2] = new Term("contents","some");
		terms[3] = new Term("contents","som");
		terms[4] = new Term("contents","book");
		int[] df = new int[5];
		df[0] = 1000;
		df[1] = 4000;
		df[2] = 200;
		df[3] = 500;
		df[4] = 100;
		HashSet<String> stopWords = StopWords.getPredefinedSet(src);
		int count = 0;		
		int total = 10000;
		long start = System.currentTimeMillis();
		for(int i=0;i<total;i++){
			ArrayList<String> hits = new ArrayList<String>();
			for(int j=0;j<10;j++){
				int docid = (int)(Math.random()*maxDoc);				
				Document doc = reader.document(docid);
				hits.add(doc.get("namespace")+":"+doc.get("title"));
			}
			Highlight.ResultSet rs = Highlight.highlight(hits,iid,terms,df,maxDoc,words,stopWords,false,null,false,false);
			HashMap<String,HighlightResult> res =  rs.highlighted;
			count += res.size();
			if(i!=0 && i % 200 == 0){
				long delta = System.currentTimeMillis() - start;
				System.out.println("["+formatTime(delta)+"] "+((float)delta/(count/10))+" ms / 10 articles, found "+count+"/"+(total*10));
			}
		}
		long delta = System.currentTimeMillis() - start;
		System.out.println("Elapsed "+formatTime(delta)+", "+((float)delta/(count/10))+" ms / 10 articles, found "+count+"/"+(total*10));
	}

	private static String formatTime(long l) {
		l /= 1000;
		if(l >= 3600) return l/3600+"h "+(l%3600)/60+"m "+(l%60)+"s";
		else if(l >= 60) return (l%3600)/60+"m "+(l%60)+"s";
		else return l+"s";
	}

	/**
	 * @param args
	 */
	public static void main(String[] args) throws Exception {
		Configuration.open();
		//testTokenizer();
		timeTest("enwiki.nspart1.sub2","simplewiki.nspart1.sub1");
	}

}
