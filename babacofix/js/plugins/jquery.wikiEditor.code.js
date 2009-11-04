/**
 * Code Module for wikiEditor
 */
( function( $ ) { $.wikiEditor.modules.code = {

/**
 * API accessible functions
 */
api: {
	//
},
/**
 * Internally used functions
 */
fn: {
	// Create the iframe and set things up
	create: function( context, config ) {
		context.$iframe = $( '<iframe></iframe>' )
			.attr( 'frameborder', 0 )
			.css( {
				'backgroundColor': 'white',
				'width': '100%',
				'height': context.$textarea.height(),
				'display': 'none'
			})
			.insertAfter( context.$textarea );
		context.$iframe[0].contentWindow.document.open();
		context.$iframe[0].contentWindow.document.write(
			'<html><head><title>wikiEditor</title></head><body style="margin:0;padding:0;width:100%;height:100%;">' +
			'<pre style="margin:0;padding:0;width:100%;height:100%;white-space:pre-wrap;"></pre></body></html>'
		);
		context.$iframe[0].contentWindow.document.close();
		context.$iframe[0].contentWindow.document.designMode = 'on';
		context.modules.code = {
			'editor': {
				'container': context.$iframe.contents().find( 'body > pre' ),
				'active': false,
				'config': {}
			}
		};
		// Make it happen!
		$.wikiEditor.modules.code.fn.active( context, true );
	},
	// Set config / get config
	config: function( context, config ) {
		if ( config != undefined ) {
			$.extend( context.modules.code.editor.config, config );
		} else {
			return context.modules.code.editor.config;
		}
	},
	// Set code / get code to whichever control (textarea or iframe) is active
	code: function( context, code ) {
		if ( code !== undefined ) {
			// Set
			context.modules.code.editor.active ?
				context.modules.code.editor.container.text( code ) : context.$textarea.val( code );
		} else {
			// Get
			context.modules.code.editor.active ?
				context.modules.code.editor.container.text() : context.$textarea.val();
		}
	},
	// Lock / unlock / get locked state of all editing controls
	locked: function( context, value ) {
		if ( value !== undefined ) {
			if ( value ) {
				context.$textarea.attr( 'readonly', true );
				if ( context.$iframe.css( 'display' ) != 'none' ) { // prevent exception on FF + iframe with display:none
					context.$iframe.attr( 'readonly', true );
				}
			} else {
				context.$textarea.attr( 'readonly', false );
				if ( context.$iframe.css( 'display' ) != 'none' ) { // prevent exception on FF + iframe with display:none
					context.$iframe.attr( 'readonly', false );
				}
			}
		} else {
			return context.modules.code.editor.active ?
				context.$iframe.attr( 'readonly' ) : context.$textarea.attr( 'readonly' );
		}
	},
	// Activate / deactivate / get active state of the iframe
	active: function( context, value ) {
		if ( value !== undefined ) {
			if ( value && !context.modules.code.editor.active ) {
				context.$textarea.attr( 'disabled', true );
				context.modules.code.editor.container.text( context.$textarea.val() );
				context.$textarea.hide();
				context.$iframe.show();
			} else if ( !value && context.modules.code.editor.active ) {
				context.$textarea.attr( 'disabled', false );
				context.$textarea.val( context.modules.code.editor.container.text() );
				context.$textarea.show();
				context.$iframe.hide();
			}
		} else {
			return context.modules.code.editor.active;
		}
	}
}

}; } ) ( jQuery );