package org.mediawiki.android.client;

import org.mediawiki.android.WikiApi;
import org.mediawiki.android.WikiPage;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.text.Html;
import android.view.Menu;
import android.view.MenuItem;
import android.widget.TextView;

public class ViewPage extends Activity {
	
	protected static final int PAGELINKS = 2;
	protected static final int CATEGORIES = 3;
	protected static final int LANGLINKS = 4;
	
	protected String pageTitle;
	protected WikiPage pageObj;
	
    /** Called when the activity is first created. */
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.view_page);
        
        // Grab the page title
        this.pageTitle = this.getIntent().getExtras().getString( "pageTitle" );
        
        // Make the wiki page object
        this.pageObj = new WikiPage( this.pageTitle, WikiApi.getSingleton() );
        
        // Set our view title
        this.makePageTitle();
        
        // Get the HTML
        String html = this.pageObj.getPageHtml();
        TextView view = (TextView)findViewById(R.id.ViewPage_text);
        view.setText( Html.fromHtml(html) );
    }

    private void makePageTitle() {
    	this.setTitle( this.getTitle() + " - " + this.pageTitle );
    }
    
	/**
     * Setup our menus at the bottom
     */
    @Override
	public boolean onCreateOptionsMenu( Menu menu ) {
    	menu.add(0, MainPage.HOME, MainPage.HOME, getString( R.string.menu_home ) );
    	menu.add(0, ViewPage.PAGELINKS, ViewPage.PAGELINKS, getString( R.string.menu_pagelinks ) );
    	menu.add(0, ViewPage.CATEGORIES, ViewPage.CATEGORIES, getString( R.string.menu_categories ) );
    	menu.add(0, ViewPage.LANGLINKS, ViewPage.LANGLINKS, getString( R.string.menu_langlinks ) );
    	return super.onCreateOptionsMenu(menu);
    }
    
    /**
     * Handle our menus :)
     */
	@Override
	public boolean onMenuItemSelected(int featureId, MenuItem item) {
		switch ( item.getItemId() ) {
			case MainPage.HOME:
			default:
				this.startActivity( new Intent( this, MainPage.class ) );
		}
		return super.onMenuItemSelected(featureId, item);
	}
}