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
(function($) { $.wikiEditor = { 'modules': {} }; $.fn.wikiEditor = function() {

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
			context.api[call]( arguments );
		}
		// Store the context for next time and return
		return $(this).data( 'context', context );
	}
	// Nothing to do, just return
	return $(this);
}

/* Construction */

context = { '$textarea': $(this), 'modules': {}, 'data': {} };
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
	addModule: function() {
		if ( arguments.length >= 1 && arguments[0].length >= 1 ) {
			var module = arguments[0][0];
			var configuration = ( arguments[0][1] ? arguments[0][1] : {} );
			// Check if the module is supported and that there's a create
			// method available for it
			if (
				module in $.wikiEditor.modules &&
				'create' in $.wikiEditor.modules[module]
			) {
				$.wikiEditor.modules[module].create( context, configuration );
			}
		}
	}
};
// Each browser seems to do this differently, so let's keep our editor
// consistent by allways starting at the begining
context.$textarea.scrollToCaretPosition( 0 );
// If there was a configuration passed, we can get started adding
// modules right away - which is done using the same API that could be used
// explicitly by the user
if ( arguments.length > 0 && typeof arguments[0] == 'object' ) {
	if ( 'modules' in arguments[0] ) {
		for ( module in arguments[0].modules ) {
			context.api.addModule( [module, arguments[0].modules[module]] );
		}
	}
}
// Store the context for next time, and support chaining
return $(this).data( 'context', context );;

};})(jQuery);