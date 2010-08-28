package net.psammead.mwapi.ui.action;

import java.io.IOException;
import java.net.URL;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Map;
import java.util.Set;
import java.util.regex.Pattern;

import net.htmlparser.jericho.Element;
import net.htmlparser.jericho.FormFields;
import net.htmlparser.jericho.Source;
import net.psammead.mwapi.MediaWiki;
import net.psammead.mwapi.MediaWikiException;
import net.psammead.mwapi.connection.Connection;
import net.psammead.mwapi.net.IllegalFormException;
import net.psammead.mwapi.net.JerichoUtil;
import net.psammead.mwapi.ui.MethodException;
import net.psammead.mwapi.ui.UnexpectedAnswerException;
import net.psammead.mwapi.ui.action.response.ResponseData;
import net.psammead.mwapi.ui.action.response.ResponseHandler;
import net.psammead.mwapi.ui.action.response.ResponsePattern;
import net.psammead.mwapi.ui.action.response.ResponseSelect;

import org.apache.commons.httpclient.HttpException;
import org.apache.commons.httpclient.HttpMethod;
import org.apache.commons.httpclient.StatusLine;

/** base class for complex actions which load a form, modify its values, and store it */
public abstract class UiFormActionBase extends UiActionBase {
	private String				fetchTitle;
	private	final Map<String,String>	fetchArgs;
	
	// form selector
	private String	formName;
	private String	formId;
	private int		formIndex;
	
	private final Set<String>			copyArgs;
	private	final Map<String,String>	actionArgs;
	private final ResponseSelect		responseSelect;
	
	protected UiFormActionBase(MediaWiki mediaWiki, Connection connection) {
		super(mediaWiki, connection);
		fetchTitle		= null;
		fetchArgs		= new HashMap<String,String>();
		formName		= null;
		formId			= null;
		formIndex		= -1;
		copyArgs		= new HashSet<String>();
		actionArgs		= new HashMap<String,String>();
		responseSelect	= new ResponseSelect();
	}
	
	//-------------------------------------------------------------------------
	//## configuration setter
	
	/** title of the page containing the form */
	protected void fetchTitle(String title) {
		this.fetchTitle	= title;
	}
	
	/** arguments used to fetch the form */
	protected void fetchArg(String name, String value) {
		if (value != null)	fetchArgs.put(name, value);
		else				fetchArgs.remove(name);
	}
	
	/** name of the form */
	protected void formName(String name) {
		this.formName	= name;
	}
	
	/** id of the form, alternative to formName */
	protected void formId(String id) {
		this.formId	= id;
	}
	
	/** index of the form, alternative to formId */
	protected void formIndex(int index) {
		this.formIndex	= index;
	}
	
	/** names of fields to copy from the form into the action parameters */
	protected void copyArg(String name) {
		copyArgs.add(name);
	}
	
	/** arguments for form submission */
	protected void actionArg(String name, String value) {
		if (value != null)	actionArgs.put(name, value);
		else				actionArgs.remove(name);
	}
	
	/** register a handler for a specific response to the form submission */
	protected void responseHandler(int responseCode, ResponseHandler handler) {
		responseSelect.register(new ResponsePattern(responseCode), handler);
	}

	/** register a handler for a message response to the form submission */
	protected void responseMessageHandler(int responseCode, String messageName, ResponseHandler handler) {
		Pattern	regexp	= messageRegexp(messageName);
		if (regexp == null)	throw new IllegalArgumentException("message not available: " + messageName);
		responseSelect.register(new ResponsePattern(responseCode, regexp), handler);
	}
	
	/** register a handler for a message response to the form submission */
	protected void responseLiteralHandler(String literalText, ResponseHandler handler) {
		responseSelect.register(new ResponsePattern(literalText), handler);
	}

	//-------------------------------------------------------------------------
	
	/** executes the actions declared in the constructor */
	@Override
	public final void execute() throws MediaWikiException {
		HttpMethod fetchMethod	= null;
		HttpMethod updateMethod	= null;
		try {
			// prepare first method args
			final String fetchURL	= urlManager.actionURL(fetchTitle, fetchArgs);
			
			// execute first method
			connection.throttle();
			fetchMethod	= createGetMethod(fetchURL, fetchArgs);
			final int		fetchResponseCode	= client.executeMethod(fetchMethod);
			final String	fetchResponseBody	= fetchMethod.getResponseBodyAsString();
			// StatusLine	fetchStatusLine		= fetchMethod.getStatusLine();
			debug(fetchMethod);
			if (fetchResponseCode != 200)	throw new UnexpectedAnswerException("unexpected response code (FormAction)");
			
			// prepare second method
			// TODO: use the DataSet jericho provides
			final Source		source	= JerichoUtil.createSource(fetchResponseBody, logger);
			final Element		form	= JerichoUtil.fetchForm(source, formName, formId, formIndex);
				 
			final FormFields	fields		= form.getFormFields();
			for (String key : copyArgs) {
				// TODO: was machen mit leeren boolean-feldern oder submit-feldern, wenn die keine value haben?
				final String	value	= JerichoUtil.fetchStringField(fields, key);
				actionArgs.put(key, value);
			}
			// TODO: new URL(fetchURL) is possibly borken
			final URL	actionURL	= JerichoUtil.fetchActionURL(new URL(fetchURL), form);
			
			// execute second method
			connection.throttle();
			updateMethod	= createPostMethod(actionURL.toExternalForm(), actionArgs);
			final int			updateResponseCode	= client.executeMethod(updateMethod);
			final String		updateResponseBody	= updateMethod.getResponseBodyAsString();
			final StatusLine	updateStatusLine	= updateMethod.getStatusLine();
			debug(updateMethod);
			
			// handle response of second method
			final URL	redirect;
			if (updateResponseCode == 302) {
				redirect	= extractRedirectURL(updateMethod);
			}
			else {
				redirect	= null;
			}
			
			final boolean	handled	= responseSelect.handle(new ResponseData(
					updateStatusLine, updateResponseBody, redirect, actionURL));
			if (!handled) {	
				logger.debug(updateResponseBody);
				throw new UnexpectedAnswerException("unexpected response data (FormAction)")
								.addFactoid("status", updateStatusLine);
			}
		}
		catch (HttpException		e) { throw new MethodException("method failed", e); }
		catch (IOException			e) { throw new MethodException("method failed", e); }
		catch (InterruptedException	e) { throw new MethodException("method aborted", e); }
		catch (IllegalFormException e) { throw new MethodException("method failed", e); }
		finally { 
			if (fetchMethod != null)	fetchMethod.releaseConnection(); 
			if (updateMethod != null)	updateMethod.releaseConnection(); 
		}
	}
}
