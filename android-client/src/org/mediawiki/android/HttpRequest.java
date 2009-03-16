package org.mediawiki.android;
// Imports
import java.io.IOException;
import java.net.SocketException;
import java.net.URI;
import java.net.URISyntaxException;

import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.ResponseHandler;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.BasicResponseHandler;
import org.apache.http.impl.client.DefaultHttpClient;

/**
 * Pretty simple use to get a raw HTTP GET/POST. No caching, just a raw request
 * Should remind MW devs of Http::get() and Http::post().
 */
public class HttpRequest {

	// Keep a single client 
	private static HttpClient __client;
	
	/**
	 * Http GET request
	 * @param String url URL to request
	 * @return String
	 */
	public static String get( String url ) {
		URI uri = Utils.makeUri( url );
		if ( uri == null ) {
			return null;
		}
		HttpGet get = new HttpGet( uri ); 
		ResponseHandler<String> responseHandler = new BasicResponseHandler();
		String responseBody = "";
		try {
			responseBody = HttpRequest.getClient().execute(get, responseHandler);
		} catch (ClientProtocolException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
		return responseBody;
	}

	/**
	 * Http POST request
	 * @param String url URL to request
	 * @return String
	 */
	public static String post( String url ) {
		URI uri = Utils.makeUri( url );
		if ( uri == null ) {
			return null;
		}
		HttpPost post = new HttpPost( uri ); 
		// Create a response handler
		ResponseHandler<String> responseHandler = new BasicResponseHandler();
		String responseBody = "";
		try {
			responseBody = HttpRequest.getClient().execute(post, responseHandler);
		} catch (ClientProtocolException e) {
			e.printStackTrace();
		} catch (SocketException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
		return responseBody;
	}
	
	/**
	 * Get the static instance of the HTTP client we keep around
	 * @return HttpClient
	 */
	private static HttpClient getClient() {
		if ( HttpRequest.__client == null ) {
			HttpRequest.__client = new DefaultHttpClient();
		}
		return HttpRequest.__client;
	}

	/**
	 * Class for a few Http-related utils
	 */
	public static class Utils {
		
		/**
		 * Friendly wrapper around making a URI object
		 * @param String uri String URL to make into a URI object
		 * @return URI
		 */
		public static URI makeUri( String uri ) {
			try {
				return new URI( uri );
			}
			catch( URISyntaxException use ) {
				return null;
			}
		} 
		
		/**
		 * Check if two URLs are from the same hostname 
		 */
		public static boolean areSameHost( String src, String dest ) {
			// If the destination URL is just relative, then 
			if ( dest.startsWith( "/" ) ) {
				return true;
			}
			URI srcUri = Utils.makeUri( src );
			URI destUri = Utils.makeUri( dest );
			
			// Bad URLs :(
			if ( srcUri == null || destUri == null ) {
				return false;
			}
			return srcUri.getHost() == destUri.getHost();
		}
	};
}