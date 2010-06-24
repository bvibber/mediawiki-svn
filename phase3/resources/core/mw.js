/**
 * JavaScript Backawrds Compatibility
 */

// Make calling .indexOf() on an array work on older browsers
if ( typeof Array.prototype.indexOf === 'undefined' ) { 
	Array.prototype.indexOf = function( needle ) {
		for ( var i = 0; i < this.length; i++ ) {
			if ( this[i] === needle ) {
				return i;
			}
		}
		return -1;
	};
}

/**
 * Core MediaWiki JavaScript Library
 */

// Extend window.mw rather than overriding it. This is a temporary fix designed to prevent
// stuff from blowing up when usability.js (setting mw.usability) is run before this file.
window.mw = $.extend( typeof window.mw === 'undefined' ? {} : window.mw, {
	
	/* Public Members */
	
	/**
	 * General purpose utilities
	 */
	'util': new ( function() {
		
		/* Private Members */
		
		var that = this;
		// Decoded user agent string cache
		var client = null;
		
		/* Public Functions */
		
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
		this.buildUrlString = function( components ) {
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
		};
		/**
		 * RFC 3986 compliant URI component encoder - with identical behavior as PHP's urlencode function. Note: PHP's
		 * urlencode function prior to version 5.3 also escapes tildes, this does not. The naming here is not the same
		 * as PHP because PHP can't decide out to name things (underscores sometimes?), much less set a reasonable
		 * precedence for how things should be named in other environments. We use camelCase and action-subject here.
		 */
		this.encodeUrlComponent = function( string ) {  
			return encodeURIComponent( new String( string ) )
				.replace(/!/g, '%21')
				.replace(/'/g, '%27')
				.replace(/\(/g, '%28')
				.replace(/\)/g, '%29')
				.replace(/\*/g, '%2A')
				.replace(/%20/g, '+');
		};
		/**
		 * Builds a query string from an object with key and values
		 */
		this.buildQueryString = function( parameters ) {
			if ( typeof parameters === 'object' ) {
				var parts = [];
				for ( var p in parameters ) {
					parts[parts.length] = that.encodeUrlComponent( p ) + '=' + that.encodeUrlComponent( parameters[p] );
				}
				return parts.join( '&' );
			}
			return '';
		};
		/**
		 * Returns an object containing information about the browser
		 * 
		 * The resulting client object will be in the following format:
		 *  {
		 * 		'name': 'firefox',
		 * 		'layout': 'gecko',
		 * 		'os': 'linux'
		 * 		'version': '3.5.1',
		 * 		'versionBase': '3',
		 * 		'versionNumber': 3.5,
		 * 	}
		 */
		this.client = function() {
			// Use the cached version if possible
			if ( client === null ) {
				
				/* Configuration */
				
				// Name of browsers or layout engines we don't recognize
				var uk = 'unknown';
				// Generic version digit
				var x = 'x';
				// Strings found in user agent strings that need to be conformed
				var wildUserAgents = [ 'Opera', 'Navigator', 'Minefield', 'KHTML', 'Chrome', 'PLAYSTATION 3'];
				// Translations for conforming user agent strings
				var userAgentTranslations = [
				    // Tons of browsers lie about being something they are not
					[/(Firefox|MSIE|KHTML,\slike\sGecko|Konqueror)/, ''],
					// Chrome lives in the shadow of Safari still
					['Chrome Safari', 'Chrome'],
					// KHTML is the layout engine not the browser - LIES!
					['KHTML', 'Konqueror'],
					// Firefox nightly builds
					['Minefield', 'Firefox'],
					// This helps keep differnt versions consistent
					['Navigator', 'Netscape'],
					// This prevents version extraction issues, otherwise translation would happen later
					['PLAYSTATION 3', 'PS3'],
				];
				// Strings which precede a version number in a user agent string - combined and used as match 1 in
				// version detectection
				var versionPrefixes = [
					'camino', 'chrome', 'firefox', 'netscape', 'netscape6', 'opera', 'version', 'konqueror', 'lynx',
					'msie', 'safari', 'ps3'
				];
				// Used as matches 2, 3 and 4 in version extraction - 3 is used as actual version number
				var versionSuffix = '(\/|\;?\s|)([a-z0-9\.\+]*?)(\;|dev|rel|\\)|\s|$)';
				// Names of known browsers
				var browserNames = [
				 	'camino', 'chrome', 'firefox', 'netscape', 'konqueror', 'lynx', 'msie', 'opera', 'safari', 'ipod',
				 	'iphone', 'blackberry', 'ps3'
				];
				// Tanslations for conforming browser names
				var browserTranslations = [];
				// Names of known layout engines
				var layoutNames = ['gecko', 'konqueror', 'msie', 'opera', 'webkit'];
				// Translations for conforming layout names
				var layoutTranslations = [['konqueror', 'khtml'], ['msie', 'trident'], ['opera', 'presto']];
				// Names of known operating systems
				var osNames = ['win', 'mac', 'linux', 'sunos', 'solaris', 'iphone'];
				// Translations for conforming operating system names
				var osTranslations = [['sunos', 'solaris']];
				
				/* Fucntions */
				
				// Performs multiple replacements on a string
				function translate( source, translations ) {
					for ( var i = 0; i < translations.length; i++ ) {
						source = source.replace( translations[i][0], translations[i][1] );
					}
					return source;
				};
				
				/* Pre-processing  */
				
				var userAgent = navigator.userAgent, match, browser = uk, layout = uk, os = uk, version = x;
				if ( match = new RegExp( '(' + wildUserAgents.join( '|' ) + ')' ).exec( userAgent ) ) {
					// Takes a userAgent string and translates given text into something we can more easily work with
					userAgent = translate( userAgent, userAgentTranslations );
				}
				// Everything will be in lowercase from now on
				userAgent = userAgent.toLowerCase();
				
				/* Extraction */
				
				if ( match = new RegExp( '(' + browserNames.join( '|' ) + ')' ).exec( userAgent ) ) {
					browser = translate( match[1], browserTranslations );
				}
				if ( match = new RegExp( '(' + layoutNames.join( '|' ) + ')' ).exec( userAgent ) ) {
					layout = translate( match[1], layoutTranslations );
				}
				if ( match = new RegExp( '(' + osNames.join( '|' ) + ')' ).exec( navigator.platform.toLowerCase() ) ) {
					var os = translate( match[1], osTranslations );
				}
				if ( match = new RegExp( '(' + versionPrefixes.join( '|' ) + ')' + versionSuffix ).exec( userAgent ) ) {
					version = match[3];
				}
				
				/* Edge Cases -- did I mention about how user agent string lie? */
				
				// Decode Safari's crazy 400+ version numbers
				if ( name.match( /safari/ ) && version > 400 ) {
					version = '2.0';
				}
				// Expose Opera 10's lies about being Opera 9.8
				if ( name === 'opera' && version >= 9.8) {
					version = userAgent.match( /version\/([0-9\.]*)/i )[1] || 10;
				}
				
				/* Caching */
				
				client = {
					'browser': browser,
					'layout': layout,
					'os': os,
					'version': version,
					'versionBase': ( version !== x ? new String( version ).substr( 0, 1 ) : x ),
					'versionNumber': ( parseFloat( version, 10 ) || 0.0 )
				};
			}
			return client;
		};
		/**
		 * Checks the current browser against a support map object to determine if the browser has been black-listed or
		 * not. If the browser was not configured specifically it is assumed to work. It is assumed that the body
		 * element is classified as either "ltr" or "rtl". If neither is set, "ltr" is assumed.
		 * 
		 * A browser map is in the following format:
		 *	{
		 * 		'ltr': {
		 * 			// Multiple rules with configurable operators
		 * 			'msie': [['>=', 7], ['!=', 9]],
		 *			// Blocked entirely
		 * 			'iphone': false
		 * 		},
		 * 		'rtl': {
		 * 			// Test against a string
		 * 			'msie': [['!==', '8.1.2.3']],
		 * 			// RTL rules do not fall through to LTR rules, you must explicity set each of them
		 * 			'iphone': false
		 * 		}
		 *	}
		 * 
		 * @param Object of browser support map
		 * 
		 * @return Boolean true if browser known or assumed to be supported, false if blacklisted
		 */
		this.testClient = function( map ) {
			var client = this.client();
			// Check over each browser condition to determine if we are running in a compatible client
			var browser = map[$( 'body' ).is( '.rtl' ) ? 'rtl' : 'ltr'][client.browser];
			if ( typeof browser !== 'object' ) {
				// Unknown, so we assume it's working
				return true;
			}
			for ( var condition in browser ) {
				var op = browser[condition][0];
				var val = browser[condition][1];
				if ( val === false ) {
					return false;
				} else if ( typeof val == 'string' ) {
					if ( !( eval( 'client.version' + op + '"' + val + '"' ) ) ) {
						return false;
					}
				} else if ( typeof val == 'number' ) {
					if ( !( eval( 'client.versionNumber' + op + val ) ) ) {
						return false;
					}
				}
			}
			return true;
		};
		/**
		 * Finds the highest tabindex in use.
		 * 
		 * @return Integer of highest tabindex on the page
		 */
		this.getMaxTabIndex = function() {
			var maxTI = 0;
			$( '[tabindex]' ).each( function() {
				var ti = parseInt( $(this).attr( 'tabindex' ) );
				if ( ti > maxTI ) {
					maxTI = ti;
				}
			} );
			return maxTI;
		};
	} )(),
	/**
	 * Configuration system
	 */
	'config': new ( function() {
		
		/* Private Members */
		
		var that = this;
		// List of configuration values
		var values = {};
		
		/* Public Functions */
		
		/**
		 * Sets one or multiple configuration values using a key and a value or an object of keys and values
		 */
		this.set = function( keys, value ) {
			if ( typeof keys === 'object' ) {
				for ( var key in keys ) {
					values[key] = keys[key];
				}
			} else if ( typeof keys === 'string' && typeof value !== 'undefined' ) {
				values[keys] = value;
			}
		};
		/**
		 * Gets one or multiple configuration values using a key and an optional fallback or an array of keys
		 */
		this.get = function( keys, fallback ) {
			if ( typeof keys === 'object' ) {
				var result = {};
				for ( var k = 0; k < keys.length; k++ ) {
					if ( typeof values[keys[k]] !== 'undefined' ) {
						result[keys[k]] = values[keys[k]];
					}
				}
				return result;
			} else if ( typeof values[keys] === 'undefined' ) {
				return typeof fallback !== 'undefined' ? fallback : null;
			} else {
				return values[keys];
			}
		};
	} ),
	/**
	 * Localization system
	 */
	'msg': new ( function() {
		
		/* Private Members */
		
		var that = this;
		// List of localized messages
		var messages = {};
		
		/* Public Functions */
		
		this.set = function( keys, value ) {
			if ( typeof keys === 'object' ) {
				for ( var key in keys ) {
					messages[key] = keys[key];
				}
			} else if ( typeof keys === 'string' && typeof value !== 'undefined' ) {
				messages[keys] = value;
			}
		};
		this.get = function( key, args ) {
			if ( !( key in messages ) ) {
				return '<' + key + '>';
			}
			var msg = messages[key];
			if ( typeof args == 'object' || typeof args == 'array' ) {
				for ( var argKey in args ) {
					msg = msg.replace( '\$' + ( parseInt( argKey ) + 1 ), args[argKey] );
				}
			} else if ( typeof args == 'string' || typeof args == 'number' ) {
				msg = msg.replace( '$1', args );
			}
			return msg;
		};
	} )(),
	/**
	 * Loader system
	 */
	'loader': new ( function() {
		
		/* Private Members */
		
		var that = this;
		var server = 'load.php';
		// Mapping of registered modules
		var registry = {
			/*
			 * The contents of this object are stored in the following format
			 * 
			 * 	'moduleName': {
			 * 		'requirements': ['required module', 'required module', ...],
			 * 		'state': 'registered, loading, loaded, or ready',
			 * 		'script': function() {},
			 * 		'style': 'css code string',
			 * 		'localization': { 'key': 'value' }
			 * },
			 * 	...
			 */
		};
		// List of callbacks and their dependencies - this gets reduced each time work() is called, if possible
		var queue = [];
		// List of modules to wait to load until ready, or right away after ready
		var batch = [];
		// True after document ready occurs
		var ready = false;
		
		/* Private Functions */
		
		/**
		 * Narrows a list of requirements down to only those which are still pending (not registered or not ready)
		 */
		function pending( requirements ) {
			// Collect the names of pending modules
			var list = [];
			for ( var r = 0; r < requirements.length; r++ ) {
				var requirement = requirements[r];
				if (
					typeof registry[requirements[r]] === 'undefined' ||
					typeof registry[requirements[r]].state === 'undefined' ||
					registry[requirements[r]].state !== 'ready'
				) {
					list[list.length] = requirements[r];
				}
			}
			return list;
		}
		/**
		 * Executes a loaded but not ready module, making it ready to use
		 */
		function execute( requirement ) {
			if ( registry[requirement].state === 'loaded' ) {
				// Add style, if any
				if ( typeof registry[requirement].style === 'string' ) {
					var style = document.createElement( 'style' );
					style.type = 'text/css';
					style.innerHTML = registry[requirement].style;
				}
				// Add localizations
				if ( typeof registry[requirement].localization === 'object' ) {
					mw.msg.set( registry[requirement].localization );
				}
				// Execute script, if any
				registry[requirement].script();
				// Change state
				registry[requirement].state = 'ready';
			}
		}
		
		/* Public Functions */
		
		/**
		 * Processes the queue, loading and executing when things when ready.
		 */
		this.work = function() {
			for ( var q = 0; q < queue.length; q++ ) {
				for ( var p = 0; p < queue[q].pending.length; p++ ) {
					var requirement = queue[q].pending[p];
					// If it's not in the registry yet, we're certainly not ready to execute
					if (
						typeof registry[requirement] !== 'undefined' &&
						typeof registry[requirement].state !== 'undefined'
					) {
						// Take action, or not
						switch ( registry[requirement].state ) {
							case 'registered':
								// Load (add to batch)
								if ( batch.indexOf( requirement ) == -1 ) {
									batch[batch.length] = requirement;
								}
								break;
							case 'loading':
								// Wait (do nothing...)
								break;
							case 'loaded':
								execute( requirement );
								break;
							case 'ready':
								// This doesn't belong in the queue item's pending list
								queue[q].pending.splice( p, 1 );
								// Correct the array index
								p--;
								break;
						}
					}
				}
				// If all pending requirements have been satisfied, we're ready to execute the callback
				if ( queue[q].pending.length == 0 ) {
					queue[q].callback();
					// Clean up the queue
					queue.splice( q, 1 );
				}
			}
			// Handle the batch only when ready
			if ( batch.length && ready ) {
				// It may be more performant to do this with an Ajax call, but that's limited to same-domain, so we can
				// either auto-detect (if there really is any benefit) or just use this method, which is safe either
				// way. Also note, we're using "each" here so we only clear the batch if there was a head to add to
				$( 'head' ).each( function() {
					// Always order module alphabetically to help reduce cache misses for otherwise identical content
					batch.sort();
					// Append script to head
					$(this).append(
						$( '<script type="text/javascript"></script>' )
							.attr( 'src', mw.util.buildUrlString( {
								'path': mw.config.get( 'wgScriptPath' ) + '/load.php',
								'query': $.extend(
										// Modules are in the format foo|bar|baz|buz
										{ 'modules': batch.join( '|' ) },
										// Pass configuration values through the URL
										mw.config.get( [ 'user', 'skin', 'space', 'view', 'language' ] )
									)
							} ) )
							.load( function() {
								that.work();
							} )
					);
					// Clear the batch
					batch = [];
				} );
			}
		};
		/**
		 * Registers a module, letting the system know about it and it's dependencies. loader.js files contain calls
		 * to this function.
		 */
		this.register = function( name, requirements ) {
			// Ensure input is valid and that we're not re-registering
			if ( typeof name === 'string' && typeof registry[name] === 'undefined' ) {
				// List the module as registered
				registry[name] = { 'state': 'registered' };
				// Add requirements if any - allowing requirements to be given as a string (which is one requriement)
				if ( typeof requirements === 'string' ) {
					registry[name].requirements = [requirements];
				} else if ( typeof requirements === 'object' ) {
					registry[name].requirements = requirements;
				}
				return true;
			}
			// Registration already existed, nothing was done
			return false;
		};
		/**
		 * Implements a module, giving the system a course of action to take upon loading. Results of a request for one
		 * or more modules contain calls to this function.
		 */
		this.implement = function( name, script, style, localization ) {
			// Ensure input is valid (note: if the module was never implemented, it will automatically be now)
			if (
				typeof name === 'string' &&
				typeof script === 'function' &&
				( typeof registry[name] === 'undefined' || typeof registry[name].script === 'undefined' )
			) {
				// Allow modules to be implemented without prior registration
				if ( typeof registry[name] === 'undefined' ) {
					registry[name] = {};
				}
				// Mark module as loaded
				registry[name].state = 'loaded';
				// Attach components
				registry[name].script = script;
				if ( typeof style === 'string' ) {
					registry[name].style = style;
				}
				if ( typeof localization === 'object' ) {
					registry[name].localization = localization;
				}
				// Collect requirements, if any
				var requirements = [];
				if ( typeof registry[name].requirements === 'object' ) {
					requirements = pending( registry[name].requirements );
				}
				if ( requirements.length == 0 ) {
					// Execute right away
					execute( name );
				} else {
					// Queue it up and work the queue
					queue[queue.length] = { 'pending': requirements, 'callback': function() { execute( name ); } };
					that.work();
				}
				return true;
			}
			// Implementation was invalid or already existed, nothing was done
			return false;
		};
		/**
		 * Executes a function as soon as one or more required modules are ready.
		 */
		this.using = function( requirements, callback ) {
			// Allow requirements to be given as a string (which is one requriement)
			if ( typeof requirements === 'string' ) {
				requirements = [requirements];
			}
			var requirements = pending( requirements );
			if ( requirements.length == 0 ) {
				// Execute right away
				callback();
			} else {
				// Queue it up and work the queue
				queue[queue.length] = { 'pending': requirements, 'callback': callback };
				that.work();
			}
		};
		
		/* Event Bindings */
		
		$( document ).ready( function() {
			ready = true;
			that.work();
		} );
	} )()
} );
