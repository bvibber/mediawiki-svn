package org.wikimedia.lsearch.spell;

import java.io.IOException;
import java.io.InputStream;

import org.apache.log4j.Logger;
import org.mediawiki.dumper.ProgressFilter;
import org.mediawiki.dumper.Tools;
import org.mediawiki.importer.XmlDumpReader;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
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
		String dbname = null;
		
		System.out.println("MediaWiki Lucene search indexer - build spelling suggestion index.\n");
		
		Configuration.open();
		
		if(args.length !=1 && args.length != 2){
			System.out.println("Syntax: java SuggestBuilder <dbname> [<dumpfile>]");
			return;
		}
		inputfile = args.length>1? args[1] : null;
		dbname = args[0];
	
		GlobalConfiguration global = GlobalConfiguration.getInstance(); 
		String langCode = global.getLanguage(dbname);
		// preload
		UnicodeDecomposer.getInstance();
		Localization.readLocalization(langCode);
		Localization.loadInterwiki();

		long start = System.currentTimeMillis();
		IndexId iid = IndexId.get(dbname);
		IndexId spell = iid.getSpell();
		if(spell == null){
			log.fatal("Index "+iid+" doesn't have a spell-check index assigned. Enable them in global configuration.");
			return;
		}
		
		if(inputfile != null){
			log.info("Rebuilding the temporary index for words");
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
				CleanIndexImporter importer = new CleanIndexImporter(spell,langCode);
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
		tInx.createFromTempIndex();		
		
		long end = System.currentTimeMillis();

		// make snapshots
		IndexThread.makeIndexSnapshot(spell,spell.getImportPath());

		System.out.println("Finished making spell-check index in "+formatTime(end-start));
	}
	
	private static String formatTime(long l) {
		l /= 1000;
		if(l >= 3600) return l/3600+"h "+(l%3600)/60+"m "+(l%60)+"s";
		else if(l >= 60) return (l%3600)/60+"m "+(l%60)+"s";
		else return l+"s";
	}
}
