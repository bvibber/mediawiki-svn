package org.wikimedia.lsearch.related;

import java.io.IOException;
import java.io.InputStream;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.Comparator;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Iterator;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.SimpleAnalyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.index.IndexWriter;
import org.mediawiki.dumper.ProgressFilter;
import org.mediawiki.dumper.Tools;
import org.mediawiki.importer.XmlDumpReader;
import org.wikimedia.lsearch.beans.Title;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.config.IndexRegistry;
import org.wikimedia.lsearch.index.IndexThread;
import org.wikimedia.lsearch.ranks.Links;
import org.wikimedia.lsearch.search.NamespaceFilter;
import org.wikimedia.lsearch.spell.api.Dictionary;
import org.wikimedia.lsearch.spell.api.LuceneDictionary;
import org.wikimedia.lsearch.spell.api.Dictionary.Word;
import org.wikimedia.lsearch.storage.ArticleAnalytics;
import org.wikimedia.lsearch.storage.LinkAnalysisStorage;
import org.wikimedia.lsearch.storage.RelatedStorage;
import org.wikimedia.lsearch.util.ProgressReport;

/**
 * Build an index that stores the mapping of related articles.
 * A is said to be related to B, if A links to B, and there
 * is some C that links to both A and B
 * 
 * @author rainman
 *
 */
public class RelatedBuilder {
	static Logger log = Logger.getLogger(RelatedBuilder.class);
	
	public static void main(String[] args) {
		ArrayList<String> dbnames = new ArrayList<String>();
		System.out.println("MediaWiki lucene-search indexer - build a map of related articles.\n");
		
		Configuration.open();
		GlobalConfiguration global = GlobalConfiguration.getInstance();
		if(args.length != 1){
			System.out.println("Syntax: java RelatedBuilder [-l] <dbname>");
			System.out.println("Options:");
			System.out.println("  -l    - rebuild all local wikis");
			return;
		}		
		
		for(int i=0;i<args.length;i++){
			if(args[i].equals("-l"))
				dbnames.addAll(global.getMyIndexDBnames());
			else dbnames.add(args[i]);
		}
		Collections.sort(dbnames);
		for(String dbname : dbnames){
			IndexId iid = IndexId.get(dbname);

			long start = System.currentTimeMillis();
			try {
				rebuildFromLinks(iid);
			} catch (IOException e) {
				log.fatal("Rebuild I/O error: "+e.getMessage());
				e.printStackTrace();
				continue;
			}		

			long end = System.currentTimeMillis();

			System.out.println("Finished generating related in "+formatTime(end-start));
		}
	}
	
	/** Calculate from links index */
	public static void rebuildFromLinks(IndexId iid) throws IOException {
		Links links = Links.openStandalone(iid);
		RelatedStorage store = new RelatedStorage(iid);
		
		log.info("Rebuilding related mapping from links");
		LuceneDictionary dict = links.getKeys();
		dict.setProgressReport(new ProgressReport("titles",1000));
		Word w;
		while((w = dict.next()) != null){
			String key = w.getWord();
			ArrayList<String> inlinks = links.getInLinks(key);
			ArrayList<Related> related = new ArrayList<Related>();
			for(String rel : inlinks){
				int ref = links.getNumInLinks(rel);
				if(ref == 0)
					continue;
				double lscore = links.getRelatedCountAll(key,rel);
				if(lscore == 0)
					continue;
				double rscore = links.getRelatedCountInContext(key,rel);
				double score;
				if(lscore == 1 && ref == 1)
					score = 0.1;
				else
					score = rscore * rscore/ref + lscore/ref;
				if(score >= 0.00001 && ref != 0){
					related.add(new Related(key,rel,score));
				}
			}
			Collections.sort(related,new Comparator<Related>() {
				public int compare(Related o1, Related o2){
					double d = o2.score-o1.score;
					if(d == 0) return 0;
					else if(d > 0) return 1;
					else return -1;
				}
			});
			store.addRelated(key,related);
		}
		store.snapshot();
		links.close();
	}

	
	/** 
	 * Get related articles, sorted descending by score
	 */
	public static ArrayList<CompactRelated> getRelated(CompactArticleLinks cs, CompactLinks links){
		ArrayList<CompactRelated> ret = new ArrayList<CompactRelated>();
 
		HashSet<CompactArticleLinks> ll = new HashSet<CompactArticleLinks>();
		double maxnorm = 0; // maximal value for related score, used for scaling
		if(cs.linksIn != null){
			for(CompactArticleLinks csl : cs.linksIn){
				ll.add(csl);
				maxnorm += 1.0/norm(csl.links);
			}
		}
		for(CompactArticleLinks from : ll){
			if(from != cs){
				double rscore = relatedScore(cs,ll,from); 
				double score = (rscore / maxnorm) * rscore;
				if(score != 0)
					ret.add(new CompactRelated(cs,from,score));
			}
		}
		Collections.sort(ret,new Comparator<CompactRelated>() {
			public int compare(CompactRelated o1, CompactRelated o2){
				double d = o2.score-o1.score;
				if(d == 0) return 0;
				else if(d > 0) return 1;
				else return -1;
			}
		});
		return ret;
	}
	
	public static double norm(double d){
		if(d == 0)
			return 1;
		else
			return d;
	}
	
	public static double relatedScore(CompactArticleLinks p, HashSet<CompactArticleLinks> ll, CompactArticleLinks q){
		double score = 0;
		// all r that links to q
		for(int i=0;i<q.linksInIndex;i++){
			CompactArticleLinks r = q.linksIn[i];
			if(r != q && r.links != 0 && ll.contains(r)){
				score += 1.0/norm(r.links);
			}
			
		}
		// all r that q links to
		for(int i=0;i<q.linksOutIndex;i++){
			CompactArticleLinks r = q.linksOut[i];
			if(r != q && r.links!=0 && ll.contains(r)){
				score += 1.0/norm(r.links);
			}
		}
		return score;
	}
	
	private static String formatTime(long l) {
		l /= 1000;
		if(l >= 3600) return l/3600+"h "+(l%3600)/60+"m "+(l%60)+"s";
		else if(l >= 60) return (l%3600)/60+"m "+(l%60)+"s";
		else return l+"s";
	}
}
