package org.wikimedia.lsearch.util;

import java.util.Arrays;

import org.apache.lucene.document.Document;
import org.apache.lucene.document.Fieldable;
import org.apache.lucene.index.IndexReader;

public class Examine {

	/**
	 * syntax: Examine path docid
	 * @param args
	 */
	public static void main(String[] args) throws Exception {
		int docid = 0;
		String path = "";
		if(args.length != 2){
			System.out.println("Syntax: Examine <path to index> <docid>");
			return;
		}
		path = args[0];
		docid = Integer.parseInt(args[1]);
		
		IndexReader r = IndexReader.open(path);
		Document d = r.document(docid);
		System.out.println("docid : "+docid);
		for( Object fieldObj : d.getFields() ){
			Fieldable field = (Fieldable)fieldObj;
			if(field.isStored()){
				if(field.name().endsWith("_meta")){
					System.out.println(field.name()+": "+Arrays.toString(field.binaryValue()));
				} else
					System.out.println(field.name()+": "+field.stringValue());
			}
		}
	}
	
}
