package net.psammead.mwapi.config;

import java.util.Collections;
import java.util.Set;
import java.util.TreeSet;

import net.psammead.util.ToString;

/** information about a Family with its subsites */
public final class Family {
	// identification
	public final String	name;		// "wikibooks";
	public final String	shortcut;	// "b", may be null

	// languages
	public final boolean		multilingual;		// if false, defaultLanguage and supportedLanguages are null
	public final String			defaultLanguage;	// must be contained in supportedLanguages
	public final Set<String>	supportedLanguages;
	
	/** 
	 * shortcut may be null.
	 * if multilinugal is false, then defaultLanguage and supportedLanguages are null
	 */
	public Family(String name, String shortcut, boolean multilingual, String defaultLanguage, Set<String> supportedLanguages) {
		this.name				= name;
		this.shortcut			= shortcut;
		this.multilingual		= multilingual;
		this.defaultLanguage	= defaultLanguage;
		this.supportedLanguages	= supportedLanguages != null 
								? Collections.unmodifiableSet(new TreeSet<String>(supportedLanguages))
								: null;
	}
	
	/** for debugging purposes only */
	@Override
	public String toString() {
		return new ToString(this)
				.append("name",					name)
				.append("shortcut",				shortcut)
				.append("multilingual",			multilingual)
				.append("defaultLanguage",		defaultLanguage)
				.append("supportedLanguages",	supportedLanguages)
				.toString();
	}
}
