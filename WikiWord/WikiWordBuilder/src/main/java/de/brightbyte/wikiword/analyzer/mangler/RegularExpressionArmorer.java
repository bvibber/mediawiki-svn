package de.brightbyte.wikiword.analyzer.mangler;

import java.util.regex.Matcher;
import java.util.regex.Pattern;



/**
 * An implementation of the Armorer interface based on regular expressions.
 */
public class RegularExpressionArmorer implements Armorer {
	protected String name;
	protected Matcher matcher;
	protected int replacementGroup;
	
	public RegularExpressionArmorer(String name, String pattern, int replacementGroup, int flags) {
		this(name, Pattern.compile(pattern, flags), replacementGroup);
	}
	
	public RegularExpressionArmorer(String name, Pattern pattern, int replacementGroup) {
		super();
		this.matcher = pattern.matcher("");
		this.name = name;
		this.replacementGroup = replacementGroup;
	}

	public CharSequence armor(CharSequence text, TextArmor armor) {
		matcher.reset(text);
		
		int i = armor.size();
		StringBuffer s = new StringBuffer(text.length());
		while(matcher.find()) {
			i++;
			if (replacementGroup>=0) {
				String r = matcher.group(replacementGroup);
				if (r==null) r = ""; //NOTE: don't skip armoring, construct might escape some syntax 
					
				String marker = Armorer.ARMOR_MARKER_CHAR+"+@@@+"+Armorer.ARMOR_MARKER_CHAR+name+"#"+i+Armorer.ARMOR_MARKER_CHAR+"-@@@-"+Armorer.ARMOR_MARKER_CHAR;
				matcher.appendReplacement(s, marker);
			
				armor.put(marker, r);
			}
			else {
				matcher.appendReplacement(s, "");
			}
		}
		
		matcher.appendTail(s);
		return s;
	}
}