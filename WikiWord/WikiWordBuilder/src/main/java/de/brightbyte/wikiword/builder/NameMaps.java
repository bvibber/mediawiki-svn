package de.brightbyte.wikiword.builder;

import java.util.HashMap;
import java.util.Map;

import org.ardverk.collection.PatriciaTrie;
import org.ardverk.collection.StringKeyAnalyzer;

public class NameMaps {
		public static <V> Map<String, V> newMap(String mapType) {
			if (mapType.equals("patricia")) {
					try {
						PatriciaTrie<String, V> trie = new PatriciaTrie<String, V>(new StringKeyAnalyzer());
						return trie;
					}  catch (LinkageError ex) {
						//noop
					}
			}
			
			return new HashMap<String, V>();
		}
}
