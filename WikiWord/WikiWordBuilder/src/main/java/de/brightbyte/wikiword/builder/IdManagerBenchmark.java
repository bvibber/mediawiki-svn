package de.brightbyte.wikiword.builder;

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

public class IdManagerBenchmark {

	protected static void load(File store, String encoding, Map<String, Integer> ids) throws IOException {
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
				int i = Integer.parseInt(s.substring(idx+1));
				
				Integer old = ids.put(n, i);
				if (old != null && !old.equals(i)) throw new RuntimeException("multiple entries for key "+n+": was "+old+", found "+i);
			} catch (NumberFormatException e) {
				break; //FIXME: remove broken record before appending!
			}
		}
		
		in.close();
	}
	
	public static void main(String[] args) throws IOException {
		String mode = args[0];
		String file = args[1];
		
		String encoding = "UTF-8";
		
		Map<String, Integer> map;
		
		if (mode.equals("hash")) map = new HashMap<String, Integer>();
		else if (mode.equals("trie")) map = new PatriciaTrie<String, Integer>(StringKeyAnalyzer.INSTANCE);
		else if (mode.equals("rtrie")) map = new PatriciaTrie<String, Integer>(ReverseStringKeyAnalyzer.INSTANCE);
		else throw new IllegalArgumentException("unknown mode: "+mode);
		
		Runtime.getRuntime().gc();
		System.out.println("Memory used before:"+ DebugUtil.memory());
		System.out.println("loading...");
		load(new File(file), encoding, map);
		System.out.println("loaded "+map.size()+" entries");
		System.out.println("GC...");
		Runtime.getRuntime().gc();
		System.out.println("Memory used after:"+ DebugUtil.memory());
	}
}
