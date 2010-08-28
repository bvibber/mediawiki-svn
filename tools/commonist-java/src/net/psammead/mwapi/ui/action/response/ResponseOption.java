package net.psammead.mwapi.ui.action.response;

import net.psammead.mwapi.*;

/** a ResponseHandler using child ResponseHandler, but only when a ResponsePattern matches */
public class ResponseOption implements ResponseHandler {
	private final ResponsePattern	pattern;
	private final ResponseHandler	handler;
	
	public ResponseOption(ResponsePattern pattern, ResponseHandler	handler) {
		this.pattern	= pattern;
		this.handler	= handler;
	}

	/** returns false if the pattern does not match, else returns the result of the handler */
	public boolean handle(ResponseData data) throws MediaWikiException {
		if (!pattern.match(data.statusLine.getStatusCode(), data.responseBody))	return false;
		return handler.handle(data);
	}
	
	/**  for debugging purposes only */
	@Override
	public String toString() {
		return "ResponseOption"
				+ "{ pattern="	+ pattern
				+ ", handler="	+ handler
				+ " }";
	}
}
