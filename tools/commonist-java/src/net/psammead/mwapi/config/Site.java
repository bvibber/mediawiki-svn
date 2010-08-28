package net.psammead.mwapi.config;

import java.util.Collections;
import java.util.HashMap;
import java.util.Map;
import java.util.TreeMap;

import net.psammead.mwapi.Location;
import net.psammead.mwapi.NameSpace;
import net.psammead.mwapi.connection.TitleUtil;

/** this is used by the Actions */
public final class Site {
	public final String		family;			// "wikibooks"
	public final String		language;		// "de" or null for unilingual families
	
	public final String		protocol;		// "http://"
	public final String		hostName;		// "de.wikibooks.org"
	public final String		rawPath;		// "/w/index.php"
	public final String		prettyPath;		// "/wiki/"
	public final String		apiPath;		// "/w/api.php"
	
	public final String		charSet;		// "UTF-8";
	public final String		titleCase;		// "first-letter"
	public final String		uselang;		// "nds";
	
	public final Map<Integer, String>	nameSpaceNames;
	public final Map<String, String>	specialPageNames;
	public final Map<String, String>	messages;
	
	// derived
	
	/** family:language or family if this is an unilingual site */
	public final String wiki;
	
	private  Map<Integer,NameSpace>	nameSpaces;
	
	public Site(String family, String language, 
			String protocol, String hostName, 
			String actionPath, String readPath, String apiPath,
			String charSet, String titleCase, String uselang, 
			Map<Integer,String>	nameSpaceNames, 
			Map<String,String>	specialPageNames,
			Map<String,String>	messages) {
		this.family			= family;
		this.language		= language;
		this.protocol		= protocol;
		this.hostName		= hostName;
		this.rawPath		= actionPath;
		this.prettyPath		= readPath;
		this.apiPath		= apiPath;
		
		this.charSet		= charSet.toUpperCase();
		this.titleCase		= titleCase;
		this.uselang		= uselang;
		
		this.nameSpaceNames		= Collections.unmodifiableMap(new TreeMap<Integer,String>(nameSpaceNames));
		this.specialPageNames	= Collections.unmodifiableMap(new TreeMap<String,String>(specialPageNames));
		this.messages			= Collections.unmodifiableMap(new TreeMap<String,String>(messages));
		
		wiki	= TitleUtil.buildWiki(family, language);
		
		// create NameSpace objects
		nameSpaces	= new HashMap<Integer,NameSpace>();
		for (Integer key : nameSpaceNames.keySet()) {
			final int		index	= key.intValue();
			final String	name	= nameSpaceNames.get(key);
			final NameSpace	nameSpace	= new NameSpace(index, name);
			nameSpaces.put(key, nameSpace);
		}
		// add DiscussionTwins to NameSpace objects
		for (Integer key : nameSpaces.keySet()) {
			final int		index		= key.intValue();
			final NameSpace	nameSpace	= nameSpaces.get(key);
			if (nameSpace.isSpecial()) {
				nameSpace.setDiscussionTwin(null);
			}
			else {
				try {
					nameSpace.setDiscussionTwin(nameSpace(index ^ 1));
				}
				catch (IllegalArgumentException e) {
					// HACK: Forum on kamelopedia (102) does not have a diskusison twin
					nameSpace.setDiscussionTwin(null);
				}
			}
		}
	}
	
	// TODO: move this into the LocationManager?
	
	/** creates a Location withing this Site */
	public Location location(String title) {
		return new Location(wiki, title);
	}
	
	/** creates a Location withing this Site */
	public Location location(int nameSpace, String title) {
		return location(nameSpace(nameSpace).addTo(title));
	}
	
	// TODO: move this into the MessageManager?
	
	/** returns the named Message or null if it is not defined */
	public String message(String key) {
		return messages.get(key);
	}
	
	// TODO: move this into the NameSpaceManager
	
	/** returns a NameSpace object for a NS_ index */
	public NameSpace nameSpace(int index) {
		final Integer 	key			= new Integer(index);
		final NameSpace	nameSpace	= nameSpaces.get(key); 
		if (nameSpace == null)	throw new IllegalArgumentException("nameSpace unknown: " + index);
		return nameSpace;
	}
	
	/** find out the NameSpace used for a Title */
	public NameSpace nameSpaceForTitle(String title) {
		for (Integer key : nameSpaces.keySet()) {
			final int			index		= key.intValue();
			final NameSpace	nameSpace	= nameSpaces.get(key);
			// skip article namespace because it would match everything
			if (index != NameSpace.ARTICLE 
			&& nameSpace.matches(title))	return nameSpace;
		}
		// return the article namespace by default
		return nameSpace(NameSpace.ARTICLE);	
	}
	
	/** for debugging purposes only */
	@Override
	public String toString() {
		return language != null ? family + ":" + language : family;
	}
}
