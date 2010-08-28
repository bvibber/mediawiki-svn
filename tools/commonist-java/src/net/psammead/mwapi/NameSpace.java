package net.psammead.mwapi;

import net.psammead.mwapi.connection.TitleUtil;
import net.psammead.util.ToString;

/** a namespace a page can be in */
public final class NameSpace {
	public static final int	MEDIA			= -2;	
	public static final int	SPECIAL			= -1;
	public static final int	ARTICLE			= 0;	
	public static final int	ARTICLE_TALK	= 1;
	public static final int	USER			= 2;	
	public static final int	USER_TALK		= 3;
	public static final int	PROJECT			= 4;	
	public static final int	PROJECT_TALK	= 5;
	public static final int	FILE			= 6;	
	public static final int	FILE_TALK		= 7;
	public static final int	MEDIAWIKI		= 8;	
	public static final int	MEDIAWIKI_TALK	= 9;
	public static final int	TEMPLATE		= 10;	
	public static final int TEMPLATE_TALK	= 11;
	public static final int	HELP			= 12;	
	public static final int	HELP_TALK		= 13;
	public static final int	CATEGORY		= 14;	
	public static final int	CATEGORY_TALK	= 15;
	public static final int	FREE			= 100;
	
	private static final String[]	CANONICAL =  new String[] {
		"Media",	"Special",
		"",			"Talk",
		"User",		"User talk",
		"Project",	"Project talk",
		"File",		"File talk",
		"MediaWiki","MediaWiki talk",
		"Template",	"Template talk",
		"Help",		"Help talk",
		"Category",	"Categoy talk"
	};
	
	/** returns the canonical english name of the indexed NameSpace */
	public static String canonical(int index) {
		return TitleUtil.underscores(
				CANONICAL[index-MEDIA]);
	}

	public final int	index;
	public final String	name;
	
	private	NameSpace	discussionTwin;
	
	/** a namespace a page can be in */
	public NameSpace(int index, String name) {
		this.index	= index;
		// normalize the name
		this.name	= TitleUtil.underscores(name);
	}
			
	/** returns true for the article namespace */
	public boolean isArticle() { 
		return index == ARTICLE; 
	}
	
	/** returns true when this is Special or Media where no discussion is possible  */
	public boolean isSpecial()	{
		return index < ARTICLE; 
	}

	/** returns true when the discussion is possbile */
	public boolean isRegular()	{ 
		return index >= ARTICLE 
			&& (index % 2) == 0; 
	}
	
	/** returns true when this is a discussion page */
	public boolean isDiscussion() { 
		return index >= ARTICLE 
			&& (index % 2) == 1; 
	}
	
	/** returns whether an article title is in this namespace */
	public boolean matches(String title) {
		title	= TitleUtil.underscores(title);
		return title.startsWith(name + ":");
			//### would not work with removeFrom!
			//|| title.startsWith(canonical(index) + ":");
	}
	
	/** add this namespace in front of the title */
	public String addTo(String title) {
		if (isArticle())	return title;
		return TitleUtil.spaces(name) + ":" + title;
	}
	
	/** remove this namespace from the front of a title */
	public String removeFrom(String title) {
		if (isArticle())		return title;
		if (!matches(title))	throw new IllegalArgumentException("cannot remove namespace " + name + " from title " + title);
		return title.substring(name.length()+1);
	}
	
	/** returns the parallel discussion namespace or null for Media and Special */
	public NameSpace toggleDiscussion() {
		return discussionTwin;
	}
	
	/** must not be called from code using this API */
	public void setDiscussionTwin(NameSpace discussionTwin) {
		this.discussionTwin	= discussionTwin;
	}
	
	/** for debugging purposes only */
	@Override
	public String toString() {
		return new ToString(this)
				.append("index",	index)
				.append("name",		name)
				.toString();
	}
}
