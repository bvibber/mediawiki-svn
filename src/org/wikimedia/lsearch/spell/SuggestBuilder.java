package org.wikimedia.lsearch.spell;

import java.io.IOException;
import java.io.InputStream;
import java.util.ArrayList;
import java.util.Collections;

import org.apache.log4j.Logger;
import org.mediawiki.dumper.ProgressFilter;
import org.mediawiki.dumper.Tools;
import org.mediawiki.importer.XmlDumpReader;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.config.IndexRegistry;
import org.wikimedia.lsearch.index.IndexThread;
import org.wikimedia.lsearch.spell.api.SpellCheckIndexer;
import org.wikimedia.lsearch.util.Localization;
import org.wikimedia.lsearch.util.UnicodeDecomposer;

/**
 * Build suggest (did you mean...) indexes
 * 
 * @author rainman
 *
 */
public class SuggestBuilder {
	static Logger log = Logger.getLogger(SuggestBuilder.class);
	public static void main(String args[]){
		String inputfile = null;
		boolean useSnapshot = false;
		boolean local = false;
		ArrayList<String> dbnames = new ArrayList<String>();
		
		System.out.println("MediaWiki lucene-search indexer - build spelling suggestion index.\n");
		
		Configuration.open();
		GlobalConfiguration global = GlobalConfiguration.getInstance();
		
		for(int i=0;i<args.length;i++){
			if(args[i].equals("-s"))
				useSnapshot = true;
			else if(args[i].equals("-l")){
				local = true;
				useSnapshot = true;
				dbnames.addAll(global.getMyIndexDBnames());
			}
			else if(dbnames.size() == 0)
				dbnames.add(args[i]);
			else if(inputfile == null)
				inputfile = args[i]; 
		}
		
		if(dbnames.size() == 0 && !local){
			System.out.println("Syntax: java SuggestBuilder [-l] [-s] [<dbname>] [<dumpfile>]");
			System.out.println("Options:");
			System.out.println("   -s    use latest precursor snapshot");
			System.out.println("   -l    rebuild all local indexes from snapshots");
			return;
		}
			
		UnicodeDecomposer.getInstance();
		Localization.loadInterwiki();
		Collections.sort(dbnames);
		long start = System.currentTimeMillis();
		for(String dbname : dbnames){
			// preload
			Localization.readLocalization(global.getLanguage(dbname));

			IndexId iid = IndexId.get(dbname);
			IndexId spell = iid.getSpell();
			IndexId pre = spell.getPrecursor();
			if(spell == null){
				log.fatal("Index "+iid+" doesn't have a spell-check index assigned. Enable them in global configuration.");
				continue;
			}

			if(inputfile != null){
				log.info("Rebuilding precursor index...");
				// open			
				InputStream input = null;
				try {
					input = Tools.openInputFile(inputfile);
				} catch (IOException e) {
					log.fatal("I/O error opening "+inputfile+" : "+e.getMessage());
					return;
				}

				// make fresh clean index		
				try {
					CleanIndexImporter importer = new CleanIndexImporter(pre,iid.getLangCode());
					XmlDumpReader reader = new XmlDumpReader(input,new ProgressFilter(importer, 1000));
					reader.readDump();
					importer.closeIndex();
				} catch (IOException e) {
					if(!e.getMessage().equals("stopped")){
						e.printStackTrace();
						log.fatal("I/O error reading dump for "+dbname+" from "+inputfile+" : "+e.getMessage());
						return;
					}
				}		
			}

			log.info("Making spell-check index");
			// make phrase index

			SpellCheckIndexer tInx = new SpellCheckIndexer(spell);
			String path = pre.getImportPath();
			if(useSnapshot)
				path = IndexRegistry.getInstance().getLatestSnapshot(pre).getPath();
			tInx.createFromPrecursor(path);		

			// make snapshots
			IndexThread.makeIndexSnapshot(spell,spell.getImportPath());
		}
		long end = System.currentTimeMillis();

		System.out.println("Finished making spell-check index in "+formatTime(end-start));
	}
	
	private static String formatTime(long l) {
		l /= 1000;
		if(l >= 3600) return l/3600+"h "+(l%3600)/60+"m "+(l%60)+"s";
		else if(l >= 60) return (l%3600)/60+"m "+(l%60)+"s";
		else return l+"s";
	}
}
