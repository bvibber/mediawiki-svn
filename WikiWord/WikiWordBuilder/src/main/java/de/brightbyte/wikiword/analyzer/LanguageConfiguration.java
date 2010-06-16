package de.brightbyte.wikiword.analyzer;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Collection;
import java.util.HashSet;
import java.util.List;
import java.util.Set;
import java.util.regex.Pattern;

import de.brightbyte.data.Pair;
import de.brightbyte.wikiword.analyzer.mangler.Mangler;
import de.brightbyte.wikiword.analyzer.mangler.RegularExpressionMangler;


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
	public List<Mangler> sentenceManglers = new ArrayList<Mangler>();

	/**
	 * A pattern matching individual words, for splitting a string into words. This is usually
	 * set to match any sequence of letters but not numbers, whitespace or punctuation, except
	 * those that may accurs as part of a word, such as an apostrophy or hyphen.
	 */
	public Pattern wordPattern;

	/**
	 * A pattern matching individual words parts, for splitting a words into components. This is usually
	 * set to match any sequence of letters but not numbers, whitespace or punctuation, nor
	 * those that may accurs as part of a word, such as an apostrophy or hyphen. 
	 */
	public Pattern wordPartPattern;

	protected String languageName;

	/**
	 * List of stopwords, that is, words that are too frequent to be useful for searches.
	 */
	public Set<String> stopwords;

	/**
	 * Symbols that break a phrase, like most punctuation would
	 */
	public Pattern phraseBreakerPattern;

	/**
	 * pairs of matching parantecies 
	 */
	public Collection<Pair<String, String>> parentacies;
	
	public LanguageConfiguration() {
		this(null);
	}
	
	public LanguageConfiguration(String languageName) {
		if (languageName==null) {
			languageName = AnalyzerUtils.getClassNameSuffix(getClass());
		}
 
		this.languageName = languageName;
	}

	public String getLanguageName() {
		return languageName;
	}
	
	public void defaults() throws IOException {
		if (this.wordPattern==null) this.wordPattern = Pattern.compile("[\\p{L}']+(?:[\\p{Pc}\\p{Pd}][\\p{L}']+)*|\\p{Nd}+(?:.\\p{Nd}+)?"); 
		if (this.wordPartPattern==null) this.wordPartPattern = Pattern.compile("[\\p{L}]+|\\p{Nd}+"); 

		this.sentenceManglers.add( new RegularExpressionMangler("\\s+\\(.*?\\)", "", 0) ); //strip parentacized blocks 
		this.sentenceManglers.add( new RegularExpressionMangler("^([^\\p{L}]*(\\r\\n|\\r|\\n))+[^\\p{L}0-9]*\\s*", "", 0) ); //strip leading cruft (lines without any characters)
		this.sentencePattern = Pattern.compile("(\\r\\n|\\n|\\r)|\\.[\\s\\r\\n]"); //TODO: check what happens if we allow single newlines in sentences! Breaking on single newlines causes truncated definitions. 
		this.sentenceTailGluePattern = Pattern.compile("(^|\\s)([VIX]+|\\d{1,2})$");
		this.sentenceFollowGluePattern = Pattern.compile("^\\p{Ll}");

		this.stopwords = new HashSet<String>();
		List<String> stop = AuxilliaryWikiProperties.loadList("Stopwords", languageName);
		if (stop!=null) this.stopwords.addAll(stop);
		
		this.phraseBreakerPattern = Pattern.compile("[,;:\".!?]\\s*");
		this.parentacies = new ArrayList<Pair<String, String>>();
		this.parentacies.add( new Pair<String, String>("(", ")") );
		this.parentacies.add( new Pair<String, String>("[", "]") );
		this.parentacies.add( new Pair<String, String>("{", "}") );
		this.parentacies.add( new Pair<String, String>("\"", "\"") );
	}

	public void merge(LanguageConfiguration with) {
		if (with.sentencePattern!=null) sentencePattern = with.sentencePattern;
		if (with.sentenceTailGluePattern!=null) sentenceTailGluePattern = with.sentenceTailGluePattern;
		if (with.sentenceFollowGluePattern!=null) sentenceFollowGluePattern = with.sentenceFollowGluePattern;
		
		if (with.sentenceManglers!=null) sentenceManglers.addAll(with.sentenceManglers);

		if (with.wordPattern!=null) wordPattern = with.wordPattern;
		if (with.wordPartPattern!=null) wordPartPattern = with.wordPartPattern;
		if (with.phraseBreakerPattern!=null) phraseBreakerPattern = with.phraseBreakerPattern;
		
		if (with.stopwords!=null) stopwords.addAll(with.stopwords);
		if (with.parentacies!=null) parentacies.addAll(with.parentacies);
	}	
}
