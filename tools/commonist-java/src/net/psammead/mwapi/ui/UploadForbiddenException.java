package net.psammead.mwapi.ui;

/** a File cannot be uploaded */ 
public final class UploadForbiddenException extends UploadException {
	/** Constructs a new exception with the specified detail message. */
	public UploadForbiddenException(String message) {
		super(message);
	}
	
//	/** Constructs a new exception with the specified detail message and cause. */
//	public UploadForbiddenException(String message, Throwable cause) {
//		super(message, cause);
//	}
//
//	/** Constructs a new exception with the specified cause and a detail 
//		message of (cause==null ? null : cause.toString()) (which 
//		typically contains the class and detail message of cause). */
// 	public UploadForbiddenException(Throwable cause) {
//		super(cause);	
//	} 
}
