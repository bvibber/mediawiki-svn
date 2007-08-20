<?php

/**
 * Various HTTP related functions
 */
class Http {
	private static $data = array();

	static function get( $url, $timeout = 'default' ) {
		return Http::request( "GET", $url, $timeout );
	}

	static function post( $url, $timeout = 'default' ) {
		return Http::request( "POST", $url, $timeout );
	}

	/**
	* Add to data sent with the next request. Applies to GET and POST.
	* @param string $name Must be valid for inclusion in a url query.
	* @param string $value Should not be pre-urlencoded.
	*/
	static function addNameValuePair($name, $value)
	{
		Http::$data[$name] = urlencode($value);
	}

	/**
	* Forget previous calls to addNameValuePair.
	*/
	static function resetNameValuePairs()
	{
		Http::$data = array();
	}

	private static function makeQueryString()
	{
		if(count(Http::$data))
		{
			list($name, $val) = each(Http::$data);
			$out = $name . '=' . $val;
			while(list($name, $val) = each(Http::$data))
			{
				$out .= '&' . $name . '=' . $val;
			}
			return $out;
		} else {
			return '';
		}
	}

	/**
	 * Get the contents of a file by HTTP
	 *
	 * if $timeout is 'default', $wgHTTPTimeout is used
	 */
	static function request( $method, $url, $timeout = 'default' ) {
		global $wgHTTPTimeout, $wgHTTPProxy, $wgVersion, $wgTitle;

		# Use curl if available
		if ( function_exists( 'curl_init' ) ) {
			$c = curl_init( $url );
			if ( wfIsLocalURL( $url ) ) {
				curl_setopt( $c, CURLOPT_PROXY, 'localhost:80' );
			} else if ($wgHTTPProxy) {
				curl_setopt($c, CURLOPT_PROXY, $wgHTTPProxy);
			}

			if ( $timeout == 'default' ) {
				$timeout = $wgHTTPTimeout;
			}
			curl_setopt( $c, CURLOPT_TIMEOUT, $timeout );
			curl_setopt( $c, CURLOPT_USERAGENT, "MediaWiki/$wgVersion" );
			if ( $method == 'POST' )
			{
				curl_setopt( $c, CURLOPT_POST, true );
				if(count(Http::$data))
				{
					curl_setopt( $c, CURLOPT_POSTFIELDS, Http::makeQueryString() );
				}
			} else if ( $method == 'GET' && count(Http::$data))
			{
				curl_setopt( $c, CURLOPT_URL, $url . '?' . Http::makeQueryString() );
			} else {
				curl_setopt( $c, CURLOPT_CUSTOMREQUEST, $method );
			}

			# Set the referer to $wgTitle, even in command-line mode
			# This is useful for interwiki transclusion, where the foreign
			# server wants to know what the referring page is.
			# $_SERVER['REQUEST_URI'] gives a less reliable indication of the
			# referring page.
			if ( is_object( $wgTitle ) ) {
				curl_setopt( $c, CURLOPT_REFERER, $wgTitle->getFullURL() );
			}

			ob_start();
			curl_exec( $c );
			$text = ob_get_contents();
			ob_end_clean();

			# Don't return the text of error messages, return false on error
			if ( curl_getinfo( $c, CURLINFO_HTTP_CODE ) != 200 ) {
				$text = false;
			}
			curl_close( $c );
		} else {
			# Otherwise use file_get_contents, or its compatibility function from GlobalFunctions.php
			# This may take 3 minutes to time out, and doesn't have local fetch capabilities
			$httpOpts = array( 'method' => $method );
			if(count(Http::$data))
			{
				if($method == 'POST')
				{
					$httpOpts['header'] = 'Content-type: application/x-www-form-urlencoded';
					$httpOpts['content'] = Http::makeQueryString();
				} else if($method == 'GET')
				{
					$url .= '?' . Http::makeQueryString();
				}
			}

			$opts = array('http' => $httpOpts);
			$ctx = stream_context_create($opts);

			$url_fopen = ini_set( 'allow_url_fopen', 1 );
			$text = file_get_contents( $url, false, $ctx );
			ini_set( 'allow_url_fopen', $url_fopen );
		}

		Http::resetNameValuePairs();

		return $text;
	}

	/**
	 * Check if the URL can be served by localhost
	 */
	static function isLocalURL( $url ) {
		global $wgCommandLineMode, $wgConf;
		if ( $wgCommandLineMode ) {
			return false;
		}

		// Extract host part
		$matches = array();
		if ( preg_match( '!^http://([\w.-]+)[/:].*$!', $url, $matches ) ) {
			$host = $matches[1];
			// Split up dotwise
			$domainParts = explode( '.', $host );
			// Check if this domain or any superdomain is listed in $wgConf as a local virtual host
			$domainParts = array_reverse( $domainParts );
			for ( $i = 0; $i < count( $domainParts ); $i++ ) {
				$domainPart = $domainParts[$i];
				if ( $i == 0 ) {
					$domain = $domainPart;
				} else {
					$domain = $domainPart . '.' . $domain;
				}
				if ( $wgConf->isLocalVHost( $domain ) ) {
					return true;
				}
			}
		}
		return false;
	}
}
