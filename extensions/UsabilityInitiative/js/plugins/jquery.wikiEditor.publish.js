/* Publish module for wikiEditor */
( function( $ ) { $.wikiEditor.modules.publish = {

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
	 * Creates a publish module within a wikiEditor
	 * @param context Context object of editor to create module in
	 * @param config Configuration object to create module from
	 */
	create: function( context, config ) {
		/*
		 * This code was moved here from the old version of the preview module - it needs more work to run on it's own,
		 * and many of the names of classes and messages need to be updated
		 * 
		mvJsLoader.doLoad( [ '$j.ui' ], function() {
			// Build the dialog behind the Publish button
			var dialogID = 'wikiEditor-' + context.instance + '-savedialog';
			$.wikiEditor.modules.dialogs.fn.create(
				context,
				{
					previewsave: {
						id: dialogID,
						titleMsg: 'wikieditor-preview-savedialog-title',
						html: '\
							<div class="wikiEditor-savedialog-copywarn"></div>\
							<div class="wikiEditor-savedialog-editoptions">\
								<form>\
									<label for="wikiEditor-' + context.instance + '-savedialog-summary"\
										rel="wikieditor-preview-savedialog-summary"></label>\
									<br />\
									<input type="text" id="wikiEditor-' + context.instance + '-savedialog-summary"\
										style="width: 100%;" />\
									<br />\
									<input type="checkbox"\
										id="wikiEditor-' + context.instance + '-savedialog-minor" />\
									<label for="wikiEditor-' + context.instance + '-savedialog-minor"\
										rel="wikieditor-preview-savedialog-minor"></label>\
									<br />\
									<input type="checkbox"\
										id="wikiEditor-' + context.instance + '-savedialog-watch" />\
									<label for="wikiEditor-' + context.instance + '-savedialog-watch"\
										rel="wikieditor-preview-savedialog-watch"></label>\
								</form>\
							</div>',
						init: function() {
							$(this).find( '[rel]' ).each( function() {
								$(this).text( gM( $(this).attr( 'rel' ) ) );
							});
							$(this).find( '.wikiEditor-savedialog-copywarn' )
								.html( $( '#editpage-copywarn' ).html() );
							
							if ( $( '#wpMinoredit' ).size() == 0 )
								$( '#wikiEditor-' + context.instance + '-savedialog-minor' ).hide();
							else if ( $( '#wpMinoredit' ).is( ':checked' ) )
								$( '#wikiEditor-' + context.instance + '-savedialog-minor' )
									.attr( 'checked', 'checked' );
							if ( $( '#wpWatchthis' ).size() == 0 )
								$( '#wikiEditor-' + context.instance + '-savedialog-watch' ).hide();
							else if ( $( '#wpWatchthis' ).is( ':checked' ) )
								$( '#wikiEditor-' + context.instance + '-savedialog-watch' )
									.attr( 'checked', 'checked' );
							
							$(this).find( 'form' ).submit( function( e ) {
								$(this).closest( '.ui-dialog' ).find( 'button:first' ).click();
								e.preventDefault();
							});
						},
						dialog: {
							buttons: {
								'wikieditor-preview-savedialog-publish': function() {
									var minorChecked = $( '#wikiEditor-' + context.instance +
										'-savedialog-minor' ).is( ':checked' ) ?
											'checked' : '';
									var watchChecked = $( '#wikiEditor-' + context.instance +
										'-savedialog-watch' ).is( ':checked' ) ?
											'checked' : '';
									$( '#wpMinoredit' ).attr( 'checked', minorChecked );
									$( '#wpWatchthis' ).attr( 'checked', watchChecked );
									$( '#wpSummary' ).val( $j( '#wikiEditor-' + context.instance +
										'-savedialog-summary' ).val() );
									$( '#editform' ).submit();
								},
								'wikieditor-preview-savedialog-goback': function() {
									$(this).dialog( 'close' );
								}
							},
							open: function() {
								$( '#wikiEditor-' + context.instance + '-savedialog-summary' ).focus();
							},
							width: 500
						},
						resizeme: false
					}
				}
			);
			// Paranoia: initialize context.modules before running
			// tabs() and binding event handlers
			context.modules.preview = {
				'editTab': editTab,
				'previewTab': previewTab,
				'tabList': tabList,
				'saveDialog': $( '#' + dialogID ),
				'prevText': null
			};
			tabList
				.append( $( '<ul />' )
					.append( $( '<li />' )
						.append( $( '<a />' )
							.attr( 'href', '#wikiEditor-' + context.instance + '-tab-edit' )
							.text( gM( 'wikieditor-preview-tab-edit' ) )
						)
					)
					.append( $( '<li />' )
						.append( $( '<a />' )
							.attr( 'href', '#wikiEditor-' + context.instance + '-tab-preview' )
							.text( gM( 'wikieditor-preview-tab-preview' ) )
						)
					)
					.append( $( '<button />' )
						.text( gM( 'wikieditor-preview-button-publish' ) )
						.click( function() {
							context.modules.preview.saveDialog.dialog( 'open' );
							return false;
						})
					)
					// These have to go in reverse because they're floated right
					.append( $( '<button />' )
						.text( gM( 'wikieditor-preview-button-cancel' ) )
					)
				)
				.append( editTab )
				.append( previewTab );
			
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
			// FIXME: Don't use jQuery UI tabs, implement our own tabs
			tabList.closest( '.ui-tabs' ).removeClass( 'ui-widget' );
		} );
		*/
	}
}

}; } )( jQuery );