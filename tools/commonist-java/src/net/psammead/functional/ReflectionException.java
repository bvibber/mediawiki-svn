package net.psammead.functional;

public class ReflectionException extends RuntimeException {
	public ReflectionException(Exception cause) { super(cause); }
	public ReflectionException(String message)  { super(message); }
	public ReflectionException(String message, Exception cause) { super(message, cause); }
}