/**
 * Core MediaWiki JavaScript Library
 */

window.mw = {
	
	/* Public Members */
	
	/**
	 * Localization system
	 */
	'msg': new ( function() {
		
		/* Private Members */
		
		var messages = {};
		
		/* Public Functions */
		
		this.set = function( keys, value ) {
			if ( typeof key === 'object' ) {
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
		
		var self = this;
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
		
		/* Private Functions */
		
		/**
		 * Processes the queue, loading and executing when things when ready.
		 */
		function work() {
			var batch = [];
			var head = document.getElementById( 'head' )[0];
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
								delete queue[q].requirements[r];
								break;
						}
						// If all pending requirements have been satisfied, we're ready to execute the callback
						if ( queue[q].requirements.length == 0 ) {
							queue[q].callback();
							// Clean up the queue
							delete queue[q];
						}
					}
				}
			}
			// Handle the batch
			if ( batch.length ) {
				// It may be more performant to do this with an Ajax call, but that's limited to same-domain, so we can
				// either auto-detect (if there really is any benefit) or just use this method, which is safe either way
				var script = document.createElement( 'script' );
				script.type = 'text/javascript';
				script.src = 'load.php?modules=' + batch.join( '|' );
				// Good browsers
				script.onload = work;
				// Bad browsers (IE 6 & 7)
				script.onreadystatechange = function() {
					if ( this.readyState == 'complete' ) {
						work();
					}
				}
				head.appendChild( script );
			}
		}
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
					// Automatically register as loaded, assuming no requirements
					registry[name] = { 'state': 'loaded' };
				} else {
					// If it was already registered, let's make sure we mark it as loaded
					registry[name].state = 'loaded';
				}
				// Attach script
				registry[name].script = script;
				// Attach style, if any
				if ( typeof style === 'string' ) {
					implementations[name].style = style;
				}
				// Attach localization, if any
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
					work();
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
				work();
			}
		};
	} )()
};
