package org.wikimedia.lsearch.analyzers;

import java.util.ArrayList;

import org.wikimedia.lsearch.test.WikiTestCase;

public class WordNetTest extends WikiTestCase {
	
	protected ArrayList<String> list(String[] strs){
		ArrayList<String> l = new ArrayList<String>();
		for( String s : strs )
			l.add(s);
		return l;
	}
	public void testWordNet(){
		assertEquals("[[ten]]", WordNet.replaceOne(list(new String[]{"10"}),"en").toString());
		assertEquals("[[ten, riders]]", WordNet.replaceOne(list(new String[]{"10", "riders"}),"en").toString());
		assertEquals("[[in, united, kingdom]]", WordNet.replaceOne(list(new String[]{"in", "uk"}),"en").toString());
		assertEquals("[]",WordNet.replaceOne(list(new String[]{"in", "us"}),"en").toString());
		assertEquals("[[in, the, united, states]]",WordNet.replaceOne(list(new String[]{"in", "the", "us"}),"en").toString());
	}

}
