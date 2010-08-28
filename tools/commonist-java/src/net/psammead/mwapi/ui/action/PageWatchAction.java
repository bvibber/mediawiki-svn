package net.psammead.mwapi.ui.action;

import net.psammead.mwapi.MediaWiki;
import net.psammead.mwapi.MediaWikiException;
import net.psammead.mwapi.connection.Connection;
import net.psammead.mwapi.ui.WatchException;
import net.psammead.mwapi.ui.action.response.ResponseData;
import net.psammead.mwapi.ui.action.response.ResponseHandler;

public final class PageWatchAction extends UiSimpleActionBase {
	public PageWatchAction(MediaWiki mediaWiki, Connection connection, String title, final boolean watch) {
		super(mediaWiki, connection);
		
		simpleMethod(GET);
		simpleTitle(title);
		simpleArg("action",	watch 
				? "watch" 
				: "unwatch");
		
		responseMessageHandler(200, "addedwatch", new ResponseHandler() {
			public boolean handle(ResponseData data) throws MediaWikiException {
				if (!watch)	throw new WatchException("expected addedwatch");
				return true;
				/* return watch */
			}
		});
		responseMessageHandler(200, "removedwatch", new ResponseHandler() {
			public boolean handle(ResponseData data) throws MediaWikiException {
				if (watch)	throw new WatchException("expected removedwatch message");
			 	return true;
				/* return !watch */
			}
		});
		/*
		responseHandler(200, new ResponseHandler() {
			public boolean handle(ResponseData data) throws MediaWikiException {
				throw new WatchException("unexpected reponse to the watch action");
			}
		});
		*/
	}
}
