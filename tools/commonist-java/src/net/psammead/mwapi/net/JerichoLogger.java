package net.psammead.mwapi.net;

/** adapter class to fit jericho's logger to my own logger */
public final class JerichoLogger implements net.htmlparser.jericho.Logger {
	private final net.psammead.util.Logger	delegate;

	public JerichoLogger(net.psammead.util.Logger delegate) {
		this.delegate	= delegate;
	}

	public void debug(String message) {
		delegate.debug(message);
	}

	public void info(String message) {
		delegate.info(message);
	}
	
	public void warn(String message) {
		delegate.warn(message);
	}
	
	public void error(String message) {
		delegate.error(message);
	}

	public boolean isDebugEnabled() {
		return true;
	}
	
	public boolean isInfoEnabled() {
		return true;
	}

	public boolean isWarnEnabled() {
		return true;
	}

	public boolean isErrorEnabled() {
		return true;
	}
}
