package net.psammead.mwapi.ui.action.response;

import net.psammead.mwapi.*;
import net.psammead.mwapi.ui.UnexpectedAnswerException;

/** a minimal handler throwing UnexpectedAnswerExceptions */
public final class UnexpectedAnswerHandler implements ResponseHandler {
	private String	text;
	public UnexpectedAnswerHandler(String text) { this.text	= text; }
	public boolean handle(ResponseData data) throws MediaWikiException {
		throw new UnexpectedAnswerException(text);
	}
}