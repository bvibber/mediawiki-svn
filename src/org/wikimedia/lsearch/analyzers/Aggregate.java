package org.wikimedia.lsearch.analyzers;

import java.util.ArrayList;

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
	
	/** Construct from arbitrary text that will be tokenized */
	public Aggregate(String text, float boost, IndexId iid, boolean exactCase){
		tokens = new FastWikiTokenizerEngine(text,iid,exactCase).parse();
		this.boost = boost;
	}
	
	/** Number of tokens */
	public int length(){
		if(tokens != null)
			return tokens.size();
		else
			return 0;
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
