package net.psammead.mwapi.ui.action;

import net.psammead.mwapi.MediaWiki;
import net.psammead.mwapi.MediaWikiException;
import net.psammead.mwapi.connection.Connection;
import net.psammead.mwapi.ui.MoveCallback;
import net.psammead.mwapi.ui.UnexpectedAnswerException;
import net.psammead.mwapi.ui.action.response.ResponseData;
import net.psammead.mwapi.ui.action.response.ResponseHandler;
import net.psammead.mwapi.ui.action.response.UnexpectedAnswerHandler;

/** 
 * moves an article
 * the callback is used to query overwriting existing articles and may be null
 */
public final class PageMoveAction extends UiFormActionBase {
	public PageMoveAction(MediaWiki mediaWiki, Connection connection, String oldTitle, String newTitle, String reason, MoveCallback moveCallback) {
		this(mediaWiki, connection, oldTitle, newTitle, reason, false, moveCallback);
	}

	protected PageMoveAction(final MediaWiki mediaWiki, final Connection connection, final String oldTitle, final String newTitle, final String reason, final boolean confirmOverwrite, final MoveCallback moveCallback) {
		super(mediaWiki, connection);
		
		fetchTitle(specialPage("Movepage"));
		fetchArg("target",	oldTitle);
		
		formName("movepage");
		formId("movepage");
		
		copyArg("wpEditToken");
		//addCopyfield("wpMove");
		
		actionArg("wpOldTitle",	oldTitle);
		actionArg("wpNewTitle",	newTitle);
		actionArg("wpReason",	reason);
		//if (withDiscussion)
		//setActionArg("wpMovetalk",	"1");
		if (confirmOverwrite)
		actionArg("wpConfirm",	"1");
		actionArg("wpMove",		"yes");
		actionArg("action",		"submit");
		
		// retry after query
		responseMessageHandler(200, "delete_and_move_text", new ResponseHandler() { 
			public boolean handle(ResponseData data) throws MediaWikiException {
				if (confirmOverwrite || moveCallback == null) {
					throw new UnexpectedAnswerException("could not overwrite")
									.addFactoid("status", data.statusLine);
				}
				boolean	confirm	= moveCallback.ignoreDeleteAndMoveText();
				if (confirm) {
					// TODO: use a loop?
					PageMoveAction	retry	= new PageMoveAction(mediaWiki, connection, oldTitle, newTitle, reason, true, moveCallback);
					retry.execute();
				}
				return true;
			}
		});
		
		// errors
		responseMessageHandler(200, "talkexists",			new UnexpectedAnswerHandler("talkexists"));
		responseMessageHandler(200, "talkpagemoved",		new UnexpectedAnswerHandler("talkpagemoved"));
		responseMessageHandler(200, "talkpagenotmoved",	new UnexpectedAnswerHandler("talkpagemoved"));

		// warning handler
		responseHandler(302, new ResponseHandler() {
			public boolean handle(ResponseData data) throws MediaWikiException {
				// TODO: does this happen?
				System.err.println("### target=" + data.redirect);
				return true;
			}
		});
	}
}
