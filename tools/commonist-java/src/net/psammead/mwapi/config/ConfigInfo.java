package net.psammead.mwapi.config;

import java.util.regex.Pattern;

public final class ConfigInfo {
	/** fully static utility class, shall not be instantiated */
	private ConfigInfo() {}
	
	//-------------------------------------------------------------------------
	//## file

	public static final String	FILE_ENCODING	= "UTF-8";

	public static String familyListFileName() {
		return "Families.list";
	}

	public static String familyFileName(String familyName) {
		return familyName + ".family";
	}

	// siteLanguage is null for unilingual families
	// @see TitleUtil 
	public static String siteFileName(String familyName, String siteLanguage) {
		if (siteLanguage != null)	return familyName + "_" + siteLanguage + ".site";
		else						return familyName + ".site";
	}
	
	//-------------------------------------------------------------------------
	//## language
	
	public static final String	LINE_COMMENT_RE	= "#.*";
	
	public static final String	EOL				= "\n";
	public static final String	EOL_RE			= "\n";
	
	private static final String QUOTED			= "'(?:\\\\'|[^'])*'";
	
	// TODO: use QUOTED
	public static final Pattern	LINE_RE			= Pattern.compile("([a-zA-Z]+)\\s+(.*)");
	public static final Pattern	NAMESPACE_RE	= Pattern.compile("(-?[0-9]+)\\s+(.*)");
	public static final Pattern	MESSAGE_RE		= Pattern.compile("([a-zA-Z0-9_.-]+)\\s+(.*)");
	public static final Pattern	SPECIALPAGE_RE	= Pattern.compile("(" + QUOTED + ")\\s+(" + QUOTED + ")");

	//-------------------------------------------------------------------------
	//## content
	
	public static final String[]	SPECIAL_PAGES	= new String[] { "Upload",
		"Filepath", "Movepage", "Blockip", "Userlogin", "Userlogout" 
	};
	
	public static final String[]	MESSAGE_NAMES	= new String[] { 
		// login
		"loginsuccess",
		"nosuchuser",
		"wrongpassword",
		// logout
		"logouttext",
		// store
		"spamprotectionmatch",
		"confirmrecreate",
		// upload
		"successfulupload", 
		"uploadnologintext",
		"badfilename",	
		"badfiletype",	// TODO: ist am veralten, siehe filetype-*
		"fileexists",
		"largefile",	// TODO: veraltet
		"large-file",
		"largefileserver",
		"filewasdeleted",
		"uploadscripted",
		"uploadvirus",
		"uploadcorrupt",
		"uploaddisabled",
		"filetype-badmime",
		"filetype-badtype",
		"filetype-missing",
		// watch
		"addedwatch",
		"removedwatch",
		// move
		"talkexists",
		"talkpagemoved",
		"talkpagenotmoved",
		"delete_and_move_text",
		// delete
		"cannotdelete"
		
	};
}
