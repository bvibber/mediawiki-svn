package org.wikimedia.lsearch.spell;

import java.io.IOException;

import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.spell.api.TitleNgramIndexer;

public class TitleNgramBuilder {
	public static void main(String[] args) throws IOException{
		System.out.println("MediaWiki lucene-search indexer - build title ngram index (for similar titles)\n");
		Configuration.open();
		GlobalConfiguration.getInstance();
		if(args.length == 0 || args[0].startsWith("-")){
			System.out.println("Syntax: TitleNgramBuilder <dbname>");
			return;
		}
		String dbname = args[0];
		IndexId iid = IndexId.get(dbname);
		
		TitleNgramIndexer.importFromLinks(iid);
	}
}
