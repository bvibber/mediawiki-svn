package de.brightbyte.wikiword.store.builder;

import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.io.PrintWriter;
import java.security.NoSuchAlgorithmException;

import de.brightbyte.data.Functor;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.builder.NameMaps;

public class NameHashTrial {
	public static void main(String[] args) throws IOException, PersistenceException, NoSuchAlgorithmException, InterruptedException {
		String params = args[0];
		int limit = Integer.parseInt(args[1]);
		
		Functor<?, String> hash = NameMaps.newHash(params, "en");
		
		InputStream rawIn = args.length>2  && !args[2].equals("-") ? new FileInputStream(args[2]) : System.in;
		OutputStream rawOut = args.length>3 && !args[3].equals("-") ? new FileOutputStream(args[3]) : System.out;
		
		BufferedReader in = new BufferedReader(new InputStreamReader(rawIn, "UTF-8"));
		PrintWriter out = new PrintWriter(new BufferedWriter(new OutputStreamWriter(rawOut, "UTF-8")));
		
		long start = System.nanoTime();
		
		System.out.println("Reading input...");
		String s;
		int c = 0;
		while ((s = in.readLine()) != null) {
			c++;
			if (c>limit) break;

			Object h = hash.apply(s);
			
			out.println(h+"\t"+s);
			if (rawOut==System.out) out.flush();
			
			if (c % 10000 == 0) System.out.format(" at %d\n", c);
		}
		
		if (rawOut!=System.out) out.close();
		else out.flush();
		
		if (rawIn!=System.in) in.close();
		
		long t = System.nanoTime() - start;
		System.out.format("Processed %d entries in %01.3f sec\n", c, t/1000000000.0);
	}
}
