package net.psammead.mwapi.ui.action;

import java.io.File;
import java.util.List;
import java.util.Map;

import net.htmlparser.jericho.Attribute;
import net.htmlparser.jericho.Attributes;
import net.htmlparser.jericho.Source;
import net.htmlparser.jericho.StartTag;
import net.psammead.mwapi.Location;
import net.psammead.mwapi.MediaWiki;
import net.psammead.mwapi.MediaWikiException;
import net.psammead.mwapi.NameSpace;
import net.psammead.mwapi.connection.Connection;
import net.psammead.mwapi.net.IllegalFormException;
import net.psammead.mwapi.net.JerichoUtil;
import net.psammead.mwapi.ui.ProgressCallback;
import net.psammead.mwapi.ui.UnexpectedAnswerException;
import net.psammead.mwapi.ui.UnsupportedURLException;
import net.psammead.mwapi.ui.UploadCallback;
import net.psammead.mwapi.ui.UploadFileExistsException;
import net.psammead.mwapi.ui.UploadFileLargeException;
import net.psammead.mwapi.ui.UploadFileWasDeletedException;
import net.psammead.mwapi.ui.UploadForbiddenException;
import net.psammead.mwapi.ui.action.parser.ParsedUploadWarning;
import net.psammead.mwapi.ui.action.response.ResponseData;
import net.psammead.mwapi.ui.action.response.ResponseHandler;

import org.apache.commons.httpclient.HttpMethod;

/**
 * upload a File and returns an Uploaded object. 
 * progressListener may be null.
 * callback may be null.
 */
public final class FileUploadAction extends UiSimpleActionBase {
	// in
	private final File		file;
	private final String	description;
	private final String	title;
	private final boolean	watchThis;
	private final ProgressCallback	progressListener;
	private final UploadCallback	uploadCallback;
	
	// out
	private Location uploaded;

	/**
	 * upload a File and returns an Uploaded object. 
	 * progressListener may be null.
	 * callback may be null.
	 */
	public FileUploadAction(MediaWiki mediaWiki, Connection connection,
			String title, String description, File file, 
			boolean watchThis, ProgressCallback progressListener, UploadCallback callback) {
		this(mediaWiki, connection, title, description, file, false, watchThis, null, progressListener, callback);
		
		uploaded	= null;
	}
	
	/** the location of the uploaded File */
	public Location getUploaded() {
		return uploaded;
	}

	/** 
	 * upload a File and returns an Uploaded object. 
	 * file exor sessionKey may be null.
	 * progressListener may be null.
	 * callback may be null.
	 */
	protected FileUploadAction(MediaWiki mediaWiki, Connection connection,
			final String title, String description, File file, 
			boolean ignoreWarning, boolean watchThis, String sessionKey, 
			ProgressCallback progressListener, UploadCallback callback) {
		super(mediaWiki, connection);
		this.title				= title;
		this.description		= description;
		this.file				= file;
		this.watchThis			= watchThis;
		this.progressListener	= progressListener;
		this.uploadCallback 	= callback;
		this.uploaded			= null;
//		if (!file.exists())	throw new FileNotFoundException("file does not exist: " + file);
		
		// default, overwritten createRequest for File content
		simpleMethod(POST);
		
		// TODO: test whether this works with title smushing wikis
		simpleTitle(specialPage("Upload"));
		
		simpleArg("wpDestFile",				title);
		simpleArg("wpUploadDescription",	description);
		simpleArg("wpUpload",				"yes");
		if (ignoreWarning)
		simpleArg("wpIgnoreWarning",		"1");
		if (sessionKey != null)
		simpleArg("wpSessionKey",			sessionKey);
		if (watchThis)
		simpleArg("wpWatchthis",			"true");
		
		// before mediawiki 1.6
		simpleArg("wpUploadAffirm",			"1");
		
		// normally, uploaded files redirect to the image page
		responseHandler(302, new ResponseHandler() {
			public boolean handle(ResponseData data) throws MediaWikiException {
				logger.info("upload successful");

				// since 11dec04 there is a single linefeed instead of an empty page.. trim() helps.
				// sometimes there is some output coming from some blacklist - a warning shouldbe enough
				if (data.responseBody.trim().length() != 0) {
					logger.warn("weird, store returned: " + data.statusLine + "\n" + data.responseBody); 
				}

				// get image title from the location header
				uploaded	= urlManager.anyURLToLocation(data.redirect);
				if (uploaded == null)	throw new UnexpectedAnswerException("upload not successful, could not find target location")
													.addFactoid("status", data.statusLine);
				return true;
			}
		});
		
		// when overwriting a file, a successfulupload occurs instead of a redirect.
		responseMessageHandler(200, "successfulupload", new ResponseHandler() {
			public boolean handle(ResponseData data) throws MediaWikiException {
				logger.info("upload successful");
				
				// TODO: write parser class
				final Source			source		= JerichoUtil.createSource(data.responseBody, logger);
				final List<StartTag>	aTags		= source.getAllStartTags("a");
				String	newTitle	= null;
				for (StartTag aTag : aTags) {
					final Attributes	attrs	= aTag.getAttributes();
					if (attrs == null)								continue;
					final Attribute	classAttr	= attrs.get("class");
					if (classAttr == null)							continue;
					if (!"internal".equals(classAttr.getValue()))	continue;
					final Attribute	titleAttr	= attrs.get("title");
					if (titleAttr == null)							continue;
					newTitle	= titleAttr.getValue();				break;
				}
				
				// TODO: when does this happen?
				if (newTitle == null) {
					logger.warn("successfulupload without a name");
					//### BÄH: use the original title
					newTitle	= title;	
				}
				
				// originally lacks the File: prefix
				uploaded	= site.location(NameSpace.FILE, newTitle);
				return true;
			}
		});
		
		/*
		// TODO additional messages ?
		"fileexists-forbidden",			// rename
		"fileexists-shared-forbidden",	// rename
		"minlength"						// rename
		*/
		
		// errors
		responseMessageHandler(200, "uploadnologintext",	new UploadForbiddenHandler("cannot upload without being logged in"));
        // gets old
        if (messageAvailable("badfiletype"))
		responseMessageHandler(200, "badfiletype",			new UploadForbiddenHandler("uploading this type of file is forbidden"));
		responseMessageHandler(200, "badfilename",			new UploadForbiddenHandler("uploading a file with this name is forbidden"));
		responseMessageHandler(200, "largefileserver",		new UploadForbiddenHandler("uploading this file exceeds the hard file size limit"));
		responseMessageHandler(200, "uploadscripted",		new UploadForbiddenHandler("uploading this file is forbidden, it seems to contain a script"));
		responseMessageHandler(200, "uploadvirus",			new UploadForbiddenHandler("uploading this file is forbidden, it seems to contain a virus"));
		responseMessageHandler(200, "uploadcorrupt",		new UploadForbiddenHandler("uploading this file is forbidden, it may be corrupted or have the wrong ending"));
		responseMessageHandler(200, "uploaddisabled",		new UploadForbiddenHandler("uploading files is disabled"));
		// filetype-* will replace badfiletype in the future
        if (messageAvailable("filetype-badmime"))
        responseMessageHandler(200, "filetype-badmime",    new UploadForbiddenHandler("uploading a file with this MIME-type is forbidden"));
        if (messageAvailable("filetype-badtype"))
        responseMessageHandler(200, "filetype-badtype",    new UploadForbiddenHandler("uploading a file with this file format is forbidden"));
        if (messageAvailable("filetype-missing"))
        responseMessageHandler(200, "filetype-missing",    new UploadForbiddenHandler("uploading a file without a type extension is forbidden"));
            
		// uploadWarnings
		responseMessageHandler(200, "fileexists", new ResponseHandler() {
			public boolean handle(ResponseData data) throws MediaWikiException {
				if (uploadCallback == null) throw new UploadFileExistsException("upload warning: fileexists");
				final ParsedUploadWarning	warning	= parsedUploadWarning(data.responseBody);
				if (uploadCallback.ignoreFileexists()) {
					logger.debug("upload incomplete, fileexists: overwriting");
					uploaded	= retry(title, true, warning.sessionKey);
					// the user decided, so it's not an error and everything is well
					return true;
				}
				final String	ersatzTitle	= uploadCallback.renameFileexists();
				if (ersatzTitle != null) {
					logger.debug("upload incomplete, fileexists: renamed to " + ersatzTitle);
					uploaded	= retry(ersatzTitle, false, warning.sessionKey);
					// the user decided, so it's not an error and everything is well
					return true;
				}
				throw new UploadFileExistsException("upload warning: fileexists");
			}
		});
		
		responseMessageHandler(200, "filewasdeleted", new ResponseHandler() {
			public boolean handle(ResponseData data) throws MediaWikiException {
				if (uploadCallback == null)	throw new UploadFileWasDeletedException("upload warning: filewasdeleted");
				final ParsedUploadWarning	warning	= parsedUploadWarning(data.responseBody);
				if (uploadCallback.ignoreFilewasdeleted()) {
					logger.debug("upload incomplete, filewasdeleted");
					uploaded	= retry(title, true, warning.sessionKey);
					return true;
				}
				throw new UploadFileWasDeletedException("upload warning: filewasdeleted");
			}
		});
		
		// NOTE: the older message largefile is handled in SimpleActionBase.responseHandler
		responseMessageHandler(200, "large-file", new ResponseHandler() {
			public boolean handle(ResponseData data) throws MediaWikiException {
				if (uploadCallback == null)	throw new UploadFileLargeException("upload warning: large-file");
				final ParsedUploadWarning	warning	= parsedUploadWarning(data.responseBody);
				if (uploadCallback.ignoreLargefile()) {
					logger.debug("upload incomplete, large-file");
					uploaded	= retry(title, true, warning.sessionKey);
					return true;
				}
				throw new UploadFileLargeException("upload warning: large-file");
			}
		});
		
//		responseMessageHandler(200, "badfilename", new ResponseHandler() {
//			public boolean handle(ResponseData data) throws MediaWikiException {
//				if (uploadCallback == null)	throw new UploadForbiddenException("upload warning: badfilename");
//				ParsedUploadWarning	warning	= parsedUploadWarning(data.responseBody);
//				// TODO: use uploadwarning to find out the new name
//				if (uploadCallback.ignoreBadfilename()) {
//					logger.debug("upload incomplete, badfilename");
//					uploaded	= retry(title, true, warning.sessionKey);
//				}
//				return true;
//			}
//		});
	}
	
	/** overriden to use a MultiPartPostRequest in the presence of a file */
	@Override
	protected HttpMethod createRequest(String requestURL, Map<String,String> requestArgs) {
		if (file != null)	return createMultipartPostMethod(requestURL, requestArgs, "wpUploadFile", file, progressListener);
		else				return super.createRequest(requestURL, requestArgs);
	}
	
	/** can be overridden to use readURLs instead of actionURLs */
	@Override
	protected String fetchURL(String fetchTitle, Map<String,String> fetchArgs) throws UnsupportedURLException {
		return urlManager.readURL(fetchTitle, fetchArgs);
	}
	
	
	/** throws an UploadForbiddenException */
	private static class UploadForbiddenHandler implements ResponseHandler {
		private final String	text;
		public UploadForbiddenHandler(String text) { this.text	= text; }
		public boolean handle(ResponseData data) throws MediaWikiException {
			throw new UploadForbiddenException(text);
		}
	}

	/** helps using the ParsedUploadWarning */
	private ParsedUploadWarning parsedUploadWarning(String responseBody) throws MediaWikiException {
		try {
			return new ParsedUploadWarning(JerichoUtil.createSource(responseBody, logger));
		}
		catch (IllegalFormException e) {
			final Location	errorLocation	= site.location(title);
			throw new UnexpectedAnswerException("cannot parse uploadwarning", e)
					.addFactoid("location", errorLocation);
		}
	}
	
	/** retires File upload with modified values */ 
	private Location retry(String title_, boolean ignoreWarning_, String sessionKey_) throws MediaWikiException {
		final FileUploadAction action = new FileUploadAction(
				mediaWiki, connection, title_, description, null,
				ignoreWarning_, watchThis, sessionKey_, 
				progressListener, uploadCallback);
		action.execute();
		return action.getUploaded();
	}
}
