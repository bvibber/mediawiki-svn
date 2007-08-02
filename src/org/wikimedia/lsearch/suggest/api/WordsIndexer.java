package org.wikimedia.lsearch.suggest.api;

import java.io.IOException;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.SimpleAnalyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.wikimedia.lsearch.suggest.api.Dictionary.Word;
import org.wikimedia.lsearch.suggest.dist.DoubleMetaphone;

/**
 * Create the index with words. Overview:
 * - 1 word = 1 document
 * - split the word into ngrams and index those   
 * 
 * @author rainman
 *
 */
public class WordsIndexer {
	static Logger log = Logger.getLogger(WordsIndexer.class);
	protected DoubleMetaphone dmeta;
	/** If word occurs less that minFreq times, it will be discarded */
	protected int minFreq;
	protected NgramIndexer indexer;
	String path;
	
	public WordsIndexer(String path, int minFreq) throws IOException {
		this.path = path;
		this.minFreq = minFreq;
		this.dmeta = new DoubleMetaphone();
		this.indexer = new NgramIndexer();
	}
		
	public void createIndex() throws IOException{
		indexer.createIndex(path, new SimpleAnalyzer());
	}
	
	/** Add word to the index, make sure index is open */
	public void addWord(Word word){
		if(word.frequency < minFreq)
			return;
		if(word.getWord().length() < 2)
			return;
		Document doc = new Document();
		indexer.createNgramFields(doc,"",word.word);
		doc.add(new Field("word",word.word, Field.Store.YES, Field.Index.UN_TOKENIZED));
		doc.add(new Field("freq",Integer.toString(word.frequency), Field.Store.YES, Field.Index.NO));
		doc.add(new Field("metaphone1",dmeta.doubleMetaphone(word.word), Field.Store.NO, Field.Index.UN_TOKENIZED));
		doc.add(new Field("metaphone2",dmeta.doubleMetaphone(word.word,true), Field.Store.NO, Field.Index.UN_TOKENIZED));

		indexer.addDocument(doc);
	}
	
	public void closeAndOptimze() throws IOException{
		indexer.closeAndOptimize();
	}
}
