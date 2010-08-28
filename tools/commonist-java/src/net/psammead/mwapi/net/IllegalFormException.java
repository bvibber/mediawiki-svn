package net.psammead.mwapi.net;


/** a Page can not be edited */ 
public final class IllegalFormException extends Exception {
	/** Constructs a new exception with the specified detail message. */
	public IllegalFormException(String message) {
		super(message);
	}
	
	/** Constructs a new exception with the specified detail message and cause. */
	public IllegalFormException(String message, Throwable cause) {
		super(message, cause);
	}

	/** Constructs a new exception with the specified cause and a detail 
		message of (cause==null ? null : cause.toString()) (which 
		typically contains the class and detail message of cause). */
 	public IllegalFormException(Throwable cause) {
		super(cause);	
	}
}
