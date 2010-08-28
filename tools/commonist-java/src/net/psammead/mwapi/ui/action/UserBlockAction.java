package net.psammead.mwapi.ui.action;

import net.psammead.mwapi.MediaWiki;
import net.psammead.mwapi.MediaWikiException;
import net.psammead.mwapi.connection.Connection;
import net.psammead.mwapi.ui.BlockAlreadySetException;
import net.psammead.mwapi.ui.action.response.ResponseData;
import net.psammead.mwapi.ui.action.response.ResponseHandler;

/**
 * blocks a user
 * @param expiry may be empty for indefinite, "indefinite", 
 * 			or a number followed by a space and 
 * 			"years", "months", "days", "hours" or "minutes"
 * @param anonOnly defaults to false
 * @param createAccounts defaults to true  
 */
public final class UserBlockAction extends UiFormActionBase {
	public UserBlockAction(MediaWiki mediaWiki, Connection connection, final String user, String expiry, String reason,
			boolean anonOnly, boolean createAccount, boolean enableAutoblock, boolean emailBan) {
		super(mediaWiki, connection);

		fetchTitle(specialPage("Blockip"));
		//fetchArg("target",	user);
		
		formName("blockip");
		formId("blockip");
		
		copyArg("wpEditToken");
		//copyArg("wpBlockExpiry");
		//copyArg("wpBlock");
		
		actionArg("wpBlockExpiry",		"other");
		actionArg("wpBlockAddress",		user);
		actionArg("wpBlockReason",		reason);
		if (anonOnly)
		actionArg("wpAnonOnly",			"1");
		if (createAccount)
		actionArg("wpCreateAccount",	"1");
		if (enableAutoblock)
		actionArg("wpEnableAutoblock",	"1");
		if (emailBan)
		actionArg("wpEmailBan",	"1");
		actionArg("wpBlockOther",		expiry);
		actionArg("wpBlock",			"yes");
		
		// TODO: more ReponseHandlers
		
		// normally, block redirect to a success page
		responseHandler(302, new ResponseHandler() {
			public boolean handle(ResponseData data) throws MediaWikiException {
				logger.info("block successful");
				return true;
			}
		});
		
		// normally, block redirect to a success page
		responseHandler(200, new ResponseHandler() {
			public boolean handle(ResponseData data) throws MediaWikiException {
				throw new BlockAlreadySetException("user is already blocked: " + user);
			}
		});
		
	}
}
