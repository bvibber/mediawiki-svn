package net.psammead.mwapi.api;

import net.psammead.mwapi.MediaWikiException;

/** base class for problems with api.php */
public abstract class APIException extends MediaWikiException {
	/** Constructs a new exception with the specified detail message. */
	public APIException(String message) {
		super(message);
	}
	
	/** Constructs a new exception with the specified detail message and cause. */
	public APIException(String message, Throwable cause) {
		super(message, cause);
	}
}
