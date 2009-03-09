package org.mediawiki.android;

// Imports
import java.util.ArrayList;
import java.util.Hashtable;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import org.mediawiki.android.WikiApi;

// Class to handle the concept of a page in a Mediawiki install
public class WikiPage {

	// Have we loaded the page
	private boolean __loaded = false;
	
	// Our private instance of the WikiApi
	private WikiApi __api;
	
	// Aspects of the page
	protected int __revId;
	protected String __pageName;
	protected String __pageContent;
	protected ArrayList<String> __categories;
	protected ArrayList<String> __externalLinks;
	protected ArrayList<String> __pageLinks;
	protected ArrayList<String> __images;
	protected ArrayList<String> __templates;
	protected Hashtable<String,String> __interwikiLinks;
	
	/** 
	 * Get the API
	 * @return WikiApi
	 */
	public WikiApi getApi() {
		return this.__api;
	}
	
	/**
	 * Constructor, helpful for making a quick page object
	 * before we want to load data
	 */
	public WikiPage( String pageName, WikiApi api ) {
		this.clearData();
		this.__pageName = pageName;
		this.__api = api;
	}
	
	/**
	 * Clear all member fields for a new request
	 */
	private void clearData() {
		this.__loaded = false;
		this.__revId = 0;
		this.__pageContent = "";
		this.__pageName = "";
		this.__categories = new ArrayList<String>();
		this.__externalLinks = new ArrayList<String>();
		this.__pageLinks = new ArrayList<String>();
		this.__images = new ArrayList<String>();
		this.__templates = new ArrayList<String>();
		this.__interwikiLinks = new Hashtable<String,String>();
	}

	/** Accessors **/
	public String getPageHtml() {
		this.loadData();
		return this.__pageContent;
	}
	public Hashtable<String,String> getInterwikiLinks() {
		this.loadData();
		return this.__interwikiLinks;
	}
	public ArrayList<String> getCategories() {
		this.loadData();
		return this.__categories;
	}
	public ArrayList<String> getExternalLinks() {
		this.loadData();
		return this.__externalLinks;
	}
	public ArrayList<String> getImages() {
		this.loadData();
		return this.__images;
	}
	public ArrayList<String>getTemplates() {
		this.loadData();
		return this.__templates;
	}
	public ArrayList<String>getPagelinks() {
		this.loadData();
		return this.__pageLinks;
	}
	
	/**
	 * Load a new page from the current API
	 * @param String page Page title to load
	 */
	public void loadPage( String page ) {
		if ( this.__pageName == page ) {
			return;
		}
		this.clearData();
		this.__pageName = page;
		this.loadData();
	}
	
	/**
	 * Have we loaded the data on this page?
	 * @return boolean
	 */
	protected boolean isLoaded() {
		return this.__loaded;
	}
	
	/**
	 * Load the page data. Functions needing data should call this,
	 * as it handles (potentially) reading data from cache instead
	 * of making remote requests.
	 * @return boolean
	 */
	protected boolean loadData() {
		if ( this.isLoaded() ) {
			return true;
		}
		try {
			this.loadDataFromRemote();
			this.__loaded = true;
		} catch ( JSONException e ) {
			this.__loaded = false;
		}
		return this.__loaded;
	}
	
	/**
	 * Couldn't find it in the cache, so make some remote calls to get
	 * the information
	 * @return boolean
	 * @throws JSONException
	 */
	protected boolean loadDataFromRemote() throws JSONException {
		WikiApi api = this.getApi();
		api.newAction( "parse" );
		api.addParams( "prop", "revid|text|categories|externallinks|images|templates|links" );
		api.addParams( "page", this.__pageName );
		if ( api.doRequest() ) {
			JSONObject json = api.getJson().getJSONObject( "parse" );
			
			// Revid and page content
			this.__revId = json.getInt( "revid" );
			this.__pageContent = json.getJSONObject( "text" ).getString( "*" );
			this.__pageContent = this.__pageContent.replace("\\", "");
			
			// Populate categories
			JSONArray arr = json.optJSONArray( "categories" );
			for ( int i = 0; i < arr.length(); i++ ) {
					this.__categories.add( arr.getString(i) );
			}
			
			// Populate external links
			arr = json.optJSONArray( "externallinks" );
			for ( int i = 0; i < arr.length(); i++ ) {
					this.__externalLinks.add( arr.getString(i) );
			}
			
			// Populate links
			arr = json.optJSONArray( "links" );
			for ( int i = 0; i < arr.length(); i++ ) {
				this.__pageLinks.add( arr.getString(i) );
			}
			
			// Populate images
			arr = json.optJSONArray( "images" );
			for ( int i = 0; i < arr.length(); i++ ) {
				this.__images.add( arr.getString(i) );
			}
			
			// Populate images
			arr = json.optJSONArray( "templates" );
			for ( int i = 0; i < arr.length(); i++ ) {
				this.__templates.add( arr.getString(i) );
			}
			
			return true;
			
		}
		else {
			return false;
		}
	}
}
