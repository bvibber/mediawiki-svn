package org.mediawiki.android;

// Imports
import java.net.URLEncoder;
import java.util.Hashtable;

import org.json.JSONException;
import org.json.JSONObject;

/**
 * Interact with MediaWikis anywhere. Keep in mind that we don't cache
 * ANYTHING at this level. The WikiDataProvider handles caching, but
 * if you're making requests here, you'll be getting completely fresh
 * data.
 */
public class WikiApi {

	/**
	 * Big big array of actions we can do over api.php or index.php
	 * Copied from API help as of r48414
	 */
	public static class actions {
		// Api action= params
		public static class api {
			// Standard requests
			public static final int opensearch    = 101;
			public static final int login         = 102; 
			public static final int logout        = 103;
			public static final int query         = 104;
			public static final int parse         = 105;
			public static final int feedwatchlist = 106;
			public static final int help          = 107;
			public static final int paraminfo     = 108;
			public static final int purge         = 109;
			// These require the write-api enabled
			public static class write {
				public static final int rollback = 201;
				public static final int delete   = 202;
				public static final int undelete = 203;
				public static final int protect  = 204;
				public static final int block    = 205;
				public static final int unblock  = 206;
				public static final int move 	 = 207;
				public static final int edit 	 = 208;
			}
			// These are only in extensions
			public static class ext {
				public static final int sitematrix      = 301;
				public static final int expandtemplates = 302;
			}
		}
		// Index action= params
		public static class index {
			public static final int view = 401;
		}
	};
	
	// Always always always request JSON.
	public static final String DEFAULT_FORMAT = "json";
	
	// Info about the wiki
	private String __name, __urlBase, __description;
	
	// About our query
	private String __lastResult, __fullRequestUrl = null;
	private boolean __status, __post, __useIndex = false;
	private String __params = "?";
	private String __format = WikiApi.DEFAULT_FORMAT;
		
	// We use a singleton() method, here's the instance
	private static WikiApi __instance = null;
	
	// Keep track of the wiki apis we're using. Format: < name, { urlBase, description } >
	private Hashtable<String, String[]> __wikis = new Hashtable<String, String[]>();
	
	/**
	 * Protect the constructor, call getSingleton()
	 */
	protected WikiApi() {
		this.useDefaultWiki();
		this.newRequest();
	}
	
	/** Get singleton */
	public static WikiApi getSingleton() {
		if ( WikiApi.__instance == null ) {
			WikiApi.__instance = new WikiApi();
		}
		return WikiApi.__instance;
	}
	
	/** Destroy singleton */
	public void destroySingleton() {
		WikiApi.__instance = null;
	}
	
	/** Get the name of this wiki */
	public String getName() {
		return this.__name;
	}
	
	/**
	 * Get the wikis description. 
	 * @return String
	 */
	public String getDescription() {
		return this.__description;
	}
	
	/**
	 * Use the default wiki
	 * @TODO @FIXME: Put this in preferences!
	 */
	public void useDefaultWiki() {
		this.__name = "enwikipediaorg";
		this.__urlBase = "http://en.wikipedia.org/w";
		this.__description = "English Wikipedia";
		String[] cache = {this.__urlBase, this.__description};
		this.__wikis.put( this.__name, cache );
	}
	
	/** 
	 * Use the specified wiki
	 * @param String wikiId ID name of the wiki to use
	 * @return boolean
	 */
	public boolean useWiki( String wikiId ) {
		// Already using this. Return
		if ( this.__name == wikiId ) {
			this.newRequest();
			return true;
		}
		else if ( this.__wikis.containsKey( wikiId ) ) {
			// We've used it this session, it's in the local cache
			this.__name = wikiId;
			this.__urlBase = this.__wikis.get( wikiId )[0];
			this.__description = this.__wikis.get( wikiId )[1];
			this.newRequest();
			return true;
		}
		else if ( false ) {
			// @TODO @FIXME This will be where we load our saved wiki sources
			// from wherever I end up deciding to put them (Preferences, Sql, Xml?)
			this.newRequest();
			return true;
		}
		else {
			// Return false because we couldn't find the wiki, but still
			// load the defaults in case the caller isn't error checking
			// and tries a request anyway...
			this.useDefaultWiki();
			return false;
		}
		
	}

	/**
	 * Clear all the fields for a new request
	 */
	public void newRequest() {
		this.__params = "?";
		this.__lastResult = this.__fullRequestUrl = null;
		this.__post = this.__status = this.__useIndex = false;
	}
	
	/**
	 * Get the API URL
	 * @return String
	 */
	public String getApiUrl() {
		return this.__urlBase + "/api.php";
	}
	
	/**
	 * Get index.php URL
	 * @return String
	 */
	public String getIndexUrl() {
		return this.__urlBase + "/index.php";
	}
	
	/**
	 * Add some params to the request
	 * @param String key Name of the param (prop, etc)
	 * @param String value Value to set
	 */
	public void addParams( String key, String value ) {
		if ( this.__params != "?" ) {
			this.__params += "&"; 
		}
		this.__params += key + "=" + URLEncoder.encode(value);
	}
	
	/** 
	 * Should this be a post?
	 * @param boolean p Should we post? 
	 */
	public void setPost( boolean p ) {
		this.__post = p;
	}
	
	/** 
	 * Should we use index.php
	 * @param boolean i Should we use index.php? 
	 */
	public void useIndex( boolean i ) {
		this.__useIndex = i;
	}
	
	/**
	 * Set the format. Default is JSON
	 * @param String r Return type to use
	 */
	public void setFormat( String f ) {
		this.__format = f;
	}
	
	/**
	 * Get the currently set format
	 */
	public String getFormat() { return this.__format; }
	
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
			this.__lastResult = res;
			this.__status = true;
		} else {
			this.__lastResult = null;
			this.__status = false;
		}
		return this.__status;
	}
	
	/** 
	 * Construct the full query URL
	 * @return String
	 */
	public String getFullRequestUrl() {
		String url;
		if ( this.__useIndex ) {
			// Index.php request
			url = this.getIndexUrl() + this.__params;
		}
		else {
			// Api.php request
			url = this.getApiUrl() + this.__params + "&format=" + this.__format;
		}
		return url;
	}
	
	/**
	 * Return the last results raw
	 * @return String
	 */
	public String getRawResult() {
		return this.__lastResult;
	}
	
	/**
	 * Get the last JSON results in a pretty wrapper
	 * @return JSONObject
	 */
	public JSONObject getJson() {
		try {
			return new JSONObject( this.__lastResult );
		} catch (JSONException e) {
			return null;
		}
	}
}
