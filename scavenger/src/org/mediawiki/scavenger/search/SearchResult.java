package org.mediawiki.scavenger.search;

import org.apache.lucene.document.Document;
import org.mediawiki.scavenger.Title;

public class SearchResult {
	Title t;
	
	public SearchResult(Document d) {
		t = new Title(d.get("key"));
	}
	
	public Title getTitle() {
		return t;
	}
}
