package org.mediawiki.android;

// Imports
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.ObjectInputStream;
import java.io.ObjectOutputStream;
import java.util.Calendar;
import java.util.HashMap;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteException;
import android.database.sqlite.SQLiteOpenHelper;
import android.database.sqlite.SQLiteQueryBuilder;
import android.provider.BaseColumns;
import android.util.Log;

/**
 * AndCache - Android object caching
 * 
 * Generic caching class for the storing of pretty much anything you want.
 * It's backed by a context-based SQLite database. Just include in your
 * Android app, call setContext() with whatever your context is, and you're
 * set.
 *
 * @author Chad Horohoe
 * @version 0.2
 * @license GNU GPL v2 (or later)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 */
public class AndCache {

	// Default cache length, in seconds
	private static final int DEFAULT_EXPIRY = 3600 * 24; // 1 day
	
	// For debugging
	private static final String LOG = "org.mediawiki.android.Cache";
	
	// Call lockCache() to get a lock on the cache
	private static boolean locked = false;	
	
	// Database info
	private static final String DB_NAME = "cache.db";
	private static final int DB_VERSION = 1;
	private static final String TBL_NAME = "main_cache";
	private static final class TBL_SCHEMA { 
		public static final class cols implements BaseColumns {
			public static final String KEY = "key";
			public static final String VALUE = "value";
			public static final String EXPIRY = "expiry";
		}
		public static final class types {
			public static final String _ID = "INTEGER PRIMARY KEY";
			public static final String KEY = "TEXT";
			public static final String VALUE = "BLOB";
			public static final String EXPIRY = "TEXT";
		}
	}
	
	// Mapping of columns for select queries
	private static HashMap<String,String> getColumns = new HashMap<String, String>();
	static {
    	getColumns.put( TBL_SCHEMA.cols._ID, TBL_SCHEMA.cols._ID );
    	getColumns.put( TBL_SCHEMA.cols.KEY, TBL_SCHEMA.cols.KEY );
    	getColumns.put( TBL_SCHEMA.cols.VALUE, TBL_SCHEMA.cols.VALUE );
    	getColumns.put( TBL_SCHEMA.cols.EXPIRY, TBL_SCHEMA.cols.EXPIRY );
    }
	
	// DbWrapper and Context
	private static Context ctx;
	private static DbWrapper mWrapper = null;
	
	/**
	 * Get a result from the cache, if we've got it and it's recent
	 * @param String key The cache key to retrieve
	 * @return Object
	 */
	public static Object get( String key ) {
		SQLiteDatabase db = mWrapper.getReadableDatabase();

		// Build the query
		SQLiteQueryBuilder qb = new SQLiteQueryBuilder();
		qb.setDistinct( true );
		qb.setTables( TBL_NAME );
		qb.appendWhere( TBL_SCHEMA.cols.KEY + " = '" + key + "'" );
		qb.setProjectionMap( getColumns );

		// Do the query
		Cursor c = qb.query( db, null, null, null, null, null, null );
		
		// We only got 1 result, that or 0
		if ( c.moveToFirst() ) {
			Calendar cal = Calendar.getInstance();
			cal.setTimeInMillis( c.getLong( c.getColumnIndexOrThrow( TBL_SCHEMA.cols.EXPIRY ) ) );
			try {
				if ( AndCache.expired( cal ) ) {
					AndCache.delete( key );
					Log.i( AndCache.LOG, "entry expired for key '" + key + "'" );
					return null;
				}
				Log.i( AndCache.LOG, "HIT for key '" + key + "'" );
				return AndCache.unserialize( c.getBlob( c.getColumnIndexOrThrow( TBL_SCHEMA.cols.VALUE ) ) );
			} catch (CacheException e) {
				return null;
			}
		}
		else {
			Log.i( AndCache.LOG, "no results for key '" + key + "'" );
			return null;
		}
	}
	
	/**
	 * Put a result into the database
	 * @param String key Some key to cache with
	 * @param Object val Something to put into the database (unserialized)
	 * @param int expiry How long to keep it cached (in seconds) 0 defaults to
	 *        AndCache.DEFAULT_EXPIRY
	 * @return boolean On successful set
	 */
	public static boolean set( String key, Object val, int expiry ) {
		SQLiteDatabase db = mWrapper.getWritableDatabase();
		ContentValues vals = new ContentValues();
		
		// Setup the expiry
		if ( expiry < 1 ) {
			expiry = AndCache.DEFAULT_EXPIRY;
		}
		Calendar c = Calendar.getInstance();
		c.add( Calendar.SECOND, expiry );
		
		// Try to put it in the db now
		try {
			vals.put( TBL_SCHEMA.cols.KEY, key );
			vals.put( TBL_SCHEMA.cols.VALUE,  AndCache.serialize( val ) );
			vals.put( TBL_SCHEMA.cols.EXPIRY, c.getTimeInMillis() );
			db.insertOrThrow( TBL_NAME, TBL_SCHEMA.cols.VALUE, vals );
			return true;
		} catch (CacheException e) {
			return false;
		} finally {
			Log.i( AndCache.LOG, "Put key '" + key + "' into cache with expiry of " + c.getTimeInMillis() );
		}
	}

	/**
	 * Remove something from the cache
	 * @param String key Key name of object to remove
	 */
	private static void delete( String key ) throws CacheException {
		SQLiteDatabase db = mWrapper.getWritableDatabase();
		try {
			db.delete( TBL_NAME, TBL_SCHEMA.cols.KEY + " = '" + key + "'", null );
		} catch ( SQLiteException e ) {
			throw new CacheException( "SQL", "AndCache.delete", e.getMessage() ); 
		} finally {
			Log.i( "org.mediawiki.android.Cache", "Deleting key: " + key );
		}
	}
	
	/**
	 * Flush the entire cache. Helpful if you've got stuff caught in it
	 */
	public static void flush() throws CacheException {
		SQLiteDatabase db = mWrapper.getWritableDatabase();
		try {
			db.delete( TBL_NAME , "1", null );
		} catch( SQLiteException e ) {
			throw new CacheException( "Query", "flush()", e.getMessage() );
		}
	}
	
	/**
	 * Is the entry expired?
	 * @param
	 * @return boolean
	 */
	private static boolean expired( Calendar time ) {
		Calendar now = Calendar.getInstance();
		return time.getTimeInMillis() < now.getTimeInMillis();
	}
	
	/**
	 * Serialize an object for putting into the database
	 * @param Object obj Some generic object to serialize
	 * @return byte[]
	 */
	private static byte[] serialize( Object obj ) throws CacheException {
		ByteArrayOutputStream bOut;
		ObjectOutputStream oOut;
		byte[] bytes = null;
		try {
			bOut = new ByteArrayOutputStream();
			oOut = new ObjectOutputStream(bOut);
			oOut.writeObject(obj);
			bytes = bOut.toByteArray();
		} catch ( IOException e ) {
			throw new CacheException( "IOException", "AndCache.serialize", e.getMessage() );
		}
		return bytes;
	}

	/**
	 * Unserialize an object
	 * @param byte[] bytes A byte array to be deserialized into an object
	 * @return Object
	 */
	private static Object unserialize( byte[] bytes ) throws CacheException {
		try {
			ByteArrayInputStream bIn = new ByteArrayInputStream( bytes );
		    ObjectInputStream oIn = new ObjectInputStream( bIn );
		    Object obj = oIn.readObject();
		    oIn.close();
		    bIn.close();
		    return obj;
		} catch( IOException e ) {
			throw new CacheException( "IOException", "AndCache.unserialize", e.getMessage() );
		} catch (ClassNotFoundException e) {
			throw new CacheException( "ClassNotFoundException", "AndCache.unserialize" ,e.getMessage() );
		}
	}
	
	/**
	 * DB wrapper for accessing and setting up the DB
	 */
	private static class DbWrapper extends SQLiteOpenHelper {
			/**
	    	 * Constructor
	    	 * @param Context context The context we're calling from
	    	 */
	    	DbWrapper(Context context) {
	            super(context, AndCache.DB_NAME, null, AndCache.DB_VERSION );
	        }

	    	/**
	    	 * Create the database
	    	 * @param SQLiteDatabase db Database to execute the SQL on
	    	 */
	        @Override
	        public void onCreate(SQLiteDatabase db) {
            	db.execSQL("CREATE TABLE " + TBL_NAME + " ("
            			+ TBL_SCHEMA.cols._ID + " " + TBL_SCHEMA.types._ID + ","
            			+ TBL_SCHEMA.cols.KEY + " " + TBL_SCHEMA.types.KEY + ","
            			+ TBL_SCHEMA.cols.VALUE + " " + TBL_SCHEMA.types.VALUE + ","
            			+ TBL_SCHEMA.cols.EXPIRY + " " + TBL_SCHEMA.types.EXPIRY
            			+ ");");
	        }

	        /**
	         * When I bump the schema version, drop the old table and put
	         * in the new definition.
	         */
	        @Override
	        public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {
	            Log.i( AndCache.LOG, "Upgrading cache db (" + oldVersion + "->"
	                    + newVersion + "). Will flush cache." );
	            db.execSQL("DROP TABLE IF EXISTS " + AndCache.TBL_NAME );
	            onCreate( db );
	        }
	};
	
	/**
	 * Need to be able to set our context
	 * @param Context c the new Context to use
	 */
	public static void setContext( Context c, boolean lock ) throws CacheException {
		String curPackage = AndCache.ctx.getPackageName();
		if ( c.getPackageName() == curPackage ) {
			return;
		}
		else if ( AndCache.locked ) {
			throw new CacheException( "Context locked", "AndCache.setContext", 
										"locked to " + curPackage );
		}
		else {
			try {
				AndCache.ctx = c;
				mWrapper = new DbWrapper( AndCache.ctx );
				AndCache.locked = lock;
			}
			catch ( SQLiteException e ) {
				throw new CacheException( "Create DB", "AndCache.setContext", e.getMessage() );
			}
		}
	}
	
	/**
	 * Unlock the cache. Call this if you've gotten a lock and you're
	 * done with the cache.
	 */
	public static void unlockCache() {
		AndCache.locked = false;
	}
	
	/**
	 * Get the current context and re-init the DbWrapper
	 * @return Context
	 */
	public static Context getContext() {
		return AndCache.ctx;
	}
    
	/**
	 * AndCache exceptions
	 */
	public static class CacheException extends Exception {
		// Auto-gen
		private static final long serialVersionUID = 675324595007281374L;
		/**
		 * Cache exceptions. We generally throw these when catching higher 
		 * exceptions so we can bail out nicer.
		 * @param error The specific type of error or exception being caught
		 * @param method The method that is throwing the error (eg: AndCache.get)
		 * @param eMsg Any extra info to return, perhaps from a caught exception
		 */
		public CacheException( String error, String caller, String eMsg ) {
			// Java is stupid and wants the parent call first, so I can't
			// just make a string and use it twice :\
			super( error + " error in " + caller + "()" + ( eMsg != null ? ": " + eMsg : "" ) );
			Log.e( AndCache.LOG, error + " error in " + caller + "()" + 
						( eMsg != null ? ": " + eMsg : "" )  );
		}
	}
}
