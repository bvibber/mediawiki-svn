package org.wikimedia.lsearch.util;

import java.util.HashMap;

import org.apache.lucene.document.Document;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.Term;
import org.apache.lucene.index.TermDocs;
import org.apache.lucene.index.TermEnum;

public class ListAnchors {
	public static void main(String[] args) throws Exception {
		if(args.length != 2){
			System.out.println("Usage: ListAnchors <path to links index> <target article (ns:key)> ");
			return;
		}
		
		String key = args[1];
		String path = args[0];
		
		IndexReader reader = IndexReader.open(path);
		
		System.out.println("Links to article "+key);
		
		String prefix = key+"|";
		TermEnum te = reader.terms(new Term("anchors",prefix));
		for(;te.term()!=null;te.next()){
			String t = te.term().text();
			if(t.startsWith(prefix)){
				String anchor = t.substring(t.indexOf('|')+1);
				TermDocs td = reader.termDocs(new Term("anchors",t));
				while(td.next()){ // this will skip deleted docs, while docFreq won't				
					Document d = reader.document(td.doc()); 
					System.out.println("["+d.get("article_key") + "] with [" + anchor+"]");
				}
			} else
				break;
		}
		
	}
}

