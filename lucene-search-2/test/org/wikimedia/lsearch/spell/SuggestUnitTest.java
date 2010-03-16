package org.wikimedia.lsearch.spell;

import java.io.IOException;
import java.util.HashMap;
import java.util.Map;
import java.util.TreeMap;

import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.search.NamespaceFilter;
import org.wikimedia.lsearch.search.SearcherCache;
import org.wikimedia.lsearch.spell.dist.EditDistance;
import org.wikimedia.lsearch.test.WikiTestCase;

public class SuggestUnitTest extends WikiTestCase {
	public void testMakeNamespaces() throws IOException {
		IndexId iid = IndexId.get("entest");
		Suggest sug = new Suggest(iid);
		assertNull(sug.makeNamespaces(new NamespaceFilter("0")));
		assertNull(sug.makeNamespaces(new NamespaceFilter("0,2,4,14")));
		assertNotNull(sug.makeNamespaces(new NamespaceFilter("0,2,4,100")));
		assertEquals("[0, 100, 2, 4]",sug.makeNamespaces(new NamespaceFilter("0,2,4,100")).namespaces.toString());
	}

	public Map<Integer,Integer> getSpaceMap(String str1, String str2){
		EditDistance ed = new EditDistance(str1);
		int d[][] = ed.getMatrix(str2);
		// map: space -> same space in edited string
		TreeMap<Integer,Integer> spaceMap = new TreeMap<Integer,Integer>(); 
		new Suggest().extractSpaceMap(d,str1.length(),str2.length(),spaceMap,str1,str2);
		return spaceMap;
	}
	
	public void testExtractSpaceMap() throws IOException {
		assertEquals("{}",getSpaceMap(".999","0 999").toString());
		assertEquals("{4=3}",getSpaceMap("some string","som estring").toString());		
		assertEquals("",getSpaceMap("               a   ","         b         ").toString());
	}

}
