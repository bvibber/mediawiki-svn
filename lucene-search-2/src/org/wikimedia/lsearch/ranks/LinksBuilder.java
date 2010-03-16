package org.wikimedia.lsearch.ranks;

import java.io.IOException;
import java.io.InputStream;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.BitSet;
import java.util.Collection;
import java.util.Collections;
import java.util.Comparator;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Iterator;
import java.util.PriorityQueue;
import java.util.Map.Entry;

import org.apache.log4j.Logger;
import org.apache.lucene.document.Field.Store;
import org.apache.lucene.index.Term;
import org.apache.lucene.index.TermDocs;
import org.mediawiki.dumper.ProgressFilter;
import org.mediawiki.dumper.Tools;
import org.mediawiki.importer.XmlDumpReader;
import org.wikimedia.lsearch.beans.ArticleLinks;
import org.wikimedia.lsearch.beans.Title;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.index.IndexThread;
import org.wikimedia.lsearch.related.CompactArticleLinks;
import org.wikimedia.lsearch.related.CompactLinks;
import org.wikimedia.lsearch.related.Related;
import org.wikimedia.lsearch.related.RelatedTitle;
import org.wikimedia.lsearch.spell.SuggestResult;
import org.wikimedia.lsearch.spell.api.Dictionary;
import org.wikimedia.lsearch.spell.api.Dictionary.Word;
import org.wikimedia.lsearch.storage.ArticleAnalytics;
import org.wikimedia.lsearch.storage.LinkAnalysisStorage;
import org.wikimedia.lsearch.storage.Storage;
import org.wikimedia.lsearch.util.Localization;
import org.wikimedia.lsearch.util.UnicodeDecomposer;

/**
 * Build links index frontend
 * 
 * @author rainman
 *
 */
public class LinksBuilder {
	static Logger log = Logger.getLogger(LinksBuilder.class);  
	/**
	 * @param args
	 * @throws IOException 
	 */
	public static void main(String[] args) throws IOException {
		String inputfile = null;
		String dbname = null;
		
		System.out.println("MediaWiki Lucene search indexer - build links rank info from xml dumps.\n");
		
		Configuration.open();
		log = Logger.getLogger(LinksBuilder.class);
		
		if(args.length < 2){
			System.out.println("Syntax: java LinksBuilder <inputfile> <dbname>");
			return;
		}
		for(int i=0;i<args.length;i++){
			if(inputfile == null)
				inputfile = args[i];
			else if(dbname == null)
				dbname = args[i];
		}
		if(inputfile == null || dbname == null){
			System.out.println("Please specify both input xml file and database name");
			return;
		}

		String langCode = GlobalConfiguration.getInstance().getLanguage(dbname);
		IndexId iid = IndexId.get(dbname);
		// preload
		UnicodeDecomposer.getInstance();
		Localization.readLocalization(langCode);
		Localization.loadInterwiki();

		long start = System.currentTimeMillis();

		// link info
		Links links = Links.createNew(iid);
		try{
			processLinks(inputfile,links,iid,langCode);
		} catch(IOException e){
			log.fatal("I/O error processing "+inputfile+" : "+e.getMessage());
			e.printStackTrace();
		}		
		
		long end = System.currentTimeMillis();

		System.out.println("Finished generating ranks in "+formatTime(end-start));
	}
	
	public static Links processLinks(String inputfile, Links links, IndexId iid, String langCode) throws IOException {
		log.info("Calculating article links...");
		InputStream input = Tools.openInputFile(inputfile);
		// calculate ranks
		LinkReader rr = new LinkReader(links,iid,langCode);
		XmlDumpReader reader = new XmlDumpReader(input,new ProgressFilter(rr, 5000));
		reader.readDump();
		links.flush();
		IndexThread.makeIndexSnapshot(iid.getLinks(),iid.getLinks().getImportPath());
		return links;
	}
	
	/** 
	 * Get related articles, sorted descending by score
	 * @param docIdCache 
	 * @param refCache 
	 * @param outLinkCache 
	 * @param inLinkCache 
	 * @throws IOException 
	 */
	public static ArrayList<Related> getRelated(String key, Links links, HashMap<String, Integer> docIdCache, HashMap<Integer,String> keyCache, HashMap<Integer, Integer> refCache, HashMap<Integer, int[]> inLinkCache, HashMap<Integer, int[]> outLinkCache) throws IOException{
		ArrayList<Related> ret = new ArrayList<Related>();
		int docid = docIdCache.get(key);
		int[] in = inLinkCache.get(docid);
		HashSet<Integer> inLinks = new HashSet<Integer>();
		for(int i : in)
			inLinks.add(i);
		/*
		HashMap<String,Integer> map = new HashMap<String,Integer>();
		int i = 1;
		ArrayList<String> inLinks = links.getInLinks(key,keyCache);
		for(String in : inLinks){
			map.put(in,i++);
		} */
		HashSet<Long> internal = new HashSet<Long>(); 
		for(int from : in){
			long offset = ((long)from) << 32;
			int[] out = outLinkCache.get(from);
			for(int o : out){
				if(inLinks.contains(o))
					internal.add(offset + o);
			}
		}
		for(int from : in){
			//double score = relatedScore(links,in,from,refCount);
			double score = relatedScore(from,internal,refCache);
			if(score != 0)
				ret.add(new Related(key,keyCache.get(from),score));
		}
		Collections.sort(ret,new Comparator<Related>() {
			public int compare(Related o1, Related o2){
				double d = o2.getScore()-o1.getScore();
				if(d == 0) return 0;
				else if(d > 0) return 1;
				else return -1;
			}
		});
		return ret;
	}
	
	private static double relatedScore(int q, HashSet<Long> internal, HashMap<Integer, Integer> refCache) {
		double score = 0;
		for(Long l : internal){
			long l1 = l >> 32;
			long l2 = l - (l1 << 32);
			if(l1 == q && l2 == q)
				continue;
			else if(l1 == q)
				score += 1.0/norm(refCache.get((int) (l2)));
			else if(l2 == q)
				score += 1.0/norm(refCache.get((int) (l1)));
		}
		return score;
	}

	/**
	 * Get related titles (RelatedTitle is used in Article)
	 */
	public static ArrayList<RelatedTitle> getRelatedTitles(CompactArticleLinks cs, CompactLinks links){
		ArrayList<Related> rel = null; // getRelated(cs,links);
		ArrayList<RelatedTitle> ret = new ArrayList<RelatedTitle>();
		for(Related r : rel){
			ret.add(new RelatedTitle(new Title(r.getRelates().toString()),r.getScore()));
		}
		return ret;
	}
	
	public static double norm(double d){
		if(d == 0)
			return 1;
		else
			return d;
	}
	
	//public static double relatedScore(Links links, HashSet<String> inLinks, String from, HashMap<String,Integer> refCount) throws IOException{
	public static double relatedScore(long q, HashSet<Long> internal, ArrayList<String> inLinks, HashMap<String,Integer> refCache){
		//Collection<String> qInLinks = links.getInLinksFromCache(from);
		//Collection<String> qOutLinks = links.getOutLinks(from).toCollection();
		double score = 0;
		for(Long l : internal){
			long l1 = l >> 32;
			long l2 = l - (l1 << 32);
			if(l1 == q && l2 == q)
				continue;
			else if(l1 == q)
				score += 1.0/norm(refCache.get(inLinks.get((int) (l2 - 1))));
			else if(l2 == q)
				score += 1.0/norm(refCache.get(inLinks.get((int) (l1 - 1))));
		}
		/*for(int i=1;i<=inLinks.size();i++){
			if(i!=q && internal.contains(i*q)){
				score += 1.0/norm(refCache.get(inLinks.get(i-1)));
			}
		} */
			
		// all r that links to q
		/*for(String r : qInLinks){
			if(!refCount.containsKey(r))
				System.out.println("ERROR for key "+r);
			//int ref = links.getNumInLinks(r);
			int ref = refCount.get(r);
			if(!r.equals(from) && ref != 0 && inLinks.contains(r)){
				score += 1.0/norm(ref);
			}
			
		}
		// all r that q links to
		for(String r : qOutLinks){
			//int ref = links.getNumInLinks(r);
			if(!refCount.containsKey(r))
				System.out.println("ERROR for key "+r);
			int ref = refCount.get(r);
			if(!r.equals(from) && ref != 0 && inLinks.contains(r)){
				score += 1.0/norm(ref);
			}
			
		} */
		return score;
	}

	private static String formatTime(long l) {
		l /= 1000;
		if(l >= 3600) return l/3600+"h "+(l%3600)/60+"m "+(l%60)+"s";
		else if(l >= 60) return (l%3600)/60+"m "+(l%60)+"s";
		else return l+"s";
	}

}
