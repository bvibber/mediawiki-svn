package org.mediawiki.android.client;

// Imports
import java.util.ArrayList;
import java.util.List;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import org.mediawiki.android.WikiApi;

import android.app.ListActivity;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.View;
import android.widget.ArrayAdapter;
import android.widget.ListView;

// Class definition for a search results page
public class SearchPage extends ListActivity {

	/**
	 * Overriding the parent method. When the list item is clicked (in this
	 * case, a search term), load up the ViewPage and let it show us the page
	 * we've selected.
	 */
	@Override
	protected void onListItemClick(ListView l, View v, int position, long id) {
		String title = (String)l.getItemAtPosition( position );
		Intent i = new Intent( this, ViewPage.class );
		i.putExtra( "pageTitle", title );
		startActivity( i );
		super.onListItemClick(l, v, position, id);
	}

	/** Called when the activity is first created. */
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        try {
			this.fillData( getIntent().getExtras().getString( "searchTerm" ) );
		} catch (JSONException e) {
			Log.e( "JSON Error in Search", e.getMessage() );
		}
    }
    
    /**
     * Search for a term and fill ListActivity with the results
     * @param String searchTerm The term to look for in the API
     * @throws JSONException
     */
	private void fillData( String searchTerm ) throws JSONException {
        List<String> items = new ArrayList<String>();
        WikiApi api = WikiApi.getSingleton();
        api.newRequest();
        api.addParams( "action", "query" );
        api.addParams( "list", "search" );
        api.addParams( "srwhat", "text" );
        api.addParams( "srsearch", searchTerm );
        api.doRequest();
        JSONArray json = api.getJson().getJSONObject("query").getJSONArray("search");
        JSONObject jsonItem;
        for ( int i = 0; i < json.length(); i++ ) {
        	jsonItem = json.getJSONObject( i );
        	items.add( jsonItem.getString( "title" ) );
        }
        ArrayAdapter<String> results = 
            new ArrayAdapter<String>(this, R.layout.search_result_row, items);
        setListAdapter(results);
	}

	/**
     * Setup our menus at the bottom. Only linking to HOME here
     */
    @Override
	public boolean onCreateOptionsMenu( Menu menu ) {
    	Menus.Builder mb = new Menus.Builder( this, menu );
    	mb.useMenu( Menus.Targets.HOME );
    	return super.onCreateOptionsMenu( mb.getMenu() );
    }
}
