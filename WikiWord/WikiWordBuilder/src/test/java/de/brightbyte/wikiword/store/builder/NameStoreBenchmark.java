package de.brightbyte.wikiword.store.builder;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.security.NoSuchAlgorithmException;
import java.util.Arrays;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Set;

import de.brightbyte.data.BlockDigest;
import de.brightbyte.data.ByteString;
import de.brightbyte.data.Codec;
import de.brightbyte.data.Functor;
import de.brightbyte.data.KeyDigestingValueStore;
import de.brightbyte.data.KeyValueStore;
import de.brightbyte.data.MapLookup;
import de.brightbyte.data.XorFold32;
import de.brightbyte.data.XorFold64;
import de.brightbyte.data.XorWrap;
import de.brightbyte.io.HuffmanDataCodec;
import de.brightbyte.text.CharsetCodec;
import de.brightbyte.util.PersistenceException;

public class NameStoreBenchmark {
	public static void main(String[] args) throws IOException, PersistenceException, NoSuchAlgorithmException, InterruptedException {
		String type = args[0];
		int limit = Integer.parseInt(args[1]);
		
		KeyValueStore<String, Integer> store = null;
		
		String[] tt = type.split("[,;|+/]");
		
		Set<String> params = new HashSet<String>();
		params.addAll(Arrays.asList(tt));
		
		if (params.contains("none") || params.contains("null")) store = null;
		else if (params.contains("string")) store = new MapLookup<String, Integer>(new HashMap<String, Integer>());
		else if (params.contains("utf8") || params.contains("utf16")) {
			//initial digest turns string into UTF-8 bytes
			Functor<byte[], String> digest;
			
			if (params.contains("utf8")) digest = new Codec.Encoder<String, byte[]>(new CharsetCodec("UTF-8"));
			else digest = new Codec.Encoder<String, byte[]>(new CharsetCodec("UTF-16"));
			
			//apply md5 digest or huffman compression
			if (params.contains("md5")) digest = new Functor.Composite<byte[], byte[], String>(digest, new BlockDigest("MD5"));
			else if (params.contains("sha1")) digest = new Functor.Composite<byte[], byte[], String>(digest, new BlockDigest("SHA-1"));
			else if (params.contains("huff") || params.contains("huffman")) digest = new Functor.Composite<byte[], byte[], String>(digest, getHuffmanEncoder(args[3]));
			
			if (params.contains("fold64") || params.contains("fold32")) { //fold into Long
				Functor<? extends Number, byte[]> fold;
				
				if (params.contains("fold32")) fold = XorFold32.instance;
				else fold = XorFold64.instance;
				
				Functor<Number, String> convert = new Functor.Composite<Number, byte[], String>(digest, fold);

				MapLookup<Number, Integer> numStore = new MapLookup<Number, Integer>(new HashMap<Number, Integer>());
				store = new KeyDigestingValueStore<String, Number, Integer>(numStore, convert);
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
			throw new IllegalArgumentException("bad store type: "+type+"; expected 'none' or 'string' or 'utf8' as part of the type spec");
		}
		
		BufferedReader in = new BufferedReader(new InputStreamReader(new FileInputStream(args[2]), "UTF-8"));
		
		Runtime.getRuntime().gc();
		Thread.currentThread().sleep(1000);
		long baseline = Runtime.getRuntime().totalMemory() - Runtime.getRuntime().freeMemory();

		long start = System.nanoTime();
		
		System.out.println("Reading input...");
		String s;
		int c = 0;
		while ((s = in.readLine()) != null) {
			c++;
			if (c>limit) break;
			
			if (store!=null) store.put(s, c);
			if (c % 10000 == 0) System.out.format(" at %d\n", c);
		}
		
		long t = System.nanoTime() - start;
		System.out.format("Processed %d entries in %01.3f sec\n", c, t/1000000000.0);
		
		Runtime.getRuntime().gc();
		Thread.currentThread().sleep(1000);
		long m = Runtime.getRuntime().totalMemory() - Runtime.getRuntime().freeMemory();
		
		System.out.format("Memoray used: %01.2f MB\n", (m - baseline)/(1024.0*1024.0));
		
		if (store!=null) store.close();
	}

	private static Functor<byte[], byte[]> getHuffmanEncoder(String dictFile) throws IOException {
		HuffmanDataCodec codec = new HuffmanDataCodec();
		codec.buildDictionary(new File(dictFile), 0);
		return new Codec.Encoder<byte[], byte[]>(codec);
	}
}
