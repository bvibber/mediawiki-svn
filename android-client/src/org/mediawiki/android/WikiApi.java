package org.mediawiki.android;

// Imports
import org.json.JSONException;
import org.json.JSONObject;
import java.net.URLEncoder;

// Represents a single MW API somewhere in the universe
public class WikiApi {

	// Always always always request JSON.
	public static final String FORMAT = "json";
	
	// Info about the wiki
	private String __name;
	private String __urlBase;
	
	// About our query
	private String __lastJsonResult = null;
	private String __action = "query"; // Default to action=query
	private String __params = "";
	private String __fullRequestUrl;
	private boolean __status = false;
	private boolean __post = false; // set to true to be a posted request
	
	// We use a singleton() method, here's the instance
	private static WikiApi __instance = null;
	
	/**
	 * Protect the constructor
	 * @param apiName
	 * @param apiUrl
	 */
	protected WikiApi( String apiName, String apiUrl ) {
		this.__name = apiName;
		this.__urlBase = apiUrl;
	}
	
	/** Get singleton */
	public static WikiApi getSingleton() {
		if ( WikiApi.__instance == null ) {
			WikiApi.__instance = new WikiApi( "enwiki", "http://en.wikipedia.org/w/api.php" );
		}
		return WikiApi.__instance;
	}
	
	/** Destroy singleton */
	public void destroySingleton() {
		WikiApi.__instance = null;
	}
	
	/** Set a new action and void the parameters */
	public void newAction( String action ) {
		this.__action = action;
		this.__params = "";
	}
	
	/** Get the name of this wiki */
	public String getName() {
		return this.__name;
	}
	
	/**
	 * Add some params to the request
	 * @param String key Name of the param (prop, etc)
	 * @param String value Value to set
	 */
	public void addParams( String key, String value ) {
		this.__params += "&" + key + "=" + URLEncoder.encode(value);
	}
	
	/** Should this be a post? */
	public void setPost( boolean p ) {
		this.__post = p;
	}
	
	/**
	 * Do the actual request. Note that we do some sanity checking
	 * here, so we don't make the same request two times in a row.
	 * @TODO Might want a way to allow that
	 * @return boolean
	 */
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
	
	/** 
	 * Construct the full query URL
	 * @return String
	 */
	public String getFullRequestUrl() {
		return	this.__urlBase + "?action=" + this.__action +
				this.__params + "&format=" + WikiApi.FORMAT;
	}
	
	/**
	 * Return the last JSON results raw
	 * @return String
	 */
	public String getRawResult() {
		return this.__lastJsonResult;
	}
	
	/**
	 * Get the last JSON results in a pretty wrapper
	 * @return JSONObject
	 */
	public JSONObject getJson() {
		try {
			return new JSONObject( this.__lastJsonResult );
		} catch (JSONException e) {
			return null;
		}
	}
	
}
