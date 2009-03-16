package org.mediawiki.android.client;

import android.app.Activity;
import android.os.Bundle;

public class EditPage extends Activity {
		
	public final static String EDIT = "org.mediawiki.android.client.EditPage.EDIT";
	
    /** Called when the activity is first created. */
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.main);
    }

}