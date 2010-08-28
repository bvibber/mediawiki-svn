package net.psammead.mwapi.ui;

/** cannot recreate a deleted Page */
public final class StoreRecreateException extends StoreException {
	/** Constructs a new exception with the specified detail message. */
	public StoreRecreateException(String message) {
		super(message);
	}
	
//	/** Constructs a new exception with the specified detail message and cause. */
//	public StoreRecreateException(String message, Throwable cause) {
//		super(message, cause);
//	}
//
//	/** Constructs a new exception with the specified cause and a detail 
//		message of (cause==null ? null : cause.toString()) (which 
//		typically contains the class and detail message of cause). */
// 	public StoreRecreateException(Throwable cause) {
//		super(cause);	
//	} 
}
