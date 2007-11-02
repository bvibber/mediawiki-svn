package org.wikimedia.lsearch.analyzers;

import java.util.ArrayList;
import java.util.HashSet;

import org.apache.lucene.analysis.Token;
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
	
	/** Construct from arbitrary text that will be tokenized */
	public Aggregate(String text, float boost, IndexId iid, boolean exactCase, HashSet<String> stopWords){
		TokenizerOptions options = new TokenizerOptions.NoRelocation(exactCase);
		tokens = new FastWikiTokenizerEngine(text,iid,options).parse();
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
	
	/** Construct for highlight */
	public Aggregate(String text, float boost, IndexId iid){
		TokenizerOptions options = new TokenizerOptions.Highlight();
		tokens = new FastWikiTokenizerEngine(text,iid,options).parse();
		this.boost = boost;
		this.noStopWordsLength = tokens.size();
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
