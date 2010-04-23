package de.brightbyte.wikiword.builder;

import java.io.File;
import java.io.IOException;
import java.io.UnsupportedEncodingException;
import java.net.URL;
import java.security.NoSuchAlgorithmException;
import java.util.Arrays;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Map;
import java.util.Set;

import org.ardverk.collection.PatriciaTrie;
import org.ardverk.collection.StringKeyAnalyzer;

import de.brightbyte.data.BlockDigest;
import de.brightbyte.data.ByteString;
import de.brightbyte.data.Codec;
import de.brightbyte.data.Functor;
import de.brightbyte.data.KeyDigestingValueStore;
import de.brightbyte.data.KeyValueStore;
import de.brightbyte.data.LongIntLookup;
import de.brightbyte.data.MapLookup;
import de.brightbyte.data.XorFold32;
import de.brightbyte.data.XorFold64;
import de.brightbyte.data.XorWrap;
import de.brightbyte.io.HuffmanDataCodec;
import de.brightbyte.text.CharsetCodec;
import de.brightbyte.wikiword.analyzer.AuxilliaryWikiProperties;

public class NameMaps {
		/*public static <V> Map<String, V> newMap(String mapType) {
			if (mapType.equals("patricia")) {
					try {
						PatriciaTrie<String, V> trie = new PatriciaTrie<String, V>(new StringKeyAnalyzer());
						return trie;
					}  catch (LinkageError ex) {
						//noop
					}
			}
			
			return new HashMap<String, V>();
		}*/

		public static KeyValueStore<String, Integer> newStore(String storeParams, String lang) {
			KeyValueStore<String, Integer> store = null;
			
			String[] tt = storeParams.split("[,;|+/ &]+");
			
			Set<String> params = new HashSet<String>();
			params.addAll(Arrays.asList(tt));
			
			if (params.contains("none") || params.contains("null")) store = null;
			else if (params.contains("string")) store = new MapLookup<String, Integer>(new HashMap<String, Integer>());
			else if (params.contains("utf8") || params.contains("utf16")) {
				//initial digest turns string into UTF-8 bytes
				Functor<byte[], String> digest;
				
				try {
					if (params.contains("utf8")) digest = new Codec.Encoder<String, byte[]>(new CharsetCodec("UTF-8"));
					else digest = new Codec.Encoder<String, byte[]>(new CharsetCodec("UTF-16"));
					
					//apply md5 digest or huffman compression
					if (params.contains("md5")) digest = new Functor.Composite<byte[], byte[], String>(digest, new BlockDigest("MD5"));
					else if (params.contains("sha1")) digest = new Functor.Composite<byte[], byte[], String>(digest, new BlockDigest("SHA-1"));
					else if (params.contains("huff") || params.contains("huffman")) digest = new Functor.Composite<byte[], byte[], String>(digest, getHuffmanEncoder(lang));
				} catch (UnsupportedEncodingException e) {
					throw new IllegalArgumentException(e);
				} catch (NoSuchAlgorithmException e) {
					throw new IllegalArgumentException(e);
				} catch (IOException e) {
					throw new RuntimeException(e);
				}
				
				if (params.contains("fold64")) { //fold into Long
					Functor<Long, byte[]> fold;
					fold = XorFold64.instance;
					
					Functor<Long, String> convert = new Functor.Composite<Long, byte[], String>(digest, fold);

					if (params.contains("primitive")) {
						LongIntLookup<Long> numStore = new LongIntLookup<Long>();
						store = new KeyDigestingValueStore<String, Long, Integer>(numStore, convert);
					} else {
						MapLookup<Long, Integer> numStore = new MapLookup<Long, Integer>(new HashMap<Long, Integer>());
						store = new KeyDigestingValueStore<String, Long, Integer>(numStore, convert);
					}
				} else { //keep bytes, wrap in ByteArray
						if (params.contains("wrap8")) digest = new Functor.Composite<byte[], byte[], String>(digest, new XorWrap(8));
						else if (params.contains("wrap6")) digest = new Functor.Composite<byte[], byte[], String>(digest, new XorWrap(6));
						else if (params.contains("wrap4")) digest = new Functor.Composite<byte[], byte[], String>(digest, new XorWrap(4));
						else if (params.contains("wrap4")) digest = new Functor.Composite<byte[], byte[], String>(digest, new XorWrap(4));
						
						//create converter that includes wrapping the byte array in a ByteString
						Functor<ByteString, String> convert = new Functor.Composite<ByteString, byte[], String>(digest, ByteString.wrap);
			
						//set up the store
						MapLookup<ByteString, Integer> byteStore = new MapLookup<ByteString, Integer>(new HashMap<ByteString, Integer>());
						store = new KeyDigestingValueStore<String, ByteString, Integer>(byteStore, convert);
				}
			}  else {
				throw new IllegalArgumentException("bad store spec: "+storeParams+"; expected 'none' or 'string' or 'utf8' or 'utf16' as part of the type spec");
			}
			
			return store;
		}

		private static Functor<byte[], byte[]> getHuffmanEncoder(String lang) throws IOException {
			URL dictFile = getSampleDataFile(lang);
			HuffmanDataCodec codec = new HuffmanDataCodec();
			
			if (dictFile!=null) codec.buildDictionary(dictFile, 0);
			else codec.buildDictionary(HuffmanDataCodec.ENGLISH_LETTERS, "UTF-8");
			
			return new Codec.Encoder<byte[], byte[]>(codec);
		}

		private static URL getSampleDataFile(String lang) {
			URL u = AuxilliaryWikiProperties.getPropertyFileURL("title-samples", lang);
			if (u==null) u = AuxilliaryWikiProperties.getPropertyFileURL("title-samples", "en");
			return u;
		}
}
