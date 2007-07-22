package org.wikimedia.lsearch.suggest;

import java.io.IOException;
import java.io.InputStream;
import java.util.ArrayList;
import java.util.Map.Entry;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.document.Document;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.Term;
import org.apache.lucene.search.Hits;
import org.apache.lucene.search.IndexSearcher;
import org.apache.lucene.search.TermQuery;
import org.apache.lucene.store.FSDirectory;
import org.mediawiki.dumper.ProgressFilter;
import org.mediawiki.dumper.Tools;
import org.mediawiki.importer.XmlDumpReader;
import org.wikimedia.lsearch.analyzers.FastWikiTokenizerEngine;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.config.IndexRegistry;
import org.wikimedia.lsearch.importer.DumpImporter;
import org.wikimedia.lsearch.suggest.api.LuceneDictionary;
import org.wikimedia.lsearch.suggest.api.PhraseIndexer;
import org.wikimedia.lsearch.suggest.api.WordsIndexer;
import org.wikimedia.lsearch.suggest.api.WordsIndexer.Word;
import org.wikimedia.lsearch.util.Localization;
import org.wikimedia.lsearch.util.StringCounter;
import org.wikimedia.lsearch.util.UnicodeDecomposer;
import org.wikimedia.lsearch.util.StringCounter.Count;

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
		
		System.out.println("MediaWiki Lucene search indexer - build suggestions index.\n");
		
		Configuration.open();
		
		if(args.length !=1 && args.length != 2){
			System.out.println("Syntax: java SpellCheckBuilder <dbname> [<dumpfile>]");
			return;
		}
		inputfile = args.length>1? args[1] : null;
		dbname = args[0];
	

		String langCode = GlobalConfiguration.getInstance().getLanguage(dbname);
		// preload
		UnicodeDecomposer.getInstance();
		Localization.readLocalization(langCode);
		Localization.loadInterwiki();

		long start = System.currentTimeMillis();
		IndexId iid = IndexId.get(dbname);
		
		if(inputfile != null){
			// open			
			InputStream input = null;
			try {
				input = Tools.openInputFile(inputfile);
			} catch (IOException e) {
				log.fatal("I/O error opening "+inputfile);
				return;
			}
			
			// make fresh clean index		
			try {
				CleanIndexImporter importer = new CleanIndexImporter(dbname,langCode);
				XmlDumpReader reader = new XmlDumpReader(input,new ProgressFilter(importer, 1000));
				reader.readDump();
				importer.closeIndex();
			} catch (IOException e) {
				if(!e.getMessage().equals("stopped")){
					log.fatal("I/O error reading dump for "+dbname+" from "+inputfile);
					return;
				}
			}		
		}
		// make words index
		try {
			LuceneDictionary dict = new LuceneDictionary(IndexReader.open(iid.getSuggestCleanPath()),"contents");
			WordsIndexer writer = new WordsIndexer(iid.getSuggestWordsPath(),50);
			Word word;
			while((word = dict.next()) != null){
				writer.addWord(word);
			}
			writer.close();
		} catch (IOException e) {
			log.fatal("Cannot open clean dictionary for "+iid+" : "+e.getMessage());
			e.printStackTrace();
			return;
		}
		
		// make phrase index
		try {
			LuceneDictionary dict = new LuceneDictionary(IndexReader.open(iid.getSuggestCleanPath()),"title");
			PhraseIndexer writer = new PhraseIndexer(iid.getSuggestPhrasesPath(),1);
			IndexSearcher searcher = new IndexSearcher(iid.getSuggestCleanPath());
			Word word;
			while((word = dict.next()) != null){
				// index word
				writer.addWord(word);
				String w = word.getWord();
				StringCounter counter = new StringCounter();
				Hits hits = searcher.search(new TermQuery(new Term("title",w)));
				// find all phrases beginning with word
				for(int i=0;i<hits.length();i++){
					Document doc = hits.doc(i);
					// get original tokens
					FastWikiTokenizerEngine parser = new FastWikiTokenizerEngine(doc.get("title"),langCode,false); 
					ArrayList<Token> tokens = parser.parse();
					for(int j=0;j<tokens.size()-1;j++){
						Token t = tokens.get(j);
						// ignore aliases
						if(t.getPositionIncrement() == 0)
							continue;
						// find phrases beginning with the target word
						if(w.equals(t.termText())){
							counter.count(t.termText()+"_"+tokens.get(j+1).termText());
						}
					}
				}
				// index phrases
				for(Entry<String,Count> e : counter.getSet()){
					writer.addPhrase(e.getKey(),e.getValue().num);
				}
				
			}
			writer.close();
		} catch (IOException e) {
			log.fatal("Cannot open clean dictionary for "+iid+" : "+e.getMessage());
			e.printStackTrace();
			return;
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
