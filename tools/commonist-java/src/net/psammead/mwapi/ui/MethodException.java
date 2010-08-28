package net.psammead.mwapi.ui;

import net.psammead.mwapi.MediaWikiException;

/** this Exception is thrown, when the underlying HttpClient throws an Exception */
public final class MethodException extends MediaWikiException {
//	/** Constructs a new exception with the specified detail message. */
//	public MethodException(String message) {
//		super(message);
//	}
	
	/** Constructs a new exception with the specified detail message and cause. */
	public MethodException(String message, Throwable cause) {
		super(message, cause);
	}

//	/** Constructs a new exception with the specified cause and a detail 
//		message of (cause==null ? null : cause.toString()) (which 
//		typically contains the class and detail message of cause). */
// 	public MethodException(Throwable cause) {
//		super(cause);	
//	} 
}
