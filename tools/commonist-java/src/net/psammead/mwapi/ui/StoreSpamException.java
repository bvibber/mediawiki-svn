package net.psammead.mwapi.ui;

/** cannot store a Page containing spam */
public final class StoreSpamException extends StoreException {
	/** Constructs a new exception with the specified detail message. */
	public StoreSpamException(String message) {
		super(message);
	}
	
//	/** Constructs a new exception with the specified detail message and cause. */
//	public StoreSpamException(String message, Throwable cause) {
//		super(message, cause);
//	}
//
//	/** Constructs a new exception with the specified cause and a detail 
//		message of (cause==null ? null : cause.toString()) (which 
//		typically contains the class and detail message of cause). */
// 	public StoreSpamException(Throwable cause) {
//		super(cause);	
//	} 
}
