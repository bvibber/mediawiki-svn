package org.mediawiki.android;

import org.json.JSONException;
import org.json.JSONObject;
import java.net.URLEncoder;

public class WikiApi {

	public static final String FORMAT = "json";
	private String __name;
	private String __urlBase;
	private String __lastJsonResult = null;
	private String __action = "query"; // Default to action=query
	private String __params = "";
	private String __fullRequestUrl;
	private boolean __status = false;
	private boolean __post = false; // set to true to be a posted request
	private static WikiApi __instance = null;
	
	protected WikiApi( String apiName, String apiUrl ) {
		this.__name = apiName;
		this.__urlBase = apiUrl;
	}
	
	public static WikiApi getSingleton() {
		if ( WikiApi.__instance == null ) {
			WikiApi.__instance = new WikiApi( "enwiki", "http://en.wikipedia.org/w/api.php" );
		}
		return WikiApi.__instance;
	}
	
	public void newAction( String action ) {
		this.__action = action;
		this.__params = "";
	}
	
	public String getName() {
		return this.__name;
	}
	
	public void addParams( String key, String value ) {
		this.__params += "&" + key + "=" + URLEncoder.encode(value);
	}
	
	public void setPost( boolean p ) {
		this.__post = p;
	}
	
	public boolean doRequest() {
		if ( this.__fullRequestUrl == this.getFullRequestUrl() ) {
			return this.__status;
		}
		this.__fullRequestUrl = this.getFullRequestUrl();
		String res;
		if ( this.__post )
			res = HttpRequest.post( this.__fullRequestUrl );
		else
			res = HttpRequest.get( this.__fullRequestUrl );
		if ( res != "" && res != null ) {
			this.__lastJsonResult = res;
			this.__status = true;
		} else {
			this.__lastJsonResult = null;
			this.__status = false;
		}
		return this.__status;
	}
	
	public String getFullRequestUrl() {
		return	this.__urlBase + "?action=" + this.__action +
				this.__params + "&format=" + WikiApi.FORMAT;
	}
	
	public String getRawResult() {
		return this.__lastJsonResult;
	}
	
	public JSONObject getJson() {
		try {
			return new JSONObject( this.__lastJsonResult );
		} catch (JSONException e) {
			return null;
		}
	}
	
}
