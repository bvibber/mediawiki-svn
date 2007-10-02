package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.io.Reader;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Iterator;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.Tokenizer;
import org.wikimedia.lsearch.config.IndexId;

/** Uses FastWikiTokenizerEngine to tokenize text */
public class WikiTokenizer extends Tokenizer {
	protected FastWikiTokenizerEngine parser = null;
	protected ArrayList<Token> tokens = null;
	protected Iterator<Token> tokenIt = null;
	protected ArrayList<String> categories = null;
	protected HashMap<String,String> interwikis = null;
	protected HashSet<String> keywords = null;
	
	/** Use <code>WikiTokenizer(String)</code> constructor */
	@Deprecated
	public WikiTokenizer(Reader r){
		parser = new FastWikiTokenizerEngine(r);		
		this.input = r;
		//throw new Exception("Use constructor WikiTokenizer(String), to have optimal perfomance");
	}
	
	/** Use this with constructor with caution, since tokenizer won't 
	 * be able to read localized data (i.e. localized name for image, 
	 * category, etc..). This is fine for parsing search queries, but
	 * not for parsing articles.  
	 * 
	 * @param str
	 */
	
	public WikiTokenizer(String str, IndexId iid, boolean exactCase){
		parser = new FastWikiTokenizerEngine(str,iid,exactCase);		
		this.input = null;
	}
	
	/** 
	 * Invoke the wiki tokenizer, creates the token stream
	 * and the list of categories and interwikis
	 */
	public void tokenize(){
		if(tokens == null){
			tokens = parser.parse();
			tokenIt = tokens.iterator();
			categories = parser.getCategories();
			interwikis = parser.getInterwikis();
			keywords = parser.getKeywords();
		}
	}
	
	public void resetIterator(){
		if(tokens != null)
			tokenIt = tokens.iterator();
		else
			tokenize();
	}
	
	@Override
	public Token next() throws IOException {
		if(tokens == null)
			tokenize();

		if(tokenIt.hasNext())
			return tokenIt.next();
		else 
			return null;
	}

	public ArrayList<String> getCategories() {
		return categories;
	}

	public HashMap<String, String> getInterwikis() {
		return interwikis;
	}

	public ArrayList<Token> getTokens() {
		return tokens;
	}

	public HashSet<String> getKeywords() {
		return keywords;
	}
	
	
	

}
