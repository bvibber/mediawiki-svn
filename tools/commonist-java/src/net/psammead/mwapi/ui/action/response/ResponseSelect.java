package net.psammead.mwapi.ui.action.response;

import java.util.ArrayList;
import java.util.List;

import net.psammead.mwapi.MediaWikiException;

/** contains a chain of ResponseHandlers which are tried one after another until one returns true */
public final class ResponseSelect implements ResponseHandler {
	private List<ResponseHandler>	handlers;

	public ResponseSelect() {
		handlers = new ArrayList<ResponseHandler>();
	}
	
	public void register(ResponsePattern pattern, ResponseHandler handler) {
		handlers.add(new ResponseOption(pattern, handler));
	}
	
	/** ResponseHandler implementation matches previously added handlers and executes the first matching */
	public boolean handle(ResponseData data) throws MediaWikiException {
		for (ResponseHandler handler : handlers) {
			if (handler.handle(data)) {
				return true;
			}
		}
		return false;
	}
}