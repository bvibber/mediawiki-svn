package net.psammead.mwapi.ui.action;

import java.io.File;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;

import net.psammead.mwapi.MediaWiki;
import net.psammead.mwapi.MediaWikiException;
import net.psammead.mwapi.config.Site;
import net.psammead.mwapi.connection.Connection;
import net.psammead.mwapi.connection.URLManager;
import net.psammead.mwapi.net.ProgressFilePartSource;
import net.psammead.mwapi.ui.ProgressCallback;
import net.psammead.mwapi.ui.UnexpectedAnswerException;
import net.psammead.mwapi.ui.UnsupportedURLException;
import net.psammead.util.Logger;

import org.apache.commons.httpclient.Header;
import org.apache.commons.httpclient.HttpClient;
import org.apache.commons.httpclient.HttpMethod;
import org.apache.commons.httpclient.NameValuePair;
import org.apache.commons.httpclient.URI;
import org.apache.commons.httpclient.URIException;
import org.apache.commons.httpclient.methods.GetMethod;
import org.apache.commons.httpclient.methods.PostMethod;
import org.apache.commons.httpclient.methods.multipart.FilePart;
import org.apache.commons.httpclient.methods.multipart.MultipartRequestEntity;
import org.apache.commons.httpclient.methods.multipart.Part;
import org.apache.commons.httpclient.methods.multipart.StringPart;
import org.apache.commons.httpclient.util.EncodingUtil;

public abstract class ActionBase {
	// TODO: move these into the context? or out of the context?
	protected final	MediaWiki	mediaWiki;
	protected final Connection 	connection;
	protected final Site		site;
	protected final URLManager	urlManager;
	protected final HttpClient	client;
	protected final String		userAgent;
	protected final Logger		logger;
	
	protected ActionBase(MediaWiki mediaWiki, Connection connection) {
		this.mediaWiki	= mediaWiki;
		this.connection	= connection;
		this.site		= connection.site;
		this.urlManager	= connection.urlManager;
		this.client		= connection.client;
		this.userAgent	= mediaWiki.getUserAgent();
		this.logger		= mediaWiki.getLogger();
	}
	
	/** does the real work, so it must be called once (!) before getting anything out of this Action */
	public abstract void execute() throws MediaWikiException;
	
	//------------------------------------------------------------------------------
	//## httpclient helper
    
	/** extracts an URL from the location header of a HttpMethod */
	protected URL extractRedirectURL(HttpMethod method) throws UnexpectedAnswerException, UnsupportedURLException {
		final Header header 	= method.getResponseHeader("location");
		if (header == null)	throw (UnexpectedAnswerException)new UnexpectedAnswerException(
									"missing location header in the response")
									.addFactoid("status", method.getStatusLine());
		try {
			// TODO: use the formURL here?
			final URI methodURI	= method.getURI();
			final URL	baseURL		= new URL(methodURI.getURI());
			final URL	resultURL	= new URL(baseURL, header.getValue());
//			// HACK: put in uselang for localized messages
//			resultURL.
//			new URLEncoder().encode(site.uselang, site.charSet)
//			"uselang", site.uselang
			return resultURL;
		}
		catch (URIException e) {
			throw new UnsupportedURLException("malformed url", e);
		}
		catch (MalformedURLException e) {
			throw new UnsupportedURLException("malformed url", e);
		}
	}
	
	/** HttpMethod factory encoding the parameters with the site charset */
	protected GetMethod createGetMethod(String url, Map<String,String> parameters) {
		final GetMethod method	= new GetMethod(url);
		method.getParams().setCookiePolicy(MediaWiki.COOKIE_POLICY);
		method.setFollowRedirects(false);
		method.addRequestHeader("User-Agent", userAgent);
		
		final NameValuePair[]	methodParams	= new NameValuePair[parameters.size()+1];
		int	index	= 0;
		methodParams[index++]	= new NameValuePair("uselang", site.uselang);
		for (Map.Entry<String, String> param : parameters.entrySet()) {
			methodParams[index++]	= new NameValuePair(
					param.getKey(), 
					param.getValue());
		}
		final String query = EncodingUtil.formUrlEncode(methodParams, site.charSet);
		method.setQueryString(query);
		return method;
	}
	
	/** HttpMethod factory encoding the parameters with the site charset */
	protected PostMethod createPostMethod(String url, Map<String,String> parameters) {
		final PostMethod method	= new PostMethod(url);
		method.getParams().setCookiePolicy(MediaWiki.COOKIE_POLICY);
		method.setFollowRedirects(false);
		method.addRequestHeader("User-Agent", userAgent);
		
		// HTTPClient uses the Content-Type header in getRequestCharSet to find out which encoding the site uses
		method.addRequestHeader("Content-Type", PostMethod.FORM_URL_ENCODED_CONTENT_TYPE + "; charset=" + site.charSet);
		method.addParameter("uselang",	site.uselang);
		for (Map.Entry<String, String> param : parameters.entrySet()) {
			method.addParameter(
					param.getKey(), 
					param.getValue());
		}
		return method;
	}
	
	/** HttpMethod factory encoding the parameters with the site charset */
	protected PostMethod createMultipartPostMethod(String url, Map<String,String> parameters,
			String fileField, File file, ProgressCallback progressListener) {
		final PostMethod method	= new PostMethod(url);
		method.getParams().setCookiePolicy(MediaWiki.COOKIE_POLICY);
		method.setFollowRedirects(false);
		method.addRequestHeader("User-Agent", userAgent);

		// setting the encoding like this does not work :(
		//method.addRequestHeader("Content-Type", MultipartPostMethod.MULTIPART_FORM_CONTENT_TYPE + "; charset=" + site.charSet);
		final List<Part>	partList	= new ArrayList<Part>();
		partList.add(new StringPart("uselang",	site.uselang,	site.charSet));
		for (Map.Entry<String, String> param : parameters.entrySet()) {
			partList.add(new StringPart(
					param.getKey(), 
					param.getValue(),
					site.charSet));
		}
		partList.add(new FilePart(
				fileField, 
				new ProgressFilePartSource(file, progressListener), // file.getName(), file,
				"application/octet-stream",
				site.charSet));
		
		final Part[]	parts	= partList.toArray(new Part[partList.size()]);
		method.setRequestEntity(new MultipartRequestEntity(parts, method.getParams())); 
		return method;
	}		
		
	//------------------------------------------------------------------------------
	//## logging
	
	/** print debug info for a HTTP-request */
	protected void debug(HttpMethod method) throws URIException {
		logger.debug("HTTP " + method.getName()	+ " " + method.getURI().toString() + " " + method.getStatusLine());
	}
    
    /*
    var line    = this.namespace.InvocationLine;    // InvocationText ..
    var space   = this.caller.namespace;
    // the space is WikiConnection.<method> at the moment
    //space = space.parent;
    log( 
    //  "[" + space.name                                        + ":"       + line                      + "]" +
    //  "[" + method.StatusCode                                 + " | "     + method.StatusText         + "]" + 
        "[" + method.Name                                       + " "       + method.URI.toString()     + "]" +
        "[" + method.StatusLine                                                                         + "]"
    //  "[" + "length:" + method.ResponseBodyAsString.length()  + " cs:"    + method.ResponseCharSet    + "]"
    );
    //------------------------------------------------------------------------------
    print("method=" + method);
    print("headers=" + method.ResponseHeaders);
    for (var header : method.ResponseHeaders) {
        print("header=" + header);
    }
    */
	
//	/** print out method results 
//	 * @throws IOException */
//	private void debug(HttpMethod method) {
//		log(""+method.getStatusLine());
//		Header[] headers = method.getResponseHeaders();
//		for (int i=0; i<headers.length; i++) {
//			Header	header	= headers[i];
//			log(""+header);
//		}
//		log(method.getResponseBodyAsString());
//		log("----------------------------");
//	}
}
