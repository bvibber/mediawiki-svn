package net.psammead.mwapi.test;

import net.psammead.mwapi.MediaWiki;
import net.psammead.mwapi.MediaWikiException;

public final class LoginTest {
	public static void main(String[] args) throws MediaWikiException {
		final MediaWiki mw	= new MediaWiki();
		mw.setLog(System.err);
		mw.setupProxy();
		final boolean success = mw.login("commons", "xxxx", "xxxx", true);
		System.err.println("### " + success);
	}
}
