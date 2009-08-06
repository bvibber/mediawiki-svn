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

import org.ardverk.collection.PatriciaTrie;
import org.ardverk.collection.StringKeyAnalyzer;

import de.brightbyte.audit.DebugUtil;
import de.brightbyte.data.Functor;

public class IdManagerBenchmark {

	protected static <D> void load(File store, String encoding, Map<D, Integer> ids, Functor<D, String> converter) throws IOException {
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
				D x = converter==null ? (D)n : converter.apply(n);
				int i = Integer.parseInt(s.substring(idx+1));
				
				Integer old = ids.put(x, i);
				if (old != null && !old.equals(i)) throw new RuntimeException("multiple entries for key "+n+": was "+old+", found "+i);
			} catch (NumberFormatException e) {
				break; //FIXME: remove broken record before appending!
			}
		}
		
		in.close();
	}
	
	public static void main(String[] args) throws IOException {
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
			else if (mode.equals("trie")) map = new PatriciaTrie<String, Integer>(StringKeyAnalyzer.INSTANCE);
			else if (mode.equals("rtrie")) map = new PatriciaTrie<String, Integer>(ReverseStringKeyAnalyzer.INSTANCE);
			else throw new IllegalArgumentException("unknown mode: "+mode);

			load(new File(file), fileEncoding, map, null);
			System.out.println("loaded "+map.size()+" entries");
		}	else {
			Map<byte[], Integer> map;
			
			StringEncoder converter = new StringEncoder(enc);
			
			if (mode.equals("hash")) map = new HashMap<byte[], Integer>();
			else if (mode.equals("trie")) map = new PatriciaTrie<byte[], Integer>(ByteArrayKeyAnalyzer.INSTANCE);
			else if (mode.equals("rtrie")) throw new IllegalArgumentException("Reverte Trie is not yet supported for byte arrays");
			else throw new IllegalArgumentException("unknown mode: "+mode);

			load(new File(file), fileEncoding, map, converter);
			System.out.println("loaded "+map.size()+" entries");
		}	
		
		System.out.println("GC...");
		Runtime.getRuntime().gc();
		System.out.println("Memory used after:"+ DebugUtil.memory());
	}
}
