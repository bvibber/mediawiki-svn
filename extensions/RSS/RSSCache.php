<?php
/**
 * A simple, rolling (no GC) cache for RSS objects, keyed on URL.
 *
 * @file
 */

class RSSCache {
	public $BASE_CACHE = './cache'; // where the cache files are stored
	public $MAX_AGE = 3600; // when are files stale, default one hour
	public $ERROR = ''; // accumulate error messages

	function __construct( $base = '', $age = '' ) {
		if ( $base ) {
			$this->BASE_CACHE = $base;
		}
		if ( $age ) {
			$this->MAX_AGE = $age;
		}

		// attempt to make the cache directory
		if ( !file_exists( $this->BASE_CACHE ) ) {
			$status = @mkdir( $this->BASE_CACHE, 0755 );

			// if make failed
			if ( !$status ) {
				wfDebugLog(
					'RSS',
					"Cache couldn't make dir '" . $this->BASE_CACHE . "'."
				);
			}
		}
	}

	/**
	 * Add an item to the cache, keyed on URL.
	 * @param $url String: URL from which the RSS file was fetched
	 * @param $rss Mixed: data to serialize
	 */
	function set( $url, $rss ) {
		$this->ERROR = '';
		$cache_file = $this->file_name( $url );
		$fp = @fopen( $cache_file, 'w' );

		if ( !$fp ) {
			wfDebugLog(
				'RSS',
				"Cache unable to open file for writing: $cache_file"
			);
			return 0;
		}

		$data = serialize( $rss );
		fwrite( $fp, $data );
		fclose( $fp );

		return $cache_file;
	}

	/**
	 * Fetch an item from the cache.
	 * @param $url String: URL from which the RSS file was fetched
	 * @return Object or false: cached object on HIT, false on MISS
	 */
	function get( $url ) {
		$this->ERROR = '';
		$cache_file = $this->file_name( $url );

		if ( !file_exists( $cache_file ) ) {
			wfDebugLog(
				'RSS',
				"Cache doesn't contain: $url (cache file: $cache_file)"
			);
			return 0;
		}

		$fp = @fopen( $cache_file, 'r' );
		if ( !$fp ) {
			wfDebugLog(
				'RSS',
				"Failed to open cache file for reading: $cache_file"
			);
			return 0;
		}

		$filesize = filesize( $cache_file );
		if ( $filesize ) {
			$data = fread( $fp, filesize( $cache_file ) );
			$rss = unserialize( $data );

			return $rss;
		}

		return 0;
	}

	/**
	 * Check a URL for membership in the cache and whether the object is older
	 * then MAX_AGE (ie. STALE)
	 *
	 * @param $url String: URL from which the RSS file was fetched
	 * @return String: indicates whether there was a cache hit or not
	 */
	function check_cache( $url ) {
		$this->ERROR = '';
		$filename = $this->file_name( $url );

		if ( file_exists( $filename ) ) {
			// find how long ago the file was added to the cache
			// and whether that is longer then MAX_AGE
			$mtime = filemtime( $filename );
			$age = time() - $mtime;
			if ( $this->MAX_AGE > $age ) {
				// object exists and is current
				return 'HIT';
			} else {
				// object exists but is old
				return 'STALE';
			}
		} else {
			// object does not exist
			return 'MISS';
		}
	}

	/**
	 * @param $cache_key String: cache key, consisting of the URL + output enc.
	 * @return Integer
	 */
	function cache_age( $cache_key ) {
		$filename = $this->file_name( $url );
		if ( file_exists( $filename ) ) {
			$mtime = filemtime( $filename );
			$age = time() - $mtime;
			return $age;
		} else {
			return -1;
		}
	}

	/**
	 * Map URL to location in cache.
	 * @param $url String: URL from which the RSS file was fetched
	 * @return String: file name
	 */
	function file_name( $url ) {
		$filename = md5( $url );
		return join( DIRECTORY_SEPARATOR, array( $this->BASE_CACHE, $filename ) );
	}

}