package org.wikimedia.lsearch.suggest.api;

import java.io.IOException;

import org.apache.lucene.analysis.SimpleAnalyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.wikimedia.lsearch.suggest.api.WordsIndexer.Word;

/**
 * Class to build an index of phrases. It indexes:
 *  1) sets of two words as douglas_adams
 *  2) individual words
 *  
 * 1) is useful for content-dependant suggestions and
 * suggesting splits (splitting one word into two), while
 * 2) is useful for suggesting joins 
 * 
 * @author rainman
 *
 */
public class PhraseIndexer extends Indexer {
	int minFreq;
	
	public PhraseIndexer(String path, int minFreq) throws IOException{
		super(path,new SimpleAnalyzer());
		this.minFreq = minFreq;
	}
	
	/** Add phrase, convenient for suggesting splits and context-dependend suggestions */
	public void addPhrase(String word1, String word2, int frequency){
		addPhrase(word1+"_"+word2,frequency);
	}
	/** Add phrase, join two words by underscore */
	public void addPhrase(String phrase, int frequency){		
		if(frequency < minFreq)
			return;
		Document doc = new Document();
		addNgramFields(doc,phrase);
		doc.add(new Field("phrase",phrase, Field.Store.YES, Field.Index.UN_TOKENIZED));
		doc.add(new Field("freq",Integer.toString(frequency), Field.Store.YES, Field.Index.UN_TOKENIZED));

		try {
			writer.addDocument(doc);
		} catch (Exception e) {
			log.error("Cannot add document "+doc);
			e.printStackTrace();
		}
	}
	
	/** Add ordinary word to the index, convenient for suggesting joins */
	public void addWord(Word word){
		Document doc = new Document();
		doc.add(new Field("word",word.word, Field.Store.YES, Field.Index.UN_TOKENIZED));
		doc.add(new Field("freq",Integer.toString(word.frequency), Field.Store.YES, Field.Index.UN_TOKENIZED));
		
		try {
			writer.addDocument(doc);
		} catch (Exception e) {
			log.error("Cannot add document "+doc);
			e.printStackTrace();
		}
	}
	
	/** Get minimal ngram size for word. Short words (<=3 chars) will have 1-grams, other 2-grams */
	public static int getMinNgram(String word){
		if(word.length() == 2)
			return 1;
		if(word.length() <= 6)
			return word.length() - 2;
		else
			return 5;
	}
	
	/** Get minimal ngram size for word. Long words: 4-grams, other 3-grams, 2-char word only 1-grams */
	public static int getMaxNgram(String word){		
		if(word.length() == 2)
			return 1;
		else
			return getMinNgram(word) + 4;
	}
}
