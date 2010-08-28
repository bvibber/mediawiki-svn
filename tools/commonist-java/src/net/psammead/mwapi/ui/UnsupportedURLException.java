package net.psammead.mwapi.ui;

import net.psammead.mwapi.MediaWikiException;

/** this URL does not belong to a supported Site */
public final class UnsupportedURLException extends MediaWikiException {
	/** Constructs a new exception with the specified detail message. */
	public UnsupportedURLException(String message) {
		super(message);
	}
	
	/** Constructs a new exception with the specified detail message and cause. */
	public UnsupportedURLException(String message, Throwable cause) {
		super(message, cause);
	}

//	/** Constructs a new exception with the specified cause and a detail 
//		message of (cause==null ? null : cause.toString()) (which 
//		typically contains the class and detail message of cause). */
// 	public UnsupportedURLException(Throwable cause) {
//		super(cause);	
//	} 
}
