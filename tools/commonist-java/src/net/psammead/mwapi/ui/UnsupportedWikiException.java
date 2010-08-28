package net.psammead.mwapi.ui;

import net.psammead.mwapi.MediaWikiException;

/** this wiki is not supported */ 
public final class UnsupportedWikiException extends MediaWikiException {
	/** Constructs a new exception with the specified detail message. */
	public UnsupportedWikiException(String message) {
		super(message);
	}
	
//	/** Constructs a new exception with the specified detail message and cause. */
//	public UnsupportedWikiException(String message, Throwable cause) {
//		super(message, cause);
//	}
//
//	/** Constructs a new exception with the specified cause and a detail 
//		message of (cause==null ? null : cause.toString()) (which 
//		typically contains the class and detail message of cause). */
// 	public UnsupportedWikiException(Throwable cause) {
//		super(cause);	
//	} 
}
