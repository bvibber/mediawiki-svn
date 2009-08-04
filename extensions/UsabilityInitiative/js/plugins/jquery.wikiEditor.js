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
(function($) {
$.wikiEditor = { 'modules': {}, 'instances': [] };
$.fn.wikiEditor = function() {

/* Initialization */

// The wikiEditor context is stored in the element, so when this function
// gets called again we can pick up where we left off
var context = $(this).data( 'context' );

/* API */

// The first time this is called, we expect context to be undefined, meaning
// the editing ui has not yet been, and still needs to be built, however each
// additional call after that is expected to be an API call, which contains a
// string as the first argument which corrosponds to a supported api call
if ( typeof context !== 'undefined' ) {
	// Since javascript gives arugments as an object, we need to convert them
	// so they can be used more easily
	arguments = $.makeArray( arguments );
	if ( arguments.length > 0 ) {
		// Handle API calls
		var call = arguments.shift();
		if ( call in context.api ) {
			context.api[call](
				context, arguments[0] == undefined ? {} : arguments[0]
			);
		}
		// Store the context for next time and return
		return $(this).data( 'context', context );
	}
	// Nothing to do, just return
	return $(this);
}

/* Construction */

var instance = $.wikiEditor.instances.length;
context = {
	'$textarea': $(this), 'modules': {}, 'data': {}, 'instance': instance
};
$.wikiEditor.instances[instance] = $(this);

// Encapsulate the textarea with some containers for layout
$(this)
	.wrap( $( '<div></div>' ).addClass( 'wikiEditor-ui' ) )
	.wrap( $( '<div></div>' ).addClass( 'wikiEditor-ui-bottom' ) )
	.wrap( $( '<div></div>' ).addClass( 'wikiEditor-ui-text' ) );
// Get a refrence to the outter container
context.$ui = $(this).parent().parent().parent();
// Attach a container in the top
context.$ui.prepend( $( '<div></div>' ).addClass( 'wikiEditor-ui-top' ) );
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
// Each browser seems to do this differently, so let's keep our editor
// consistent by allways starting at the begining
context.$textarea.scrollToCaretPosition( 0 );
// If there was a configuration passed, it's assumed to be for the addModule
// API call, so we can just send it on it's way right now
if ( arguments.length > 0 && typeof arguments[0] == 'object' ) {
	context.api.addModule( context, arguments[0] );
}
// Store the context for next time, and support chaining
return $(this).data( 'context', context );;

};})(jQuery);