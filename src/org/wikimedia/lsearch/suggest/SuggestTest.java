package org.wikimedia.lsearch.suggest;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.util.Arrays;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.search.spell.SpellChecker;
import org.apache.lucene.store.FSDirectory;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.config.IndexRegistry;
import org.wikimedia.lsearch.suggest.dist.DoubleMetaphone;

public class SuggestTest {
	
	public static void main(String args[]) throws IOException{
		Configuration.open();
		String dbname = "wikilucene";
		if(args.length==1)
			dbname = args[0];
		IndexId iid = IndexId.get(dbname);
		Suggest sc = new Suggest(iid);
		DoubleMetaphone dmeta = new DoubleMetaphone();
		System.out.println("Suggest test interface. Type in a word you want suggestions for.");
		BufferedReader in = new BufferedReader(new InputStreamReader(System.in));
		while(true){
			System.out.print(">> ");
			String inputtext = in.readLine().trim().toLowerCase();
			String last = null;
			long start = System.currentTimeMillis();
			for(String text : inputtext.split(" ")){
				if(text.length()>2){
					System.out.println("METAPHONES: "+dmeta.doubleMetaphone(text)+", "+dmeta.doubleMetaphone(text,true));
					System.out.println("SUGGEST: ");
					for(SuggestResult r : sc.suggestWords(text,10)){
						System.out.println(r);
					}
					System.out.println("SPLIT: "+sc.suggestSplitFromTitle(text));
				}
				if(last != null){
					System.out.println("JOIN: "+sc.suggestJoinFromTitle(last,text));
					System.out.println("PHRASE: "+sc.suggestPhrase(last,text,2));
				}
				last = text;
			}
			System.out.println("(finished in "+(System.currentTimeMillis()-start)+" ms)");
		}
	}
}
