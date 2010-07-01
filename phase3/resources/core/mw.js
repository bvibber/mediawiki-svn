/**
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

/**
 * Core MediaWiki JavaScript Library
 */

window.mw = $.extend( typeof window.mw === 'undefined' ? {} : window.mw, {
	// Core stuff
} );