package net.psammead.util.reflect;

public class ReflectException extends Exception {
	public ReflectException(Exception cause) { super(cause); }
	public ReflectException(String message) { super(message); }
}
