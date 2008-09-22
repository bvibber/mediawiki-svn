package org.wikimedia.lsearch.search;

import java.util.HashMap;
import java.util.Map.Entry;

import org.apache.lucene.search.ArticleNamespaceScaling;

import junit.framework.TestCase;

public class ArticleNamespaceScaleTest extends TestCase {
	public void testComplex(){
		HashMap<Integer,Float> map = new HashMap<Integer,Float>();
		// (0, 1) (2, 0.005) (3, 0.001) (4, 0.01), (6, 0.02), (10, 0.0005), (12, 0.01), (14, 0.02)
		map.put(0,1f);
		map.put(2,0.005f);
		map.put(3,0.001f);
		map.put(4,0.01f);
		map.put(6,0.02f);
		map.put(10,0.0005f);
		map.put(12,0.01f);
		map.put(14,0.02f);
		
		ArticleNamespaceScaling a = new ArticleNamespaceScaling(map);
		
		for(Entry<Integer,Float> e : map.entrySet())
			assertEquals(e.getValue(),a.scaleNamespace(e.getKey()));
		
		assertEquals(0.0025f,a.scaleNamespace(5));		
	}
	
	public void testDefault(){
		ArticleNamespaceScaling a = new ArticleNamespaceScaling(new HashMap<Integer,Float>());
		assertEquals(1f,a.scaleNamespace(0));
		assertEquals(1f,a.scaleNamespace(100));
		
		assertEquals(0.25f,a.scaleNamespace(1));
		assertEquals(0.25f,a.scaleNamespace(101));
	}
}
