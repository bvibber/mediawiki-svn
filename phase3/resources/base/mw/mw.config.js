/**
 * Configuration system
 */

window.mw.config = new ( function() {
	
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
} )();