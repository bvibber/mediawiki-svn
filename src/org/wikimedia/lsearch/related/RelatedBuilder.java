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
import org.wikimedia.lsearch.spell.api.Dictionary.Word;
import org.wikimedia.lsearch.storage.ArticleAnalytics;
import org.wikimedia.lsearch.storage.LinkAnalysisStorage;
import org.wikimedia.lsearch.storage.RelatedStorage;

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
		String dbname = null;
		String dumpfile = null;
		System.out.println("MediaWiki Lucene search indexer - build a map of related articles.\n");
		
		Configuration.open();
		if(args.length > 2 || args.length < 1){
			System.out.println("Syntax: java RelatedBuilder <dbname> [<dump file>]");
			return;
		}		
		dbname = args[0];
		IndexId iid = IndexId.get(dbname);
		if(iid == null){
			System.out.println("Invalid dbname "+iid);
			return;
		}
		if(args.length == 2)
			dumpfile = args[1];
		
		long start = System.currentTimeMillis();
		try {
			if(dumpfile != null)
				rebuildFromDump(dumpfile,iid);
			else
				rebuildFromLinks(iid);
		} catch (IOException e) {
			log.fatal("Rebuild I/O error: "+e.getMessage());
			e.printStackTrace();
			return;
		}		
		
		long end = System.currentTimeMillis();

		System.out.println("Finished generating related in "+formatTime(end-start));
	}

	public static void rebuildFromDump(String inputfile, IndexId iid) throws IOException{
		GlobalConfiguration global = GlobalConfiguration.getInstance();
		String langCode = global.getLanguage(iid);
		log.info("First pass, getting a list of valid articles...");
		// first pass - titles
		InputStream input = null;
		input = Tools.openInputFile(inputfile);
		NamespaceFilter nsf = GlobalConfiguration.getInstance().getDefaultNamespace(iid);
		TitleReader tr = new TitleReader(iid,langCode,nsf);
		XmlDumpReader reader = new XmlDumpReader(input,new ProgressFilter(tr, 5000));
		reader.readDump();
		input.close();
		CompactLinks links = tr.getTitles();
		tr = null; // GC
		
		log.info("Second pass, geting in/out links...");
		// second pass - in/out links
		input = Tools.openInputFile(inputfile);
		LinkReader rr = new LinkReader(links,langCode);
		reader = new XmlDumpReader(input,new ProgressFilter(rr, 5000));
		reader.readDump();
		links.compactAll();
		store(links,iid);		
	}
	
	/**
	 * Rebuild related articles index for iid
	 * @throws IOException 
	 */
	public static void rebuildFromLinks(IndexId iid) throws IOException {
		CompactLinks links = new CompactLinks();
		Links temp = Links.openForRead(iid,iid.getLinks().getImportPath());
		
		NamespaceFilter nsf = GlobalConfiguration.getInstance().getDefaultNamespace(iid); 
		log.info("Reading titles in default search");
		Dictionary dict = temp.getKeys();
		Word w;
		HashMap<Integer,CompactArticleLinks> keyCache = new HashMap<Integer,CompactArticleLinks>();
		while((w = dict.next()) != null){
			String key = w.getWord();
			int ns = Integer.parseInt(key.substring(0,key.indexOf(':')));
			if(nsf.contains(ns)){
				links.add(key,temp.getNumInLinks(key));
				keyCache.put(temp.getDocId(key),links.get(key));
			}
		}

		log.info("Reading in/out links");
		dict = temp.getKeys();
		while((w = dict.next()) != null){
			String key = w.getWord();
			int ns = Integer.parseInt(key.substring(0,key.indexOf(':')));
			if(nsf.contains(ns)){
				CompactArticleLinks l = links.get(key);
				// inlinks
				l.setInLinks(temp.getInLinks(l,keyCache));
				// outlinks
				ArrayList<CompactArticleLinks> out = new ArrayList<CompactArticleLinks>();
				for(String k : temp.getOutLinks(key).toCollection()){
					CompactArticleLinks cs = links.get(k);
					if(cs != null)
						out.add(cs);
				}
				l.setOutLinks(out);
			}
		}
		temp.close(); 
		temp = null; // GC
		keyCache = null; // GC
		
		store(links,iid);		
	}
	
	/** Calculate and store related info 
	 * @throws IOException */
	public static void store(CompactLinks links, IndexId iid) throws IOException{
		RelatedStorage store = new RelatedStorage(iid);
		int num = 0;
		int total = links.getAll().size();
		NamespaceFilter nsf = GlobalConfiguration.getInstance().getDefaultNamespace(iid);
		for(CompactArticleLinks cs : links.getAll()){			
			num++;
			if(num % 1000 == 0)
				log.info("Storing ["+num+"/"+total+"]");
			Title t = new Title(cs.getKey());
			// do analysis only for default search namespace (usually main namespace)
			if(nsf.contains(t.getNamespace())){				
				ArrayList<CompactRelated> rel = getRelated(cs,links);
				if(rel.size() == 0)
					continue;
				store.addRelated(cs.toString(),rel);
			}
		}
		store.snapshot();
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
