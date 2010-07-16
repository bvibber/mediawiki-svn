/**
 * Localization system
 */

window.mw.msg = new ( function() {
	
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
} )();