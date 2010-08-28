package net.psammead.mwapi.ui.action;

import net.psammead.mwapi.MediaWiki;
import net.psammead.mwapi.connection.Connection;


/** 
 * change a page's protection state 
 * @param levelEdit may be "", "autoconfirmed" and "sysop" 
 * @param levelMove may be "", "autoconfirmed" and "sysop" 
 * @cascade if transcluded pages should be protected, too
 * @param expiry may be empty for indefinite, "indefinite", 
 * 			or a number followed by a space and 
 * 			"years", "months", "days", "hours" or "minutes"
 */
public final class PageProtectAction extends UiFormActionBase {
	public PageProtectAction(MediaWiki mediaWiki, Connection connection,
			String title, String levelEdit, String levelMove, boolean cascade, String expiry, String reason) {
		super(mediaWiki, connection);
		
		fetchTitle(title);
		fetchArg("action",	"protect");
		
		formIndex(0);
		
		copyArg("wpEditToken");
		
		actionArg("mwProtect-level-edit",	levelEdit);
		actionArg("mwProtect-level-move",	levelMove);
		if (cascade)
		actionArg("mwProtect-cascade",		"1");
		actionArg("mwProtect-expiry",		expiry);
		actionArg("mwProtect-reason",		reason);
		// the submit-button is unnamed!
		
		// TODO: more ReponseHandlers
	}
}
