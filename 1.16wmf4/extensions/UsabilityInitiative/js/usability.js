/**
 * Common version-independent functions
 */

if ( typeof mw == 'undefined' ) {
	mw = {};
}
/**
 * Base object for Usability Initiative functionality - naming is temporary
 */
mw.usability = {
	'messages': {}
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
/**
 * Checks the current browser against a support map object to determine if the browser has been black-listed or not.
 * Because these rules are often very complex, the object contains configurable operators and can check against
 * either the browser version number or string. This process also involves checking if the current browser is amung
 * those which we have configured as compatible or not. If the browser was not configured as comptible we just go on
 * assuming things will work - the argument here is to prevent the need to update the code when a new browser comes
 * to market. The assumption here is that any new browser will be built on an existing engine or be otherwise so
 * similar to another existing browser that things actually do work as expected. The merrits of this argument, which
 * is essentially to blacklist rather than whitelist are debateable, but at this point we've decided it's the more
 * "open-web" way to go.
 * 
 * This function depends on the jquery browser plugin.
 * 
 * A browser map is in the following format:
 * {
 * 		'ltr': {
 * 			// Multiple rules with configurable operators
 * 			'msie': [['>=', 7], ['!=', 9]],
 *			// Blocked entirely
 * 			'iphone': false
 * 		},
 * 		'rtl': {
 * 			// Test against a string
 * 			'msie': [['!==', '8.1.2.3']],
 * 			// RTL rules do not fall through to LTR rules, you must explicity set each of them
 * 			'iphone': false
 * 		}
 * 	}
 * 
 * The user agent string is interpreted. Common browser names are as follows:
 * 		'msie', 'firefox', 'opera', 'safari', 'chrome', 'blackberry', 'ipod', 'iphone', 'ps3', 'konqueror'
 * 
 * @param Object of browser support map
 */
mw.usability.testBrowser = function( map ) {
	// Check over each browser condition to determine if we are running in a compatible client
	var browser = map[$j( 'body' ).is( '.rtl' ) ? 'rtl' : 'ltr'][$j.browser.name];
	if ( typeof browser !== 'object' ) {
		// Unknown, so we assume it's working
		return true;
	}
	for ( var condition in browser ) {
		var op = browser[condition][0];
		var val = browser[condition][1];
		if ( val === false ) {
			return false;
		} else if ( typeof val == 'string' ) {
			if ( !( eval( '$j.browser.version' + op + '"' + val + '"' ) ) ) {
				return false;
			}
		} else if ( typeof val == 'number' ) {
			if ( !( eval( '$j.browser.versionNumber' + op + val ) ) ) {
				return false;
			}
		}
	}
	return true;
};
/**
 * Finds the highest tabindex in use.
 * 
 * @return Integer of highest tabindex on the page
 */
mw.usability.getMaxTabIndex = function() {
	var maxTI = 0;
	$j( '[tabindex]' ).each( function() {
		var ti = parseInt( $j(this).attr( 'tabindex' ) );
		if ( ti > maxTI ) {
			maxTI = ti;
		}
	} );
	return maxTI;
};
