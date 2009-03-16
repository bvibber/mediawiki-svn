package org.mediawiki.android.client;

// Imports
import org.mediawiki.android.HttpRequest;
import org.mediawiki.android.WikiApi;
import org.mediawiki.android.WikiPage;

import android.app.Activity;
import android.os.Bundle;
import android.view.Menu;
import android.webkit.WebView;
import android.webkit.WebViewClient;

// Base class definition for viewing pages
public class ViewPage extends Activity {
	
	public final static String VIEW = "org.mediawiki.android.client.ViewPage.VIEW";
	
	protected String __pageTitle;
	protected WikiPage __pageObj;
		
	// We need our own WebClient so we can override URLs being redirected
	// to the default Browser
	protected class webClient extends WebViewClient {
		@Override
		public boolean shouldOverrideUrlLoading(WebView view, String url) {
			if ( this.shouldBeIntercepted( view, url ) ) {
				return true;
			}
			return super.shouldOverrideUrlLoading(view, url);
		}

		/**
		 * Does the URL we're given need to be intercepted and handled with
		 * our WebView rather than Browser? 
		 * 
		 * @TODO! @FIXME! Right now this is only if the target is the same host
		 * Should ideally work for IW links too.
		 * 
		 * @param String url URL to check
		 * @return boolean
		 */
		private boolean shouldBeIntercepted( WebView view, String url ) {
			return HttpRequest.Utils.areSameHost( view.getUrl(), url );
		}
	};
	
    /** Called when the activity is first created. */
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        
        // Grab the page title
        this.__pageTitle = this.getIntent().getExtras().getString( "pageTitle" );
        
        // Make the wiki page object
        this.__pageObj = new WikiPage( this.__pageTitle, WikiApi.getSingleton() );
        
        // Set our view title
        this.makePageTitle();
        
        // Get the HTML
        WebView view = new WebView( this );
        view.setWebViewClient( new ViewPage.webClient() );
        view.loadData( this.__pageObj.getPageHtml(), "text/html", "utf-8" );
        this.setContentView( view );        
    }

    /** Accessor method for setting our page title. */
    private void makePageTitle() {
    	this.setTitle( this.getTitle() + " - " + this.__pageTitle );
    }
    
	/**
     * Setup our menus at the bottom
     * @TODO: UI for pagelinks/categorylinks/langlinks
     */
    @Override
	public boolean onCreateOptionsMenu( Menu menu ) {
    	Menus.Builder mb = new Menus.Builder( this, menu );
    	mb.useMenu( Menus.Targets.HOME );
    	return super.onCreateOptionsMenu(menu);
    }
}