package net.psammead.mwapi.ui.action;

import java.util.regex.Pattern;

import net.psammead.mwapi.MediaWiki;
import net.psammead.mwapi.NameSpace;
import net.psammead.mwapi.connection.Connection;

public abstract class UiActionBase extends ActionBase {
	protected UiActionBase(MediaWiki mediaWiki, Connection connection) {
		super(mediaWiki, connection);
	}
	
	/** returns whether a message exists in the site configuration */
	protected boolean messageAvailable(String messageName) {
		return site.message(messageName) != null;
	}
	
	/** returns a regexp for a named message, or null when not available */
	protected Pattern messageRegexp(String messageName) {
		final String  raw = site.message(messageName);
		if (raw == null)    return null;
		return Pattern.compile(messageToRegexp(raw));
	}
	
    /** compile a mediawiki message from Allmessages.php into a regexp */
    private String messageToRegexp(String message) {
		/*
		// TODO @see rcapi/src/net/psammead/rcapi/action/MessageParser.java
		String	messageRE	= messageMarkup
				.replaceAll(												// move PLURAL out of the way
							"\\{\\{"	+
							"PLURAL:"	+ 
							"([^|]*)"	+ 
							"[|]"		+
							"([^|]*)"	+ 
							"[|]"		+
							"([^|]*)"	+ 
							"\\}\\}",
							"\u0001$2\u0002$3\u0003")
				.replaceAll("\\$\\d", "\u0000")								// save $n
				.replaceAll("([\\\\\\[\\](){}|^$.?+*\r\n\t])", "\\\\$1")	// escape regexp metacharacters
				.replaceAll("\\\\\\{\\\\\\{[^}]+\\\\\\}\\\\\\}", ".+?")		// remove templates
				.replaceAll("\u0000", "(.*?)")								// use $n as group
				.replaceAll(												// splice in PLURAL alternatives
							"\u0001"	+
							"(.*?)"		+
							"\u0002"	+
							"(.*?)"		+
							"\u0003",
							"(?:$1|$2)");
		*/
        return  // intro
                "(?s).*?" + 
                // remove spaces from both ends
                message.trim()
                // ULGY: remove everything after first LF -- this could solve the problem
                // that confirmrecreate looped endlessly when matching for a session loss
                .replaceAll("\n.*", "")
                // convert HTML tags, $1-like, {{}}-like, [[]] like, meta characters, CR, LF and TAB to .*?
                .replaceAll("(<.*?>|\\$\\d+|\\[\\[.*?\\]\\]|\\{\\{.*?\\}\\}|\\r|\\n|\\t|\\.|\\*|\\?|\\^|\\$|\\(|\\)|\\[|\\]|\\\\)+", ".*?")
                // convert multiple .?* to a single one
                .replaceAll("\\.\\*\\?(\\s*\\.\\*\\?)+", ".*?")
                // remove .*? from start and end
                .replaceAll("^\\.\\*\\?", "").replaceAll("\\.\\*\\?$", "") +
                // outro
                ".*";
                /*
                // replace $0-$n with .*? groups
                .replaceAll("\\$\\d+", "(.*?)");
                */
    }
	
    /** returns the localized title of a SpecialPage */
	protected String specialPage(String specialPage) {
		final String	localized	= site.specialPageNames.get(specialPage);
		// TODO: use a better Exception
		if (localized == null)	throw new RuntimeException("specialPage not localized: " + specialPage + " in site: " + site);	
		return site.nameSpace(NameSpace.SPECIAL).addTo(localized);
	}
}
