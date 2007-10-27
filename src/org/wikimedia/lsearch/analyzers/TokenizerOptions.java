package org.wikimedia.lsearch.analyzers;

/**
 * FastWikiTokenizerEngine options 
 * 
 * @author rainman
 *
 */
public class TokenizerOptions {
	/** if capitalization should be preserved */
	boolean exactCase = false; 
	/** if templates should be relocated, etc.. makes sense only if whole article 
	 * is parsed (and not query,or part of an article) */
	boolean relocationParsing = true;
	
	public TokenizerOptions(boolean exactCase){
		this.exactCase = exactCase;
	}
	
	public static class NoRelocation extends TokenizerOptions {	
		public NoRelocation(boolean exactCase){
			super(exactCase);
			this.relocationParsing = false;
		}
	}
}
