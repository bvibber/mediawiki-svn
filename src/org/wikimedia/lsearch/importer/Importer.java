package org.wikimedia.lsearch.importer;

import java.io.IOException;
import java.io.InputStream;

import org.apache.log4j.Logger;
import org.mediawiki.dumper.ProgressFilter;
import org.mediawiki.dumper.Tools;
import org.mediawiki.importer.XmlDumpReader;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.GlobalConfiguration;
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

	/**
	 * @param args
	 */
	public static void main(String[] args) {
		System.out.println("MediaWiki Lucene search indexer - index builder from xml database dumps.\n");
		
		Configuration.open();
		Logger log = Logger.getLogger(Importer.class); 
		
		if(args.length != 2){
			System.out.println("Syntax: java Importer <inputfile> <dbname>");
			return;
		}
		String inputfile = args[0];
		String dbname = args[1];

		// preload
		UnicodeDecomposer.getInstance();
		Localization.readLocalization(GlobalConfiguration.getInstance().getLanguage(dbname));
		Localization.loadInterwiki();
		
		// open
		InputStream input = null;
		try {
			input = Tools.openInputFile(inputfile);
		} catch (IOException e) {
			log.fatal("I/O error opening "+inputfile);
		}
		
		// read
		DumpImporter dp = new DumpImporter(dbname);
		XmlDumpReader reader = new XmlDumpReader(input,new ProgressFilter(dp, 100));
		try {
			reader.readDump();
		} catch (IOException e) {
			log.warn("I/O error reading dump for "+dbname+" from "+inputfile);
		}

		log.info("Closing/optimizing index...");
		dp.closeIndex();
	}

}
