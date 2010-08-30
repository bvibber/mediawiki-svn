package net.psammead.mwapi.ui.action.parser;

import net.htmlparser.jericho.Element;
import net.htmlparser.jericho.FormFields;
import net.htmlparser.jericho.Source;
import net.psammead.mwapi.net.IllegalFormException;
import net.psammead.mwapi.net.JerichoUtil;
import net.psammead.util.TextUtil;

/**
 * parsed contents of an UploadWarning form 
 * throws a UploadException when something in the form is missing.
 */
public final class ParsedUploadWarning {
	public final boolean	ignoreWarning;
	public final String		sessionKey;
	public final String		uploadDescription;
	public final String		destFile;
	public final String		license;
	public final String		editToken;
	
	public ParsedUploadWarning(Source source) throws IllegalFormException {
		Element		form;
		boolean		ignore;
		String		token;
		try {
			form = JerichoUtil.fetchForm(source, "uploadwarning", "uploadwarning", -1);
		} catch (IllegalFormException e) {
			form = JerichoUtil.fetchForm(source, "uploadwarning", "mw-upload-form", -1);
		}
		final FormFields	fields	= form.getFormFields();
		try {
			ignore		= JerichoUtil.fetchBooleanField(fields,	"wpIgnoreWarning");	// contains "1" 
		} catch (IllegalFormException e) {
			ignore		= true;
		}
		ignoreWarning = ignore;
		sessionKey			= JerichoUtil.fetchStringField(fields,	"wpSessionKey");
		uploadDescription	= TextUtil.unixLF(
							  JerichoUtil.fetchStringField(fields,	"wpUploadDescription"));
		destFile			= JerichoUtil.fetchStringField(fields,	"wpDestFile");
		license				= JerichoUtil.fetchStringField(fields,	"wpLicense");
		try {
				token		= JerichoUtil.fetchStringField(fields,	"wpEditToken");
		} catch (IllegalFormException e) {
			// this field may be not present
				token		= null;
		}
		editToken = token;
	}
}
