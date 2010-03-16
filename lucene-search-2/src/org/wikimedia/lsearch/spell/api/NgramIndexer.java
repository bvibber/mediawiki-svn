package org.wikimedia.lsearch.spell.api;

import java.io.IOException;
import java.util.Collection;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.IndexWriter;
import org.apache.lucene.index.Term;
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
	protected IndexReader reader;
	
	public static enum Type {WORDS, TITLES, TITLE_NGRAM};
	
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
	
	protected void openIndex(String path, Analyzer analyzer, boolean newIndex) throws IOException{
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
				log.error("I/O error openning index for addition of documents at "+path+" : "+e.getMessage(),e);
				throw e1;
			}				
		}
		writer.setMergeFactor(20);
		writer.setMaxBufferedDocs(500);		
		writer.setUseCompoundFile(true);
		writer.setMaxFieldLength(WikiIndexModifier.MAX_FIELD_LENGTH);
		
		reader = IndexReader.open(path);
			
	}
	
	/** Check if index is open and ready for modification */
	public boolean isOpen(){
		return writer != null;
	}
	
	/** Optimize and close index, always call when done indexing */
	public void close() throws IOException {
		try{
			reader.close();
			reader = null;
			writer.close();
			writer = null;			
		} catch(IOException e){
			log.warn("I/O error closing index at "+path,e);
			throw e;
		}
	}
	
	/** Optimize and close index, always call when done indexing */
	public void closeAndOptimize() throws IOException {
		try{
			reader.close();
			reader = null;
			writer.optimize();
			writer.close();
			writer = null;
		} catch(IOException e){
			log.warn("I/O error optimizing/closing index at "+path,e);
			throw e;
		}
	}
	
	/** Return ngrams of specific size for text */
	public static String[] nGramsRegular(String text, int size) {
		int len = text.length();
		String[] res = new String[len - size + 1];
		for (int i = 0; i < len - size + 1; i++) {
			res[i] = text.substring(i, i + size);
		}
		return res;
	}
	
	/** Reverse a string */
	public static String reverse(String source){
	    int len = source.length();
	    StringBuilder dest = new StringBuilder(len);

	    for (int i = (len - 1); i >= 0; i--)
	      dest.append(source.charAt(i));
	    return dest.toString();
	}
	
	/** Return ngrams of specific size for text, assuming circular string */
	public static String[] nGramsCir(String text, int size) {
		int len = text.length();
		String[] res = null;
		if(len <= 6 && size == 2){ // produce reversed 2-grams
			String[] rev = nGramsRegular(reverse(text),size);
			res = new String[len - size + 1 + rev.length];
			System.arraycopy(rev,0,res,len - size + 1,rev.length);
		} else
			res = new String[len - size + 1];
		for (int i = 0; i < len - size + 1; i++) {
			res[i] = text.substring(i, i + size);
		}
		return res;
	}
	
	/** Return normal ngrams + reverse ones for size 2 */
	public static String[] nGrams(String text, int size) {
		int len = text.length();
		String[] res = null;
		if(len <= 6 && size == 2){ // produce reversed 2-grams
			String[] rev = nGramsRegular(reverse(text),size);
			res = new String[len + rev.length];
			System.arraycopy(rev,0,res,len,rev.length);
		} else
			res = new String[len];
		for (int i = 0; i < len; i++) {
			if(i + size <= len)
				res[i] = text.substring(i, i + size);
			else // string is assumed to be circular
				res[i] = text.substring(i)+text.substring(0,(i+size)%len);
		}
		return res;
	}
	
	/** Get minimal ngram size for word. the minimal size should be at least 1/2 of word length */
	public static int getMinNgram(String word, Type type){
		switch(type){
		case WORDS:
			if(word.length() <= 5)
				return 1;
			else if(word.length() <= 7)
				return 2;
			else
				return 3;
		case TITLES:
			if(word.length() < 5)
				new RuntimeException("title in getMinNgram() too short");
			return 5;
		case TITLE_NGRAM:
			if(word.length() <= 4)
				return 1;
			else if(word.length() <= 6)
				return 2;
			else if(word.length() <= 8)
				return 3;
			else if(word.length() <= 10)
				return 4;
			else 
				return 5;
		}
		return 0; // err
	}
	
	/** Maximal size of ngram block, at most the length of word */
	public static int getMaxNgram(String word, Type type){
		switch(type){
		case WORDS:
			if(word.length() <= 4)
				return 2;
			else
				return 3;
		case TITLES:			
			if(word.length() < 5)
				new RuntimeException("title in getMaxNgram() too short");
			return 5;
		case TITLE_NGRAM:
			if(word.length() <= 4)
				return 2;
			else if(word.length() <= 6)
				return 3;
			else if(word.length() <= 8)
				return 4;
			else 
				return 5;
		}
		return 0; // err
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
	
	/** Get prefixed ngram field name */
	public static String getStartField(String prefix){
		if(prefix == null || prefix.equals(""))
			return "start";
		else
			return prefix+"_start";
	}
	
	/** 
	 * Add ngrams of all sizes from 1 to word.length to document
	 * 
	 * @param doc - document to add fields to
	 * @param prefix - prefix to ngram field name
	 * @param word - word
	 */
	protected void createNgramFields(Document doc, String prefix, String word, Type type) {
		int min = getMinNgram(word,type);
		int max = getMaxNgram(word,type);
		String fieldBase = getNgramField(prefix);
		String startField= getStartField(prefix);
		for(int i=min ; i <= max ; i++ ){
			String[] ngrams = nGrams(word,i);
			String field = fieldBase+i;
			for(int j=0 ; j<ngrams.length ; j++){
				String ngram = ngrams[j];				
				if(j==0)
					doc.add(new Field(startField+i, ngram, Field.Store.NO, Field.Index.UN_TOKENIZED));
				doc.add(new Field(field, ngram, Field.Store.NO, Field.Index.UN_TOKENIZED));
			}
		}		
	}
	
	public void deleteDocuments(Term t){
		try {
			log.debug("Deleting document matching term "+t);
			writer.deleteDocuments(t);
		} catch (Exception e) {
			log.error("Cannot delete document : "+e.getMessage(),e);
			e.printStackTrace();
		}
	}
	
	public void addDocument(Document doc){
		try {
			log.debug("Adding document "+doc);
			writer.addDocument(doc);
		} catch (Exception e) {
			log.error("Cannot add document "+doc+" : "+e.getMessage(),e);
			e.printStackTrace();
		}
	}

	public IndexReader getReader() {
		return reader;
	}
}
