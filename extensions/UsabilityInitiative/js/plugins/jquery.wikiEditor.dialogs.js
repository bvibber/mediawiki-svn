/**
 * Extend the RegExp object with an escaping function
 * From http://simonwillison.net/2006/Jan/20/escape/
 */
RegExp.escape = function( s ) { return s.replace(/([.*+?^${}()|\/\\[\]])/g, '\\$1'); };

/**
 * Dialog Module for wikiEditor
 */
( function( $ ) { $.wikiEditor.modules.dialogs = {

/**
 * API accessible functions
 */
api: {
	addDialog: function( context, data ) {
		$.wikiEditor.modules.dialogs.fn.create( context, { 'modules': data } )
	},
	openDialog: function( context, data ) {
		if ( data.dialog in $.wikiEditor.modules.dialogs.modules ) {
			$( '#' + $.wikiEditor.modules.dialogs.modules[data.dialog].id ).dialog( 'open' );
		}
	},
	closeDialog: function( context, data ) {
		if ( data.dialog in $.wikiEditor.modules.dialogs.modules ) {
			$( '#' + $.wikiEditor.modules.dialogs.modules[data.dialog].id ).dialog( 'close' );
		}
	}
},
/**
 * Internally used functions
 */
fn: {
	/**
	 * Creates a dialog module within a wikiEditor
	 * 
	 * @param {Object} context Context object of editor to create module in
	 * @param {Object} config Configuration object to create module from
	 */
	create: function( context, config ) {
		// Add modules
		for ( module in config ) {
			$.wikiEditor.modules.dialogs.modules[module] = config[module];
		}
		// Build out modules immediately
		for ( module in $.wikiEditor.modules.dialogs.modules ) {
			var module = $.wikiEditor.modules.dialogs.modules[module];
			// Only create the dialog if it doesn't exist yet
			if ( $( '#' + module.id ).size() == 0 ) {
				var configuration = module.dialog;
				// Add some stuff to configuration
				configuration.bgiframe = true;
				configuration.autoOpen = false;
				configuration.modal = true;
				configuration.title = $.wikiEditor.autoMsg( module, 'title' );
				// Transform messages in keys
				// Stupid JS won't let us do stuff like
				// foo = { gM ('bar'): baz }
				configuration.newButtons = {};
				for ( msg in configuration.buttons )
					configuration.newButtons[gM( msg )] = configuration.buttons[msg];
				configuration.buttons = configuration.newButtons;
				// Create the dialog <div>
				$( '<div /> ' )
					.attr( 'id', module.id )
					.html( module.html )
					.data( 'context', context )
					.appendTo( $( 'body' ) )
					.each( module.init )
					.dialog( configuration );
			}
		}
	}
},
'modules': {}

}; } ) ( jQuery );
