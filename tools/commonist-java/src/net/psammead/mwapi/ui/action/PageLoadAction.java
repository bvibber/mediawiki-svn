package net.psammead.mwapi.ui.action;

import net.htmlparser.jericho.Source;
import net.psammead.mwapi.Location;
import net.psammead.mwapi.MediaWiki;
import net.psammead.mwapi.MediaWikiException;
import net.psammead.mwapi.connection.Connection;
import net.psammead.mwapi.net.IllegalFormException;
import net.psammead.mwapi.net.JerichoUtil;
import net.psammead.mwapi.ui.EditException;
import net.psammead.mwapi.ui.Page;
import net.psammead.mwapi.ui.action.parser.ParsedEditForm;
import net.psammead.mwapi.ui.action.response.ResponseData;
import net.psammead.mwapi.ui.action.response.ResponseHandler;

/** 
 * load the current version of a Page (oldid==null)
 * or an old version
 */
public final class PageLoadAction extends UiSimpleActionBase {
	// in
	private final String title;
	
	// out 
	private Page page;

	public PageLoadAction(MediaWiki mediaWiki, Connection connection, String title, String oldid) {
		super(mediaWiki, connection);
		this.title	= title;
		page	= null;
		
		simpleMethod(GET);
		simpleTitle(title);
		simpleArg("action",	"edit");
		if (oldid != null)
		simpleArg("oldid",	oldid);
		
		responseHandler(200, new ResponseHandler() {
			public boolean handle(ResponseData data) throws MediaWikiException {
				handleResponse(data, false);
				return true;
			}
		});

		responseHandler(404, new ResponseHandler() {
			public boolean handle(ResponseData data) throws MediaWikiException {
				// since 13oct05 new pages send 404
				handleResponse(data, true);
				return true;
			}
		});
	}
	
	/** the loaded Page */
	public Page getPage() {
		return page;
	}
	
	private void handleResponse(ResponseData data, boolean fresh) throws MediaWikiException {
		try {
			final Source			source	= JerichoUtil.createSource(data.responseBody, logger);
			final ParsedEditForm	parsed	= new ParsedEditForm(urlManager, data.formURL, source);
			page	= parsed.page(fresh);
		}
		catch (IllegalFormException e) {
			Location	errorLocation	= site.location(title);
			throw new EditException("editform not usable, the page may be protected")
						.addFactoid("location", errorLocation);
		}
	}	
}
