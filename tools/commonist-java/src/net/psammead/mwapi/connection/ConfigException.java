package net.psammead.mwapi.connection;

import net.psammead.mwapi.*;

/** something wend wrong with a Config file */
public final class ConfigException extends MediaWikiException {
	/** Constructs a new exception with the specified detail message. */
	public ConfigException(String message) {
		super(message);
	}
	
	/** Constructs a new exception with the specified detail message and cause. */
	public ConfigException(String message, Throwable cause) {
		super(message, cause);
	}
//	/** Constructs a new exception with the specified cause and a detail 
//		message of (cause==null ? null : cause.toString()) (which 
//		typically contains the class and detail message of cause). */
// 	public ConfigException(Throwable cause) {
//		super(cause);	
//	} 
}
