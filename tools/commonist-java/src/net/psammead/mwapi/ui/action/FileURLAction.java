package net.psammead.mwapi.ui.action;

import java.net.URL;

import net.psammead.mwapi.MediaWiki;
import net.psammead.mwapi.MediaWikiException;
import net.psammead.mwapi.connection.Connection;
import net.psammead.mwapi.ui.action.response.ResponseData;
import net.psammead.mwapi.ui.action.response.ResponseHandler;

/** gets the URL of a full resolution image */
public final class FileURLAction extends UiSimpleActionBase {
	// out
	private URL	url;

	public FileURLAction(MediaWiki mediaWiki, Connection connection, String title) {
		super(mediaWiki, connection);
		url	= null;
		
		simpleMethod(GET);
		simpleTitle(specialPage("Filepath"));
		simpleArg("file",	title);
		
		responseHandler(302, new ResponseHandler() {
			public boolean handle(ResponseData data) throws MediaWikiException {
				url	= data.redirect;
				return true;
			}
		});
	}
	
	/** the URL of the file */
	public URL getURL() {
		return url;
	}
}
