/**
 * Core MediaWiki JavaScript Library
 */

window.mw = {
	
	/* Public Members */
	
	'util': new ( function() {
		
		/* Private Members */
		
		var that = this;
		
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
		 * RFC 3986 compliant URI component encoder
		 */
		this.urlencode = function( string ) {  
			return encodeURIComponent( string )
				.replace(/!/g, '%21')
				.replace(/'/g, '%27')
				.replace(/\(/g, '%28')
				.replace(/\)/g, '%29')
				.replace(/\*/g, '%2A');  
		}
		/**
		 * Builds a query string from an object with key and values
		 */
		this.buildQueryString = function( parameters ) {
			if ( typeof parameters === 'object' ) {
				var parts = [];
				for ( p in parameters ) {
					parts[parts.length] = that.urlencode( p ) + '=' + that.urlencode( parameters[p] );
				}
				return parts.join( '&' );
			}
			return '';
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
				for ( key in keys ) {
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
				for ( k in keys ) {
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
				for ( key in keys ) {
					messages[key] = keys[key];
				}
			} else if ( typeof keys === 'string' && typeof value !== 'undefined' ) {
				messages[keys] = value;
			}
		};
		this.get = function( key, options ) {
			if ( typeof messages[key] === 'undefined' ) {
				return '<' + key + '>';
			} else {
				// TODO: Do something clever with options
				return messages[key];
			}
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
		
		/* Event Bindings */
		
		$( document ).ready( function() {
			ready = true;
			that.work();
		} );
		
		/* Private Functions */
		
		/**
		 * Narrows a list of requirements down to only those which are still pending (not registered or not ready)
		 */
		function pending( requirements ) {
			// Collect the names of pending modules
			var list = [];
			for ( r in requirements ) {
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
			for ( q in queue ) {
				for ( p in queue[q].pending ) {
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
								queue[q].pending.splice( p );
								break;
						}
					}
				}
				// If all pending requirements have been satisfied, we're ready to execute the callback
				if ( typeof queue[q].pending.length == 0 ) {
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
					implementations[name].style = style;
				}
				if ( typeof localization === 'object' ) {
					implementations[name].localization = localization;
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
	} )()
};
