package org.wikimedia.lsearch.search;

import org.apache.lucene.search.ArticleNamespaceScaling;
import org.wikimedia.lsearch.test.WikiTestCase;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.search.SearchEngine.PrefixMatch;

public class PrefixMatchTest extends WikiTestCase {
	public void testDeserialization(){
		IndexId iid = IndexId.get("enwiki");
		PrefixMatch m = new PrefixMatch("0:Some_title 10 ",iid.getNamespaceScaling());
		assertEquals(m.key,"0:Some title");
		assertEquals(m.score,10d);
		assertEquals(m.redirect,"");
		
		m = new PrefixMatch("1:Some_title 10 0:Redirect",iid.getNamespaceScaling());
		assertEquals(m.key,"1:Some title");
		assertEquals(m.score,(double)(10*ArticleNamespaceScaling.talkPageScale));
		assertEquals(m.redirect,"0:Redirect");
	}
}
