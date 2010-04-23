package de.brightbyte.wikiword.store.builder;

import java.io.BufferedReader;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.security.NoSuchAlgorithmException;

import de.brightbyte.data.KeyValueStore;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.builder.NameMaps;

public class NameStoreBenchmark {
	public static void main(String[] args) throws IOException, PersistenceException, NoSuchAlgorithmException, InterruptedException {
		String params = args[0];
		int limit = Integer.parseInt(args[1]);
		
		KeyValueStore<String, Integer> store = NameMaps.newStore(params, "en");
		
		BufferedReader in = new BufferedReader(new InputStreamReader(new FileInputStream(args[2]), "UTF-8"));
		
		Runtime.getRuntime().gc();
		Thread.sleep(1000);
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
		Thread.sleep(1000);
		long m = Runtime.getRuntime().totalMemory() - Runtime.getRuntime().freeMemory();
		
		System.out.format("Memoray used: %01.2f MB\n", (m - baseline)/(1024.0*1024.0));
		
		if (store!=null) store.close();
	}
}
