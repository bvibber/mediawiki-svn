package net.psammead.mwapi.config;

import net.psammead.mwapi.MediaWikiException;


public final class ConfigSyntaxException extends MediaWikiException {
	public ConfigSyntaxException(String message, int lineNumber, String lineText) { 
		super(lineNumber + ": " + message + ": " + lineText); 
	}
	public ConfigSyntaxException(String message) { 
		super(message); 
	}
}