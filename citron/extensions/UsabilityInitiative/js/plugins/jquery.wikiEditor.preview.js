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
		mvJsLoader.doLoad( [ '$j.ui', '$j.ui.tabs' ], function() {
			if ( 'preview' in context.modules )
				return;
			
			context.$ui
				.wrapInner( $j( '<div />' )
					.addClass( 'wikiEditor-tab-edit' )
					.attr( 'id', 'wikiEditor-' + context.instance + '-tab-edit' )
				)
				.wrapInner( $j( '<div />' )
					.addClass( 'wikiEditor-tabs' )
				);
			var tabList = context.$ui.children();
			var editTab = tabList.children();
			
			var loadingMsg = gM( 'wikieditor-preview-loading' );
			var previewTab = $j( '<div />' )
				.addClass( 'wikiEditor-tab-preview' )
				.attr( 'id', 'wikiEditor-' + context.instance + '-tab-preview' )
				.append( $j( '<div />' )
					.addClass( 'wikiEditor-preview-spinner' )
					.append( $j( '<img />' )
						.attr( {
							'src': $j.wikiEditor.imgPath + 'dialogs/loading.gif',
							'alt': loadingMsg,
							'title': loadingMsg
						} )
					)
				)
				.append( $j( '<div />' )
					.addClass( 'wikiEditor-preview-contents' )
				)
				.insertAfter( editTab );
			tabList
				.append( $j( '<ul />' )
					.append( $j( '<li />' )
						.append( $j( '<a />' )
							.attr( 'href', '#wikiEditor-' + context.instance + '-tab-edit' )
							.text( gM( 'wikieditor-preview-tab-edit' ) )
						)
					)
					.append( $j( '<li />' )
						.append( $j( '<a />' )
							.attr( 'href', '#wikiEditor-' + context.instance + '-tab-preview' )
							.text( gM( 'wikieditor-preview-tab-preview' ) )
						)
					)
				)
				.append( editTab )
				.append( previewTab );
			
			// Paranoia: initialize context.modules before running
			// tabs() and binding event handlers
			context.modules.preview = {
				'editTab': editTab,
				'previewTab': previewTab,
				'tabList': tabList,
				'prevText': null
			};
			tabList
				.bind( 'tabsshow', function() {
					if ( context.modules.preview.previewTab.is( ':visible' ) )
						$.wikiEditor.modules.preview.fn.showPreview( context );
				})
				.tabs();
			
			// Remove the ui-widget class from the tabs div,
			// causes NTOC mispositioning
			// FIXME: Find out which CSS rule is causing this
			// and override it
			tabList.closest( '.ui-tabs' ).removeClass( 'ui-widget' );
		});
	},
	
	showPreview: function( context ) {
		var wikitext = context.$textarea.val();
		if ( context.modules.preview.prevText == wikitext )
			// Nothing changed since the last preview
			return;
		
		context.modules.preview.previewTab
			.children( '.wikiEditor-preview-contents' )
			.empty();
		context.modules.preview.previewTab
			.children( '.wikiEditor-preview-spinner' )
			.show();
		
		$.post( wgScriptPath + '/api.php', {
			'action': 'parse',
			'title': wgPageName,
			'text': wikitext,
			'prop': 'text',
			'pst': '',
			'format': 'json'
		}, function( data ) {
			if ( data.parse == undefined || data.parse.text == undefined ||
					data.parse.text['*'] == undefined )
				return;
			context.modules.preview.prevText = wikitext;
			context.modules.preview.previewTab
				.children( '.wikiEditor-preview-spinner' )
				.hide();
			context.modules.preview.previewTab
				.children( '.wikiEditor-preview-contents' )
				.html( data.parse.text['*'] );
		}, 'json' );
	}
}
};
})( jQuery );