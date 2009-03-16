package org.mediawiki.android.client;

// Imports
import android.content.Context;
import android.os.Bundle;
import android.preference.CheckBoxPreference;
import android.preference.Preference;
import android.preference.PreferenceActivity;
import android.preference.PreferenceCategory;
import android.preference.PreferenceScreen;
import android.preference.Preference.OnPreferenceChangeListener;
import android.widget.Toast;

// Simple dummy activity for our preferences. Android manages 99% of this for us 
public class ConfigurationPage extends PreferenceActivity implements OnPreferenceChangeListener {
	
	public static final String CONFIG = "org.mediawiki.android.client.ConfigurationPage.CONFIG";
	
	public static final String PREFS_NAME = "org.mediawiki.android.client.ConfigurationPage";
	public final Context __ctx = this;
	
    /** Called when the activity is first created. */
    @Override
    public void onCreate(Bundle savedInstanceState) {
    	super.onCreate(savedInstanceState);
    	try {
    		this.setPreferenceScreen( this.createPrefScreen() );
    	}
    	catch( Exception e ) {
    		Toast t = Toast.makeText( this , e.toString(), Toast.LENGTH_LONG );
    		t.show();
    	}
    }

    /**
     * Create the preferences screen dynamically dynamically return it
     * @return PreferenceScreen
     */
    private PreferenceScreen createPrefScreen() {
    	PreferenceScreen screen = this.getPreferenceManager().createPreferenceScreen( this );
    	screen.addPreference( this.cachePrefsCategory() );
    	screen.addPreference( this.wikiPrefsCategory() );
    	return screen;
    }
    
    /**
     * Builder functions for the various preference categories
     * @return PreferenceCategory
     */
    private PreferenceCategory cachePrefsCategory() {
    	PreferenceCategory pfcat = new PreferenceCategory( this );
    	pfcat.setTitle( R.string.prefcat_caching );
    	pfcat.addPreference( this.makeUseCache() );
    	return pfcat;
    }
    private PreferenceCategory wikiPrefsCategory() {
    	PreferenceCategory pfcat = new PreferenceCategory( this );
    	pfcat.setTitle( R.string.prefcat_wikis );
    	return pfcat;
    }
   
    /**
     * And now for the various user preferences
     * @return Preference
     */
    private CheckBoxPreference makeUseCache() { 
    	CheckBoxPreference chbx = new CheckBoxPreference( this );
    	chbx.setKey( "usecache" );
    	chbx.setPersistent( true );
    	chbx.setDefaultValue( false );
    	chbx.setTitle( R.string.pref_usecache );
    	chbx.setSummaryOn( R.string.pref_usecache_on );
    	chbx.setSummaryOff( R.string.pref_usecache_off );
    	return chbx;
    }

    /**
     * Validate some preferences (none right now)
     */
	@Override
	public boolean onPreferenceChange(Preference preference, Object newValue) {
		return true;
	}
}
