package org.mediawiki.android.client;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;

// Entry point into MW client
public class MainPage extends Activity {
	
	// Menu constants. We keep HOME public so other places can use it
	public static final int HOME   = 1;
	private static final int ABOUT  = 2;
	private static final int CONFIG = 3;

	// Button handlers for view/search
	private OnClickListener mSearchOnClickListener = new OnClickListener() {
		public void onClick(View v) {
			Intent i = new Intent( v.getContext(), SearchPage.class );
			EditText txt = (EditText)findViewById( R.id.MainPage_EditText );
			i.putExtra( "searchTerm", txt.getText().toString() );
			startActivity( i );
		}
	};
	private OnClickListener mViewOnClickListener = new OnClickListener() {
		public void onClick(View v) {
			Intent i = new Intent( v.getContext(), ViewPage.class );
			EditText txt = (EditText)findViewById( R.id.MainPage_EditText );
			i.putExtra( "pageTitle",  txt.getText().toString() );
			startActivity( i );
		}
	};
		
    /** Called when the activity is first created. */
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.main);
        
        // Set OnClickListeners
        Button view = (Button)findViewById(R.id.ViewPage);
        view.setOnClickListener( this.mViewOnClickListener );
        Button search = (Button)findViewById(R.id.SearchPage);
        search.setOnClickListener( this.mSearchOnClickListener );
    }
    
	/**
     * Setup our menus at the bottom
     */
    @Override
	public boolean onCreateOptionsMenu( Menu menu ) {
    	menu.add(0, MainPage.HOME, MainPage.HOME, getString( R.string.menu_home ) );
    	menu.add(0, MainPage.ABOUT, MainPage.ABOUT, getString( R.string.menu_about ) );
    	menu.add(0, MainPage.CONFIG, MainPage.CONFIG, getString( R.string.menu_config ) );
    	return super.onCreateOptionsMenu(menu);
    }
    
    /**
     * Handle our menus :)
     */
	@Override
	public boolean onMenuItemSelected(int featureId, MenuItem item) {
		switch ( item.getItemId() ) {
			case MainPage.ABOUT:
				this.constructAboutDialogue();
				break;
			case MainPage.CONFIG:
				this.startActivity( new Intent( this, ConfigurationPage.class ) );
				break;
			case MainPage.HOME:
			default:
				this.startActivity( new Intent( this, MainPage.class ) );
				break;
		}
		return super.onMenuItemSelected(featureId, item);
	}
	
	/**
	 * Simple helper function for making the "About" dialogue.
	 */
	private void constructAboutDialogue() {
		String msg = getString(R.string.app_name) + "\nVersion " + 
					 getString(R.string.app_version) + "\nBy: " +
					 getString(R.string.app_authors) + "\nReleased under: " +
					 getString(R.string.app_license);
		Toast t = Toast.makeText(this, msg, Toast.LENGTH_LONG);
		t.show();
	}
}