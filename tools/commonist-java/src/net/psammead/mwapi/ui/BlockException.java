package net.psammead.mwapi.ui;

import net.psammead.mwapi.MediaWikiException;

/** a block cannot be executed */
public abstract class BlockException extends MediaWikiException {
	/** Constructs a new exception with the specified detail message. */
	public BlockException(String message) {
		super(message);
	}
	
	/** Constructs a new exception with the specified detail message and cause. */
	public BlockException(String message, Throwable cause) {
		super(message, cause);
	}

//	/** Constructs a new exception with the specified cause and a detail 
//		message of (cause==null ? null : cause.toString()) (which 
//		typically contains the class and detail message of cause). */
// 	public BlockException(Throwable cause) {
//		super(cause);	
//	} 
}
