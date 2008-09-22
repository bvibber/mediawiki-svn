package org.apache.lucene.search;

import org.wikimedia.lsearch.search.NamespaceFilter;


public class ArticleNamespaceScalingImpl {
	NamespaceFilter defaultSearch;

	public ArticleNamespaceScalingImpl(NamespaceFilter defaultSearch){
		this.defaultSearch = defaultSearch;
	}
	
	public float scaleNamespace(int ns) {
		switch(ns){
		case 0: return 1;
		case 2: return 0.005f;
		case 3: return 0.001f;
		case 4: return 0.01f;
		case 6: return 0.02f;
		case 10: return 0.0005f;
		case 12: return 0.01f;
		case 14: return 0.02f;
		default:
			return 1;
		}		
	}

}
