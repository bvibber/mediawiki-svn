package de.brightbyte.wikiword.builder.util;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.Reader;
import java.util.HashMap;
import java.util.Map;

import javolution.util.FastMap;

import org.ardverk.collection.PatriciaTrie;
import org.ardverk.collection.StringKeyAnalyzer;

import de.brightbyte.audit.DebugUtil;
import de.brightbyte.data.Codec;
import de.brightbyte.data.CodecException;
import de.brightbyte.data.NaturalComparator;
import de.brightbyte.data.ArrayComparator;
import de.brightbyte.data.TerseIdMap;
import de.brightbyte.text.CharsetCodec;

public class IdManagerBenchmark {

	protected static <D> void load(File store, String encoding, Map<D, Integer> ids, Codec<String, D> converter) throws IOException, CodecException {
		InputStream f = new FileInputStream(store);
		Reader rd = encoding!=null ? new InputStreamReader(f, encoding) : new InputStreamReader(f);
		BufferedReader in = new BufferedReader(rd); 
		
		String s;
		while ((s = in.readLine()) != null) {
			int idx = s.indexOf('\t');
			if (idx<0) {
				break; //FIXME: remove broken record before appending!
			}
			
			try {
				String n = s.substring(0, idx);
				D x = converter==null ? (D)n : converter.encode(n);
				int i = Integer.parseInt(s.substring(idx+1));
				
				Integer old = ids.put(x, i);
				if (old != null && !old.equals(i)) throw new RuntimeException("multiple entries for key "+n+": was "+old+", found "+i);
			} catch (NumberFormatException e) {
				break; //FIXME: remove broken record before appending!
			}
		}
		
		in.close();
	}
	
	public static void main(String[] args) throws IOException, CodecException {
		String mode = args[0];
		String enc = args[1];
		String file = args[2];
		
		String fileEncoding = "UTF-8";
		
		
		Runtime.getRuntime().gc();
		System.out.println("Memory used before:"+ DebugUtil.memory());
		System.out.println("loading...");

		if (enc.equals("-") || enc.equals("none") || enc.equals("string")) {
			Map<String, Integer> map;
			
			if (mode.equals("hash")) map = new HashMap<String, Integer>();
			else if (mode.equals("fast")) map = new FastMap<String, Integer>();
			else if (mode.equals("trie")) map = new PatriciaTrie<String, Integer>(StringKeyAnalyzer.INSTANCE);
			else if (mode.equals("rtrie")) map = new PatriciaTrie<String, Integer>(ReverseStringKeyAnalyzer.INSTANCE);
			else if (mode.equals("terse")) map = new TerseIdMap<String>(String.class, NaturalComparator.<String>instance());
			else throw new IllegalArgumentException("unknown mode: "+mode);

			load(new File(file), fileEncoding, map, null);
			System.out.println("loaded "+map.size()+" entries");
		}	else {
			Map<byte[], Integer> map;
			
			CharsetCodec converter = new CharsetCodec(enc);
			
			if (mode.equals("hash")) map = new HashMap<byte[], Integer>();
			else if (mode.equals("fast")) map = new FastMap<byte[], Integer>();
			else if (mode.equals("trie")) map = new PatriciaTrie<byte[], Integer>(ByteArrayKeyAnalyzer.INSTANCE);
			else if (mode.equals("rtrie")) throw new IllegalArgumentException("Reverte Trie is not yet supported for byte arrays");
			else if (mode.equals("terse")) map = new TerseIdMap<byte[]>(byte[].class, ArrayComparator.BYTES);
			else throw new IllegalArgumentException("unknown mode: "+mode);

			load(new File(file), fileEncoding, map, converter);
			System.out.println("loaded "+map.size()+" entries");
		}	
		
		System.out.println("GC...");
		Runtime.getRuntime().gc();
		System.out.println("Memory used after:"+ DebugUtil.memory());
	}
}
