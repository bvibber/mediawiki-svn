package net.psammead.mwapi.ui.action;

import net.psammead.mwapi.MediaWiki;
import net.psammead.mwapi.MediaWikiException;
import net.psammead.mwapi.connection.Connection;
import net.psammead.mwapi.ui.action.response.ResponseData;
import net.psammead.mwapi.ui.action.response.ResponseHandler;

/** log in - returns success */
public final class UserLoginAction extends UiSimpleActionBase {
	// out
	private	boolean	success;

	public UserLoginAction(MediaWiki mediaWiki, final Connection connection, final String user, String password, boolean remember) {
		super(mediaWiki, connection);
		success	= false;
		
		simpleMethod(POST);
		simpleTitle(specialPage("Userlogin"));
		simpleArg("action",			"submitlogin");
		simpleArg("wpName",			user);
		simpleArg("wpPassword",		password);
		simpleArg("wpRemember",		remember ? "1" : "0");
		simpleArg("wpLoginattempt",	"submit");
		// without this we're redirected to Special:UserLogin?wpCookieCheck=login
		simpleArg("wpSkipCookieCheck",	"1");
		
		responseMessageHandler(200, "loginsuccess", new ResponseHandler() {
			public boolean handle(ResponseData data) throws MediaWikiException {
				connection.setUserName(user);
				connection.setLoggedIn(true);
				success	= true;
				return true;
			}
		});
		
		responseMessageHandler(200, "wrongpassword", new ResponseHandler() {
			public boolean handle(ResponseData data) throws MediaWikiException {
				connection.setLoggedIn(false);
				success	= false;
				return true;
			}
		});
		
		responseMessageHandler(200, "nosuchuser", new ResponseHandler() {
			public boolean handle(ResponseData data) throws MediaWikiException {
				connection.setLoggedIn(false);
				success	= false;
				return true;
			}
		});
		
		responseHandler(302, new ResponseHandler() {
			public boolean handle(ResponseData data) throws MediaWikiException {
				connection.setUserName(user);
				connection.setLoggedIn(true);
				success	= true;
				return true;
			}
		});
		
		// HACK for SUL-weirdnesses and ignorance of uselang
		
		responseLiteralHandler("var wgUserName = null;", new ResponseHandler() {
			public boolean handle(ResponseData data) throws MediaWikiException {
				connection.setLoggedIn(false);
				success	= false;
				return true;
			}
		});
		
		// HACK for SUL-weirdnesses and ignorance of uselang
		
		responseHandler(200, new ResponseHandler() {
			public boolean handle(ResponseData data) throws MediaWikiException {
				connection.setLoggedIn(false);
				success	= false;
				return true;
			}
		});
	}
	
	/** whether login was successful */
	public boolean isSuccess() {
		return success;
	}
}
