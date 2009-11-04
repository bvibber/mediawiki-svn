/**
 * This plugin provides a way to build a user interface around a textarea. You
 * can build the UI from a confguration..
 * 	$j( 'div#edittoolbar' ).wikiEditor(
 * 		{ 'modules': { 'toolbar': { ... config ... } } }
 * 	);
 * ...and add modules after it's already been initialized...
 * 	$j( 'textarea#wpTextbox1' ).wikiEditor(
 * 		'addModule', 'toc', { ... config ... }
 *	);
 * ...using the API, which is still be finished.
 */
( function( $ ) {

$.wikiEditor = {
	'modules': {},
	'instances': [],
	/**
	 * For each browser name, an array of conditions that must be met are supplied in [operaton, value] form where
	 * operation is a string containing a JavaScript compatible binary operator and value is either a number to be
	 * compared with $.browser.versionNumber or a string to be compared with $.browser.version
	 */
	'browsers': {
		'ltr': {
			'msie': [['>=', 7]],
			'firefox': [
				['>=', 2],
				['!=', '2.0'],
				['!=', '2.0.0.1'],
				['!=', '2.0.0.2'],
				['!=', '2.0.0.3'],
				['!=', '2.0.0.4']
			],
			'opera': [['>=', 9.6]],
			'safari': [['>=', 3.1]]
		},
		'rtl': {
			'msie': [['>=', 8]],
			'firefox': [
				['>=', 2],
				['!=', '2.0'],
				['!=', '2.0.0.1'],
				['!=', '2.0.0.2'],
				['!=', '2.0.0.3'],
				['!=', '2.0.0.4']
			],
			'opera': [['>=', 9.6]],
			'safari': [['>=', 3.1]]
		}
	},
	/**
	 * Path to images - this is a bit messy, and it would need to change if
	 * this code (and images) gets moved into the core - or anywhere for
	 * that matter...
	 */
	imgPath : wgScriptPath + '/extensions/UsabilityInitiative/images/wikiEditor/'
};

$.wikiEditor.isSupportKnown = function() {
	return $.browser.name in $.wikiEditor.browsers[$( 'body.rtl' ).size() ? 'rtl' : 'ltr'];
};
$.wikiEditor.isSupported = function() {
	if ( !$.wikiEditor.isSupportKnown ) {
		// Assume good faith :)
		return true;
	}
	var browser = $.wikiEditor.browsers[$( 'body.rtl' ).size() ? 'rtl' : 'ltr'][$.browser.name];
	for ( condition in browser ) {
		var op = browser[condition][0];
		var val = browser[condition][1];
		if ( typeof val == 'string' ) {
			if ( !( eval( '$.browser.version' + op + '"' + val + '"' ) ) ) {
				return false;
			}
		} else if ( typeof val == 'number' ) {
			if ( !( eval( '$.browser.versionNumber' + op + val ) ) ) {
				return false;
			}
		}
	}
	return true;
};
// Wraps gM from js2, but allows raw text to supercede
$.wikiEditor.autoMsg = function( object, property ) {
	// Accept array of possible properties, of which the first one found will be used
	if ( typeof property == 'object' ) {
		for ( i in property ) {
			if ( property[i] in object || property[i] + 'Msg' in object ) {
				property = property[i];
				break;
			}
		}
	}
	if ( property in object ) {
		return object[property];
	} else if ( property + 'Msg' in object ) {
		return gM( object[property + 'Msg'] );
	} else {
		return '';
	}
};

$.wikiEditor.fixOperaBrokenness = function( s ) {
	// This function works around Opera's
	// broken newline handling in textareas.
	// .val() has \n while selection functions
	// treat newlines as \r\n
	
	if ( typeof $.isOperaBroken == 'undefined' && $.wikiEditor.instances.length > 0 ) {
		// Create a textarea inside a div
		// with zero area, to hide it properly
		var div = $( '<div />' )
			.height( 0 )
			.width( 0 )
			.insertBefore( $.wikiEditor.instances[0] );
		var textarea = $( '<textarea />' )
			.height( 0 )
			.appendTo( div )
			.val( "foo\r\nbar" );
		
		// Try to search&replace bar --> BAR
		var index = textarea.val().indexOf( 'bar' );
		textarea.select();
		textarea.setSelection( index, index + 3 );
		textarea.encapsulateSelection( '', 'BAR', '', false, true );
		if ( textarea.val().substr( -4 ) != 'BARr' )
			$.isOperaBroken = false;
		else
			$.isOperaBroken = true;
		div.remove();
	}
	if ( $.isOperaBroken )
		s = s.replace( /\n/g, "\r\n" );
	return s;
};

$.fn.wikiEditor = function() {

/* Initialization */

// The wikiEditor context is stored in the element, so when this function
// gets called again we can pick up where we left off
var context = $(this).data( 'wikiEditor-context' );


if ( typeof context == 'undefined' ) {
	/* Construction */
	var instance = $.wikiEditor.instances.length;
	context = { '$textarea': $(this), 'modules': {}, 'data': {}, 'instance': instance };
	$.wikiEditor.instances[instance] = $(this);
	
	// Encapsulate the textarea with some containers for layout
	$(this)
		.wrap( $( '<div></div>' ).addClass( 'wikiEditor-ui' ).attr( 'id', 'wikiEditor-ui' ) )
		.wrap( $( '<div></div>' ).addClass( 'wikiEditor-ui-bottom' ).attr( 'id', 'wikiEditor-ui-bottom' ) )
		.wrap( $( '<div></div>' ).addClass( 'wikiEditor-ui-text' ).attr( 'id', 'wikiEditor-ui-text' ) );
	
	// Get a reference to the outer container
	context.$ui = $(this).parent().parent().parent();
	context.$ui.after( $( '<div style="clear:both;"></div>' ) );
	// Attach a container in the top
	context.$ui.prepend( $( '<div></div>' ).addClass( 'wikiEditor-ui-top' ).attr( 'id', 'wikiEditor-ui-top' ) );
	
	// Some browsers don't restore the cursor position on refocus properly
	// Do it for them
	$(this)
		.focus( function() {
			var pos = $(this).data( 'wikiEditor-cursor' );
			if ( pos )
				$(this).setSelection( pos[0], pos[1] );
			$(this).data( 'wikiEditor-cursor', false );
		})
		.blur( function() {
			$(this).data( 'wikiEditor-cursor', $(this).getCaretPosition( true ) );
		});
	
	// Create a set of standard methods for internal and external use
	context.api = {
		/**
		 * Accepts either a string of the name of a module to add without any
		 * additional configuration parameters, or an object with members keyed with
		 * module names and valued with configuration objects
		 */
		addModule: function( context, data ) {
			// A safe way of calling an API function on a module
			function callModuleApi( module, call, data ) {
				if (
					module in $.wikiEditor.modules &&
					'fn' in $.wikiEditor.modules[module] &&
					call in $.wikiEditor.modules[module].fn
				) {
					$.wikiEditor.modules[module].fn[call]( context, data );
				}
			}
			if ( typeof data == 'string' ) {
				callModuleApi( data, 'create', {} );
			} else if ( typeof data == 'object' ) {
				for ( module in data ) {
					if ( typeof module == 'string' ) {
						callModuleApi( module, 'create', data[module] );
					}
				}
			}
		}
	};
	// Allow modules to extend the API
	for ( module in $.wikiEditor.modules ) {
		if ( 'api' in $.wikiEditor.modules[module] ) {
			for ( call in $.wikiEditor.modules[module].api ) {
				// Modules may not overwrite existing API functions - first come,
				// first serve
				if ( !( call in context.api ) ) {
					context.api[call] = $.wikiEditor.modules[module].api[call];
				}
			}
		}
	}
}

// If there was a configuration passed, it's assumed to be for the addModule
// API call
if ( arguments.length > 0 && typeof arguments[0] == 'object' ) {
	context.api.addModule( context, arguments[0] );
} else {
	// Since javascript gives arguments as an object, we need to convert them
	// so they can be used more easily
	arguments = $.makeArray( arguments );
	if ( arguments.length > 0 ) {
		// Handle API calls
		var call = arguments.shift();
		if ( call in context.api ) {
			context.api[call]( context, arguments[0] == undefined ? {} : arguments[0] );
		}
	}
}

// Store the context for next time, and support chaining
return $(this).data( 'wikiEditor-context', context );

};})(jQuery);