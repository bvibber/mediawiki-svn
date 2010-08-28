package net.psammead.mwapi.scrapper;

public final class BasicInfo {
	public final String	charset;
	public final String	specialNs;	// attention, this string is still URLencoded
	
	BasicInfo(String charset, String specialNs) {
		this.charset	= charset;
		this.specialNs	= specialNs;
	}
}