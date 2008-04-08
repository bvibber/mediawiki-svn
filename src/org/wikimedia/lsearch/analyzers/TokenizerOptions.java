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
	/** don't do decompostion and common transliterations (useful for spellcheck) */
	boolean noAliases = false;
	/** don't pick put trailing chars, e.g. ++ in c++ */
	boolean noTrailing = false;
	/** catch ? and ! as trailing chars (and not sentence breaks) - useful for titles */
	boolean extendedTrailing = false;
	/** if to split tokens with apostrophes and points in them */
	boolean split = true;
	/** generate extra original token if the word is in upper case */
	boolean extraUpperCaseToken = false;
	
	public TokenizerOptions(boolean exactCase){
		this.exactCase = exactCase;
	}
	
	public static class NoRelocation extends TokenizerOptions {	
		public NoRelocation(boolean exactCase){
			super(exactCase);
			this.relocationParsing = false;
		}
	}
	
	public static class NoRelocationNoSplit extends NoRelocation {
		public NoRelocationNoSplit(boolean exactCase){
			super(exactCase);
			this.split = false;
		}
	}
	
	public static class Title extends TokenizerOptions {	
		public Title(boolean exactCase){
			super(exactCase);
			relocationParsing = false;
			noCaseDetection = true;
			extendedTrailing = true;
			extraUpperCaseToken = true;
		}
	}
	
	public static class TitleNoSplit extends Title {
		public TitleNoSplit(boolean exactCase){
			super(exactCase);
			this.split = false;
		}
	}
	
	public static class Highlight extends TokenizerOptions {
		public Highlight(boolean exactCase){
			super(exactCase); 
			this.highlightParsing = true;
			this.relocationParsing = false;
			this.simplifyGlue = true;
		}
	}
	
	/** Used for titles, doesn't simply glue and has no case detection */
	public static class HighlightOriginal extends Highlight {
		public HighlightOriginal(boolean exactCase){
			super(exactCase);
			this.simplifyGlue = false;
			this.noCaseDetection = true;
		}
	}
	/** Used to filter prefixes (up to FastWikiTokenizer.MAX_WORD_LEN chars) */
	public static class PrefixCanonization extends TokenizerOptions {
		public PrefixCanonization(){
			super(false);
			this.noTokenization = true;
		}
	}
	
	public static class SpellCheck extends TokenizerOptions {
		public SpellCheck(){
			super(false);
			relocationParsing = false;
			noAliases = true;
			noTrailing = true;
		}
	}
	
	public static class SpellCheckSearch extends TokenizerOptions {
		public SpellCheckSearch(){
			super(false);
			relocationParsing = false;
			noAliases = true;
			noTrailing = true;
		}
	}
	
	public static class SpellCheckTitle extends Title {
		public SpellCheckTitle(){
			super(false);
			noAliases = true;
			noTrailing = true;
			extraUpperCaseToken = false;
		}
	}
}
