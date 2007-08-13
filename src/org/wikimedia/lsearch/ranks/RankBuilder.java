package org.wikimedia.lsearch.ranks;

import java.io.IOException;
import java.io.InputStream;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collection;
import java.util.Collections;
import java.util.Comparator;
import java.util.HashMap;
import java.util.HashSet;
import java.util.PriorityQueue;
import java.util.Map.Entry;

import org.apache.log4j.Logger;
import org.mediawiki.dumper.ProgressFilter;
import org.mediawiki.dumper.Tools;
import org.mediawiki.importer.XmlDumpReader;
import org.wikimedia.lsearch.beans.ArticleLinks;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.index.IndexThread;
import org.wikimedia.lsearch.spell.SuggestResult;
import org.wikimedia.lsearch.storage.Storage;
import org.wikimedia.lsearch.util.Localization;
import org.wikimedia.lsearch.util.UnicodeDecomposer;

/**
 * Main class, builds index from a database dump.
 * Syntax: java Importer inputfile dbname 
 * 
 * @author rainman
 *
 */
public class RankBuilder {
	static Logger log = Logger.getLogger(RankBuilder.class);  
	/**
	 * @param args
	 * @throws IOException 
	 */
	public static void main(String[] args) throws IOException {
		String inputfile = null;
		String dbname = null;
		
		System.out.println("MediaWiki Lucene search indexer - build rank info from xml dumps.\n");
		
		Configuration.open();
		log = Logger.getLogger(RankBuilder.class);
		
		if(args.length < 2){
			System.out.println("Syntax: java RankBuilder <inputfile> <dbname>");
			return;
		}
		inputfile = args[0];
		dbname = args[1];
		if(inputfile == null || dbname == null){
			System.out.println("Please specify both input xml file and database name");
			return;
		}

		String langCode = GlobalConfiguration.getInstance().getLanguage(dbname);
		// preload
		UnicodeDecomposer.getInstance();
		Localization.readLocalization(langCode);
		Localization.loadInterwiki();

		long start = System.currentTimeMillis();

		// regenerate link info
		Links links = processLinks(inputfile,getTitles(inputfile,langCode),langCode,LinkReader.NO_REDIRECTS);
		links.compactAll();
		Storage store = Storage.getInstance();
		//store.storePageReferences(links.getAll(),dbname);
		printRelated(links);
		
		/*for(CompactArticleLinks cs : links.values()){
				System.out.println(cs);
			}*/

		long end = System.currentTimeMillis();

		System.out.println("Finished generating ranks in "+formatTime(end-start));
	}

	public static Links processLinks(String inputfile, Links links, String langCode, boolean readRedirects) {
		log.info("Second pass, calculating article links...");
		InputStream input = null;
		// second pass - calculate page ranks
		try {
			input = Tools.openInputFile(inputfile);
		} catch (IOException e) {
			log.fatal("I/O error opening "+inputfile);
			return null;
		}
		// calculate ranks
		LinkReader rr = new LinkReader(links,langCode,readRedirects);
		XmlDumpReader reader = new XmlDumpReader(input,new ProgressFilter(rr, 5000));
		try {
			reader.readDump();
		} catch (IOException e) {
			log.fatal("I/O error reading dump while calculating ranks for from "+inputfile);
			return null;
		}
		return links;
	}

	public static Links getTitles(String inputfile,String langCode) {
		log.info("First pass, getting a list of valid articles...");
		InputStream input = null;
		try {
			input = Tools.openInputFile(inputfile);
		} catch (IOException e) {
			log.fatal("I/O error opening "+inputfile);
			return null;
		}
		// first pass, get titles
		TitleReader tr = new TitleReader(langCode);
		XmlDumpReader reader = new XmlDumpReader(input,new ProgressFilter(tr, 5000));
		try {
			reader.readDump();
			input.close();
		} catch (IOException e) {
			log.fatal("I/O error reading dump while getting titles from "+inputfile);
			return null;
		}
		return tr.getTitles();
	}
	
	static class Related {
		CompactArticleLinks title;
		CompactArticleLinks relates;
		double score;
		HashMap<CompactArticleLinks,Double> scores;
		public Related(CompactArticleLinks title, CompactArticleLinks relates, double score, HashMap<CompactArticleLinks,Double> scores) {
			this.title = title;
			this.relates = relates;
			this.score = score;
			this.scores = scores;
		}
		@Override
		public String toString() {
			return title+"->"+relates+" : "+score;
		}
	}
	
	public static void printRelated(Links links){
		int num = 0;
		int total = links.getAll().size();
		for(CompactArticleLinks cs : links.getAll()){
			num++;

			ArrayList<Related> pq = new ArrayList<Related>();
			HashSet<CompactArticleLinks> ll = new HashSet<CompactArticleLinks>();
			//HashSet<CompactArticleLinks> lin = new HashSet<CompactArticleLinks>();
			//HashSet<CompactArticleLinks> lout = new HashSet<CompactArticleLinks>();
			System.out.println("["+num+"/"+total+" - "+cs.linksInIndex+"] "+cs.toString());
			if(cs.linksIn != null){
				for(CompactArticleLinks csl : cs.linksIn)
					ll.add(csl);
			}
			/* if(cs.linksOut != null){
				for(CompactArticleLinks csl : cs.linksOut)
					ll.add(csl);
			} */
			if(cs.toString().equals("0:Douglas Adams")){
				int b = 01;
				b++;
			}
			for(CompactArticleLinks from : ll){
				//double score = relatedScore(cs,ll,from);
				Object[] ret = relatedScore(cs,ll,from);
				double score = (Double) ret[0];
				HashMap<CompactArticleLinks,Double> scores = (HashMap<CompactArticleLinks, Double>) ret[1];				
				if(score != 0)
					pq.add(new Related(cs,from,score,scores));

			}
			/*for(CompactArticleLinks to : lout){
				if(!lin.contains(to)){
					double score = relatedScore(cs,lin,lout,to);
					if(score != 0)
						pq.add(new Related(cs,to,score));
				}
			}*/
			if(pq.size() > 0){
				Collections.sort(pq,new Comparator<Related>() {
					public int compare(Related o1, Related o2){
						double d = o2.score-o1.score;
						if(d == 0) return 0;
						else if(d > 0) return 1;
						else return -1;
					}
				});
				System.out.println(cs.getKey()+" -> ");
				for(Related r : pq){
					System.out.println("     -> "+r.relates+" ("+r.score+")");					
					if(r.scores != null){
						ArrayList<Entry<CompactArticleLinks, Double>> ss = new ArrayList<Entry<CompactArticleLinks, Double>>();
						ss.addAll(r.scores.entrySet());
						Collections.sort(ss,new Comparator<Entry<CompactArticleLinks, Double>>() {
							public int compare(Entry<CompactArticleLinks, Double> o1, Entry<CompactArticleLinks, Double> o2){
								double d = o2.getValue()-o1.getValue();
								if(d == 0) return 0;
								else if(d > 0) return 1;
								else return -1;
							}
						});
						for(Entry<CompactArticleLinks, Double> e : ss){
							System.out.println("          + "+e.getKey().toString()+" = "+e.getValue());
						}
					}
				}
				System.out.println();
			}
		}
	}
	
	public static double norm(double d){
		if(d == 0)
			return 1;
		else
			return d;
	}
	
	public static Object[] relatedScore(CompactArticleLinks p, HashSet<CompactArticleLinks> ll, CompactArticleLinks q){
		double score = 0;
		//HashMap<CompactArticleLinks,Double> scores = new HashMap<CompactArticleLinks,Double>(); 
		//int links = q.links;
		// iterate the neighbourhood of q and see it they link to p
		for(int i=0;i<q.linksInIndex;i++){
			CompactArticleLinks r = q.linksIn[i];
			if(r != q && r.links != 0 && ll.contains(r)){
				//score += 1.0/(norm(q.links)*norm(r.links));
				score += 1.0/norm(r.links);
				//scores.put(r,1.0/norm(r.links));
			}
			
		}
		for(int i=0;i<q.linksOutIndex;i++){
			CompactArticleLinks r = q.linksOut[i];
			if(r != q && r.links!=0 && ll.contains(r)){
				//score += 1.0/(norm(q.links)*norm(r.links));
				score += 1.0/norm(r.links);
				//scores.put(r,1.0/norm(r.links));
			}
		}
		// iterate neighbourhood of p and see if it links to q
		/*for(int i=0;i<p.linksInIndex;i++){
			CompactArticleLinks r = p.linksIn[i];
			if(q.hasLinkFrom(r))
				score += 1.0/(norm(q.links)*norm(r.links));
		} */
		//return score * (count / (double)(q.linksInIndex+q.linksOutIndex)) * q.links;
		//return score * q.links;
		return new Object[]{ score, null };
	}

	private static String formatTime(long l) {
		l /= 1000;
		if(l >= 3600) return l/3600+"h "+(l%3600)/60+"m "+(l%60)+"s";
		else if(l >= 60) return (l%3600)/60+"m "+(l%60)+"s";
		else return l+"s";
	}

}
