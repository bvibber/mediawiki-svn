package org.mediawiki.android.client;

// Imports
import android.app.ListActivity;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.ArrayAdapter;
import android.widget.ListView;
import android.widget.Toast;
import java.util.ArrayList;
import java.util.List;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import org.mediawiki.android.WikiApi;

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
        api.newAction( "query" );
        api.addParams( "list", "search" );
        api.addParams( "srwhat", "text" );
        api.addParams( "srsearch", searchTerm );
        Toast t = Toast.makeText( this, this.getString(R.string.fetch_data), 60 * 5 );
        t.show();
        api.doRequest();
        t.cancel();
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
    	menu.add(0, MainPage.HOME, MainPage.HOME, getString( R.string.menu_home ) );
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
				break;
		}
		return super.onMenuItemSelected(featureId, item);
	}
}
