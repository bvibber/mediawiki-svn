package net.psammead.mwapi.ui.action.response;

import java.net.*;

import net.psammead.util.ToString;

import org.apache.commons.httpclient.*;

/** everything a ResponseHandler needs to know */
public class ResponseData {
	public final StatusLine	statusLine;
	public final String		responseBody;
	public final URL		redirect;
	public final URL		formURL;
	
	/** all value constructor */
	public ResponseData(StatusLine statusLine, String responseBody, URL redirect, URL formURL) {
		this.statusLine		= statusLine;
		this.responseBody	= responseBody;
		this.redirect		= redirect;
		this.formURL		= formURL;
	}
	
	/** for debugging purposes only */
	@Override
	public String toString() {
		return new ToString(this)
				.append("statusLine",	statusLine)
				.append("responseBody",	responseBody)
				.append("redirect",		redirect)
				.append("formURL",		formURL)
				.toString();
	}
}
