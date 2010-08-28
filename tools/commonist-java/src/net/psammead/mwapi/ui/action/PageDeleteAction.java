package net.psammead.mwapi.ui.action;

import net.psammead.mwapi.MediaWiki;
import net.psammead.mwapi.MediaWikiException;
import net.psammead.mwapi.connection.Connection;
import net.psammead.mwapi.ui.DeleteNonexistingException;
import net.psammead.mwapi.ui.action.response.ResponseData;
import net.psammead.mwapi.ui.action.response.ResponseHandler;

/** deletes an article */
public final class PageDeleteAction extends UiFormActionBase {
	public PageDeleteAction(MediaWiki mediaWiki, Connection connection, String title, String reason) {
		super(mediaWiki, connection);
		
		fetchTitle(title);
		fetchArg("action",	"delete");
		
		formName("deleteconfirm");
		formId("deleteconfirm");
		
		copyArg("wpEditToken");
		
		actionArg("wpReason",	reason);
		actionArg("wpConfirmB",	"1");
		
		// TODO: more ResponseHandlers
		
		responseMessageHandler(200, "cannotdelete", new ResponseHandler() {
			public boolean handle(ResponseData data) throws MediaWikiException {
				throw new DeleteNonexistingException("cannot delete nonexisting page");
			}
		});
	}
}
