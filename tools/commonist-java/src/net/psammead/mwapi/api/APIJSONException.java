package net.psammead.mwapi.api;

/** api.php returned unexpected JSON code */
public final class APIJSONException extends APIException {
	/** Constructs a new exception with the specified detail message. */
	public APIJSONException(String message) {
		super(message);
	}
	
	/** Constructs a new exception with the specified detail message and cause. */
	public APIJSONException(String message, Throwable cause) {
		super(message, cause);
	}

//	/** Constructs a new exception with the specified cause and a detail 
//		message of (cause==null ? null : cause.toString()) (which 
//		typically contains the class and detail message of cause). */
// 	public QueryErrorException(Throwable cause) {
//		super(cause);	
//	} 
}
