package net.psammead.mwapi.ui;

import net.psammead.mwapi.MediaWikiException;

/** a Page could niot be deleted */ 
public abstract class DeleteException extends MediaWikiException {
	/** Constructs a new exception with the specified detail message. */
	public DeleteException(String message) {
		super(message);
	}
	
	/** Constructs a new exception with the specified detail message and cause. */
	public DeleteException(String message, Throwable cause) {
		super(message, cause);
	}

//	/** Constructs a new exception with the specified cause and a detail 
//		message of (cause==null ? null : cause.toString()) (which 
//		typically contains the class and detail message of cause). */
// 	public DeleteException(Throwable cause) {
//		super(cause);	
//	} 
}
