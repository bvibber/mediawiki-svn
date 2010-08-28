package net.psammead.mwapi.ui;

import net.psammead.mwapi.MediaWikiException;

/** a Page could not be stored */ 
public abstract class StoreException extends MediaWikiException {
	/** Constructs a new exception with the specified detail message. */
	public StoreException(String message) {
		super(message);
	}
	
	/** Constructs a new exception with the specified detail message and cause. */
	public StoreException(String message, Throwable cause) {
		super(message, cause);
	}

//	/** Constructs a new exception with the specified cause and a detail 
//		message of (cause==null ? null : cause.toString()) (which 
//		typically contains the class and detail message of cause). */
// 	public StoreException(Throwable cause) {
//		super(cause);	
//	} 
}
