/* TOC Module for wikiEditor */
( function( $ ) { $.wikiEditor.modules.toc = {

/**
 * Default width of table of contents
 */
defaultWidth: '166px',
/**
 * Minimum width to allow resizing to before collapsing the table of contents
 * Only used if resizing and collapsing is enabled
 */
minimumWidth: '70px',
/**
 * API accessible functions
 */
api: {
	//
},
/**
 * Event handlers
 */
evt: {
	ready: function( context, event ) {
		// Only run this code if this module is turned on
		if ( !( '$toc' in context.modules ) ) {
			return;
		}
		
		// Add the TOC to the document
		$.wikiEditor.modules.toc.fn.build( context );
		context.$content.parent()
			.delayedBind( 250, 'mouseup scrollToTop keyup change',
				function() {
					$(this).eachAsync( {
						bulk: 0,
						loop: function() {
							$.wikiEditor.modules.toc.fn.build( context );
							$.wikiEditor.modules.toc.fn.update( context );
						}
					} );
				}
			)
			.blur( function( event ) {
				var context = event.data.context;
				context.$textarea.delayedBindCancel( 250, 'mouseup scrollToTop keyup change' );
				$.wikiEditor.modules.toc.fn.unhighlight( context );
			});
	}
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
		if ( '$toc' in context.modules ) {
			return;
		}
		
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
			.css( 'marginRight', "-" + $.wikiEditor.modules.toc.defaultWidth )
			.children()
			.css( 'marginRight', $.wikiEditor.modules.toc.defaultWidth );
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
	 * @param {Object} event Event object with context as data
	 */
	collapse: function( event ) {
		var $this = $( this ), context = $this.data( 'context' ),
			pT = $this.parent().position().top - 1;
		$this.parent()
			.css( { 'marginTop': '1px', 'position': 'absolute', 'left': 'auto', 'right': 0, 'top': pT } )
			.fadeOut( 'fast', function() {
				$( this ).hide()
				.css( { 'marginTop': '0', 'width': '1px' } );
				context.$ui.find( '.wikiEditor-ui-toc-expandControl' ).fadeIn( 'fast' );
			 } )
			.prev()
			.animate( { 'marginRight': '-1px' }, 'fast', function() {
				$( this ).css( 'marginRight', 0 );
				// Let the UI know things have moved around
				context.fn.trigger( 'resize' );
			} )
			.children()
			.animate( { 'marginRight': '1px' }, 'fast',  function() { $( this ).css( 'marginRight', 0 ); } );
		$.cookie( 'wikiEditor-' + context.instance + '-toc-width', 0 );
		return false;
	},
	
	/**
	 * Expand the contents module
	 *
	 * @param {Object} event Event object with context as data
	 */
	expand: function( event ) {
		var $this = $( this ),
			context = $this.data( 'context' ),
			openWidth = context.modules.$toc.data( 'openWidth' );
		context.$ui.find( '.wikiEditor-ui-toc-expandControl' ).hide();
		$this.parent()
			.show()
			.css( 'marginTop', '1px' )
			.animate( { 'width' : openWidth }, 'fast', function() {
				context.$content.trigger( 'mouseup' );
				$( this ).css( { 'marginTop': '0', 'position': 'relative', 'right': 'auto', 'top': 'auto' } );
				// Let the UI know things have moved around
				context.fn.trigger( 'resize' );
			 } )
			.prev()
			.animate( { 'marginRight': ( parseFloat( openWidth ) * -1 ) }, 'fast' )
			.children()
			.animate( { 'marginRight': openWidth }, 'fast' );
		$.cookie( 'wikiEditor-' + context.instance + '-toc-width',
			context.modules.$toc.data( 'openWidth' ) );
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
					.click( function( event ) {
						context.fn.scrollToTop( $(this).data( 'wrapper' ) );
						context.$textarea.textSelection( 'setSelection', {
							'start': 0,
							'startContainer': $(this).data( 'wrapper' )
						} );
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
		/**
		 * Builds controls for collapsing and expanding the TOC
		 *
		 */
		function buildCollapseControls( ) {
			var $collapseControl = $( '<div />' ), $expandControl = $( '<div />' );
			$collapseControl
				.addClass( 'tab' )
				.addClass( 'tab-toc' )
				.append( '<a href="#" />' )
				.bind( 'click.wikiEditor-toc', function() {
					context.modules.$toc.trigger( 'collapse.wikiEditor-toc' ); return false;
				} )
				.find( 'a' )
				.text( gM( 'wikieditor-toc-hide' ) );
			$expandControl
				.addClass( 'wikiEditor-ui-toc-expandControl' )
				.append( '<a href="#" />' )
				.bind( 'click.wikiEditor-toc', function() {
					context.modules.$toc.trigger( 'expand.wikiEditor-toc' ); return false;
				} )
				.hide()
				.find( 'a' )
				.text( gM( 'wikieditor-toc-show' ) );
			$collapseControl.insertBefore( context.modules.$toc );
			context.$ui.find( '.wikiEditor-ui-left .wikiEditor-ui-top' ).append( $expandControl );
		}
		/**
		 * Initializes resizing controls on the TOC and sets the width of
		 * the TOC based on it's previous state
		 *
		 */
		function buildResizeControls( ) {
			context.$ui
				.data( 'resizableDone', true )
				.find( '.wikiEditor-ui-right' )
				.data( 'wikiEditor-ui-left', context.$ui.find( '.wikiEditor-ui-left' ) )
				.resizable( { handles: 'w,e', preventPositionLeftChange: true, minWidth: 50,
					start: function( e, ui ) {
						var $this = $( this );
						// Toss a transparent cover over our iframe
						$( '<div />' )
							.addClass( 'wikiEditor-ui-resize-mask' )
							.css( { 'position': 'absolute', 'z-index': 2, 'left': 0, 'top': 0, 'bottom': 0, 'right': 0 } )
							.appendTo( context.$ui.find( '.wikiEditor-ui-left' ) );
						$this.resizable( 'option', 'maxWidth', $this.parent().width() - 450 );
					},
					resize: function( e, ui ) {
						// for some odd reason, ui.size.width seems a step ahead of what the *actual* width of
						// the resizable is
						$( this ).css( { 'width': ui.size.width, 'top': 'auto', 'height': 'auto' } )
							.data( 'wikiEditor-ui-left' ).css( 'marginRight', ( -1 * ui.size.width ) )
							.children().css( 'marginRight', ui.size.width );
						// Let the UI know things have moved around
						context.fn.trigger( 'resize' );
					},
					stop: function ( e, ui ) {
						context.$ui.find( '.wikiEditor-ui-resize-mask' ).remove();
						context.$content.trigger( 'mouseup' );
						if( ui.size.width < parseFloat( $.wikiEditor.modules.toc.minimumWidth ) ) {
							context.modules.$toc.trigger( 'collapse' );
						} else {
							context.modules.$toc.data( 'openWidth', ui.size.width );
							$.cookie( 'wikiEditor-' + context.instance + '-toc-width', ui.size.width );
						}
						// Let the UI know things have moved around
						context.fn.trigger( 'resize' );
					}
				});
			// Convert our east resize handle into a secondary west resize handle
			context.$ui.find( '.ui-resizable-e' )
				.removeClass( 'ui-resizable-e' )
				.addClass( 'ui-resizable-w' )
				.addClass( 'wikiEditor-ui-toc-resize-grip' );
			// Bind collapse and expand event handlers to the TOC
			context.modules.$toc
				.bind( 'collapse.wikiEditor-toc', $.wikiEditor.modules.toc.fn.collapse )
				.bind( 'expand.wikiEditor-toc', $.wikiEditor.modules.toc.fn.expand  );
			context.modules.$toc.data( 'openWidth', $.wikiEditor.modules.toc.defaultWidth );
			// If the toc-width cookie is set, reset the widths based upon that
			if ( $.cookie( 'wikiEditor-' + context.instance + '-toc-width' ) == 0 ) {
				context.modules.$toc.trigger( 'collapse.wikiEditor-toc', { data: context } );
			} else if ( $.cookie( 'wikiEditor-' + context.instance + '-toc-width' ) > 0 ) {
				var initialWidth = $.cookie( 'wikiEditor-' + context.instance + '-toc-width' );
				if( initialWidth < parseFloat( $.wikiEditor.modules.toc.minimumWidth ) )
					initialWidth = parseFloat( $.wikiEditor.modules.toc.minimumWidth ) + 1;
				context.modules.$toc.data( 'openWidth', initialWidth + 'px' );
				context.$ui.find( '.wikiEditor-ui-right' )
					.css( 'width', initialWidth + 'px' );
				context.$ui.find( '.wikiEditor-ui-left' )
					.css( 'marginRight', ( parseFloat( initialWidth ) * -1 ) + 'px' )
					.children()
					.css( 'marginRight', initialWidth + 'px' );
			}
		}
		
		// Build outline from wikitext
		var outline = [], h = 0;
		
		// Traverse all text nodes in context.$content
		function traverseTextNodes() {
			if ( this.nodeName != '#text' ) {
				$( this.childNodes ).each( traverseTextNodes );
				return;
			}
			var text = this.nodeValue;
			
			// Get the previous and next node in Euler tour order
			var p = this;
			while( !p.previousSibling )
				p = p.parentNode;
			var prev = p ? p.previousSibling : null;
			
			p = this;
			while ( p && !p.nextSibling )
				p = p.parentNode;
			var next = p ? p.nextSibling : null;
			
			// Edge case: there are more equals signs,
			// but they're not all in the <div>. Eat them.
			if ( prev && prev.nodeName == '#text' ) {
				var prevText = prev.nodeValue;
				while ( prevText.substr( -1 ) == '=' ) {
					prevText = prevText.substr( 0, prevText.length - 1 );
					text = '=' + text;
				}
				prev.nodeValue = prevText;
			}
			var next = this.nextSibling;
			if ( next && next.nodeName == '#text' ) {
				var nextText = next.nodeValue;
				while ( nextText.substr( 0, 1 ) == '=' ) {
					nextText = nextText.substr( 1 );
					text = text + '=';
				}
				next.nodeValue = nextText;
			}
			if ( text != this.nodeValue )
				this.nodeValue = text;
			
			var match = text.match( /^(={1,6})(.+?)\1\s*$/ );
			if ( !match ) {
				if ( $(this).parent().is( '.wikiEditor-toc-header' ) )
					// Header has become invalid
					// Remove the class but keep the <div> intact
					// to prevent issues with Firefox
					// TODO: Fix this issue
					//$(this).parent()
					//	.removeClass( 'wikiEditor-toc-header' );
					$(this).parent().replaceWith( text );
				return;
			}
			
			// Wrap the header in a <div>, unless it's already wrapped
			var div;
			if ( $(this).parent().is( '.wikiEditor-toc-header' ) )
				div = $(this).parent();
			else if ( $(this).parent().is( 'div' ) )
				div = $(this).parent().addClass( 'wikiEditor-toc-header' );
			else {
				div = $( '<div />' )
					.text( text )
					.css( 'display', 'inline' )
					.addClass( 'wikiEditor-toc-header' );
				$(this).replaceWith( div );
			}
			outline[h] = { 'text': match[2], 'wrapper': div, 'level': match[1].length, 'index': h + 1 };
			h++;
		}
		context.$content.each( traverseTextNodes );
				
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
			structure.unshift( { 'text': wgPageName.replace(/_/g, ' '), 'level': 1, 'index': 0,
				'wrapper': context.$content } );
		}
		context.modules.$toc.html( buildList( structure ) );
		
		if ( wgNavigableTOCResizable && !context.$ui.data( 'resizableDone' ) ) {
			buildResizeControls();
			buildCollapseControls();
		}
		context.modules.$toc.find( 'div' ).autoEllipse( { 'position': 'right', 'tooltip': true } );
		// Cache the outline for later use
		context.data.outline = outline;
	}
}

};

/*
 * Extending resizable to allow west resizing without altering the left position attribute
 */
$.ui.plugin.add( "resizable", "preventPositionLeftChange", {
	resize: function( event, ui ) {
		$( this ).data( "resizable" ).position.left = 0;
	}
} );
 
} ) ( jQuery );
