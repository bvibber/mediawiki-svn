package net.psammead.mwapi.scrapper;

import java.io.IOException;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;

import net.psammead.util.IOUtil;
import net.psammead.util.Logger;
import net.psammead.util.Throttle;

public final class HttpUtilJDK implements HttpUtil {
	private static final Logger log	= new Logger(HttpUtilJDK.class);
	
	private final Throttle throttle	= new Throttle(100);	// 10 requests per second

	/** download a page and return its charset and body */
	public HttpResult download(URL url) throws IOException {
		try { throttle.gate(); } catch (InterruptedException e) { throw new IOException("download aborted"); }
		log.info("GET " + url);
		
		HttpURLConnection	connection	= null;
		try {
			connection	= (HttpURLConnection)url.openConnection();
			connection.setRequestProperty ("User-agent", "scrapper/1.0");
			connection.setDoInput(true);
			connection.setUseCaches(false);
			connection.setRequestMethod("GET");
			connection.setAllowUserInteraction(false);
			connection.connect();
			
			final String	type	= connection.getContentType();
			if (type == null)	throw new RuntimeException("no contenttype in: " + url);
			final String	charset	= charset(type);
			
			final String	body	= IOUtil.readStringFromStream(connection.getInputStream(), charset);
			return new HttpResult(charset, body);
		}
		finally {
			if (connection != null) {
				try { connection.disconnect(); }
				catch (Exception e) { log.warn("closing connection failed", e); }
			}
		}
	}
	
	private String charset(String contentType) {
		final int	pos		= contentType.indexOf("charset=");
		if (pos >= 0) {
			String charset	= contentType.substring(pos+"charset=".length());
			final int pos2	= charset.indexOf(";");
			if (pos2 >= 0) {
				charset	= charset.substring(0,pos2);
			}
			return charset;
		}
		else if ("text/xml".equals(contentType)){
			return "utf-8";
		}
		else {
			return "iso-8859-1";
		}
	}

	/** finds out where an URL redirects to */
	public String redirectsTo(URL url) throws IOException {
		try { throttle.gate(); } catch (InterruptedException e) { throw new IOException("download aborted"); }
		log.info("GET " + url);
		
		HttpURLConnection connection = null;
		try {
			connection	= (HttpURLConnection)url.openConnection();
			connection.setRequestProperty("User-agent", "scrapper/1.0");
			connection.setDoInput(true);
			connection.setUseCaches(false);
			connection.setRequestMethod("GET");
			connection.setAllowUserInteraction(false);
			
			connection.setInstanceFollowRedirects(false);
			
			final int	code	= connection.getResponseCode();
			if (code != HttpURLConnection.HTTP_MOVED_TEMP 
			&& code != HttpURLConnection.HTTP_MOVED_PERM) {
			// no redirect? this is an english wiki.
				return null;
			}
			
			final String location	= connection.getHeaderField("location");
			return location;
		}
		finally {
			if (connection != null) {
				try {
					connection.disconnect();
				}
				catch (Exception e) {
					e.printStackTrace();
				}
			}
		}
	}

	public void useSystemProxy() throws MalformedURLException {
		final String proxy = System.getenv("http_proxy");
		if (proxy == null)	return;
		final URL url	= new URL(proxy);
		useProxy(url.getHost(), url.getPort());
	}

	public void useProxy(String host, int port) {
		System.setProperty("http.proxyHost", host);
		System.setProperty("http.proxyPort", ""+port);
	}
}
