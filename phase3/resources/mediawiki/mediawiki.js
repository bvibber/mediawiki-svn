/*
 * JavaScript backwards-compatibility and support
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
// Add array comparison functionality
if ( typeof Array.prototype.compare === 'undefined' ) { 
	Array.prototype.compare = function( against ) {
		if ( this.length != against.length ) {
			return false;
		}
		for ( var i = 0; i < against.length; i++ ) {
			if ( this[i].compare ) { 
				if ( !this[i].compare( against[i] ) ) {
					return false;
				}
			}
			if ( this[i] !== against[i] ) {
				return false;
			}
		}
		return true;
	};
}

/*
 * Core MediaWiki JavaScript Library
 */
// Attach to window
window.mediaWiki = new ( function( $ ) {
	
	/* Constants */
	
	// This will not change until we are 100% ready to turn off legacy globals
	const LEGACY_GLOBALS = true;
	
	/* Members */
	
	this.legacy = LEGACY_GLOBALS ? window : {};
	
	/* Methods */
	
	/*
	 * Dummy function which in debug mode can be replaced with a function that does something clever
	 */
	this.log = function() { };
	/*
	 * An object which allows single and multiple existence, setting and getting on a list of key / value pairs
	 */
	this.config = new ( function() {
		
		/* Private Members */
		
		var that = this;
		// List of configuration values - in legacy mode these configurations were ALL in the global space
		var values = LEGACY_GLOBALS ? window : {};
		
		/* Public Methods */
		
		/**
		 * Sets one or multiple configuration values using a key and a value or an object of keys and values
		 */
		this.set = function( keys, value ) {
			if ( typeof keys === 'object' ) {
				for ( var k in keys ) {
					values[k] = keys[k];
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
			} else if ( typeof keys === 'string' ) {
				if ( typeof values[keys] === 'undefined' ) {
					return typeof fallback !== 'undefined' ? fallback : null;
				} else {
					return values[keys];
				}
			} else {
				return values;
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
	} )();
	/*
	 * Localization system
	 */
	this.msg = new ( function() {
		
		/* Private Members */
		
		var that = this;
		// List of localized messages
		var messages = {};
		
		/* Public Methods */
		
		this.set = function( keys, value ) {
			if ( typeof keys === 'object' ) {
				for ( var k in keys ) {
					messages[k] = keys[k];
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
				for ( var a = 0; a < args.length; a++ ) {
					msg = msg.replace( '\$' + ( parseInt( a ) + 1 ), args[a] );
				}
			} else if ( typeof args == 'string' || typeof args == 'number' ) {
				msg = msg.replace( '$1', args );
			}
			return msg;
		};
	} )();
	/*
	 * Client-side module loader which integrates with the MediaWiki ResourceLoader
	 */
	this.loader = new ( function() {

		/* Private Members */
		
		var that = this;
		/*
		 * Mapping of registered modules
		 * 
		 * Format:
		 * 	{
		 * 		'moduleName': {
		 * 			'needs': ['required module', 'required module', ...],
		 * 			'state': 'registered', 'loading', 'loaded', 'ready', or 'error'
		 * 			'script': function() {},
		 * 			'style': 'css code string',
		 * 			'messages': { 'key': 'value' }
		 * 		}
		 * 	}
		 */
		var registry = {};
		// List of modules which will be loaded as when ready
		var batch = [];
		// List of modules to be loaded
		var queue = [];
		// List of callback functions waiting for modules to be ready to be called
		var jobs = [];
		// Flag indicating document ready has occured
		var ready = [];
		
		/* Private Methods */
		
		/**
		 * Recursively resolves dependencies and detects circular references
		 */
		function recurse( module, resolved, unresolved ) {
			unresolved[unresolved.length] = module;
			for ( var n = 0; n < registry[module].needs.length; n++ ) {
				if ( resolved.indexOf( registry[module].needs[n] ) === -1 ) {
					if ( unresolved.indexOf( registry[module].needs[n] ) !== -1 ) {
						throw new Error(
							'Circular reference detected: ' + module + ' -> ' + registry[module].needs[n]
						);
					}
					recurse( registry[module].needs[n], resolved, unresolved );
				}
			}
			resolved[resolved.length] = module;
			unresolved.splice( unresolved.indexOf( module ), 1 );
		}
		/**
		 * Gets a list of modules names that a module needs in their proper dependency order
		 * 
		 * @param mixed string module name or array of string module names
		 * @return list of dependencies
		 * @throws Error if circular reference is detected
		 */
		function resolve( module, resolved, unresolved ) {
			// Allow calling with an array of module names
			if ( typeof module === 'object' ) {
				var modules = [];
				for ( var m = 0; m < module.length; m++ ) {
					var needs = resolve( module[m] );
					for ( var n = 0; n < needs.length; n++ ) {
						modules[modules.length] = needs[n];
					}
				}
				return modules;
			} else if ( typeof module === 'string' ) {
				// Undefined modules have no needs
				if ( !( module in registry ) ) {
					return [];
				}
				var resolved = [];
				recurse( module, resolved, [] );
				return resolved;
			}
			throw new Error( 'Invalid module argument: ' + module );
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
			// Allow states to be given as a string
			if ( typeof states === 'string' ) {
				states = [states];
			}
			// If called without a list of modules, build and use a list of all modules
			var list = [];
			if ( typeof modules === 'undefined' ) {
				modules = [];
				for ( module in registry ) {
					modules[modules.length] = module;
				}
			}
			// Build a list of modules which are in one of the specified states
			for ( var s = 0; s < states.length; s++ ) {
				for ( var m = 0; m < modules.length; m++ ) {
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
				throw new Error( 'Module has not been registered yet: ' + module );
			} else if ( registry[module].state === 'registered' ) {
				throw new Error( 'Module has not been requested from the server yet: ' + module );
			} else if ( registry[module].state === 'loading' ) {
				throw new Error( 'Module has not completed loading yet: ' + module );
			} else if ( registry[module].state === 'ready' ) {
				throw new Error( 'Module has already been loaded: ' + module );
			}
			// Add style sheet to document
			if ( typeof registry[module].style === 'string' && registry[module].style.length ) {
				$( 'head' ).append( '<style type="text/css">' + registry[module].style + '</style>' );
			}
			// Add localizations to message system
			if ( typeof registry[module].messages === 'object' ) {
				mediaWiki.msg.set( registry[module].messages );
			}
			// Execute script
			try {
				registry[module].script();
				registry[module].state = 'ready';
				// Run jobs who's needs have just been met
				for ( var j = 0; j < jobs.length; j++ ) {
					if ( filter( 'ready', jobs[j].needs ).compare( jobs[j].needs ) ) {
						if ( typeof jobs[j].ready === 'function' ) {
							jobs[j].ready();
						}
						jobs.splice( j, 1 );
						j--;
					}
				}
				// Execute modules who's needs have just been met
				for ( r in registry ) {
					if ( registry[r].state == 'loaded' ) {
						if ( filter( ['ready'], registry[r].needs ).compare( registry[r].needs ) ) {
							execute( r );
						}
					}
				}
			} catch ( e ) {
				mediaWiki.log( 'Exception thrown by ' + module + ': ' + e.message );
				registry[module].state = 'error';				
				// Run error callbacks of jobs affected by this condition
				for ( var j = 0; j < jobs.length; j++ ) {
					if ( jobs[j].needs.indexOf( module ) !== -1 ) {
						if ( typeof jobs[j].error === 'function' ) {
							jobs[j].error();
						}
						jobs.splice( j, 1 );
						j--;
					}
				}
			}
		}
		/**
		 * Adds a needs to the queue with optional callbacks to be run when the needs are ready or fail
		 * 
		 * @param mixed string moulde name or array of string module names
		 * @param function ready callback to execute when all needs are ready
		 * @param function error callback to execute when any need fails
		 */
		function request( needs, ready, error ) {
			// Allow calling by single module name
			if ( typeof needs === 'string' ) {
				needs = [needs];
				if ( needs[0] in registry ) {
					for ( var n = 0; n < registry[needs[0]].needs.length; n++ ) {
						needs[needs.length] = registry[needs[0]].needs[n];
					}
				}
			}
			// Add ready and error callbacks if they were given
			if ( arguments.length > 1 ) {
				jobs[jobs.length] = {
					'needs': filter( ['undefined', 'registered', 'loading', 'loaded'], needs ),
					'ready': ready,
					'error': error
				};
			}
			// Queue up any needs that are undefined or registered
			needs = filter( ['undefined', 'registered'], needs );
			for ( var n = 0; n < needs.length; n++ ) {
				if ( queue.indexOf( needs[n] ) === -1 ) {
					queue[queue.length] = needs[n];
				}
			}
			// Work the queue
			that.work();
		}
		
		/* Public Methods */
		
		/**
		 * Requests needs from server, loading and executing when things when ready.
		 */
		this.work = function() {
			// Appends a list of modules to the batch
			for ( var q = 0; q < queue.length; q++ ) {
				// Only request modules which are undefined or registered
				if ( !( queue[q] in registry ) || registry[queue[q]].state == 'registered' ) {
					// Prevent duplicate entries
					if ( batch.indexOf( queue[q] ) === -1 ) {
						batch[batch.length] = queue[q];
						// Mark registered modules as loading
						if ( queue[q] in registry ) {
							registry[queue[q]].state = 'loading';
						}
					}
				}
			}
			// Clean up the queue
			queue = [];
			// After document ready, handle the batch
			if ( ready && batch.length ) {
				// Always order modules alphabetically to help reduce cache misses for otherwise identical content
				batch.sort();
				// Build a list of request parameters
				var base = {
					'user': mediaWiki.config.get( 'wgUserName' ) !== null,
					'skin': mediaWiki.config.get( 'skin' ),
					'lang': mediaWiki.config.get( 'wgUserLanguage' ),
					'skin': mediaWiki.config.get( 'debug' ),
				};
				// Extend request parameters with a list of modules in the batch
				var requests = [];
				if ( base.debug == '1' ) {
					for ( var b = 0; b < batch.length; b++ ) {
						requests[requests.length] = $.extend( { 'modules': batch[b] }, base );
					}
				} else {
					requests[requests.length] = $.extend( { 'modules': batch.join( '|' ) }, base );
				}
				// Clear the batch - this MUST happen before we append the script element to the body or it's
				// possible that the script will be locally cached, instantly load, and work the batch again,
				// all before we've cleared it causing each request to include modules which are already loaded
				batch = [];
				// Asynchronously append a script tag to the end of the body
				setTimeout(  function() {
					var html = '';
					for ( var r = 0; r < requests.length; r++ ) {
						// Build out the HTML
						var src = mediaWiki.config.get( 'server' ) + '/load.php?' + $.param( requests[r] );
						html += '<script type="text/javascript" src="' + src + '"></script>';
					}
					// Append script to body
					$( 'body' ).append( html );
				}, 0 )
			}
		};
		/**
		 * Registers a module, letting the system know about it and it's dependencies. loader.js files contain calls
		 * to this function.
		 */
		this.register = function( module, needs, status ) {
			// Allow multiple registration
			if ( typeof module === 'object' ) {
				for ( var n = 0; n < module.length; n++ ) {
					if ( typeof module[n] === 'string' ) {
						that.register( module[n] );
					} else if ( typeof module[n] === 'object' ) {
						that.register.apply( that, module[n] );
					}
				}
				return;
			}
			// Validate input
			if ( typeof module !== 'string' ) {
				throw new Error( 'module must be a string, not a ' + typeof module );
			}
			if ( typeof registry[module] !== 'undefined' && typeof status === 'undefined' ) {
				throw new Error( 'module already implemeneted: ' + module );
			}
			// List the module as registered
			registry[module] = { 'state': typeof status === 'string' ? status : 'registered', 'needs': [] };
			// Allow needs to be given as a function which returns a string or array
			if ( typeof needs === 'function' ) {
				needs = needs();
			}
			if ( typeof needs === 'string' ) {
				// Allow needs to be given as a single module module
				registry[module].needs = [needs];
			} else if ( typeof needs === 'object' ) {
				// Allow needs to be given as an array of module modules
				registry[module].needs = needs;
			}
		};
		/**
		 * Implements a module, giving the system a course of action to take upon loading. Results of a request for
		 * one or more modules contain calls to this function.
		 */
		this.implement = function( module, script, style, localization ) {
			// Automaically register module
			if ( typeof registry[module] === 'undefined' ) {
				that.register( module );
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
			if ( typeof registry[module] !== 'undefined' && typeof registry[module].script !== 'undefined' ) {
				throw new Error( 'module already implemeneted: ' + module );
			}
			// Mark module as loaded
			registry[module].state = 'loaded';
			// Attach components
			registry[module].script = script;
			if ( typeof style === 'string' ) {
				registry[module].style = style;
			}
			if ( typeof localization === 'object' ) {
				registry[module].messages = localization;
			}
			// Execute or queue callback
			if ( filter( ['ready'], registry[module].needs ).compare( registry[module].needs ) ) {
				execute( module );
			} else {
				request( module );
			}
		};
		/**
		 * Executes a function as soon as one or more required modules are ready
		 * 
		 * @param mixed string or array of strings of modules names the callback needs to be ready before executing
		 * @param function callback to execute when all needs are ready (optional)
		 * @param function callback to execute when if needs have a errors (optional)
		 */
		this.using = function( needs, ready, error ) {
			// Validate input
			if ( typeof needs !== 'object' && typeof needs !== 'string' ) {
				throw new Error( 'needs must be a string or an array, not a ' + typeof needs )
			}
			// Allow calling with a single need as a string
			if ( typeof needs === 'string' ) {
				needs = [needs];
			}
			// Resolve entire dependency map
			needs = resolve( needs );
			// If all needs are met, execute ready immediately
			if ( filter( ['ready'], needs ).compare( needs ) ) {
				if ( typeof ready !== 'function' ) {
					ready();
				}
			}
			// If any needs have errors execute error immediately
			else if ( filter( ['error'], needs ).length ) {
				if ( typeof error === 'function' ) {
					error();
				}
			}
			// Since some needs are not yet ready, queue up a request
			else {
				request( needs, ready, error );
			}
		};
		/**
		 * Loads one or more modules for future use
		 */
		this.load = function( modules ) {
			// Validate input
			if ( typeof modules !== 'object' && typeof modules !== 'string' ) {
				throw new Error( 'needs must be a string or an array, not a ' + typeof needs )
			}
			// Allow calling with a single need as a string
			if ( typeof modules === 'string' ) {
				modules = [modules];
			}
			// Resolve entire dependency map
			modules = resolve( modules );
			// If all modules are ready, nothing need be done
			if ( filter( ['ready'], modules ).compare( modules ) ) {
				return true;
			}
			// If any modules have errors return false
			else if ( filter( ['error'], modules ).length ) {
				return false;
			}
			// Since some modules are not yet ready, queue up a request
			else {
				request( modules );
				return true;
			}
		};
		
		/* Event Bindings */
		
		$( document ).ready( function() {
			ready = true;
			that.work();
		} );
	} )();
	
	/* Extension points */
	
	this.utilities = {};
} )( jQuery );
