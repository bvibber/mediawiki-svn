package org.wikimedia.lsearch.spell;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.util.Arrays;
import java.util.HashSet;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.index.IndexReader;
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
import org.wikimedia.lsearch.spell.Suggest;
import org.wikimedia.lsearch.spell.SuggestResult;
import org.wikimedia.lsearch.spell.SuggestSimilar;
import org.wikimedia.lsearch.spell.Suggest.ExtraInfo;
import org.wikimedia.lsearch.spell.dist.DoubleMetaphone;

public class SuggestShell {
	
	public static void main(String args[]) throws IOException{
		Configuration.open();
		GlobalConfiguration global = GlobalConfiguration.getInstance();
		boolean suggestOnly = false, similarOnly = false;;
		int limit = 10;
		String dbname = "enwiki";
		for(int i=0;i<args.length;i++){
			if(args[i].equals("-s"))
				suggestOnly = true;
			else if(args[i].equals("-sm"))
				similarOnly = true;
			else if(args[i].equals("-l"))
				limit = Integer.parseInt(args[++i]);
			else if(args[i].equals("--help")){
				System.out.println("java SuggestTest [-s] [-l num] [dbname]");
				System.out.println("Where:");
				System.out.println("  -s       - final suggest only, no detailed report");
				System.out.println("  -sm      - similar titles only");
				System.out.println("  -l <num> - limit number of results (default:"+limit+")");
				System.out.println("  dbname   - database name (default:"+dbname+")");
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
		SuggestSimilar similar = null;
		try{
			if(iid.hasTitleNgram())
				similar = new SuggestSimilar(iid);
		} catch(Exception e){
			// might be broken, go silently
		}
		while(true){
			System.out.print(">> ");
			String inputtext = in.readLine().trim();
			System.out.println("Query: "+inputtext);
			String last = null;
			int ns = inputtext.startsWith("[2]")? 2 : 0;
			if(inputtext.startsWith("[2]:"))
				inputtext = inputtext.substring(4);
			long start = System.currentTimeMillis();
			if(!suggestOnly && !similarOnly){
				for(String text : inputtext.split(" ")){
					if(text.length()>=2){
						System.out.println("METAPHONES: "+dmeta.doubleMetaphone(text)+", "+dmeta.doubleMetaphone(text,true));
						System.out.println("SUGGEST: ");
						int count = 0;
						for(SuggestResult r : sc.suggestWords(text,Suggest.POOL,null)){
							if(++count >= limit )
								break;
							System.out.println(r);
						}

						System.out.println("SPLIT: "+sc.suggestSplit(text,null));
					}
					if(last != null){
						System.out.println("JOIN: "+sc.suggestJoin(last,text,null));
					}
					last = text;
				}
			}
			if(!similarOnly)
				System.out.println("#suggest: "+sc.suggest(inputtext,parser.tokenizeForSpellCheck(inputtext),new Suggest.ExtraInfo(new HashSet<String>(),new HashSet<String>(),new HashSet<String>(),0,false),new NamespaceFilter("0")));
			if(!suggestOnly && similar!=null)
				System.out.println("#similar: "+similar.getSimilarTitles(inputtext,new NamespaceFilter(0),4));
			System.out.println("(finished in "+(System.currentTimeMillis()-start)+" ms)");
		}
		
	}
}
