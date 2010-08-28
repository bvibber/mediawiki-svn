package net.psammead.mwapi.scrapper;

import java.util.Collections;
import java.util.Map;

public final class SiteInfo {
	public final String	sitename;
	public final String	base;
	public final String	generator;
	public final String	titleCase;	// was "case", but that's a keyword in java
	public final Map<String,String>	specialPages;
	public final Map<Integer,String>	nameSpaces;
	
	SiteInfo(String sitename, String base, String generator, String titleCase,
			Map<String,String>	specialPages,
			Map<Integer,String>	nameSpaces) {
		this.sitename		= sitename;
		this.base			= base;
		this.generator		= generator;
		this.titleCase		= titleCase;
		this.nameSpaces		= Collections.unmodifiableMap(nameSpaces);
		this.specialPages	= Collections.unmodifiableMap(specialPages);
	}
}