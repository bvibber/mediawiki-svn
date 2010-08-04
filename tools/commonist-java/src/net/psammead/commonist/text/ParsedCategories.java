package net.psammead.commonist.text;

/** a set of categories */
public final class ParsedCategories {
	private static final String	SEPARATOR_RE	= "\\|";
	private static final String	LINK_START		= "[[";
	private static final String	LINK_END		= "]]";
	private static final String	CATEGORY		= "Category";
	private static final String	NS_SEPARATOR	= ":";
	
	public final String wikiText;
	
	/** 
	 * parses a '|' separated String and compiles it to a categories list
	 * returns the original String when it contains [[ and ]] to allow [[Category:Something]]
	 */
	public ParsedCategories(String source) {
		wikiText	= maybeLink(source)
					? source
					: compile(source);
	}
	
	/** parses the decsriptor and compiles it into wikitext */
	private String compile(String source) {
		final String[]	split	= source.split(SEPARATOR_RE);
		String	out		= "";
		for (int i=0; i<split.length; i++) {
			final String	name	= split[i].trim();
			if (name.length() == 0)	continue;
			out	+= LINK_START + CATEGORY + NS_SEPARATOR + name + LINK_END;
		}
		return out;
	}
	
	/** returns whether s contains link markers */
	private boolean maybeLink(String s) {
		return s.indexOf(LINK_START) != -1 
			|| s.indexOf(LINK_END) != -1;
	}
}
