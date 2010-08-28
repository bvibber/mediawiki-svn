package net.psammead.mwapi.ui;

import net.psammead.mwapi.MediaWikiException;

/** a Page can not be edited */ 
public final class EditException extends MediaWikiException {
	/** Constructs a new exception with the specified detail message. */
	public EditException(String message) {
		super(message);
	}
	
//	/** Constructs a new exception with the specified detail message and cause. */
//	public EditException(String message, Throwable cause) {
//		super(message, cause);
//	}

//	/** Constructs a new exception with the specified cause and a detail 
//		message of (cause==null ? null : cause.toString()) (which 
//		typically contains the class and detail message of cause). */
// 	public EditException(Throwable cause) {
//		super(cause);	
//	} 
}
