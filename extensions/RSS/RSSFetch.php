<?php
/**
 * A simple functional interface to fetching and parsing RSS files, via the
 * function fetch_rss().
 *
 * @file
 */

/**
 * Globals - redefine these in your script to change the
 * behaviour of fetch_rss() currently, most options effect the cache
 *
 * $wgMagpieRSSCache - Should Magpie cache parsed RSS objects?
 *
 * $wgMagpieRSSCacheDirectory - Where should Magpie cache parsed RSS objects?
 * This should be a location that the webserver can write to. If this
 * directory does not already exist, Magpie will try to be smart and create it.
 * This will often fail for permissions reasons.
 *
 * $wgMagpieRSSCacheAge - How long to store cached RSS objects (in seconds)?.
 *
 * $wgMagpieRSSCacheFreshOnly - If remote fetch fails, throw an error
 * instead of returning stale object?
 */

$MAGPIE_ERROR = '';

/**
 * Return RSS object for the given URL, maintaining caching.
 *
 * NOTES ON CACHING:
 * If caching is on ($wgMagpieRSSCache) fetch_rss will first check the cache.
 *
 * NOTES ON RETRIEVING REMOTE FILES:
 * If conditional gets are on (MAGPIE_CONDITIONAL_GET_ON) fetch_rss will
 * return a cached object, and touch the cache object upon recieving a 304.
 *
 * NOTES ON FAILED REQUESTS:
 * If there is an HTTP error while fetching an RSS object, the cached version
 * will be returned, if it exists (and if $wgMagpieRSSCacheFreshOnly is off)
 *
 * @param $url String: URL of RSS file
 * @return parsed RSS object (see RSSParse)
 */
function fetch_rss( $url ) {
	global $wgMagpieRSSCache, $wgMagpieRSSCacheAge, $wgMagpieRSSCacheFreshOnly;
	global $wgMagpieRSSCacheDirectory, $wgMagpieRSSFetchTimeout;
	global $wgMagpieRSSOutputEncoding, $wgMagpieRSSInputEncoding;
	global $wgMagpieRSSDetectEncoding, $wgMagpieRSSUseGzip;

	$wgMagpieRSSCache = true;
	$wgMagpieRSSCacheAge = 60 * 60; // one hour
	$wgMagpieRSSCacheFreshOnly = false;
	$wgMagpieRSSCacheDirectory = '/extensions/RSS/cache';
	$wgMagpieRSSOutputEncoding = 'ISO-8859-1';
	$wgMagpieRSSInputEncoding = null;
	$wgMagpieRSSDetectEncoding = true;

	$wgMagpieRSSFetchTimeout = 5; // 5 second timeout

	// use gzip encoding to fetch RSS files if supported?
	$wgMagpieRSSUseGzip = true;

	if ( !isset( $url ) ) {
		wfDebugLog( 'RSS', 'fetch_rss (RSSFetch.php) called without a URL!' );
		return false;
	}

	// if cache is disabled
	if ( !$wgMagpieRSSCache ) {
		// fetch file, and parse it
		$resp = _fetch_remote_file( $url );
		if ( $resp->status >= 200 && $resp->status < 300 ) {
			return _response_to_rss( $resp );
		} else {
			wfDebugLog( 'RSS', "Failed to fetch $url and cache is off" );
			return false;
		}
	} else { // else cache is ON
		// Flow
		// 1. check cache
		// 2. if there is a hit, make sure its fresh
		// 3. if cached obj fails freshness check, fetch remote
		// 4. if remote fails, return stale object, or error
		$cache = new RSSCache( $wgMagpieRSSCacheDirectory, $wgMagpieRSSCacheAge );

		if ( $cache->ERROR ) {
			wfDebugLog(
				'RSS',
				'MagpieRSS: cache error on RSSFetch.php! Error msg: ' .
					$cache->ERROR
			);
		}

		$cache_status = 0; // response of check_cache
		$request_headers = array(); // HTTP headers to send with fetch
		$rss = 0; // parsed RSS object
		$errormsg = 0; // errors, if any

		// store parsed XML by desired output encoding
		// as character munging happens at parse time
		$cache_key = $url . $wgMagpieRSSOutputEncoding;

		if ( !$cache->ERROR ) {
			// return cache HIT, MISS, or STALE
			$cache_status = $cache->check_cache( $cache_key );
		}

		// if object cached, and cache is fresh, return cached obj
		if ( $cache_status == 'HIT' ) {
			$rss = $cache->get( $cache_key );
			if ( isset( $rss ) && $rss ) {
				// should be cache age
				$rss->from_cache = 1;
				wfDebugLog( 'RSS', 'MagpieRSS: Cache HIT' );
				return $rss;
			}
		}

		// else attempt a conditional get

		// setup headers
		if ( $cache_status == 'STALE' ) {
			$rss = $cache->get( $cache_key );
			if ( $rss && $rss->etag && $rss->last_modified ) {
				$request_headers['If-None-Match'] = $rss->etag;
				$request_headers['If-Last-Modified'] = $rss->last_modified;
			}
		}

		$resp = _fetch_remote_file( $url, $request_headers );

		if ( isset( $resp ) && $resp ) {
			if ( $resp->status == '304' ) {
				// we have the most current copy
				wfDebugLog( 'RSS', "Got 304 for $url" );
				// reset cache on 304 (at minutillo insistent prodding)
				$cache->set( $cache_key, $rss );
				return $rss;
			} elseif ( $resp->status >= 200 && $resp->status < 300 ) {
				$rss = _response_to_rss( $resp );
				if ( $rss ) {
					wfDebugLog( 'RSS', 'Fetch successful' );
					// add object to cache
					$cache->set( $cache_key, $rss );
					return $rss;
				}
			} else {
				$errormsg = "Failed to fetch $url ";
				if ( $resp->status == '-100' ) {
					global $wgMagpieRSSFetchTimeout;
					$errormsg .= '(Request timed out after ' . $wgMagpieRSSFetchTimeout . ' seconds)';
				} elseif ( $resp->error ) {
					// compensate for Snoopy's annoying habbit to tacking
					// on '\n'
					$http_error = substr( $resp->error, 0, -2 );
					$errormsg .= "(HTTP Error: $http_error)";
				} else {
					$errormsg .= '(HTTP Response: ' . $resp->response_code . ')';
				}
			}
		} else {
			$errormsg = 'Unable to retrieve RSS file for unknown reasons.';
		}

		// else fetch failed

		// attempt to return cached object
		if ( $rss ) {
			wfDebugLog( 'RSS', "Returning STALE object for $url" );
			return $rss;
		}

		// else we totally failed
		$MAGPIE_ERROR = $errormsg;
		wfDebugLog(
			'MagpieRSS (RSSFetch): we totally failed :-( Error message:' .
			$errormsg
		);

		return false;
	} // end if ( !$wgMagpieRSSCache ) {
} // end fetch_rss()

/**
 * Retrieve an arbitrary remote file.
 * @param $url String: URL of the remote file
 * @param $headers Array: headers to send along with the request
 * @return an HTTP response object (see Snoopy.class.php)
 */
function _fetch_remote_file( $url, $headers = '' ) {
	global $wgMagpieRSSFetchTimeout, $wgMagpieRSSUseGzip;
	// Snoopy is an HTTP client in PHP
	if ( !class_exists( 'Snoopy', false ) ) {
		require_once( dirname( __FILE__ ) . '/Snoopy.class.php' );
	}
	$client = new Snoopy();
	$client->agent = 'MagpieRSS/0.72 (+http://magpierss.sourceforge.net) / MediaWiki RSS extension';
	$client->read_timeout = $wgMagpieRSSFetchTimeout;
	$client->use_gzip = $wgMagpieRSSUseGzip;
	if ( is_array( $headers ) ) {
		$client->rawheaders = $headers;
	}

	@$client->fetch( $url );
	return $client;
}

/**
 * Parse an HTTP response object into an RSS object.
 * @param $resp Object: an HTTP response object (see Snoopy)
 * @return parsed RSS object (see RSSParse) or false
 */
function _response_to_rss( $resp ) {
	global $wgMagpieRSSOutputEncoding, $wgMagpieRSSInputEncoding, $wgMagpieRSSDetectEncoding;
	$rss = new MagpieRSS(
		$resp->results,
		$wgMagpieRSSOutputEncoding,
		$wgMagpieRSSInputEncoding,
		$wgMagpieRSSDetectEncoding
	);

	// if RSS parsed successfully
	if ( $rss && !$rss->ERROR ) {
		// find Etag and Last-Modified
		foreach( $resp->headers as $h ) {
			// 2003-03-02 - Nicola Asuni (www.tecnick.com) - fixed bug "Undefined offset: 1"
			if ( strpos( $h, ': ' ) ) {
				list( $field, $val ) = explode( ': ', $h, 2 );
			} else {
				$field = $h;
				$val = '';
			}

			if ( $field == 'ETag' ) {
				$rss->etag = $val;
			}

			if ( $field == 'Last-Modified' ) {
				$rss->last_modified = $val;
			}
		}

		return $rss;
	} else { // else construct error message
		$errormsg = 'Failed to parse RSS file.';

		if ( $rss ) {
			$errormsg .= ' (' . $rss->ERROR . ')';
		}
		$MAGPIE_ERROR = $errormsg;
		wfDebugLog( 'RSS', 'error!' . $errormsg );

		return false;
	} // end if ( $rss && !$rss->ERROR )
}