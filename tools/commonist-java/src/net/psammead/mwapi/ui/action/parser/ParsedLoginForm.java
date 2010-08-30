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

/** 
 * the decoded data of an editform. the body uses unix-EOLs.
 * throws a EditException when something in the form is missing.
 */
public final class ParsedLoginForm {
	public final String		loginToken;
	
	public ParsedLoginForm(URLManager urlManager, URL formURL, Source source) throws IllegalFormException {
		final Element		form	= JerichoUtil.fetchForm(source, "userlogin", "userlogin", -1);
		final FormFields	fields	= form.getFormFields();
		
		final FormField	wpLoginToken	= fields.get("wpLoginToken");
		if (wpLoginToken != null) {
			loginToken	= wpLoginToken.getValues().iterator().next();
		}
		else {
			loginToken	= null;
		}
	}
}
