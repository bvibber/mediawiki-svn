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
	/** parse for highlighting, will parse tokens and gaps (which are normalized) */
	boolean highlightParsing = false;
	/** if text should be tidied */
	boolean simplifyGlue = false;
	/** Treat whole text as single token */
	boolean noTokenization = false;
	/** dont' output tokens with tipe upper and titlecase */
	boolean noCaseDetection = false;
	
	public TokenizerOptions(boolean exactCase){
		this.exactCase = exactCase;
	}
	
	public static class NoRelocation extends TokenizerOptions {	
		public NoRelocation(boolean exactCase){
			super(exactCase);
			this.relocationParsing = false;
		}
	}
	
	public static class Title extends TokenizerOptions {	
		public Title(boolean exactCase){
			super(exactCase);
			relocationParsing = false;
			noCaseDetection = true;
		}
	}
	
	public static class Highlight extends TokenizerOptions {
		public Highlight(){
			super(false); 
			this.highlightParsing = true;
			this.relocationParsing = false;
			this.simplifyGlue = true;
		}
	}
	
	public static class HighlightOriginal extends TokenizerOptions {
		public HighlightOriginal(){
			super(false); 
			this.highlightParsing = true;
			this.relocationParsing = false;
			this.simplifyGlue = false;
		}
	}
	/** Used to filter prefixes (up to FastWikiTokenizer.MAX_WORD_LEN chars) */
	public static class PrefixCanonization extends TokenizerOptions {
		public PrefixCanonization(){
			super(false);
			this.noTokenization = true;
		}
	}
}
