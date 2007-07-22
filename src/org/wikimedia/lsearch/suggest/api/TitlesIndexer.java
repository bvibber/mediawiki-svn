package org.wikimedia.lsearch.suggest.api;

import java.io.IOException;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.document.Field.Index;
import org.apache.lucene.document.Field.Store;

public class TitlesIndexer extends Indexer {
	static Logger log = Logger.getLogger(TitlesIndexer.class);
	
	public TitlesIndexer(String path, Analyzer analyzer) throws IOException{
		super(path,analyzer);
	}

	public void addTitle(int ns, String title){
		Document doc = new Document();
		doc.add(new Field("title",title,Store.YES,Index.TOKENIZED));
		doc.add(new Field("namespace",Integer.toString(ns),Store.YES,Index.UN_TOKENIZED));
		try {
			writer.addDocument(doc);
		} catch (IOException e) {
			log.error("Cannot add document "+doc);
			e.printStackTrace();
		}
	}
	
}
