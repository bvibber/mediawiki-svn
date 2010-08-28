package net.psammead.mwapi.api;

import net.psammead.mwapi.api.data.Error_;

/** api.php returned an error */
public final class APIErrorException extends APIException {
	public final Error_	error;

	/** Constructs a new exception with the specified detail message. */
	public APIErrorException(Error_ error) {
		super(error.toString());
		this.error	= error;
	}
}
