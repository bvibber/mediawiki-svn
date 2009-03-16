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
	
	public static final String MAIN = "org.mediawiki.android.client.MainPage.MAIN";
	
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
    	Menus.Builder mb = new Menus.Builder( this, menu );
    	mb.useMenu( Menus.Targets.HOME );
    	mb.useMenu( Menus.Targets.ABOUT );
    	mb.useMenu( Menus.Targets.CONFIG );
    	return super.onCreateOptionsMenu( mb.getMenu() );
    }
    
    /**
     * Handle our menus :)
     */
	@Override
	public boolean onMenuItemSelected(int featureId, MenuItem item) {
		if ( item.getItemId() == Menus.Targets.ABOUT ) {
			this.constructAboutDialogue();
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