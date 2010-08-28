package net.psammead.mwapi.ui;

/** a File cannot be uploaded, because it had existed on the server and has been deleted */ 
public final class UploadFileWasDeletedException extends UploadException {
	/** Constructs a new exception with the specified detail message. */
	public UploadFileWasDeletedException(String message) {
		super(message);
	}
	
//	/** Constructs a new exception with the specified detail message and cause. */
//	public UploadFileWasDeletedException(String message, Throwable cause) {
//		super(message, cause);
//	}
//
//	/** Constructs a new exception with the specified cause and a detail 
//		message of (cause==null ? null : cause.toString()) (which 
//		typically contains the class and detail message of cause). */
// 	public UploadFileWasDeletedException(Throwable cause) {
//		super(cause);	
//	} 
}
