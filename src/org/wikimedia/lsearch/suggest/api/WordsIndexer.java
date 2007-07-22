package org.wikimedia.lsearch.suggest.api;

import java.io.IOException;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.SimpleAnalyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.index.IndexWriter;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.index.WikiIndexModifier;
import org.wikimedia.lsearch.suggest.dist.DoubleMetaphone;

/**
 * Create the index with words. Overview:
 * - 1 word = 1 document
 * - split the word into ngrams and index those   
 * 
 * @author rainman
 *
 */
public class WordsIndexer extends Indexer {
	public static class Word {
		protected String word;
		protected int frequency;
		public Word(String word, int frequency) {
			super();
			this.word = word;
			this.frequency = frequency;
		}
		public int getFrequency() {
			return frequency;
		}
		public void setFrequency(int frequency) {
			this.frequency = frequency;
		}
		public String getWord() {
			return word;
		}
		public void setWord(String word) {
			this.word = word;
		}
		public String toString(){
			return word+" : "+frequency;
		}
		
	}
	static Logger log = Logger.getLogger(WordsIndexer.class);
	DoubleMetaphone dmeta;
	/** If word occurs less that minFreq times, it will be discarded */
	protected int minFreq;
	
	public WordsIndexer(String path, int minFreq) throws IOException {
		super(path,new SimpleAnalyzer());
		this.minFreq = minFreq;
		this.dmeta = new DoubleMetaphone();
	}
	
	/** Add word to the index */
	public void addWord(Word word){
		if(word.frequency < minFreq)
			return;
		Document doc = new Document();
		addNgramFields(doc,word.word);
		doc.add(new Field("word",word.word, Field.Store.YES, Field.Index.UN_TOKENIZED));
		doc.add(new Field("freq",Integer.toString(word.frequency), Field.Store.YES, Field.Index.UN_TOKENIZED));
		doc.add(new Field("metaphone1",dmeta.doubleMetaphone(word.word), Field.Store.NO, Field.Index.UN_TOKENIZED));
		doc.add(new Field("metaphone2",dmeta.doubleMetaphone(word.word,true), Field.Store.NO, Field.Index.UN_TOKENIZED));

		try {
			writer.addDocument(doc);
		} catch (Exception e) {
			log.error("Cannot add document "+doc);
			e.printStackTrace();
		}
	}
}
