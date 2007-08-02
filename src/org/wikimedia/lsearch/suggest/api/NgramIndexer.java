package org.wikimedia.lsearch.suggest.api;

import java.io.IOException;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.index.IndexWriter;
import org.wikimedia.lsearch.index.WikiIndexModifier;

/** 
 * Useful for basic ngram indexes handling, open/close indexes, add ngram fields, etc..  
 * 
 * @author rainman
 *
 */
public class NgramIndexer {
	Logger log = Logger.getLogger(NgramIndexer.class);
	protected String path;
	protected Analyzer analyzer;
	protected IndexWriter writer;
	
	public NgramIndexer(){
		path = null;
		analyzer = null;
		writer = null;
	}
	
	/** Make a new ngram index */
	public void createIndex(String path, Analyzer analyzer) throws IOException{
		openIndex(path,analyzer,true);
	}
	
	/** Reopen old index, make if doesn't exist */
	public void reopenIndex(String path, Analyzer analyzer) throws IOException{
		openIndex(path,analyzer,false);
	}
	
	public void openIndex(String path, Analyzer analyzer, boolean newIndex) throws IOException{
		this.path = path;
		this.analyzer = analyzer;
		try {
			writer = new IndexWriter(path,analyzer,newIndex);
		} catch (IOException e) {				
			try {
				log.info("Making new index at path "+path);
				// try to make brand new index
				WikiIndexModifier.makeDBPath(path); // ensure all directories are made				
				writer = new IndexWriter(path,analyzer,newIndex);
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
	
	/** Check if index is open and ready for modification */
	public boolean isOpen(){
		return writer != null;
	}
	
	/** Optimize and close index, always call when done indexing */
	public void close() throws IOException {
		try{
			writer.close();
			writer = null;
		} catch(IOException e){
			log.warn("I/O error closing index at "+path);
			throw e;
		}
	}
	
	/** Optimize and close index, always call when done indexing */
	public void closeAndOptimize() throws IOException {
		try{
			writer.optimize();
			writer.close();
			writer = null;
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
	
	/** Get minimal ngram size for word. the minimal size should be at least 1/2 of word length */
	public static int getMinNgram(String word){
		if(word.length() <= 3)
			return 1;
		else if(word.length() == 4 || word.length() == 5)
			return 2;
		else
			return 3;
	}
	
	/** Maximal size of ngram block, at most the length of word */
	public static int getMaxNgram(String word){
		if(word.length() == 2)
			return 2;
		else
			return 3;
	}
	
	/** Get ngram field name with no prefix */
	public static String getNgramField(){
		return getNgramField(null);
	}
	
	/** Get prefixed ngram field name */
	public static String getNgramField(String prefix){
		if(prefix == null || prefix.equals(""))
			return "ngram";
		else
			return prefix+"_ngram";
	}
	
	/** 
	 * Add ngrams of all sizes from 1 to word.length to document
	 * 
	 * @param doc - document to add fields to
	 * @param prefix - prefix to ngram field name
	 * @param word - word
	 */
	protected void createNgramFields(Document doc, String prefix, String word) {
		int min = getMinNgram(word);
		int max = getMaxNgram(word);
		String fieldBase = getNgramField(prefix);
		for(int i=min ; i <= max ; i++ ){
			String[] ngrams = nGrams(word,i);
			String field = fieldBase+i;
			for(int j=0 ; j<ngrams.length ; j++){
				String ngram = ngrams[j];				
				doc.add(new Field(field, ngram, Field.Store.NO, Field.Index.UN_TOKENIZED));
			}
		}		
	}
	
	public void addDocument(Document doc){
		try {
			log.debug("Adding document "+doc);
			writer.addDocument(doc);
		} catch (Exception e) {
			log.error("Cannot add document "+doc+" : "+e.getMessage());
			e.printStackTrace();
		}
	}
}
