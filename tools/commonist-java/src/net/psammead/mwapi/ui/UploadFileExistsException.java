package net.psammead.mwapi.ui;

/** a File cannot be uploaded, because it already exists on the server */ 
public final class UploadFileExistsException extends UploadException {
	/** Constructs a new exception with the specified detail message. */
	public UploadFileExistsException(String message) {
		super(message);
	}
//	
//	/** Constructs a new exception with the specified detail message and cause. */
//	public UploadFileExistsException(String message, Throwable cause) {
//		super(message, cause);
//	}
//
//	/** Constructs a new exception with the specified cause and a detail 
//		message of (cause==null ? null : cause.toString()) (which 
//		typically contains the class and detail message of cause). */
// 	public UploadFileExistsException(Throwable cause) {
//		super(cause);	
//	} 
}
