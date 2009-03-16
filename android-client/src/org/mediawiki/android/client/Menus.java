package org.mediawiki.android.client;

// Imports
import android.app.SearchManager;
import android.content.Context;
import android.content.Intent;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;

public class Menus {

	// Menu targets
	public static class Targets {
		public static final int HOME   = 1;
		public static final int ABOUT  = 2;
		public static final int CONFIG = 3;
		public static final int SEARCH = 4;
		public static final int PAGELINKS = 5;
		public static final int CATEGORIES = 6;
		public static final int LANGLINKS = 7;
	}
	
	// Build menus
	public static class Builder {
		// A few member variables to use
		private Menu __menu;
		private Context __ctx;
		
		// Keep a count of where we are in the menu
		private int __itemCount = Menu.FIRST;
		
		/**
		 * Constructor. Give us a context and base menu object
		 * @param Context ctx The context we're called from
		 * @param Menu menu Menu object to add items to
		 */
		public Builder( Context ctx, Menu menu ) {
			this.__ctx = ctx;
			this.__menu = menu;
		}
		
		/**
		 * Get the menu
		 * @return Menu
		 */
		public Menu getMenu() {
			return this.__menu;
		}
		
		/**
		 * Use a particular menu. It will handle strings, order, etc.
		 * @param int menu The class constant to use
		 */
		public void useMenu( int menu ) {
			MenuItem item = this.__menu.add( 0, this.__itemCount, this.__itemCount,
											this.getDescription( menu ) );
			
			// Get the icon, if there is one
			int icon = this.getIconId( menu );
			if ( icon != 0 ) {
				item.setIcon( icon );
			}
			
			// Same for the shortcut
			char shortcut = this.getShortcut( menu );
			if ( shortcut != '\u0000' ) {
				item.setAlphabeticShortcut( shortcut );
			}
			
			// And finally the intent
			Intent intent = this.getIntent( menu );
			if ( intent != null ) {
				item.setIntent( intent );
			}
			
			// Increase the menu count by 1
			this.__itemCount++;
		}
	
		/**
		 * Using our current context, get the description for a particular menu item
		 * @param int menu A menu constant
		 * @return String
		 */
		private String getDescription( int menu ) {
			int msgKey = 0;
			switch ( menu ) {
				case Menus.Targets.HOME:
					msgKey = R.string.menu_home;
					break;
				case Menus.Targets.ABOUT:
					msgKey = R.string.menu_about;
					break;
				case Menus.Targets.CONFIG:
					msgKey = R.string.menu_config;
					break;
				case Menus.Targets.SEARCH:
					msgKey = R.string.menu_search;
					break;
				default:
					// This shouldn't happen :(
					Log.i( "Menu.Builder", "getDescription called with invalid menu code: " + menu );
			}
			return this.__ctx.getString( msgKey );
		}
	
		/** 
		 * Get the icon, if the menu has one
		 * @param int menu Menu constant
		 * @return int
		 */
		private int getIconId( int menu ) {
			switch ( menu ) {
				case Menus.Targets.HOME:
					return android.R.drawable.ic_menu_compass;
				case Menus.Targets.ABOUT:
					return android.R.drawable.ic_menu_info_details;
				case Menus.Targets.CONFIG:
					return android.R.drawable.ic_menu_preferences;
				case Menus.Targets.SEARCH:
					return android.R.drawable.ic_search_category_default;
				default:
					return 0;
			}
		}
	
		/**
		 * Get the alphabetic shortcut for this link
		 * @param int menu Menu constant
		 * @return String 
		 */
		private char getShortcut( int menu ) {
			switch ( menu ) {
				case Menus.Targets.HOME:
					return 'h';
				case Menus.Targets.SEARCH:
					return SearchManager.MENU_KEY;
				default:
					return '\u0000';
			}
		}
	
		/**
		 * Get the intent for the link
		 * @param int menu Menu constant
		 * @return Intent
		 */
		private Intent getIntent( int menu ) {
			switch ( menu ) {
				case Menus.Targets.HOME:
					return new Intent( MainPage.MAIN );
				case Menus.Targets.CONFIG:
					return new Intent( ConfigurationPage.CONFIG );
				default:
					return null;
			}
		}
	};

}
