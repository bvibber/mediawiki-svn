package net.psammead.mwapi.ui.action.response;

import net.psammead.mwapi.*;

/** may handle a Response or return false when asked to do so */
public interface ResponseHandler {
	/** returns true when the response was handled and no further handlers should be tried */
	boolean handle(ResponseData data) throws MediaWikiException;
}
