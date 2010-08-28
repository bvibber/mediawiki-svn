package net.psammead.mwapi.ui;

import net.psammead.mwapi.MediaWikiException;

/** a File could not be uploaded */ 
public abstract class UploadException extends MediaWikiException {
	/** Constructs a new exception with the specified detail message. */
	public UploadException(String message) {
		super(message);
	}
	
	/** Constructs a new exception with the specified detail message and cause. */
	public UploadException(String message, Throwable cause) {
		super(message, cause);
	}

//	/** Constructs a new exception with the specified cause and a detail 
//		message of (cause==null ? null : cause.toString()) (which 
//		typically contains the class and detail message of cause). */
// 	public UploadException(Throwable cause) {
//		super(cause);	
//	} 
}
