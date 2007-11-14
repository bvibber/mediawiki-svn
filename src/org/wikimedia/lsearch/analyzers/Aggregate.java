package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.util.ArrayList;
import java.util.HashSet;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenStream;
import org.wikimedia.lsearch.config.IndexId;

/**
 * Aggregate bean that captures information about one
 * item going into the some index aggregate field. 
 * 
 * @author rainman
 *
 */
public class Aggregate {
	protected ArrayList<Token> tokens;
	protected float boost;
	protected int noStopWordsLength;
	
	/** Construct from arbitrary text that will be tokenized 
	 * @throws IOException */
	public Aggregate(String text, float boost, IndexId iid, Analyzer analyzer, String field, HashSet<String> stopWords) throws IOException{
		this.tokens = toTokenArray(analyzer.tokenStream(field,text));
		this.boost = boost;
		if(stopWords != null){
			noStopWordsLength = 0;		
			for(Token t : tokens){
				if(!stopWords.contains(t))
					noStopWordsLength++;
			}
		} else
			noStopWordsLength = tokens.size();
	}
	
	/** Construct with specific analyzer  
	 * @throws IOException */
	public Aggregate(String text, float boost, IndexId iid, Analyzer analyzer, String field) throws IOException{		
		this.tokens = toTokenArray(analyzer.tokenStream(field,text));
		this.boost = boost;
		this.noStopWordsLength = tokens.size();
	}
	
	private ArrayList<Token> toTokenArray(TokenStream stream) throws IOException {
		ArrayList<Token> tt = new ArrayList<Token>();
		Token t = null;
		while( (t = stream.next()) != null){
			tt.add(t);
		}
		return tt;
	}

	/** Number of tokens */
	public int length(){
		if(tokens != null)
			return tokens.size();
		else
			return 0;
	}
	
	/** Number of tokens when stop words are excluded */
	public int getNoStopWordsLength(){
		return noStopWordsLength;
	}
	
	/** boost factor */
	public float boost(){
		return boost;
	}

	public Token getToken(int index){
		return tokens.get(index);
	}
	
	public ArrayList<Token> getTokens() {
		return tokens;
	}
	
}
