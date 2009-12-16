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
		
		// Before the form is submitted, if in iframe mode, we need to sync the textarea and the iframe
		context.$textarea.closest( 'form' ).submit( function() {
			if ( context.modules.code.editor.active ) {
				$.wikiEditor.modules.code.fn.active( context, false );
			}
		} );
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
				// We need to properly escape any HTML entities like &amp;, &lt; and &gt; so they end up as visible
				// characters rather than actual HTML tags in the code editor container.
				context.modules.code.editor.container.text( context.$textarea.val() );
				context.$textarea.hide();
				context.$iframe.show();
				context.modules.code.editor.active = true;
			} else if ( !value && context.modules.code.editor.active ) {
				context.$textarea.attr( 'disabled', false );
				// To properly decode the HTML entities, we set the HTML rather than the val of the textarea - also, all
				// of the text will have been properly escaped with HTML entities except the <br> tags which are in the
				// place of end line characters - so we just swap those out.
				context.$textarea.html( context.modules.code.editor.container.html().replace( /\<br\>/g, "\n" ) );
				context.$textarea.show();
				context.$iframe.hide();
				context.modules.code.editor.active = false;
			}
		} else {
			return context.modules.code.editor.active;
		}
	}
}

}; } ) ( jQuery );


$j( '#wpTextbox1' ).wikiEditor( 'caretPosition' )