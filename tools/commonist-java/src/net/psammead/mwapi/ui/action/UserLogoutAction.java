package net.psammead.mwapi.ui.action;

import net.psammead.mwapi.MediaWiki;
import net.psammead.mwapi.MediaWikiException;
import net.psammead.mwapi.connection.Connection;
import net.psammead.mwapi.ui.action.response.ResponseData;
import net.psammead.mwapi.ui.action.response.ResponseHandler;

public final class UserLogoutAction extends UiSimpleActionBase {
	// out
	private	boolean	success;

	public UserLogoutAction(MediaWiki mediaWiki, final Connection connection) {
		super(mediaWiki, connection);
		success	= false;
		
		simpleMethod(POST);
		simpleTitle(specialPage("Userlogout"));
		simpleArg("action",	"submit");
		
		responseMessageHandler(200, "logouttext", new ResponseHandler() {
			public boolean handle(ResponseData data) throws MediaWikiException {
				connection.setLoggedIn(false);
				success	= true;
				return true;
			}
		});
		responseHandler(200, new ResponseHandler() {
			public boolean handle(ResponseData data) throws MediaWikiException {
				//TODO check for a failure message
				success	= false;
				return true;
			}
		});
		responseHandler(302, new ResponseHandler() {
			public boolean handle(ResponseData data) throws MediaWikiException {
				connection.setLoggedIn(false);
				success	= true;
				return true;
			}
		});
	}
	
	/** whether logout was successful */
	public boolean isSuccess() {
		return success;
	}
}
