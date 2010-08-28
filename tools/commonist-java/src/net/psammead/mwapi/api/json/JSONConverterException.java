package net.psammead.mwapi.api.json;

public final class JSONConverterException extends Exception {
	public JSONConverterException(String message) {
		super(message);
	}

	public JSONConverterException(Throwable cause) {
		super(cause);
	}
	
	public JSONConverterException(String message, Throwable cause) {
		super(message, cause);
	}
}
