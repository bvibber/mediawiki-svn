package org.wikimedia.lsearch.importer;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;

import org.apache.log4j.Logger;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.prefix.PrefixIndexBuilder;
import org.wikimedia.lsearch.spell.SuggestBuilder;
import org.wikimedia.lsearch.spell.TitleNgramBuilder;
import org.wikimedia.lsearch.util.FSUtils;
import org.wikimedia.lsearch.util.ProgressReport;

/**
 * Take a list of entries like dbname dump-file and rebuild
 * everything from scratch (links, related, index, highlight, 
 * title, prefix, spell)  
 * 
 * @author rainman
 *
 */
public class BuildAll {
	static org.apache.log4j.Logger log = null;
	
	protected static void printHelp(){
		System.out.println("Syntax: BuildAll [-f <file>] [-lt] [-i] [-sc] [dump file] [dbname]");
		System.out.println("Options:");
		System.out.println("    -f <file>   - use a file with a list of pairs <dbname> <dump file>");
		System.out.println("    -lt         - leave titles - don't delete old titles indexes");
		System.out.println("    -i          - import only - don't copy over all indexes in index/ folder");
		System.out.println("    -sc         - don't rebuild spell-check index");
	}
	public static void main(String[] args) throws IOException{
		System.out.println("MediaWiki lucene-search indexer - rebuild all indexes associated with a database.");
		Configuration.open();
		GlobalConfiguration.getInstance();
		log = Logger.getLogger(BuildAll.class);
		String path = null, dbname = null, dump = null;
		boolean noSpellcheck = false;
		boolean leaveTitles = false, importOnly = false;
		for(int i=0;i<args.length;i++){
			if(args[i].equals("-f"))
				path = args[++i];
			else if(args[i].equals("-lt"))
				leaveTitles = true;
			else if(args[i].equals("-i"))
				importOnly = true;
			else if(args[i].equals("-sc"))
				noSpellcheck = true;
			else if(args[i].startsWith("-")){
				System.out.println("Unrecognized option "+args[i]);
				printHelp();
				return;
			} else if(dump == null)
				dump = args[i];
			else if(dbname == null)
				dbname = args[i];
			else if(args[i].equals("--help")){
				printHelp();
				return;
			}
		}
		if(!(path!=null || (dbname!=null && dump!=null))){
			System.out.println("Insufficient number of parameters");
			printHelp();
			return;
		}
		
		long start = System.currentTimeMillis();
		
		HashMap<String,String> dumpFiles = new HashMap<String,String>();
		ArrayList<IndexId> iids = new ArrayList<IndexId>();
		if(path != null){
			File f = new File(path);
			BufferedReader reader = new BufferedReader(new InputStreamReader(new FileInputStream(f)));
			String line = null;
			while((line = reader.readLine())!=null){
				String[] parts = line.split(" +",2);				
				iids.add(IndexId.get(parts[0]));
				dumpFiles.put(parts[0],parts[1]);				
			}
		} else{
			iids.add(IndexId.get(dbname));
			dumpFiles.put(dbname,dump);
		}
		
		// delete old titles indexes
		if(!leaveTitles && !importOnly){
			for(IndexId iid : iids){
				if(iid.hasTitlesIndex()){
					IndexId titles = iid.getTitlesIndex();
					FSUtils.deleteRecursive(titles.getImportPath());
				}
			}
		}
		
		// rebuild indexes
		for(IndexId iid : iids){
			try{
				String dumpFile = dumpFiles.get(iid.toString());
				Importer.main(new String[] {"-s","-h","-t",dumpFile,iid.toString()});
				if(!importOnly){
					for(IndexId piid : iid.getPhysicalIndexIds()){
						copy(piid.getImportPath(),piid.getIndexPath());
						copy(piid.getHighlight().getImportPath(),piid.getHighlight().getIndexPath());
					}
					copy(iid.getLinks().getImportPath(),iid.getLinks().getIndexPath());
					
				}
				if(iid.hasPrefix()){
					PrefixIndexBuilder.main(new String[] {iid.toString()});
					if(!importOnly)
						copy(iid.getPrefix().getPrecursor().getImportPath(),iid.getPrefix().getPrecursor().getIndexPath());
				}
				if(iid.hasSpell() && !noSpellcheck){
					SuggestBuilder.main(new String[] {iid.toString(),dumpFile});
					if(!importOnly)
						copy(iid.getSpell().getPrecursor().getImportPath(),iid.getSpell().getPrecursor().getIndexPath());
				}
				if(iid.hasTitleNgram()){
					TitleNgramBuilder.main(new String[] {iid.toString()});
					if(!importOnly)
						copy(iid.getTitleNgram().getImportPath(),iid.getTitleNgram().getIndexPath());
				}
			} catch(IOException e){
				e.printStackTrace();
				log.error("Error during rebuild of "+iid+" : "+e.getMessage());
			}			
		}
		// link titles
		if(!importOnly){
			HashSet<String> processed = new HashSet<String>(); 
			for(IndexId iid : iids){
				if(iid.hasTitlesIndex()){
					IndexId titles = iid.getTitlesIndex();
					String tpath = titles.getImportPath();
					if(!processed.contains(tpath)){
						copy(tpath,titles.getIndexPath());
						processed.add(tpath);
					}
				}
			}
		}
		System.out.println("Finished build in "+ProgressReport.formatTime(System.currentTimeMillis()-start));
	}
	
	protected static void copy(String from, String to) throws IOException{
		FSUtils.deleteRecursive(to);
		FSUtils.createHardLinkRecursive(from,to);
	}
}
