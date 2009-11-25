/* Preview module for wikiEditor */
( function( $ ) { $.wikiEditor.modules.preview = {

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
	/**
	 * Creates a preview module within a wikiEditor
	 * @param context Context object of editor to create module in
	 * @param config Configuration object to create module from
	 */
	create: function( context, config ) {
		if ( 'preview' in context.modules ) {
			return;
		}
		context.modules.preview = {
			'previousText': null
		};
		context.$preview = context.fn.addView( {
			'name': 'preview',
			'titleMsg': 'wikieditor-preview-tab',
			'init': function( context ) {
				// Gets the latest copy of the wikitext
				var wikitext = context.fn.getContents();
				// Aborts when nothing has changed since the last preview
				if ( context.modules.preview.previousText == wikitext ) {
					return;
				}
				context.$preview.find( '.wikiEditor-preview-contents' ).empty();
				context.$preview.find( '.wikiEditor-preview-loading' ).show();
				$.post(
					wgScriptPath + '/api.php',
					{
						'action': 'parse',
						'title': wgPageName,
						'text': wikitext,
						'prop': 'text',
						'pst': '',
						'format': 'json'
					},
					function( data ) {
						if (
							data.parse == undefined ||
							data.parse.text == undefined ||
							data.parse.text['*'] == undefined
						) {
							return;
						}
						context.modules.preview.previousText = wikitext;
						context.$preview.find( '.wikiEditor-preview-loading' ).hide();
						context.$preview.find( '.wikiEditor-preview-contents' ).html( data.parse.text['*'] );
					},
					'json'
				);
			}
		} );
		var loadingMsg = gM( 'wikieditor-preview-loading' );
		context.$preview
			.append( $( '<div />' )
				.addClass( 'wikiEditor-preview-loading' )
				.append( $( '<img />' )
					.addClass( 'wikiEditor-preview-spinner' )
					.attr( {
						'src': $.wikiEditor.imgPath + 'dialogs/loading.gif',
						'valign': 'absmiddle',
						'alt': loadingMsg,
						'title': loadingMsg
					} )
				)
				.append(
					$( '<span></span>' ).text( loadingMsg )
				)
			)
			.append( $( '<div />' )
				.addClass( 'wikiEditor-preview-contents' )
			);
	}
}

}; } )( jQuery );