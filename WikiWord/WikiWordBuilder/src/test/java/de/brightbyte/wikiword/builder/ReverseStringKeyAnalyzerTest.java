package de.brightbyte.wikiword.builder;

import org.ardverk.collection.KeyAnalyzer;
import org.ardverk.collection.PatriciaTrie;

import junit.framework.TestCase;

public class ReverseStringKeyAnalyzerTest extends TestCase {

	public void testBitIndex() {
		ReverseStringKeyAnalyzer analyzer = new ReverseStringKeyAnalyzer();
		
		String key = "AAXB";
		String other = "AAYB";
		assertEquals(1*16+8+7,
				analyzer.bitIndex(key, 0, 4*ReverseStringKeyAnalyzer.LENGTH, 
						other, 0, 4*ReverseStringKeyAnalyzer.LENGTH));

		assertEquals(KeyAnalyzer.EQUAL_BIT_KEY,
				analyzer.bitIndex(key, 0, 1*ReverseStringKeyAnalyzer.LENGTH, 
						other, 0, 1*ReverseStringKeyAnalyzer.LENGTH));

		assertEquals(0+8+7,
				analyzer.bitIndex(key, 1*ReverseStringKeyAnalyzer.LENGTH, 1*ReverseStringKeyAnalyzer.LENGTH, 
						other, 1*ReverseStringKeyAnalyzer.LENGTH, 1*ReverseStringKeyAnalyzer.LENGTH));

		assertEquals(1*16+8+1,
				analyzer.bitIndex(key, 0, 1*ReverseStringKeyAnalyzer.LENGTH, 
						other, 0, 2*ReverseStringKeyAnalyzer.LENGTH));

		assertEquals(1*16+8+1,
				analyzer.bitIndex(key, 0, 2*ReverseStringKeyAnalyzer.LENGTH, 
						other, 0, 1*ReverseStringKeyAnalyzer.LENGTH));

		assertEquals(KeyAnalyzer.EQUAL_BIT_KEY,
				analyzer.bitIndex(key,  2*ReverseStringKeyAnalyzer.LENGTH, 2*ReverseStringKeyAnalyzer.LENGTH, 
						other, 2*ReverseStringKeyAnalyzer.LENGTH, 2*ReverseStringKeyAnalyzer.LENGTH));

		assertEquals(KeyAnalyzer.NULL_BIT_KEY,
				analyzer.bitIndex(key,  0, 0, 
						other, 0, 0));

		assertEquals(KeyAnalyzer.NULL_BIT_KEY,
				analyzer.bitIndex("\0",  0, ReverseStringKeyAnalyzer.LENGTH, 
						"\0", 0, ReverseStringKeyAnalyzer.LENGTH));
	}

	public void testPatriciaTrie() {
		ReverseStringKeyAnalyzer analyzer = new ReverseStringKeyAnalyzer();
		PatriciaTrie<String, Integer> trie = new PatriciaTrie<String, Integer>(analyzer);
		
		trie.put("ABC", 1);
		trie.put("XBC", 2);
		trie.put("ABD", 3);				
		trie.put("XBC", 4);
		
		assertEquals(new Integer(1), trie.get("ABC"));
		assertEquals(new Integer(3), trie.get("ABD"));
		assertEquals(new Integer(4), trie.get("XBC"));

		trie.put("", 0);
		assertEquals(new Integer(0), trie.get(""));

		trie.put("\0", 10);
		assertEquals(new Integer(10), trie.get("\0"));
	}
	
}
