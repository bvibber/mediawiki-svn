package net.psammead.mwapi.ui;

import net.psammead.mwapi.MediaWikiException;

public final class LoginException extends MediaWikiException {
	public LoginException(String message) {
		super(message);
	}
}
