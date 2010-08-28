package net.psammead.mwapi.ui;

import net.psammead.mwapi.MediaWikiException;

/** a Page's watch state cannot be changed */ 
public final class WatchException extends MediaWikiException {
	/** Constructs a new exception with the specified detail message. */
	public WatchException(String message) {
		super(message);
	}
	
//	/** Constructs a new exception with the specified detail message and cause. */
//	public WatchException(String message, Throwable cause) {
//		super(message, cause);
//	}
//
//	/** Constructs a new exception with the specified cause and a detail 
//		message of (cause==null ? null : cause.toString()) (which 
//		typically contains the class and detail message of cause). */
// 	public WatchException(Throwable cause) {
//		super(cause);	
//	} 
}
