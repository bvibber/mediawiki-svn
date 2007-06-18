package org.wikimedia.lsearch.ranks;

import java.io.IOException;
import java.io.InputStream;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;
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

		Storage store = Storage.getInstance();
		store.storePageReferences(links.getAll(),dbname);
		
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

	private static String formatTime(long l) {
		l /= 1000;
		if(l >= 3600) return l/3600+"h "+(l%3600)/60+"m "+(l%60)+"s";
		else if(l >= 60) return (l%3600)/60+"m "+(l%60)+"s";
		else return l+"s";
	}

}
