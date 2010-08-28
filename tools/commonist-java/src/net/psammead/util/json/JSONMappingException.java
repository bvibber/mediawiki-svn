package net.psammead.util.json;

/** a value could not be serialized */
public class JSONMappingException extends RuntimeException {
	public JSONMappingException(String message) { super(message); }
	public JSONMappingException(String message, Throwable cause) { super(message, cause); }
}