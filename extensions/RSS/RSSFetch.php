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
 * $wgRSSCache - Should we cache parsed RSS objects?
 *
 * $wgRSSCacheDirectory - Where should we cache parsed RSS objects?
 * This should be a location that the webserver can write to. If this
 * directory does not already exist, We will try to be smart and create it.
 * This will often fail for permissions reasons.
 *
 * $wgRSSCacheAge - How long to store cached RSS objects (in seconds)?.
 *
 * $wgRSSCacheFreshOnly - If remote fetch fails, throw an error
 * instead of returning stale object?
 */

$RSS_FETCH_ERROR = '';

/**
 * Return RSS object for the given URL, maintaining caching.
 *
 * NOTES ON CACHING:
 * If caching is on ($wgRSSCache) fetch_rss will first check the cache.
 *
 * NOTES ON RETRIEVING REMOTE FILES:
 * If conditional gets are on (MAGPIE_CONDITIONAL_GET_ON) fetch_rss will
 * return a cached object, and touch the cache object upon recieving a 304.
 *
 * NOTES ON FAILED REQUESTS:
 * If there is an HTTP error while fetching an RSS object, the cached version
 * will be returned, if it exists (and if $wgRSSCacheFreshOnly is off)
 *
 * @param $url String: URL of RSS file
 * @return parsed RSS object (see RSSParse)
 */
function fetch_rss( $url ) {
	global $wgRSSCache, $wgRSSCacheAge, $wgRSSCacheFreshOnly;
	global $wgRSSCacheDirectory, $wgRSSFetchTimeout;
	global $wgRSSOutputEncoding, $wgRSSInputEncoding;
	global $wgRSSDetectEncoding, $wgRSSUseGzip;

	$nameValue = array('wgRSSCache' => true,
		'wgRSSCacheAge' => 60 * 60, // one hour
		'wgRSSCacheFreshOnly' => false,
		'wgRSSCacheDirectory' => '/extensions/RSS/cache',
		'wgRSSOutputEncoding' => 'ISO-8859-1',
		'wgRSSInputEncoding' => null,
		'wgRSSDetectEncoding' => true,
		'wgRSSFetchTimeout' => 5, // 5 second timeout
		'wgRSSUseGzip' => true);

	foreach($nameValue as $n => $v) {
		if( !isset( $GLOBALS[$n] ) ) {
			$GLOBALS[$n] = $v;
		}
	}

	if ( !isset( $url ) ) {
		wfDebugLog( 'RSS', 'fetch_rss (RSSFetch.php) called without a URL!' );
		return false;
	}

	// if cache is disabled
	if ( !$wgRSSCache ) {
		// fetch file, and parse it
		$resp = _fetch_remote_file( $url );
		$errs = $resp->getErrorsArray();
		if ( count( $errs ) == 0 ) {
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
		$cache = new RSSCache( $wgRSSCacheDirectory, $wgRSSCacheAge );

		if ( $cache->ERROR ) {
			wfDebugLog(
				'RSS',
				'cache error on RSSFetch.php! Error msg: ' .
					$cache->ERROR
			);
		}

		$cache_status = 0; // response of check_cache
		$request_headers = array(); // HTTP headers to send with fetch
		$rss = 0; // parsed RSS object
		$errormsg = 0; // errors, if any

		// store parsed XML by desired output encoding
		// as character munging happens at parse time
		$cache_key = $url . $wgRSSOutputEncoding;

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
				wfDebugLog( 'RSS', 'Cache HIT' );
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
			if ( $resp->getStatus() === 304 ) {
				// we have the most current copy
				wfDebugLog( 'RSS', "Got 304 for $url" );
				// reset cache on 304 (at minutillo insistent prodding)
				$cache->set( $cache_key, $rss );
				return $rss;
			} elseif ( $resp->getStatus() >= 200 && $resp->getStatus() < 300 ) {
				$rss = _response_to_rss( $resp );
				if ( $rss ) {
					wfDebugLog( 'RSS', 'Fetch successful' );
					// add object to cache
					$cache->set( $cache_key, $rss );
					return $rss;
				}
			} else {
				$errormsg = "Failed to fetch $url ";
				if ( $resp->getStatus() === -100 ) {
					global $wgRSSFetchTimeout;
					$errormsg .= '(Request timed out after ' . $wgRSSFetchTimeout . ' seconds)';
				} elseif ( $resp->error ) {
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
		$RSS_FETCH_ERROR = $errormsg;
		wfDebugLog(
			'RSSFetch: we totally failed :-( Error message:' .
			$errormsg
		);

		return false;
	} // end if ( !$wgRSSCache ) {
} // end fetch_rss()

/**
 * Retrieve an arbitrary remote file.
 * @param $url String: URL of the remote file
 * @param $headers Array: headers to send along with the request
 * @return an HTTP response object
 */
function _fetch_remote_file( $url, $headers = '' ) {
	global $wgRSSFetchTimeout, $wgRSSUseGzip;

	$client =
		HttpRequest::factory($url, array('timeout' => $wgRSSFetchTimeout));
	$client->setUserAgent('MediawikiRSS/0.01 (+http://www.mediawiki.org/wiki/Extension:RSS) / MediaWiki RSS extension');
	/* $client->use_gzip = $wgRSSUseGzip; */
	if ( is_array( $headers ) && count( $headers ) > 0 ) {
		foreach($headers as $h) {
			if( count( $h ) > 1 ) {
				$client->setHeader($h[0], $h[1]);
			}
		}
	}

	$fetch = $client->execute();

	/* @$client->fetch( $url ); */
	if( $fetch->isGood() ) {
		return $client;
	} else {
		wfDebugLog( 'RSS', 'error fetching $url: ' . $fetch->getMessage() );
	}
}

/**
 * Parse an HTTP response object into an RSS object.
 * @param $resp Object: an HTTP response object (see Snoopy)
 * @return parsed RSS object (see RSSParse) or false
 */
function _response_to_rss( $resp ) {
	global $wgRSSOutputEncoding, $wgRSSInputEncoding, $wgRSSDetectEncoding;
	$rss = new RSSData($resp);

	// if RSS parsed successfully
	if ( $rss && !$rss->ERROR ) {
		// find Etag and Last-Modified

		return $rss;
	} else { // else construct error message
		$errormsg = 'Failed to parsex RSS file.';

		if ( $rss ) {
			$errormsg .= ' (' . $rss->ERROR . ')';
		}
		$RSS_FETCH_ERROR = $errormsg;
		wfDebugLog( 'RSS', 'error!' . $errormsg );

		return false;
	} // end if ( $rss && !$rss->ERROR )
}

