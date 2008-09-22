package org.wikimedia.lsearch.util;

import java.util.ArrayList;

import org.apache.lucene.search.PrefixQuery;
import org.wikimedia.lsearch.analyzers.WordNet;

public class Test {

	String prefixFilter;
	
	protected String extractPrefixFilter(String queryText){
		prefixFilter = "";
		int start = 0;
		while(start < queryText.length()){
			int end = indexOf(queryText,'"',start); // begin of phrase
			int inx = queryText.indexOf("prefix:"); 
			if(inx >=0 && inx < end){
				prefixFilter = queryText.substring(inx+"prefix:".length());
				return queryText.substring(0,inx);
			}
			start = end+1;
			if(start < queryText.length()){
				// skip phrase
				start = indexOf(queryText,'"',start) + 1;
			}
		}
		return queryText;
	}
	
	protected int indexOf(String string, char needle, int start){
		int inx = string.indexOf(needle,start);
		if(inx == -1)
			return string.length();
		else
			return inx;
	}
	
	protected void test(String s){
		System.out.println(s+" -> "+extractPrefixFilter(s)+" prefix="+prefixFilter);
	}
	
	protected void getKeyExact(String key){
		int sc = key.indexOf(':')+1;
		String k1 = key.substring(0,sc)+key.substring(sc,sc+1).toUpperCase()+( (sc+1<key.length())? key.substring(sc+1) : "");
		System.out.println(key+" -> "+k1);
	}
	
	protected void testWordnet(String str){
		String[] parts = str.split(" ");
		ArrayList<String> list = new ArrayList<String>();
		for(String p : parts)
			list.add(p);
		System.out.println(str+" -> " + WordNet.replaceOne(list,"en"));
	}
	
	/**
	 * @param args
	 */
	public static void main(String[] args) {
		
		Test t = new Test();
		t.test("Simple string");
		t.test("Simple \"string\"");
		t.test("More comple \"something\" or other");
		t.test("\"something\" or other");
		t.test("prefix:something");
		t.test("prefix:something else \"quoted\"");		
		t.test("query here prefix:something");
		t.test("query \"here prefix:something\"");
		t.test("\"here prefix:something\"");
		
		t.getKeyExact("0:nesto");
		t.getKeyExact("0:n");
		
		System.out.println("some #other".replaceFirst("#.*",""));
		System.out.println("#comment".replaceFirst("#.*",""));
		
		t.testWordnet("1st something 100 two u.k");

	}

}
