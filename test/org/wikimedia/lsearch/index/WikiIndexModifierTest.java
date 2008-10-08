package org.wikimedia.lsearch.index;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Date;
import java.util.HashSet;
import java.util.Hashtable;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenStream;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.wikimedia.lsearch.analyzers.Analyzers;
import org.wikimedia.lsearch.analyzers.FieldBuilder;
import org.wikimedia.lsearch.beans.Article;
import org.wikimedia.lsearch.beans.Redirect;
import org.wikimedia.lsearch.beans.Title;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.ranks.StringList;
import org.wikimedia.lsearch.related.RelatedTitle;
import org.wikimedia.lsearch.test.WikiTestCase;

public class WikiIndexModifierTest extends WikiTestCase {
	Document doc = null;
	Analyzer analyzer = null;
	Analyzer highlightAnalyzer = null;

	public void testMakeDocuments(){
		IndexId iid = IndexId.get("enwiki");
		String text = "Some very [[simple]] text used for testing\n== Heading 1 ==\nParagraph\n[[Category:Category1]]";
		int references = 100;
		int redirectTargetNamespace = -1;
		ArrayList<Redirect> redirects = new ArrayList<Redirect>();
		redirects.add(new Redirect(0,"Redirect",2));
		ArrayList<RelatedTitle> rel = new ArrayList<RelatedTitle>();
		rel.add(new RelatedTitle(new Title(0,"Related test"),50));
		Hashtable<String,Integer> anchors = new Hashtable<String,Integer>();
		anchors.put("Anchor",20);
		Date date = new Date();
		
		Article article = new Article(10,0,"Test page",text,null,
				references,redirectTargetNamespace,0,redirects,rel,anchors,date);
		
		analyzer = Analyzers.getIndexerAnalyzer(new FieldBuilder(iid));
		try{
			doc = WikiIndexModifier.makeDocument(article,new FieldBuilder(iid),iid,new HashSet<String>(),analyzer);
			assertEquals("1 [test] 1 [page] 3 [some] 1 [very] 1 [simple] 1 [text] 1 [used] 0 [use] 1 [for] 1 [testing] 0 [test] 500 [heading] 1 [1] 1 [paragraph] 1 [category1] 10 [test] 1 [page] 10 [anchor] 10 [redirect]", 
					tokens("contents"));
			assertEquals("1 [test] 1 [page]",
					tokens("title"));
			assertEquals("1 [test] 1 [page] 255 [anchor] 256 [redirect]",
					tokens("alttitle"));
			assertEquals("1 [related] 1 [test]",
					tokens("related"));
			assertEquals("1 [heading] 1 [1]",
					tokens("sections"));
			assertEquals("1 [category1]",
					tokens("category"));
			assertEquals("10",
					value("key"));
			assertEquals("103",
					value("rank"));
			assertEquals("1 [0:test page]",
					tokens("prefix"));
			assertEquals("1 [egap] 1 [tset]",
					tokens("reverse_title"));
			
		} catch(IOException e){
			fail();
		}
	}
	
	public void testMakeHighlightDocuments(){
		IndexId iid = IndexId.get("enwiki");
		String text = "Some very [[simple]] text used for testing\n== Heading 1 ==\nParagraph\n[[Category:Category1]]";
		int references = 100;
		int redirectTargetNamespace = -1;
		ArrayList<Redirect> redirects = new ArrayList<Redirect>();
		redirects.add(new Redirect(0,"Redirect",2));
		ArrayList<RelatedTitle> rel = new ArrayList<RelatedTitle>();
		rel.add(new RelatedTitle(new Title(0,"Related test"),50));
		Hashtable<String,Integer> anchors = new Hashtable<String,Integer>();
		anchors.put("Anchor",20);
		Date date = new Date();
		
		Article article = new Article(10,0,"Test page",text,null,
				references,redirectTargetNamespace,0,redirects,rel,anchors,date);
		
		analyzer = Analyzers.getHighlightAnalyzer(iid, false);
		try{
			doc = WikiIndexModifier.makeHighlightDocument(article,new FieldBuilder(iid),iid);
			assertEquals("1 [10]", 
					tokens("pageid"));
			assertEquals("1 [0:Test page]",
					tokens("key"));			
		} catch(IOException e){
			fail();
		}
	}
	
	public void testMakeTitleDocument(){
		IndexId iid = IndexId.get("en-titles");
		String text = "Some very simple text used for testing\n== Heading 1 ==\nParagraph\n[[Category:Category1]]";
		int references = 100;
		int redirectTargetNamespace = -1;
		ArrayList<Redirect> redirects = new ArrayList<Redirect>();
		redirects.add(new Redirect(0,"Redirect",2));
		ArrayList<RelatedTitle> rel = new ArrayList<RelatedTitle>();
		rel.add(new RelatedTitle(new Title(0,"Related test"),50));
		Hashtable<String,Integer> anchors = new Hashtable<String,Integer>();
		anchors.put("Anchor",20);
		Date date = new Date();
		
		Article article = new Article(10,0,"Test page",text,null,
				references,redirectTargetNamespace,0,redirects,rel,anchors,date);
		
		analyzer = Analyzers.getIndexerAnalyzer(new FieldBuilder(iid));
		highlightAnalyzer = Analyzers.getHighlightAnalyzer(iid,false);
		
		try{
			doc = WikiIndexModifier.makeTitleDocument(article,analyzer,highlightAnalyzer,iid,"wiki","enwiki",false,new HashSet<String>());
			assertEquals("1 [test] 1 [page] 255 [redirect]",
					tokens("alttitle"));
			assertEquals("wiki:10",
					value("pageid"));
			assertEquals("1 [wiki:0:Test page]",
					tokens("key"));
			assertEquals("wiki",
					value("suffix"));
			assertEquals("1 [enwiki]",
					tokens("dbname"));
			assertEquals("0",
					value("namespace"));
			assertEquals("Test page",
					value("title"));
		} catch(IOException e){
			fail();
		}
	}
	
	public void testSpellcheck(){
		IndexId iid = IndexId.get("enwiki");
		String text = "Some very [[simple]] text used for testing, used for testing of something\n== Heading 1 ==\nParagraph\n[[Category:Category1]]";
		int references = 100;
		int redirectTargetNamespace = -1;
		ArrayList<Redirect> redirects = new ArrayList<Redirect>();
		redirects.add(new Redirect(0,"Redirect",2));
		ArrayList<RelatedTitle> rel = new ArrayList<RelatedTitle>();
		rel.add(new RelatedTitle(new Title(0,"Related test"),50));
		Hashtable<String,Integer> anchors = new Hashtable<String,Integer>();
		anchors.put("Anchor",20);
		Date date = new Date();
		
		HashSet<String> stopWords = new HashSet<String>();
		stopWords.add("of"); stopWords.add("for");
		
		Article article = new Article(10,0,"Test for page",text,null,
				references,redirectTargetNamespace,0,redirects,rel,anchors,date);
		
		FieldBuilder fb = new FieldBuilder(iid,FieldBuilder.Case.IGNORE_CASE,FieldBuilder.Stemmer.NO_STEMMER,FieldBuilder.Options.SPELL_CHECK);
		fb.getBuilder().getFilters().setStopWords(stopWords);
		
		analyzer = Analyzers.getSpellCheckAnalyzer(iid,stopWords);
		
		try{
			doc = WikiIndexModifier.makeDocument(article,fb,iid,stopWords,analyzer,true);
			assertEquals("1 [test] 1 [for] 1 [page] 1 [test_for_page] 1 [some] 1 [page_some] 1 [very] 1 [some_very] 1 [simple] 1 [very_simple] 1 [text] 1 [simple_text] 1 [used] 1 [text_used] 1 [testing] 1 [used_for_testing] 1 [testing_used] 1 [of] 1 [something] 1 [testing_of_something] 1 [heading] 1 [something_heading] 1 [1] 1 [heading_1] 1 [paragraph] 1 [1_paragraph] 1 [category1] 1 [paragraph_category1] 1 [category1_test] 1 [anchor] 1 [page_anchor] 1 [redirect] 1 [anchor_redirect]",
					tokens("contents"));
			assertEquals("1 [test] 1 [for] 1 [test_for] 1 [page] 1 [for_page] 1 [test_for_page]",
					tokens("title"));
			StringList sl = new StringList(value("spellcheck_context"));
			assertEquals("[1, page, for, test, simple, heading]",
					sl.toCollection().toString());
		} catch(IOException e){
			fail(e.getMessage());
		}
	}
	
	public String tokens(String field){
		try{
			Field f = doc.getField(field);
			if(f == null)
				fail("No such field "+field);
			if(!f.isTokenized()){
				String val = value(field);
				Token t = new Token(val,0,val.length());
				return t.getPositionIncrement() + " ["+t.termText()+"]";
			}
			TokenStream ts = f.tokenStreamValue();
			if(ts == null && f.stringValue()!=null)
				ts = analyzer.tokenStream(field, f.stringValue());
			if(ts == null && f.readerValue()!=null)
				ts = analyzer.tokenStream(field, f.readerValue());
			if(ts == null)
				fail("No token stream for field "+field);
			Token t = null;
			StringBuilder sb = new StringBuilder();
			while((t = ts.next()) != null){
				sb.append(t.getPositionIncrement() + " ["+t.termText()+"] ");
			}
			return sb.toString().trim();
		} catch(Exception e){
			e.printStackTrace();
			fail(e.getMessage());
			return null;
		}
	}
	
	public String value(String field){
		return doc.getField(field).stringValue();
	}
	
}
