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
import org.wikimedia.lsearch.ranks.LinkReader;
import org.wikimedia.lsearch.ranks.Links;
import org.wikimedia.lsearch.ranks.RankBuilder;
import org.wikimedia.lsearch.related.CompactLinks;
import org.wikimedia.lsearch.related.RelatedBuilder;
import org.wikimedia.lsearch.storage.LinkAnalysisStorage;
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
		boolean newIndex = true, makeSnapshot = false;
		boolean snapshotDb = false, useOldLinkAnalysis = false;
		boolean useOldRelated = false;
		boolean makeIndex = true; boolean makeHighlight = false;
		boolean makeTitles = false; boolean newTitles = false;
		
		System.out.println("MediaWiki Lucene search indexer - index builder from xml database dumps.\n");
		
		Configuration.open();
		log = Logger.getLogger(Importer.class);
		
		if(args.length < 2){
			System.out.println("Syntax: java Importer [-a] [-n] [-s] [-l] [-r] [-lm limit] [-o optimize] [-m mergeFactor] [-b maxBufDocs] <inputfile> <dbname>");
			System.out.println("Options: ");
			System.out.println("  -a              - don't create new index, append to old");
			System.out.println("  -s              - make index snapshot when finished");
			System.out.println("  -l              - use earlier link analysis index, don't recalculate");
			System.out.println("  -r              - use earlier related index, don't recalculate");
			System.out.println("  -h              - also make the highlight index");
			System.out.println("  -t              - also make the titles index (if available)");			
			System.out.println("  -ho             - make *only* the highlight index");
			System.out.println("  -to             - make *only the titles index");
			System.out.println("  -nt             - start a new titles index");
			System.out.println("  -lm limit_num   - add at most limit_num articles");
			System.out.println("  -o optimize     - true/false overrides optimization param from global settings");
			System.out.println("  -m mergeFactor  - overrides param from global settings");
			System.out.println("  -b maxBufDocs   - overrides param from global settings");
			System.out.println("  --snapshot <db> - make snapshot only for dbname");
			return;
		}
		for(int i=0;i<args.length;i++){
			if(args[i].equals("-lm"))
				limit = Integer.parseInt(args[++i]);
			else if(args[i].equals("-o"))
				optimize = Boolean.parseBoolean(args[++i]);
			else if(args[i].equals("-m"))
				mergeFactor = Integer.parseInt(args[++i]);
			else if(args[i].equals("-b"))
				maxBufDocs = Integer.parseInt(args[++i]);
			else if(args[i].equals("-a"))
				newIndex = false;
			else if(args[i].equals("-l"))
				useOldLinkAnalysis = true;
			else if(args[i].equals("-r"))
				useOldRelated = true;
			else if(args[i].equals("-h"))
				makeHighlight = true;
			else if(args[i].equals("-t"))
				makeTitles = true;
			else if(args[i].equals("-nt"))
				newTitles = true;
			else if(args[i].equals("-to")){
				makeIndex = false;
				makeTitles = true;
			} else if(args[i].equals("-ho")){
				makeHighlight = true;
				makeIndex = false;
			} else if(args[i].equals("-s"))
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
			IndexId iid = IndexId.get(dbname);
			// preload
			UnicodeDecomposer.getInstance();
			Localization.readLocalization(langCode);
			Localization.loadInterwiki();

			long start = System.currentTimeMillis();
			
			if(makeIndex){
				if(!useOldLinkAnalysis){
					// regenerate link and redirect information				
					try {
						RankBuilder.processLinks(inputfile,Links.createNew(iid),iid,langCode);
					} catch (IOException e) {
						log.fatal("Cannot store link analytics: "+e.getMessage());
						return;
					}
				}
				if(!useOldRelated){
					try {
						RelatedBuilder.rebuildFromLinksNew(iid);
					} catch (IOException e) {
						log.fatal("Cannot make related mapping: "+e.getMessage());
						return;
					}
				}
			}
			
			// open			
			InputStream input = null;
			try {
				input = Tools.openInputFile(inputfile);
			} catch (IOException e) {
				log.fatal("I/O error opening "+inputfile);
				return;
			}			
			long end = start;
			try {
				StringBuilder add = new StringBuilder("(");
				
				if(makeIndex)
					add.append("index");
				if(makeHighlight){
					if(add.length()>1)
						add.append("+");
					add.append("highlight");
				}
				if(makeTitles){
					if(add.length()>1)
						add.append("+");
					add.append("titles");
				}
				add.append(")");
				
				log.info("Indexing articles "+add+"...");
				IndexId ll = iid.getLinks();
				Links links = null;
				links = Links.openForRead(ll,ll.getImportPath());
				// read
				DumpImporter dp = new DumpImporter(dbname,limit,optimize,mergeFactor,maxBufDocs,newIndex,links,langCode,makeIndex,makeHighlight,makeTitles,newTitles);
				XmlDumpReader reader = new XmlDumpReader(input,new ProgressFilter(dp, 1000));
				reader.readDump();
				end = System.currentTimeMillis();
				log.info("Closing/optimizing index...");
				dp.closeIndex();				
			} catch (IOException e) {
				if(!e.getMessage().equals("stopped")){
					log.fatal("I/O error processing dump for "+dbname+" from "+inputfile+" : "+e.getMessage());
					e.printStackTrace();
					return;
				}
				System.exit(1);
			}
			
			long finalEnd = System.currentTimeMillis();
			
			System.out.println("Finished indexing in "+formatTime(end-start)+", with final index optimization in "+formatTime(finalEnd-end));
			System.out.println("Total time: "+formatTime(finalEnd-start));
		}
		// make snapshot if needed
		if(makeSnapshot || snapshotDb){
			IndexId iid = IndexId.get(dbname);
			if(makeIndex){
				for(IndexId p : iid.getPhysicalIndexIds()){
					IndexThread.makeIndexSnapshot(p,p.getImportPath());
				}
			}
			if(makeHighlight){
				for(IndexId p : iid.getHighlight().getPhysicalIndexIds()){
					IndexThread.makeIndexSnapshot(p,p.getImportPath());
				}
			}
			if(makeTitles){
				for(IndexId p : iid.getTitlesIndex().getPhysicalIndexIds()){
					IndexThread.makeIndexSnapshot(p,p.getImportPath());
				}
			}
		}
	}

	private static String formatTime(long l) {
		l /= 1000;
		if(l >= 3600) return l/3600+"h "+(l%3600)/60+"m "+(l%60)+"s";
		else if(l >= 60) return (l%3600)/60+"m "+(l%60)+"s";
		else return l+"s";
	}

}
