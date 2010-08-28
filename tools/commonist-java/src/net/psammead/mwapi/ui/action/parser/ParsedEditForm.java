package net.psammead.mwapi.ui.action.parser;

import java.net.URL;

import net.htmlparser.jericho.Element;
import net.htmlparser.jericho.FormField;
import net.htmlparser.jericho.FormFields;
import net.htmlparser.jericho.Source;
import net.psammead.mwapi.Location;
import net.psammead.mwapi.connection.URLManager;
import net.psammead.mwapi.net.IllegalFormException;
import net.psammead.mwapi.net.JerichoUtil;
import net.psammead.mwapi.ui.Page;
import net.psammead.mwapi.ui.UnsupportedURLException;
import net.psammead.util.TextUtil;

/** 
 * the decoded data of an editform. the body uses unix-EOLs.
 * throws a EditException when something in the form is missing.
 */
public final class ParsedEditForm {
	public final boolean	conflict;
	public final Location	location;
	public final String		body;
	public final String		editTime;
	public final String		startTime;
	public final String		editToken;
	public final boolean	watchThis;
	public final boolean	watchKnown;
	
	/** returns a Page representation of this form */
	public Page page(boolean fresh) {
		return new Page(location, body, editTime, startTime, editToken, watchThis, watchKnown, fresh);
	}

	public ParsedEditForm(URLManager urlManager, URL formURL, Source source) throws IllegalFormException {
		final Element		form	= JerichoUtil.fetchForm(source, "editform", "editform", -1);
		final FormFields	fields	= form.getFormFields();
		
		try {
			final URL	url		= JerichoUtil.fetchActionURL(formURL, form);
			location	= urlManager.anyURLToLocation(url);
			if (location == null)	throw new IllegalFormException("actionURL cannot be converted to a title: " + url);
		}
		catch (UnsupportedURLException e) {
			throw new IllegalFormException("encoding problem url", e);
		}
		
		body	= TextUtil.unixLF(JerichoUtil.fetchStringField(fields, "wpTextbox1"));
		
		// only conflict displays have this field
		conflict	= fields.get("wpTextbox2") != null;
		
		editTime	= JerichoUtil.fetchStringField(fields, "wpEdittime");
		
		// old versions do not have this field
		final FormField	wpStarttime	= fields.get("wpStarttime");
		if (wpStarttime != null) {
			startTime	= wpStarttime.getValues().iterator().next();
		}
		else {
			startTime	= null; 
		}
		
		// anonymous users do not get this input field (???)
		final FormField	wpEditToken	= fields.get("wpEditToken");
		if (wpEditToken != null) {
			editToken	= wpEditToken.getValues().iterator().next();
		}
		else {
			editToken	= null;
		}
		
		// anonymous users do not get this input field
		final FormField	wpWatchthis	= fields.get("wpWatchthis");
		if (wpWatchthis != null) {
			watchThis	= wpWatchthis.getValues().size() != 0;
			watchKnown	= true;
		}
		else {
			watchThis	= false;
			watchKnown	= false;
		}
	}
}