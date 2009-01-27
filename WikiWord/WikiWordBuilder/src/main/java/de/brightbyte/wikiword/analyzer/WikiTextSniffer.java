package de.brightbyte.wikiword.analyzer;

import java.util.regex.Matcher;
import java.util.regex.Pattern;

import de.brightbyte.util.StringUtils;

public class WikiTextSniffer {
	
	//TODO: make smellsLikeWiki optional/configurable
	protected final Matcher smellsLikeWiki = Pattern.compile("\\{\\{|\\}\\}|\\[\\[|\\]\\]|<[a-zA-Z]+.*>").matcher("");
	
	/**
	 * Check if text "smells" like wikitext. Sanity check to assist in finding problems
	 * with the analysis methods.
	 * 
	 * @param text the text to check 
	 */
	public String sniffWikiTextLocation(CharSequence text) {
		if (smellsLikeWiki==null) return null; 
		
		smellsLikeWiki.reset(text);
		if (!smellsLikeWiki.find()) return null;
		
		return StringUtils.describeLocation(text.toString(), smellsLikeWiki.start(), smellsLikeWiki.end());
	}
	
	public boolean sniffWikiText(CharSequence text) {
		if (smellsLikeWiki==null) return false; 
		
		smellsLikeWiki.reset(text);
		if (!smellsLikeWiki.find()) return false;
		
		return true;
	}
	

}
