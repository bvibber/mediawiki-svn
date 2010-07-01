/**
 * Loader system
 */

window.mw.loader = new ( function() {
	
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
			if ( typeof registry[requirement].style === 'string' && registry[requirement].style.length ) {
				$( 'head' ).append( '<style type="text/css">' + registry[requirement].style + '</style>' );
			}
			// Add localizations
			if ( typeof registry[requirement].localization === 'object' ) {
				mw.msg.set( registry[requirement].localization );
			}
			// Execute script, if any
			try {
				registry[requirement].script();
			} catch( e ) {
				
			}
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
				// Queue it up (including the base module!) and work the queue
				requirements[requirements.length] = name;
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
} )();