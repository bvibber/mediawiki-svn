package de.brightbyte.wikiword.builder;

import org.ardverk.collection.KeyAnalyzer;
import org.ardverk.collection.PatriciaTrie;

import de.brightbyte.wikiword.builder.util.ByteArrayKeyAnalyzer;

import junit.framework.TestCase;

public class ByteArrayKeyAnalyzerTest extends TestCase {

	public void testBitIndex() {
		ByteArrayKeyAnalyzer analyzer = new ByteArrayKeyAnalyzer();
		
		byte[] key = new byte[] {1, 13, 2};
		byte[] other = new byte[] {1, 14, 2};;
		
		assertEquals(KeyAnalyzer.EQUAL_BIT_KEY,
				analyzer.bitIndex(key, 0, 3*ByteArrayKeyAnalyzer.BITS, 
						key, 0, 3*ByteArrayKeyAnalyzer.BITS));
		
		assertEquals(1*8+6,
				analyzer.bitIndex(key, 0, 3*ByteArrayKeyAnalyzer.BITS, 
						other, 0, 3*ByteArrayKeyAnalyzer.BITS));

		assertEquals(KeyAnalyzer.EQUAL_BIT_KEY,
				analyzer.bitIndex(key, 0, 1*ByteArrayKeyAnalyzer.BITS, 
						other, 0, 1*ByteArrayKeyAnalyzer.BITS));

		assertEquals(0*8+6,
				analyzer.bitIndex(key, 1*ByteArrayKeyAnalyzer.BITS, 1*ByteArrayKeyAnalyzer.BITS, 
						other, 1*ByteArrayKeyAnalyzer.BITS, 1*ByteArrayKeyAnalyzer.BITS));

		assertEquals(1*8+4,
				analyzer.bitIndex(key, 0, 1*ByteArrayKeyAnalyzer.BITS, 
						other, 0, 2*ByteArrayKeyAnalyzer.BITS));

		assertEquals(1*8+4,
				analyzer.bitIndex(key, 0, 2*ByteArrayKeyAnalyzer.BITS, 
						other, 0, 1*ByteArrayKeyAnalyzer.BITS));

		assertEquals(KeyAnalyzer.EQUAL_BIT_KEY,
				analyzer.bitIndex(key,  2*ByteArrayKeyAnalyzer.BITS, 1*ByteArrayKeyAnalyzer.BITS, 
						other, 2*ByteArrayKeyAnalyzer.BITS, 1*ByteArrayKeyAnalyzer.BITS));

		assertEquals(KeyAnalyzer.NULL_BIT_KEY,
				analyzer.bitIndex(key,  0, 0, 
						other, 0, 0));

		assertEquals(KeyAnalyzer.NULL_BIT_KEY,
				analyzer.bitIndex(new byte[] { 0 },  0, ByteArrayKeyAnalyzer.BITS, 
						new byte[] { 0 }, 0, ByteArrayKeyAnalyzer.BITS));
	}

/*	public void testPatriciaTrie() {
		ByteArrayKeyAnalyzer analyzer = new ByteArrayKeyAnalyzer();
		PatriciaTrie<byte[], Integer> trie = new PatriciaTrie<byte[], Integer>(analyzer);
		
		trie.put(new byte[] {1, 2, 3}, 1);
		trie.put(new byte[] {7, 2, 3}, 2);
		trie.put(new byte[] {1, 2, 4}, 3);				
		trie.put(new byte[] {7, 2, 3}, 4);
		
		assertEquals(new Integer(1), trie.get(new byte[] {1, 2, 3}));
		assertEquals(new Integer(3), trie.get(new byte[] {1, 2, 4}));
		assertEquals(new Integer(4), trie.get(new byte[] {7, 2, 3}));

		trie.put(new byte[] {}, 0);
		assertEquals(new Integer(0), trie.get(new byte[] {}));

		trie.put(new byte[] { 0 }, 10);
		assertEquals(new Integer(10), trie.get(new byte[] { 0 }));
	}
	*/
}
