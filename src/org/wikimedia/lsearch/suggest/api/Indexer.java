package org.wikimedia.lsearch.suggest.api;

import java.io.IOException;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.index.IndexWriter;
import org.wikimedia.lsearch.index.WikiIndexModifier;

/** 
 * Base indexer class. Open/close index.
 * 
 * @author rainman
 *
 */
public class Indexer {
	Logger log = Logger.getLogger(Indexer.class);
	protected String path;
	protected Analyzer analyzer;
	protected IndexWriter writer;
	
	public Indexer(String path, Analyzer analyzer) throws IOException{
		this.path = path;
		this.analyzer = analyzer;
		try {
			writer = new IndexWriter(path,analyzer,true); // always make new index
		} catch (IOException e) {				
			try {
				log.info("Making new index at path "+path);
				// try to make brand new index
				WikiIndexModifier.makeDBPath(path); // ensure all directories are made				
				writer = new IndexWriter(path,analyzer,true);
			} catch (IOException e1) {
				log.error("I/O error openning index for addition of documents at "+path+" : "+e.getMessage());
				throw e1;
			}				
		}
		writer.setMergeFactor(20);
		writer.setMaxBufferedDocs(500);		
		writer.setUseCompoundFile(true);
		writer.setMaxFieldLength(WikiIndexModifier.MAX_FIELD_LENGTH);
			
	}
	
	/** Optimize and close index, always call when done indexing */
	public void close() throws IOException {
		try{
			writer.optimize();
			writer.close();
		} catch(IOException e){
			log.warn("I/O error optimizing/closing index at "+path);
			throw e;
		}
	}
	
	/** Return ngrams of specific size for text */
	public static String[] nGrams(String text, int size) {
		int len = text.length();
		String[] res = new String[len - size + 1];
		for (int i = 0; i < len - size + 1; i++) {
			res[i] = text.substring(i, i + size);
		}
		return res;
	}
	
	/** Get minimal ngram size for word. Short words (<=3 chars) will have 1-grams, other 2-grams */
	public static int getMinNgram(String word){
		if(word.length() <= 3)
			return 1;
		else if(word.length() == 4)
			return 2;
		else
			return 3;
	}
	/** Get minimal ngram size for word. Long words: 4-grams, other 3-grams, 2-char word only 1-grams */
	public static int getMaxNgram(String word){
		if(word.length() > 4)
			return 3;
		if(word.length() == 2)
			return 1;
		return 2;
	}
	
	/** 
	 * Add ngrams of all sizes from 1 to word.length to document
	 * 
	 * @param doc - document to add fields to
	 * @param word - word
	 */
	protected void addNgramFields(Document doc, String word) {
		int min = getMinNgram(word);
		int max = getMaxNgram(word);
		for(int i=min ; i <= max ; i++ ){
			String[] ngrams = nGrams(word,i);
			String field = "ngram"+i;
			for(int j=0 ; j<ngrams.length ; j++){
				String ngram = ngrams[j];
				if(j == 0)
					doc.add(new Field("start"+i, ngram, Field.Store.NO, Field.Index.UN_TOKENIZED));
				else if(j == ngrams.length-1)
					doc.add(new Field("end"+i, ngram, Field.Store.NO, Field.Index.UN_TOKENIZED));
				// finally add regular ngram
				doc.add(new Field(field, ngram, Field.Store.NO, Field.Index.UN_TOKENIZED));
			}
		}		
	}
}
