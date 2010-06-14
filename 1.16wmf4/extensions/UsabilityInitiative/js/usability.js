/**
 * Common version-independent functions
 */

if ( typeof mw == 'undefined' ) {
	mw = {};
}

mw.usability = {
	messages: {}
}

/**
 * Load jQuery UI if requested, otherwise just execute the callback immediately.
 * This is a dirty hack used to work around a bug in older versions of Netscape,
 * which crash when trying to parse jQuery UI
 */
mw.usability.load = function( deps, callback ) {
	// If $j.ui is in deps, load jQuery UI
	var needJUI = false;
	for ( var i = 0; i < deps.length && !needJUI; i++ ) {
		if ( deps[i] == '$j.ui' ) {
			needJUI = true;
		}
	}
	if ( needJUI && typeof $j.ui == 'undefined' ) {
		$j.getScript( wgScriptPath + '/extensions/UsabilityInitiative/js/js2stopgap/jui.combined.min.js', callback );
	} else {
		callback();
	}
};

/**
 * Add messages to a local message table
 */
mw.usability.addMessages = function( messages ) {
	for ( var key in messages ) {
		this.messages[key] = messages[key];
	}
};

/**
 * Get a message
 */
mw.usability.getMsg = function( key, args ) {
	if ( !( key in this.messages ) ) {
		return '[' + key + ']';
	}
	var msg = this.messages[key];
	if ( typeof args == 'object' || typeof args == 'array' ) {
		for ( var argKey in args ) {
			msg = msg.replace( '\$' + (parseInt( argKey ) + 1), args[argKey] );
		}
	} else if ( typeof args == 'string' || typeof args == 'number' ) {
		msg = msg.replace( '$1', args );
	}
	return msg;
};
