package net.psammead.mwapi.ui;

import net.psammead.mwapi.MediaWikiException;

/** a feature is not supported on this wiki */
public final class UnsupportedFeatureException extends MediaWikiException {
	/** Constructs a new exception with the specified detail message. */
	public UnsupportedFeatureException(String message) {
		super(message);
	}
	
	/** Constructs a new exception with the specified detail message and cause. */
	public UnsupportedFeatureException(String message, Throwable cause) {
		super(message, cause);
	}

//	/** Constructs a new exception with the specified cause and a detail 
//		message of (cause==null ? null : cause.toString()) (which 
//		typically contains the class and detail message of cause). */
// 	public UnsupportedFeatureException(Throwable cause) {
//		super(cause);	
//	} 
}
