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
	
	public ParsedUploadWarning(Source source) throws IllegalFormException {
		final Element		form	= JerichoUtil.fetchForm(source, "uploadwarning", "uploadwarning", -1);
		final FormFields	fields	= form.getFormFields();
		ignoreWarning		= JerichoUtil.fetchBooleanField(fields,	"wpIgnoreWarning");	// contains "1" 
		sessionKey			= JerichoUtil.fetchStringField(fields,	"wpSessionKey");
		uploadDescription	= TextUtil.unixLF(
							  JerichoUtil.fetchStringField(fields,	"wpUploadDescription"));
		destFile			= JerichoUtil.fetchStringField(fields,	"wpDestFile");
		license				= JerichoUtil.fetchStringField(fields,	"wpLicense");
	}
}
