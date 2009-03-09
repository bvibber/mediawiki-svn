package org.mediawiki.android.client;

// Imports
import android.os.Bundle;
import android.preference.PreferenceActivity;

// Simple dummy activity for our preferences. Android manages 99% of this for us 
public class ConfigurationPage extends PreferenceActivity {
	
    /** Called when the activity is first created. */
    @Override
    public void onCreate(Bundle savedInstanceState) {
    	super.onCreate(savedInstanceState);
    	addPreferencesFromResource(R.layout.config);
    }
}
