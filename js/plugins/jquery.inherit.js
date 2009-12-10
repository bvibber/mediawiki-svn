/*!
 * jQuery iFrame Inheritance
 *
 * Copyright (c) 2009 Eric Garside (http://eric.garside.name)
 * Dual licensed under:
 *      MIT: http://www.opensource.org/licenses/mit-license.php
 *      GPLv3: http://www.opensource.org/licenses/gpl-3.0.html
 */
( function( $ ) {

// Create a function in the Global Namespace so we can access it from the iFrame by calling parent.inherit()
this.inherit = function( child ) {
	// First, bind a copy of jQuery down into the DOM of the iFrame, so we can hook in functionality. Things may get a
	// bit confusing here, as we're creating this function in the parent, but have to set it up internally to get called
	// as if it were in the child.
	child.jQueryInherit = this.parent.jQuery;
	// Bind a special ready callback binding function, to handle the scope of responding to the document.ready hook
	// instead of the parent's document.ready
	child.jQueryInherit.fn.ready = function( fn ) {
		// Attach the listeners
		child.jQueryInherit.hooks.bindReady();
		// If the DOM is already ready
		if ( child.jQueryInherit.hooks.isReady ) {
			// Simply trigger the callback
			fn.call( child.document, child.jQueryInherit );
		} else {
			// Otherwise, remember it so we can trigger it later
			child.jQueryInherit.hooks.readyList.push( fn );
		}
		return this;
	};
	// Create a namespace for hooking some functionality to the iFrame, like document.ready detection and handling
	child.jQueryInherit.hooks = {
		isReady: false,
		readyBound: false,
		readyList: [],
		// Mimic the readyBind() function in the child, so it can set up the listeners for document.ready
		bindReady: function() {
			if ( child.jQueryInherit.hooks.readyBound ) {
				return;
			}
			child.jQueryInherit.hooks.readyBound = true;
			// Mozilla, Opera, and webkit nightlies support
			if ( child.document.addEventListener ) {
				child.document.addEventListener(
					"DOMContentLoaded",
					function() {
						child.document.removeEventListener( "DOMContentLoaded", arguments.callee, false );
						child.jQueryInherit.hooks.ready();
					},
					false
				);
			}
			// For IE
			else if ( child.document.attachEvent ) {
				// Ensure firing before onload, maybe late but safe also for iframes
				child.document.attachEvent(
					"onreadystatechange",
					function(){
						if ( child.document.readyState === "complete" ) {
							child.document.detachEvent( "onreadystatechange", arguments.callee );
							child.jQueryInherit.hooks.ready();
						}
					}
				);
				// If IE and not an iframe continually check to see if the document is ready
				if ( child.document.documentElement.doScroll && child == child.top ) {
					if ( !child.jQueryInherit.hooks.isReady ) {
						try {
							// If IE is used, use the trick by Diego Perini http://javascript.nwbox.com/IEContentLoaded/
							child.document.documentElement.doScroll( "left" );
						} catch ( error ) {
							setTimeout( arguments.callee, 0 );
							return;
						}
						// And execute any waiting functions
						child.jQueryInherit.hooks.ready();
					}
				}
			}
			// A fallback to window.onload, that will always work
			jQuery.event.add( child, "load", child.jQueryInherit.hooks.ready );
		},
		// Hook the ready trigger to fire off the hook bindings
		ready: function() {
			// Make sure the DOM is not already loaded
			if ( !child.jQueryInherit.hooks.isReady ) {
				// Remember that the DOM is ready
				child.jQueryInherit.hooks.isReady = true;
				// If there are functions bound...
				if ( child.jQueryInherit.hooks.readyList ) {
					// Execute them all
					jQuery.each( child.jQueryInherit.hooks.readyList, function() {
						this.call( child.document, child.jQueryInherit );
					} );
					// Reset the list of functions
					child.jQueryInherit.hooks.readyList = null;
				}
				// Trigger any bound ready events
				jQuery( child.document ).triggerHandler( 'ready' );
			}
		}
	};
	return child.jQuery = child.$j = function( selector, context ) {
		// Test and see if we're handling a shortcut bind for the document.ready function. This occurs when the selector
		// is a function. Because firefox throws xpconnect objects around in iFrames, the standard
		// jQuery.isFunction test returns false negatives.
		if ( selector.constructor.toString().match( /Function/ ) != null ) {
			return child.jQueryInherit.fn.ready( selector );
		}
		// Otherwise, just let the jQuery init function handle the rest. Be sure we pass in proper context of the
		// child document, or we'll never select anything useful.
		else {
			return child.jQueryInherit.fn.init( selector || this.document, context || this.document );
		}
	};
}

} )( jQuery );