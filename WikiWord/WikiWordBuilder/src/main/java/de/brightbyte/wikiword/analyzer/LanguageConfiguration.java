package de.brightbyte.wikiword.analyzer;

import java.util.ArrayList;
import java.util.List;
import java.util.regex.Pattern;

import de.brightbyte.wikiword.analyzer.AbstractAnalyzer.RegularExpressionMangler;

/**
 * This class is the basis for language-specific configuration classes. It defines the 
 * configurable properties that are used by an instance of PlainTextAnalyzer for processing
 * text in a given language, and provides sensible defaults.   
 * 
 * @author daniel
 */
public class LanguageConfiguration {
	
	/**
	 * A string containing a regular expression for matching whitespace, for use
	 * in defining complex patterns latter.
	 */
	public static final String SPACE_CHARS = "( |&\\w+sp;|\\p{Z})";
	
	/**
	 * Simple pattern for splitting a text into chunks that are candidates for sentences. This is
	 * usually done by splitt at a period (".") folowed by whitespace. Chunks that have been split 
	 * using this pattern may be "glued" together to form a real sentence later, using the 
	 * patterns defined by  sentenceTailGluePattern and sentenceFollowGluePattern.
	 */
	public Pattern sentencePattern;
	
	/**
	 * Pattern for detecting endings of text chunks (candidate sentences) that indicate that the
	 * sentence is not finished and the next chunk should be appended. This pattern usually matches
	 * acronyms, abbreviations and other structures that end with a period (".") without ending a
	 * sentence in the given language.  
	 */
	public Pattern sentenceTailGluePattern;
	
	/**
	 * Pattern for detecting beginnings of text chunks (candidate sentences) that indicate that the
	 * previous chunk does not end a sentence and this chunk should be appended. This pattern usually
	 * matches lowercase letters.  
	 */
	public Pattern sentenceFollowGluePattern;

	/**
	 * A set of Mangler objects that should be used to clean the text before trying to extract
	 * sentences. This is usually set to strip out any parts enclodes in parentacies and 
	 * possibly also quotes.
	 */
	public List<WikiTextAnalyzer.Mangler> sentenceManglers = new ArrayList<WikiTextAnalyzer.Mangler>();

	/**
	 * A pattern matching individual words, for splitting a string into words. This is usually
	 * set to match any sequence of letters but not numbers, whitespace or punctuation.
	 */
	public Pattern wordPattern;

	public void defaults() {
		if (this.wordPattern==null) this.wordPattern = Pattern.compile("\\p{L}+|\\p{Nd}+"); 

		this.sentenceManglers.add( new RegularExpressionMangler("\\s+\\(.*?\\)", "", 0) ); //strip parentacized blocks 
		this.sentenceManglers.add( new RegularExpressionMangler("^([^\\p{L}]*(\\r\\n|\\r|\\n))+[^\\p{L}0-9]*\\s*", "", 0) ); //strip leading cruft (lines without any characters)
		this.sentencePattern = Pattern.compile("(\\r\\n|\\n|\\r)|\\.[\\s\\r\\n]"); //TODO: check what happens if we allow single newlines in sentences! Breaking on single newlines causes truncated definitions. 
		this.sentenceTailGluePattern = Pattern.compile("(^|\\s)([VIX]+|\\d{1,2})$");
		this.sentenceFollowGluePattern = Pattern.compile("^\\p{Ll}");
	}

	public void merge(LanguageConfiguration with) {
		if (with.sentencePattern!=null) sentencePattern = with.sentencePattern;
		if (with.sentenceTailGluePattern!=null) sentenceTailGluePattern = with.sentenceTailGluePattern;
		if (with.sentenceFollowGluePattern!=null) sentenceFollowGluePattern = with.sentenceFollowGluePattern;
		
		sentenceManglers.addAll(with.sentenceManglers);

		if (with.wordPattern!=null) wordPattern = with.wordPattern;
	}	
}
