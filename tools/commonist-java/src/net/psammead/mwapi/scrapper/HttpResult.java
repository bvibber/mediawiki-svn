package net.psammead.mwapi.scrapper;

public final class HttpResult {
	public final String	charset;
	public final String	body;
	
	public HttpResult(String charset, String body) {
		this.charset	= charset;
		this.body		= body;
	}
}