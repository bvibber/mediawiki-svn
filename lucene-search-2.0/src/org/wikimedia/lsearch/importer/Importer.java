package org.wikimedia.lsearch.importer;

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
import org.wikimedia.lsearch.util.Localization;
import org.wikimedia.lsearch.util.UnicodeDecomposer;

/**
 * Main class, builds index from a database dump.
 * Syntax: java Importer inputfile dbname 
 * 
 * @author rainman
 *
 */
public class Importer {
	static Logger log;  
	/**
	 * @param args
	 */
	public static void main(String[] args) {
		int limit = -1;
		String inputfile = null;
		String dbname = null;
		Boolean optimize = null;
		Integer mergeFactor = null, maxBufDocs = null;
		boolean newIndex = false, makeSnapshot = false;
		boolean snapshotDb = false;
		
		System.out.println("MediaWiki Lucene search indexer - index builder from xml database dumps.\n");
		
		Configuration.open();
		log = Logger.getLogger(Importer.class);
		
		if(args.length < 2){
			System.out.println("Syntax: java Importer [-n] [-s] [-l limit] [-o optimize] [-m mergeFactor] [-b maxBufDocs] <inputfile> <dbname>");
			System.out.println("Options: ");
			System.out.println("  -n              - create a new index (erase the old one if exists)");
			System.out.println("  -s              - make index snapshot when finished");
			System.out.println("  -l limit_num    - add at most limit_num articles");
			System.out.println("  -o optimize     - true/false overrides optimization param from global settings");
			System.out.println("  -m mergeFactor  - overrides param from global settings");
			System.out.println("  -b maxBufDocs   - overrides param from global settings");
			System.out.println("  --snapshot <db> - make snapshot only for dbname");
			return;
		}
		for(int i=0;i<args.length;i++){
			if(args[i].equals("-l"))
				limit = Integer.parseInt(args[++i]);
			else if(args[i].equals("-o"))
				optimize = Boolean.parseBoolean(args[++i]);
			else if(args[i].equals("-m"))
				mergeFactor = Integer.parseInt(args[++i]);
			else if(args[i].equals("-b"))
				maxBufDocs = Integer.parseInt(args[++i]);
			else if(args[i].equals("-n"))
				newIndex = true;
			else if(args[i].equals("-s"))
				makeSnapshot = true;
			else if(args[i].equals("--snapshot")){
				dbname = args[++i];
				snapshotDb = true;
				break;
			} else if(inputfile == null)
				inputfile = args[i];
			else if(dbname == null)
				dbname = args[i];
			else
				System.out.println("Unrecognized option: "+args[i]);
		}
		if(!snapshotDb){
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
			
			// regenerate link and redirect information
			HashMap<String,ArticleLinks> links = processLinks(inputfile,getTitles(inputfile,langCode),langCode);

			log.info("Third pass, indexing articles...");
			
			// open			
			InputStream input = null;
			try {
				input = Tools.openInputFile(inputfile);
			} catch (IOException e) {
				log.fatal("I/O error opening "+inputfile);
				return;
			}

			// read
			DumpImporter dp = new DumpImporter(dbname,limit,optimize,mergeFactor,maxBufDocs,newIndex,links,langCode);
			XmlDumpReader reader = new XmlDumpReader(input,new ProgressFilter(dp, 1000));
			try {
				reader.readDump();
			} catch (IOException e) {
				if(!e.getMessage().equals("stopped")){
					log.fatal("I/O error reading dump for "+dbname+" from "+inputfile);
					return;
				}
			}

			long end = System.currentTimeMillis();

			log.info("Closing/optimizing index...");
			dp.closeIndex();

			long finalEnd = System.currentTimeMillis();
			
			System.out.println("Finished indexing in "+formatTime(end-start)+", with final index optimization in "+formatTime(finalEnd-end));
			System.out.println("Total time: "+formatTime(finalEnd-start));
		}
		
		// make snapshot if needed
		if(makeSnapshot || snapshotDb){
			IndexId iid = IndexId.get(dbname);
			if(iid.isMainsplit()){
				IndexThread.makeIndexSnapshot(iid.getMainPart(),iid.getMainPart().getImportPath());
				IndexThread.makeIndexSnapshot(iid.getRestPart(),iid.getRestPart().getImportPath());
			} else if(iid.isSplit()){
				for(String part : iid.getSplitParts()){
					IndexId iidp = IndexId.get(part);
					IndexThread.makeIndexSnapshot(iidp,iidp.getImportPath());
				}
			} else
				IndexThread.makeIndexSnapshot(iid,iid.getImportPath());
		}		
	}

	private static HashMap<String,ArticleLinks> processLinks(String inputfile, HashMap<String,ArticleLinks> links, String langCode) {
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
		LinkReader rr = new LinkReader(links,langCode);
		XmlDumpReader reader = new XmlDumpReader(input,new ProgressFilter(rr, 5000));
		try {
			reader.readDump();
		} catch (IOException e) {
			log.fatal("I/O error reading dump while calculating ranks for from "+inputfile);
			return null;
		}		
		// generate "redirects here" lists for each article
		for(Entry<String,ArticleLinks> e : links.entrySet()){
			ArticleLinks r = e.getValue();
			if(r.redirectsTo != null && r != r.redirectsTo){
				if(r.redirectsTo.redirected == null)
					r.redirectsTo.redirected = new ArrayList<String>();
				r.redirectsTo.redirected.add(e.getKey());
			}
		}
		return links;
	}

	private static HashMap<String,ArticleLinks> getTitles(String inputfile,String langCode) {
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
