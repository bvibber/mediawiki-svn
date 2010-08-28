package net.psammead.mwapi.ui.action;

import net.htmlparser.jericho.Source;
import net.psammead.mwapi.MediaWiki;
import net.psammead.mwapi.MediaWikiException;
import net.psammead.mwapi.connection.Connection;
import net.psammead.mwapi.net.IllegalFormException;
import net.psammead.mwapi.net.JerichoUtil;
import net.psammead.mwapi.ui.EditException;
import net.psammead.mwapi.ui.Page;
import net.psammead.mwapi.ui.StoreRecreateException;
import net.psammead.mwapi.ui.StoreSpamException;
import net.psammead.mwapi.ui.UnexpectedAnswerException;
import net.psammead.mwapi.ui.action.parser.ParsedEditForm;
import net.psammead.mwapi.ui.action.response.ResponseData;
import net.psammead.mwapi.ui.action.response.ResponseHandler;

public final class PageStoreAction extends UiSimpleActionBase {
	// out
	private Page	conflict;

	public PageStoreAction(MediaWiki mediaWiki, Connection connection, Page page, String summary, boolean minorEdit) {
		this(mediaWiki, connection, page, summary, minorEdit, false);
	}
	
	/** the conflicting page on the server or null */
	public Page getConflict() {
		return conflict;
	}
	
	protected PageStoreAction(final MediaWiki mediaWiki, final Connection connection, final Page page, final String summary, final boolean minorEdit, final boolean secondTry) {
		super(mediaWiki, connection);
		conflict	= null;
		
		simpleMethod(POST);
		simpleTitle(page.location.title);
		simpleArg("action",			"submit");
		simpleArg("wpTextbox1",		page.body);
		simpleArg("wpEdittime",		page.editTime);
		if (page.startTime != null)
		simpleArg("wpStarttime",	page.startTime);
		if (page.editToken != null)
		simpleArg("wpEditToken",	page.editToken);
		// since 24may05 an error is returned when wpWatchthis is set but we are not logged in
		if (page.watchThis && connection.isLoggedIn())
		simpleArg("wpWatchthis",	"1");
		simpleArg("wpSummary",		summary);
		simpleArg("wpSave",			"yes");
		if (minorEdit)	
		simpleArg("wpMinoredit",	"1");

		responseMessageHandler(200, "spamprotectionmatch", new ResponseHandler() {
			public boolean handle(ResponseData data) throws MediaWikiException {
				throw new StoreSpamException("cannot store spam link");
			}
		});
		
		responseMessageHandler(200, "confirmrecreate", new ResponseHandler() {
			public boolean handle(ResponseData data) throws MediaWikiException {
				throw new StoreRecreateException("will not recreate deleted page");
			}
		});
		
		responseHandler(200, new ResponseHandler() {
			public boolean handle(ResponseData data) throws MediaWikiException {
				final ParsedEditForm	parsed;
				try {
					final Source	source	= JerichoUtil.createSource(data.responseBody, logger);
					parsed	= new ParsedEditForm(urlManager, data.formURL, source);	// never fresh
				}
				catch (IllegalFormException e) {
					throw new EditException("editform not usable, the page may be protected")
								.addFactoid("location", page.location);
				}
				
				if (parsed.conflict) {
					logger.info("store not successful, conflict detected");
					conflict	= parsed.page(false);
					return true;
				}
				if (!secondTry) {
					// a valid editform, but no edit conflict?
					// must be the damned preview thingy (since 1.4beta6)
					// try storing the page again with the editToken we got
					logger.info("store not successful, preview detected, retrying");
					// TODO: use a loop?
					final PageStoreAction	retry	= new PageStoreAction(
							mediaWiki, connection, 
							parsed.page(false).edit(page.body), 
							summary, minorEdit, 
							true);
					retry.execute();
					conflict	= retry.getConflict();
					return true;
				}
				// ran into an endless recursion her on kamelopedia once - how could this happen?
				throw new UnexpectedAnswerException("store secondy try not successful")
								.addFactoid("status", data.statusLine);
			}
		});

		responseHandler(302, new ResponseHandler() {
			public boolean handle(ResponseData data) throws MediaWikiException {
				// since 11dec04 there is a single linefeed instead of an empty page.. trim() helps.
				// sometimes there is some output coming from some blacklist - a warning shouldbe enough
				if (data.responseBody.trim().length() != 0) {
					logger.warn("weird, store returned: " + data.statusLine + "\n" + data.responseBody); 
				}
				logger.info("store successful");
				conflict	= null;
				return true;
			}
		});
	}
}
