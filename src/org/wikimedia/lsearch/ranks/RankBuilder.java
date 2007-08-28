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
import org.apache.lucene.document.Field.Store;
import org.mediawiki.dumper.ProgressFilter;
import org.mediawiki.dumper.Tools;
import org.mediawiki.importer.XmlDumpReader;
import org.wikimedia.lsearch.beans.ArticleLinks;
import org.wikimedia.lsearch.beans.Title;
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
		OldLinks links = processLinks(inputfile,getTitles(inputfile,langCode),langCode,LinkReader.NO_REDIRECTS);
		links.compactAll();
		Storage store = Storage.getInstance();
		store.storePageReferences(links.getAll(),dbname);
		storeRelated(store,links,dbname);

		long end = System.currentTimeMillis();

		System.out.println("Finished generating ranks in "+formatTime(end-start));
	}

	public static OldLinks processLinks(String inputfile, OldLinks links, String langCode, boolean readRedirects) {
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

	public static OldLinks getTitles(String inputfile,String langCode) {
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
	
	public static void storeRelated(Storage store, OldLinks links, String dbname) throws IOException{
		int num = 0;
		int total = links.getAll().size();
		ArrayList<Related> buf = new ArrayList<Related>();
		for(CompactArticleLinks cs : links.getAll()){
			num++;
			log.debug("["+num+"/"+total+" - "+cs.linksInIndex+"] "+cs.toString());
			buf.addAll(getRelated(cs,links));			
			if(buf.size() > 10000){
				store.storeRelatedPages(buf,dbname);
				buf.clear();
			}
		}
	}
	
	/** 
	 * Get related articles, sorted descending by score
	 */
	public static ArrayList<Related> getRelated(CompactArticleLinks cs, OldLinks links){
		ArrayList<Related> ret = new ArrayList<Related>();
		
		HashSet<CompactArticleLinks> ll = new HashSet<CompactArticleLinks>();			
		if(cs.linksIn != null){
			for(CompactArticleLinks csl : cs.linksIn)
				ll.add(csl);
		}
		for(CompactArticleLinks from : ll){
			double score = relatedScore(cs,ll,from);
			if(score != 0)
				ret.add(new Related(cs,from,score));
		}
		Collections.sort(ret,new Comparator<Related>() {
			public int compare(Related o1, Related o2){
				double d = o2.score-o1.score;
				if(d == 0) return 0;
				else if(d > 0) return 1;
				else return -1;
			}
		});
		return ret;
	}
	
	/**
	 * Get related titles (RelatedTitle is used in Article)
	 */
	public static ArrayList<RelatedTitle> getRelatedTitles(CompactArticleLinks cs, OldLinks links){
		ArrayList<Related> rel = getRelated(cs,links);
		ArrayList<RelatedTitle> ret = new ArrayList<RelatedTitle>();
		for(Related r : rel){
			ret.add(new RelatedTitle(new Title(r.relates.toString()),r.score));
		}
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
