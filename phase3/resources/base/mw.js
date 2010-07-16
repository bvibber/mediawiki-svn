/*
 * JavaScript Backwards Compatibility
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

/*
 * Core MediaWiki JavaScript Library
 */

window.mw = $.extend( typeof window.mw === 'undefined' ? {} : window.mw, {
	'prototypes': {
		/*
		 * An object which allows single and multiple existence, setting and getting on a list of key / value pairs
		 */
		'Collection': function() {
			
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
			/**
			 * Checks if one or multiple configuration fields exist
			 */
			this.exists = function( keys ) {
				if ( typeof keys === 'object' ) {
					for ( var k = 0; k < keys.length; k++ ) {
						if ( !( keys[k] in values ) ) {
							return false;
						}
					}
					return true;
				} else {
					return keys in values;
				}
			};
		},
		/*
		 * Localization system
		 */
		'Language': function() {
			
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
		},
		/*
		 * Client-side module loader which integrates with the MediaWiki ResourceLoader
		 */
		'ResourceLoader': function() {
			
			/* Private Members */
			
			var that = this;
			var server = 'load.php';
			/*
			 * Mapping of registered modules
			 * 
			 * Format:
			 * 	{
			 * 		'moduleName': {
			 * 			'needs': ['required module', 'required module', ...],
			 * 			'state': 'registered, loading, loaded, or ready',
			 * 			'script': function() {},
			 * 			'style': 'css code string',
			 * 			'localization': { 'key': 'value' }
			 * 		}
			 * 	}
			 */
			var registry = {};
			// List of callbacks waiting on dependent modules to be loaded so they can be executed
			var queue = [];
			// Until document ready, load requests will be collected in a batch queue
			var batch = [];
			// True after document ready occurs
			var ready = false;
			
			/* Private Functions */
			
			/**
			 * Gets a list of modules names that a module needs in their proper dependency order
			 * 
			 * @param string module name
			 * @return 
			 * @throws Error if circular reference is detected
			 */
			function needs( module ) {
				if ( !( module in registry ) ) {
					// Undefined modules have no needs
					return [];
				}
				var resolved = [];
				var unresolved = [];
				if ( arguments.length === 3 ) {
					// Use arguemnts on inner call
					resolved = arguments[1];
					unresolved = arguments[2];
				}
				unresolved[unresolved.length] = module;
			    for ( n in registry[module].needs ) {
			        if ( resolved.indexOf( registry[module].needs[n] ) === -1 ) {
			            if ( unresolved.indexOf( registry[module].needs[n] ) !== -1 ) {
			                throw new Error(
			                	'Circular reference detected: ' + module + ' -> ' + registry[module].needs[n]
			                );
			            }
			            needs( registry[module].needs[n], resolved, unresolved );
			        }
			    }
			    resolved[resolved.length] = module;
			    unresolved.slice( unresolved.indexOf( module ), 1 );
				if ( arguments.length === 1 ) {
				    // Return resolved list on outer call
					return resolved;
				}
			};
			/**
			 * Narrows a list of module names down to those matching a specific state. Possible states are 'undefined',
			 * 'registered', 'loading', 'loaded', or 'ready'
			 * 
			 * @param mixed string or array of strings of module states to filter by
			 * @param array list of module names to filter (optional, all modules will be used by default)
			 * @return array list of filtered module names
			 */
			function filter( states, modules ) {
				var list = [];
				if ( typeof modules === 'undefined' ) {
					modules = [];
					for ( module in registry ) {
						modules[modules.length] = module;
					}
				}
				for ( var s in states ) {
					for ( var m in modules ) {
						if (
							( states[s] == 'undefined' && typeof registry[modules[m]] === 'undefined' ) ||
							( typeof registry[modules[m]] === 'object' && registry[modules[m]].state === states[s] )
						) {
							list[list.length] = modules[m];
						}
					}
				}
				return list;
			}
			/**
			 * Executes a loaded module, making it ready to use
			 * 
			 * @param string module name to execute
			 */
			function execute( module ) {
				if ( typeof registry[module] === 'undefined' ) {
					throw new Error( 'module has not been registered: ' + module );
				}
				switch ( registry[module].state ) {
					case 'registered':
						throw new Error( 'module has not completed loading: ' + module );
						break;
					case 'loading':
						throw new Error( 'module has not completed loading: ' + module );
						break;
					case 'ready':
						throw new Error( 'module has already been loaded: ' + module );
						break;
				}
				// Add style sheet to document
				if ( typeof registry[module].style === 'string' && registry[module].style.length ) {
					$( 'head' ).append( '<style type="text/css">' + registry[module].style + '</style>' );
				}
				// Add localizations to message system
				if ( typeof registry[module].localization === 'object' ) {
					mw.msg.set( registry[module].localization );
				}
				// Execute script
				try {
					registry[module].script();
				} catch( e ) {
					mw.log( 'Exception thrown by ' + module + ': ' + e.message );
				}
				// Change state
				registry[module].state = 'ready';
				
				// Execute all modules which were waiting for this to be ready
				for ( r in registry ) {
					if ( registry[r].state == 'loaded' ) {
						if ( filter( ['ready'], registry[r].needs ).length == registry[r].needs.length ) {
							execute( r );
						}
					}
				}
			}
			/**
			 * Adds a callback and it's needs to the queue
			 * 
			 * @param array list of module names the callback needs to be ready before being executed
			 * @param function callback to execute when needs are met
			 */
			function request( needs, callback ) {
				queue[queue.length] = { 'needs': filter( ['undefined', 'registered'], needs ), 'callback': callback };
			}
			
			/* Public Functions */
			
			/**
			 * Processes the queue, loading and executing when things when ready.
			 */
			this.work = function() {
				// Appends a list of modules to the batch
				function append( modules ) {
					for ( m in modules ) {
						// Prevent requesting modules which are loading, loaded or ready
						if ( modules[m] in registry && registry[modules[m]].state == 'registered' ) {
							// Since the batch can live between calls to work until document ready, we need to make sure
							// we aren't making a duplicate entry
							if ( batch.indexOf( modules[m] ) == -1 ) {
								batch[batch.length] = modules[m];
								registry[modules[m]].state = 'loading';
							}
						}
					}
				}
				// Fill batch with modules that need to be loaded
				for ( var q in queue ) {
					append( queue[q].needs );
					for ( n in queue[q].needs ) {
						append( needs( queue[q].needs[n] ) );
					}
				}
				// After document ready, handle the batch
				if ( ready && batch.length ) {
					// Always order modules alphabetically to help reduce cache misses for otherwise identical content
					batch.sort();
					
					var base = $.extend( {},
						// Pass configuration values through the URL
						mw.config.get( [ 'user', 'skin', 'space', 'view', 'language' ] ),
						// Ensure request comes back in the proper mode (debug or not)
						{ 'debug': typeof mw.debug !== 'undefined' ? '1' : '0' }
					);
					var requests = [];
					if ( base.debug == '1' ) {
						for ( b in batch ) {
							requests[requests.length] = $.extend( { 'modules': batch[b] }, base );
						}
					} else {
						requests[requests.length] = $.extend( { 'modules': batch.join( '|' ) }, base );
					}
					// It may be more performant to do this with an Ajax call, but that's limited to same-domain, so we
					// can either auto-detect (if there really is any benefit) or just use this method, which is safe
					setTimeout(  function() {
						// Clear the batch - this MUST happen before we append the script element to the body or it's
						// possible that the script will be locally cached, instantly load, and work the batch again,
						// all before we've cleared it causing each request to include modules which are already loaded
						batch = [];
						var html = '';
						for ( r in requests ) {
							// Build out the HTML
							var src = mw.util.buildUrlString( {
								'path': mw.config.get( 'wgScriptPath' ) + '/load.php',
								'query': requests[r]
							} );
							html += '<script type="text/javascript" src="' + src + '"></script>';
						}
						// Append script to head
						$( 'head' ).append( html );
					}, 0 )
				}
			};
			/**
			 * Registers a module, letting the system know about it and it's dependencies. loader.js files contain calls
			 * to this function.
			 */
			this.register = function( name, needs ) {
				// Validate input
				if ( typeof name !== 'string' ) {
					throw new Error( 'name must be a string, not a ' + typeof name );
				}
				if ( typeof registry[name] !== 'undefined' ) {
					throw new Error( 'module already implemeneted: ' + name );
				}
				// List the module as registered
				registry[name] = { 'state': 'registered', 'needs': [] };
				// Allow needs to be given as a function which returns a string or array
				if ( typeof needs === 'function' ) {
					needs = needs();
				}
				if ( typeof needs === 'string' ) {
					// Allow needs to be given as a single module name
					registry[name].needs = [needs];
				} else if ( typeof needs === 'object' ) {
					// Allow needs to be given as an array of module names
					registry[name].needs = needs;
				}
			};
			/**
			 * Implements a module, giving the system a course of action to take upon loading. Results of a request for
			 * one or more modules contain calls to this function.
			 */
			this.implement = function( name, script, style, localization ) {
				// Automaically register module
				if ( typeof registry[name] === 'undefined' ) {
					that.register( name, needs );
				}
				// Validate input
				if ( typeof script !== 'function' ) {
					throw new Error( 'script must be a function, not a ' + typeof script );
				}
				if ( typeof style !== 'undefined' && typeof style !== 'string' ) {
					throw new Error( 'style must be a string, not a ' + typeof style );
				}
				if ( typeof localization !== 'undefined' && typeof localization !== 'object' ) {
					throw new Error( 'localization must be an object, not a ' + typeof localization );
				}
				if ( typeof registry[name] !== 'undefined' && typeof registry[name].script !== 'undefined' ) {
					throw new Error( 'module already implemeneted: ' + name );
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
				// Execute or queue callback
				if ( filter( ['ready'], registry[name].needs ).length == registry[name].needs.length ) {
					execute( name );
				} else {
					request( registry[name].needs, function() { execute( name ); } );
				}
			};
			/**
			 * Executes a function as soon as one or more required modules are ready
			 * 
			 * @param mixed string or array of strings of modules names the callback needs to be ready before executing
			 * @param function callback to execute when all needs are met
			 */
			this.using = function( needs, callback ) {
				// Validate input
				if ( typeof needs !== 'object' && typeof needs !== 'string' ) {
					throw new Error( 'needs must be a string or an array, not a ' + typeof needs )
				}
				if ( typeof callback !== 'function' ) {
					throw new Error( 'callback must be a function, not a ' + typeof callback )
				}
				if ( typeof needs === 'string' ) {
					needs = [needs];
				}
				// Execute or queue callback
				if ( filter( ['ready'], needs ).length == needs.length ) {
					callback();
				} else {
					request( needs, callback );
				}
			};
			
			/* Event Bindings */
			
			$( document ).ready( function() {
				ready = true;
				that.work();
			} );
		}
	}
} );
/*
 * Read-write access to site and user configurations
 */
mw.config = new mw.prototypes.Collection();
/*
 * MediaWiki ResourceLoader client
 */
mw.loader = new mw.prototypes.ResourceLoader();
/*
 * Provides read-write access to localizations
 */
mw.msg = new mw.prototypes.Language();