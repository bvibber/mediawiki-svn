package org.wikimedia.lsearch.spell;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.util.Arrays;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.search.spell.SpellChecker;
import org.apache.lucene.store.FSDirectory;
import org.wikimedia.lsearch.analyzers.Analyzers;
import org.wikimedia.lsearch.analyzers.FieldBuilder;
import org.wikimedia.lsearch.analyzers.WikiQueryParser;
import org.wikimedia.lsearch.beans.SearchResults;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.config.IndexRegistry;
import org.wikimedia.lsearch.search.NamespaceFilter;
import org.wikimedia.lsearch.spell.dist.DoubleMetaphone;

public class SuggestTest {
	
	public static void main(String args[]) throws IOException{
		Configuration.open();
		GlobalConfiguration global = GlobalConfiguration.getInstance();
		boolean suggestOnly = false;
		String dbname = "enwiki";
		for(int i=0;i<args.length;i++){
			if(args[i].equals("-s"))
				suggestOnly = true;
			else if(args[i].equals("--help")){
				System.out.println("java SuggestTest [-s] [dbname]");
				System.out.println("Where:");
				System.out.println("  -s      - final suggest only, no detailed report");
				System.out.println("  dbname  - database name (default:"+dbname+")");
			} else
				dbname = args[i];
		}
		IndexId iid = IndexId.get(dbname);
		Suggest sc = new Suggest(iid);
		DoubleMetaphone dmeta = new DoubleMetaphone();
		System.out.println("Suggest test interface. Type in a word you want suggestions for.");
		BufferedReader in = new BufferedReader(new InputStreamReader(System.in));
		Analyzer analyzer = Analyzers.getSearcherAnalyzer(iid,false);
		NamespaceFilter nsDefault = new NamespaceFilter("0"); // default to main namespace
		FieldBuilder.Case dCase = FieldBuilder.Case.IGNORE_CASE;
		FieldBuilder.BuilderSet bs = new FieldBuilder(iid,dCase).getBuilder(dCase);
		WikiQueryParser parser = new WikiQueryParser(bs.getFields().contents(),nsDefault,analyzer,bs,WikiQueryParser.NamespacePolicy.IGNORE,null);
		while(true){
			System.out.print(">> ");
			String inputtext = in.readLine().trim();
			System.out.println("Query: "+inputtext);
			String last = null;
			int ns = inputtext.startsWith("[2]")? 2 : 0;
			if(inputtext.startsWith("[2]:"))
				inputtext = inputtext.substring(4);
			long start = System.currentTimeMillis();
			if(!suggestOnly){
				for(String text : inputtext.split(" ")){
					if(text.length()>=2){
						System.out.println("METAPHONES: "+dmeta.doubleMetaphone(text)+", "+dmeta.doubleMetaphone(text,true));
						System.out.println("SUGGEST: ");
						int count = 0;
						for(SuggestResult r : sc.suggestWords(text,10)){
							if(++count >= 10 )
								break;
							System.out.println(r);
						}

						System.out.println("SPLIT: "+sc.suggestSplit(text,0));
					}
					if(last != null){
						System.out.println("JOIN: "+sc.suggestJoin(last,text,0));
					}
					last = text;
				}
			}
			System.out.println("#suggest: "+sc.suggest(inputtext,parser,new SearchResults()));
			System.out.println("(finished in "+(System.currentTimeMillis()-start)+" ms)");
		}
		
	}
}
