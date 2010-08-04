package net.psammead.commonist.text;

/** a template could not be applied */
public class TemplateException extends Exception {
	public TemplateException(String message) {
		super(message);
	}

	public TemplateException(String message, Throwable cause) {
		super(message, cause);
	}
}
