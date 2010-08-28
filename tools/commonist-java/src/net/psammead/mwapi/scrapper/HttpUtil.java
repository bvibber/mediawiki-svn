package net.psammead.mwapi.scrapper;

import java.io.IOException;
import java.net.MalformedURLException;
import java.net.URL;

public interface HttpUtil {
	/** download a page and return its charset and body */
	HttpResult download(URL url) throws IOException;
	
	/** finds out where an URL redirects to */
	String redirectsTo(URL url) throws IOException;
	
	void useSystemProxy() throws MalformedURLException;
	
	void useProxy(String host, int port);
}
