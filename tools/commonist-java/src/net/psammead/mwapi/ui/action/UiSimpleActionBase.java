package net.psammead.mwapi.ui.action;

import java.io.IOException;
import java.net.URL;
import java.util.HashMap;
import java.util.Map;
import java.util.regex.Pattern;

import net.psammead.mwapi.MediaWiki;
import net.psammead.mwapi.MediaWikiException;
import net.psammead.mwapi.connection.Connection;
import net.psammead.mwapi.ui.MethodException;
import net.psammead.mwapi.ui.UnexpectedAnswerException;
import net.psammead.mwapi.ui.UnsupportedURLException;
import net.psammead.mwapi.ui.action.response.ResponseData;
import net.psammead.mwapi.ui.action.response.ResponseHandler;
import net.psammead.mwapi.ui.action.response.ResponsePattern;
import net.psammead.mwapi.ui.action.response.ResponseSelect;

import org.apache.commons.httpclient.HttpException;
import org.apache.commons.httpclient.HttpMethod;
import org.apache.commons.httpclient.StatusLine;

/** base class for actions issuing a single request to the server */
public abstract class UiSimpleActionBase extends UiActionBase {
	/** use a GET request with simpleMethod */
	public static final Object	GET		= "GET";
	
	/** use a POST request with simpleMethod */
	public static final Object	POST	= "POST";
	
	private Object				simpleMethod;
	private String				simpleTitle;
	private	final Map<String,String>	simpleArgs;
	private final ResponseSelect		responseSelect;
	
	protected UiSimpleActionBase(MediaWiki mediaWiki, Connection connection) {
		super(mediaWiki, connection);
		simpleMethod	= GET;
		simpleTitle		= null;
		simpleArgs		= new HashMap<String,String>();
		responseSelect	= new ResponseSelect();
	}
	
	//-------------------------------------------------------------------------
	//## configuration setter
	
	/** GET or POST, default is GET */
	protected void simpleMethod(Object method) {
		this.simpleMethod	= method;
	}
	
	/** title of the page to submit */
	protected void simpleTitle(String title) {
		this.simpleTitle	= title;
	}
	
	/** arguments for the page to submit */
	protected void simpleArg(String name, String value) {
		if (value != null)	simpleArgs.put(name, value);
		else				simpleArgs.remove(name);
	}
	
	/** register a handler for a specific response to the form submission */
	protected void responseHandler(int responseCode, ResponseHandler handler) {
		responseSelect.register(new ResponsePattern(responseCode), handler);
	}
	
	protected void responseMessageHandler(int responseCode, String messageName, ResponseHandler handler) {
		// TODO HACK: replace large-file with largefile, if necessary
		if ("large-file".equals(messageName) 
		&& !messageAvailable("large-file"))
				messageName	= "largefile";
		
		Pattern	regexp	= messageRegexp(messageName);
		if (regexp == null)	throw new IllegalArgumentException("message not available: " + messageName);
		responseSelect.register(new ResponsePattern(responseCode, regexp), handler);
	}
	
	protected void responseLiteralHandler(String literalText, ResponseHandler handler) {
		responseSelect.register(new ResponsePattern(literalText), handler);
	}
	
	//-------------------------------------------------------------------------
	//## configuration overrides
	
	// TODO: only necessary for FileUploadAction -- could be generalized
	// TODO: pretty-urls are used only in FileUploadAction, too!
	
	/** can be overridden for POST requests or mangled URL or args */
	protected HttpMethod createRequest(String url, Map<String,String> args) {
			 if (simpleMethod == GET)	return createGetMethod(url, args);
		else if (simpleMethod == POST)	return createPostMethod(url, args);
		else throw new IllegalArgumentException("only GET or POST allowed for UISimpleAction");
	}
	
	/** can be overridden to use readURLs instead of actionURLs */
	protected String fetchURL(String fetchTitle, Map<String,String> fetchArgs) throws UnsupportedURLException {
		return urlManager.actionURL(fetchTitle, fetchArgs);
	}
	
	//-------------------------------------------------------------------------

	/** executes the actions declared in the constructor */
	@Override
	public final void execute() throws MediaWikiException {
		HttpMethod	method	= null;
		try {
			// prepare first method args
			final String fetchURL	= fetchURL(simpleTitle, simpleArgs);
			
			// execute method
			connection.throttle();
			method		= createRequest(fetchURL, simpleArgs);
			final int			responseCode	= client.executeMethod(method);
			final String		responseBody	= method.getResponseBodyAsString();
			final StatusLine	statusLine		= method.getStatusLine();
			debug(method);
			
			// handle response
			final URL	redirect;
			if (responseCode == 302)	redirect	= extractRedirectURL(method);
			else						redirect	= null;
			// new URL(fetchURL) is possibly borken
			final boolean	handled	= responseSelect.handle(new ResponseData(
					statusLine, responseBody, redirect, new URL(fetchURL)));
			if (!handled) {
				logger.debug(responseBody);
				throw new UnexpectedAnswerException("unexpected response data (UiSimpleActionBase)")
								.addFactoid("status", statusLine);
			}
		}
		catch (HttpException		e) { throw new MethodException("method failed", e); }
		catch (IOException			e) { throw new MethodException("method failed", e); }
		catch (InterruptedException	e) { throw new MethodException("method aborted", e); }
		finally { if (method != null) method.releaseConnection(); }
	}
}
