/* TOC Module for wikiEditor */
( function( $ ) { $.wikiEditor.modules.tocCollapse = {

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
	 * Creates a table of contents module within a wikiEditor
	 * 
	 * @param {Object} context Context object of editor to create module in
	 * @param {Object} config Configuration object to create module from
	 */
	create: function( context, config ) {
		
		context.$ui.find( '.wikiEditor-ui-bottom' )
			.append( context.modules.$toc );
		
		if ( '$toc' in context.modules ) {
			return;
		}
		context.modules.$tocCollapse = $( '<div />' )
			.addClass( 'wikiEditor-ui-toc' )
			.attr( 'id', 'wikiEditor-ui-toc' );
		// If we ask for this later (after we insert the TOC) then in IE this measurement will be incorrect
		var height = context.$ui.find( '.wikiEditor-ui-bottom' ).height()
		context.$ui.find( '.wikiEditor-ui-bottom' )
			.append( context.modules.$toc );
		context.modules.$toc.height(
			context.$ui.find( '.wikiEditor-ui-bottom' ).height()
		);
		// Make some css modifications to make room for the toc on the right...
		// Perhaps this could be configurable?
		context.modules.$toc.css( { 'width': $.wikiEditor.modules.toc.defaults.width, 'marginTop': -( height ) } );
		context.$ui.find( '.wikiEditor-ui-text' )
			.css( ( $( 'body.rtl' ).size() ? 'marginLeft' : 'marginRight' ), $.wikiEditor.modules.toc.defaults.width );
		// Add the TOC to the document
		$.wikiEditor.modules.tocCollapse.fn.build( context, config );
	},

	/**
	 * Builds table of contents
	 * 
	 * @param {Object} context
	 */
	build: function( context ) {
		function buildCollapseBar() {
			var $collapseBar = $( '<div />' )
				.addClass( 'wikiEditor-ui-toc-collapse-open' )
				.attr( 'id', 'wikiEditor-ui-toc-colapse' )
				.data( 'oWidth', $.wikiEditor.modules.toc.defaults.width)
				.bind('mouseup', function(){
					var $e = $(this);
					var close = $e.hasClass('wikiEditor-ui-toc-collapse-open');
					if(close) {
						$e.parent()
							.animate( {'width': $e.outerWidth()}, 'fast', function() {
									$(this).find('ul:first').hide();
								} )
							.prev()
							.animate( {'marginRight': $e.outerWidth()}, 'fast', function(){
								$('#wikiEditor-ui-toc-colapse')
									.removeClass('wikiEditor-ui-toc-collapse-open')
									.addClass('wikiEditor-ui-toc-collapse-closed');
							});
					} else {
						$e.siblings().show()
						.parent()
							.animate( {'width': $e.data('oWidth')}, 'fast' )
							.prev()
							.animate( {'marginRight': $e.data('oWidth')}, 'fast', function(){
								$('#wikiEditor-ui-toc-colapse')
									.removeClass('wikiEditor-ui-toc-collapse-closed')
									.addClass('wikiEditor-ui-toc-collapse-open');
							});
					}
					
				});
			return $collapseBar;
			
		}
		context.modules.$tocCollapse.html( buildCollapseBar() );
	}
}

}; } ) ( jQuery );
