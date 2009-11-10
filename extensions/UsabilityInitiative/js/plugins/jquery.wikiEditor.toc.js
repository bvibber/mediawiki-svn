/* TOC Module for wikiEditor */
( function( $ ) { $.wikiEditor.modules.toc = {

/**
 * API accessible functions
 */
api: {
	//
},
/**
 * Default width of table of contents
 */
defaultWidth: '13em',
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
		if ( '$toc' in context.modules ) {
			return;
		}
		/*
		context.initialWidth = $.wikiEditor.modules.toc.defaultWidth;
		if( wgNavigableTOCResizable ) {
			if( !$.cookie( 'wikiEditor-' + context.instance + '-toc-width' ) ) {
				$.cookie(
					'wikiEditor-' + context.instance + '-toc-width',
					$.wikiEditor.modules.toc.defaultWidth
				);
			} else {
				context.initialWidth = $.cookie( 'wikiEditor-' + context.instance + '-toc-width' );
			}
		}
		*/
		var height = context.$ui.find( '.wikiEditor-ui-left' ).height();
		context.modules.$toc = $( '<div />' )
			.addClass( 'wikiEditor-ui-toc' )
			.data( 'context', context );
		context.$ui.find( '.wikiEditor-ui-right' )
			.css( 'width', $.wikiEditor.modules.toc.defaultWidth )
			.append( context.modules.$toc );
		context.modules.$toc.height(
			context.$ui.find( '.wikiEditor-ui-left' ).height()
		);
		context.$ui.find( '.wikiEditor-ui-left' )
			.css( 'marginRight', "-" + $.wikiEditor.modules.toc.defaultWidth)
			.children()
			.css('marginRight', $.wikiEditor.modules.toc.defaultWidth );
		
		// Add the TOC to the document
		$.wikiEditor.modules.toc.fn.build( context, config );
		context.$textarea
			.delayedBind( 250, 'mouseup scrollToPosition focus keyup encapsulateSelection change',
				function( event ) {
					var context = $(this).data( 'wikiEditor-context' );
					$(this).eachAsync( {
						bulk: 0,
						loop: function() {
							$.wikiEditor.modules.toc.fn.build( context );
							$.wikiEditor.modules.toc.fn.update( context );
						}
					} );
				}
			)
			.blur( function() {
				var context = $(this).data( 'wikiEditor-context' );
				context.$textarea.delayedBindCancel( 250,
					'mouseup scrollToPosition focus keyup encapsulateSelection change' );
				$.wikiEditor.modules.toc.fn.unhighlight( context );
			});
	},
	
	unhighlight: function( context ) {
		context.modules.$toc.find( 'div' ).removeClass( 'current' );
	},
	/**
	 * Highlight the section the cursor is currently within
	 * 
	 * @param {Object} context
	 */
	update: function( context ) {
		$.wikiEditor.modules.toc.fn.unhighlight( context );
		var position = context.$textarea.textSelection( 'getCaretPosition' );
		var section = 0;
		if ( context.data.outline.length > 0 ) {
			// If the caret is before the first heading, you must be in section
			// 0, and there is no need to look any farther - otherwise check
			// that the caret is before each section, and when it's not, we now
			// know what section it is in
			if ( !( position < context.data.outline[0].position - 1 ) ) {
				while (
					section < context.data.outline.length && context.data.outline[section].position - 1 < position
				) {
					section++;
				}
				section = Math.max( 0, section );
			}
			var sectionLink = context.modules.$toc.find( 'div.section-' + section );
			sectionLink.addClass( 'current' );
			
			// Scroll the highlighted link into view if necessary
			var relTop = sectionLink.offset().top - context.modules.$toc.offset().top;
			var scrollTop = context.modules.$toc.scrollTop();
			var divHeight = context.modules.$toc.height();
			var sectionHeight = sectionLink.height();
			if ( relTop < 0 )
				// Scroll up
				context.modules.$toc.scrollTop( scrollTop + relTop );
			else if ( relTop + sectionHeight > divHeight )
				// Scroll down
				context.modules.$toc.scrollTop( scrollTop + relTop + sectionHeight - divHeight );
		}
	},
	
	/**
	 * Collapse the contents module
	 * 
	 * @param {Object} context
	 */
	collapse: function( context ) {
		var $toc = $( '#wikiEditor-ui-toc' );
		if( !$toc.data( 'openWidth' ) ) { 
			$toc.data( 'openWidth', $toc.width() );
		}
		var $toolbar = $( '#wikiEditor-ui-toolbar .tab-toc' )
		.unbind( 'click', $.wikiEditor.modules.toc.fn.collapse )
		.bind( 'click', $.wikiEditor.modules.toc.fn.expand )
		.children( 'a' )
		.text( 'Show Contents' );
		
		$toc
		.animate( { 'width': '1px' }, 'fast', function() { $(this).hide(); } )
		.prev()
		.animate( {'marginRight': '1px'}, 'fast', function() { $(this).css('marginRight', '-1px'); } );
		$.cookie( 'wikiEditor-' + context.instance + '-toc-width', '1px' );
		return false;
	},
	
	/**
	 * Expand the contents module
	 * 
	 * @param {Object} context
	 */
	expand: function( context ) {
		$( '#wikiEditor-ui-toolbar .tab-toc' )
		.unbind( 'click', $.wikiEditor.modules.toc.fn.expand )
		.bind( 'click', $.wikiEditor.modules.toc.fn.collapse )
		.children( 'a' )
		.text( 'Hide Contents' );
		$( '#wikiEditor-ui-toc' )
		.show()
		.animate( { 'width': $( '#wikiEditor-ui-toc' ).data( 'openWidth' )}, 'fast', function() { 
			$( '#wikiEditor-ui-text textarea' ).trigger( 'mouseup' );
		} )
		.prev()
		.animate( { 'marginRight': $( '#wikiEditor-ui-toc' ).data( 'openWidth' ) }, 'fast' );
		$.cookie( 'wikiEditor-' + context.instance + '-toc-width', $( '#wikiEditor-ui-toc' ).data( 'openWidth' ) );
		return false;
	},
	/**
	 * Builds table of contents
	 * 
	 * @param {Object} context
	 */
	build: function( context ) {
		/**
		 * Builds a structured outline from flat outline
		 * 
		 * @param {Object} outline Array of objects with level fields
		 */
		function buildStructure( outline, offset, level ) {
			if ( offset == undefined ) offset = 0;
			if ( level == undefined ) level = 1;
			var sections = [];
			for ( var i = offset; i < outline.length; i++ ) {
				if ( outline[i].nLevel == level ) {
					var sub = buildStructure( outline, i + 1, level + 1 );
					if ( sub.length ) {
						outline[i].sections = sub;
					}
					sections[sections.length] = outline[i];
				} else if ( outline[i].nLevel < level ) {
					break;
				}
			}
			return sections;
		}
		/**
		 * Bulds unordered list HTML object from structured outline
		 * 
		 * @param {Object} structure Structured outline
		 */
		function buildList( structure ) {
			var list = $( '<ul />' );
			for ( i in structure ) {
				var div = $( '<div />' )
					.addClass( 'section-' + structure[i].index )
					.data( 'wrapper', structure[i].wrapper )
					.mousedown( function( event ) {
						context.fn.scrollToTop( $(this).data( 'wrapper' ) );
						// TODO: Move cursor
						if ( typeof $.trackAction != 'undefined' )
							$.trackAction( 'ntoc.heading' );
						event.preventDefault();
					} )
					.text( structure[i].text );
				if ( structure[i].text == '' )
					div.html( '&nbsp;' );
				var item = $( '<li />' ).append( div );
				if ( structure[i].sections !== undefined ) {
					item.append( buildList( structure[i].sections ) );
				}
				list.append( item );
			}
			return list;
		}
		function buildCollapseBar() {
			$( '.wikiEditor-ui-toc ul:first' ).css( 'width', '147px' )
				.css( 'margin-left', '19px' ).css( 'border-left', '1px solid #DDDDDD' );
			var $collapseBar = $( '<div />' )
				.addClass( 'wikiEditor-ui-toc-collapse-open' )
				.attr( 'id', 'wikiEditor-ui-toc-collapse' )
				.data( 'openWidth', $.wikiEditor.modules.toc.defaultWidth)
				.bind( 'mouseup', function() {
					var $e = $(this);
					var close = $e.hasClass( 'wikiEditor-ui-toc-collapse-open' );
					if( close ) {
						$( '#wikiEditor-ui-toc-collapse' )
							.removeClass( 'wikiEditor-ui-toc-collapse-open' );
						$e.parent()
							.animate( { 'width': $e.outerWidth() }, 'fast', function() {
									$(this).find( 'ul:first' ).hide();
								} )
							.prev()
							.animate( { 'marginRight': $e.outerWidth() + 1 }, 'fast', function() {
								$( '#wikiEditor-ui-toc-collapse' )
									.addClass( 'wikiEditor-ui-toc-collapse-closed' );
							});
					} else {
						$( '#wikiEditor-ui-toc-collapse' )
							.removeClass( 'wikiEditor-ui-toc-collapse-closed' );
						$e.siblings().show()
						.parent()
							.animate( { 'width': $e.data( 'openWidth' ) }, 'fast' )
							.prev()
							.animate( { 'marginRight': $e.data( 'openWidth' ) }, 'fast', function() {
								$( '#wikiEditor-ui-toc-collapse' )
									.addClass( 'wikiEditor-ui-toc-collapse-open' );
							});
					}
					
				});
			return $collapseBar;	
		}
		function drag( e ) {
			var mR = e.pageX - $( '#wikiEditor-ui-left' ).offset().left;
			mR = $( '#wikiEditor-ui-left' ).width() - mR;
			if( mR < 26 || mR >  $( '#wikiEditor-ui-left' ).width() - 250) return false;
			$( '#wikiEditor-ui-left' ).css( 'marginRight', mR+'px' );
			$( '#wikiEditor-ui-right' ).css( 'width', mR+'px' );
			return false;
		}
		function stopDrag( e ) {
			$()
			.unbind( 'mousemove', drag )
			.unbind( 'mouseup', stopDrag );
			context.modules.$toc.find( 'div' ).autoEllipse( { 'position': 'right', 'tooltip': true } );
			var mR = e.pageX - $( '#wikiEditor-ui-bottom' ).offset().left;
			mR = $( '#wikiEditor-ui-bottom' ).width() - mR;
			if( mR < 50 && wgNavigableTOCCollapseEnable ) {
				$.wikiEditor.modules.toc.fn.collapse( context );
			} else {
				$( '#wikiEditor-ui-left' ).trigger( 'mouseup' );
				$( '#wikiEditor-ui-right' )
				.data( 'openWidth', $( '#wikiEditor-ui-right' ).width() + 'px' );
				$.cookie( 'wikiEditor-' + context.instance + '-toc-width', $( '#wikiEditor-ui-right' ).width() + 'px' );
			}
			return false;
		}
		function buildResizeControls() {
			var $resizeControlVertical = $( '<div />' )
			.attr( 'id', 'wikiEditor-ui-toc-resize-vertical')
			.bind( 'mousedown', function() {
				$( '#wikiEditor-ui-toc' )
				.data( 'openWidth', $( '#wikiEditor-ui-toc' ).width() );
				$()
				.bind( 'mousemove', drag )
				.bind( 'mouseup', stopDrag );
			});
			
			var $collapseControl = $( '<div />' ).addClass( 'tab' ).addClass( 'tab-toc' )
			.append( '<a href="#"></a>' );
			if( $.cookie( 'wikiEditor-' + context.instance + '-toc-width' ) != '1px' ) {
				$collapseControl.bind( 'click', function() {
					$.wikiEditor.modules.toc.fn.collapse( context );
				} )
				.find( 'a' ).text( 'Hide Contents' );
			} else { 
				$collapseControl.bind( 'click', function() {
					$.wikiEditor.modules.toc.fn.expand( context );
				} )
				.find( 'a' ).text( 'Show Contents' );
			}
			$collapseControl.insertBefore( context.modules.$toc );
			
			if( !$( '#wikiEditor-ui-toc' ).data( 'openWidth' ) ) {
				$( '#wikiEditor-ui-toc' ).data( 'openWidth', context.initialWidth == '1px' ? 
					$.wikiEditor.modules.toc.defaultWidth : context.initialWidth );
			}
			return $resizeControlVertical;
		}
		
		// Build outline from wikitext
		var outline = [];
		// Traverse all text nodes in context.$content
		var h = 0;
		context.$content.contents().each( function() {
			if ( this.nodeName != '#text' )
				return;
			var text = this.textContent;
			var match = text.match( /^(={1,6})(.*?)\1\s*$/ );
			if ( !match )
				return;
			// Wrap the header in a <div>, unless it's already wrapped
			var div;
			if ( $(this).parent().is( 'div' ) && $(this).parent().children().size() == 1 )
				div = $(this)
					.parent()
					.addClass( 'wikiEditor-toc-header' );
			else {
				div = $j( '<div />' )
					.text( text )
					.css( 'display', 'inline' )
					.addClass( 'wikiEditor-toc-header' );
				this.parentNode.replaceChild( div.get( 0 ), this );
			}
			outline[h] = { 'text': match[2], 'wrapper': div, 'level': match[1].length, 'index': h + 1 };
			h++;
		});
		// Normalize heading levels for list creation
		// This is based on Linker::generateTOC(), so it should behave like the
		// TOC on rendered articles does - which is considdered to be correct
		// at this point in time.
		var lastLevel = 0;
		var nLevel = 0;
		for ( var i = 0; i < outline.length; i++ ) {
			if ( outline[i].level > lastLevel ) {
				nLevel++;
			}
			else if ( outline[i].level < lastLevel ) {
				nLevel -= Math.max( 1, lastLevel - outline[i].level );
			}
			if ( nLevel <= 0 ) {
				nLevel = 1;
			}
			outline[i].nLevel = nLevel;
			lastLevel = outline[i].level;
		}
		// Recursively build the structure and add special item for
		// section 0, if needed
		var structure = buildStructure( outline );
		if ( $( 'input[name=wpSection]' ).val() == '' ) {
			// Add a <div> at the beginning
			var div = $j( '<div />' )
				.addClass( 'wikiEditor-toc-start' )
				.hide()
				.prependTo( context.$content );
			structure.unshift( { 'text': wgPageName.replace(/_/g, ' '), 'level': 1, 'index': 0,
				'wrapper': div } );
		}
		context.modules.$toc.html( buildList( structure ) );

		if(wgNavigableTOCResizable) {
			context.modules.$toc.append( buildResizeControls() );
		}else if(wgNavigableTOCCollapseEnable){
			context.modules.$toc.append( buildCollapseBar() );
		}
		context.modules.$toc.find( 'div' ).autoEllipse( { 'position': 'right', 'tooltip': true } );
		// Cache the outline for later use
		context.data.outline = outline;
	}
}

}; } ) ( jQuery );
