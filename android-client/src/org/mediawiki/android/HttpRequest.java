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
 * Class definition. Pretty simple use to get a raw HTTP GET/POST
 * No caching, just a raw request.
 */
public class HttpRequest {

	// Keep a single client 
	private static HttpClient __client;
	
	public static String get( String url ) {
		URI uri = HttpRequest.makeUri( url );
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
	
	public static String post( String url ) {
		URI uri = HttpRequest.makeUri( url );
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
	
	private static HttpClient getClient() {
		if ( HttpRequest.__client == null ) {
			HttpRequest.__client = new DefaultHttpClient();
		}
		return HttpRequest.__client;
	}
	
	private static URI makeUri( String uri ) {
		try {
			return new URI( uri );
		}
		catch( URISyntaxException use ) { {
			return null;
		}
			
		}
	} 
}