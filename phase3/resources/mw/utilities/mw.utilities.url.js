/*
 * 
 */

( function( $, mw ) {

/* Extension */

$.extend( mw.utilities, {
	'url': {
		/**
		 * Builds a url string from an object containing any of the following components:
		 * 
		 * Component	Example
		 * scheme		"http"
		 * server		"www.domain.com"
		 * path			"path/to/my/file.html"
		 * query		"this=thåt" or { 'this': 'thåt' }
		 * fragment		"place_on_the_page"
		 * 
		 * Results in: "http://www.domain.com/path/to/my/file.html?this=th%C3%A5t#place_on_the_page"
		 * 
		 * All arguments to this function are assumed to be URL-encoded already, except for the
		 * query parameter if provided in object form.
		 */
		'buildUrlString': function( components ) {
			var url = '';
			if ( typeof components.scheme === 'string' ) {
				url += components.scheme + '://';
			}
			if ( typeof components.server === 'string' ) {
				url += components.server + '/';
			}
			if ( typeof components.path === 'string' ) {
				url += components.path;
			}
			if ( typeof components.query === 'string' ) {
				url += '?' + components.query;
			} else if ( typeof components.query === 'object' ) {
				url += '?' + that.buildQueryString( components.query );
			}
			if ( typeof components.fragment === 'string' ) {
				url += '#' + components.fragment;
			}
			return url;
		},
		/**
		 * RFC 3986 compliant URI component encoder - with identical behavior as PHP's urlencode function. Note: PHP's
		 * urlencode function prior to version 5.3 also escapes tildes, this does not. The naming here is not the same
		 * as PHP because PHP can't decide out to name things (underscores sometimes?), much less set a reasonable
		 * precedence for how things should be named in other environments. We use camelCase and action-subject here.
		 */
		'encodeUrlComponent': function( string ) {  
			return encodeURIComponent( new String( string ) )
				.replace(/!/g, '%21')
				.replace(/'/g, '%27')
				.replace(/\(/g, '%28')
				.replace(/\)/g, '%29')
				.replace(/\*/g, '%2A')
				.replace(/%20/g, '+');
		},
		/**
		 * Builds a query string from an object with key and values
		 */
		'buildQueryString': function( parameters ) {
			if ( typeof parameters === 'object' ) {
				var parts = [];
				for ( var p in parameters ) {
					parts[parts.length] = that.encodeUrlComponent( p ) + '=' + that.encodeUrlComponent( parameters[p] );
				}
				return parts.join( '&' );
			}
			return '';
		}
	}
} );