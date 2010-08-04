package net.psammead.commonist.data;

/** metadata of a license template */
public class LicenseData {
	public final String	template;
	public final String	wikiText;
	
	public LicenseData(String template, String wikiText) {
		this.template	= template;
		this.wikiText	= wikiText;
	}
	
	/** for use in a ComboBox */
	@Override
	public String toString() {
		return template.length() != 0 
				? template + " " + wikiText
				: wikiText;
	}
}
