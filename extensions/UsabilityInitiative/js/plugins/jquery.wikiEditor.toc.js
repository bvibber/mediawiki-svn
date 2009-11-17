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
		
		// Add the TOC to the document
		$.wikiEditor.modules.toc.fn.build( context, config );
		context.$textarea
			// FIXME: magic iframe integration
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
	 * @param {Object} event Event object with context as data
	 */
	collapse: function( event ) {
		var $this = $( this ), context = $this.data('context');
		$this.parent()
			.animate( { 'width' : '1px' }, 'fast', function() { 
				$(this).hide();
				context.$ui.find( '.wikiEditor-ui-toc-expandControl' ).show( 'fast' );
			 } )
			.prev()
			.animate( { 'marginRight': '-1px'}, 'fast', function() { $(this).css( 'marginRight', 0 ); } )
			.children()
			.animate( {'marginRight': '1px'}, 'fast',  function() { $(this).css( 'marginRight', 0 ); } );
		/* 
		 * TODO: incorporate the cookie for saving toc position
		 * $.cookie( 'wikiEditor-' + context.instance + '-toc-width', 1 );
		*/
		return false;
	},
	
	/**
	 * Expand the contents module
	 * 
	 * @param {Object} event Event object with context as data
	 */
	expand: function( event) {
		var $this = $( this ), context = $this.data('context');
		context.$ui.find( '.wikiEditor-ui-toc-expandControl' ).hide( 'fast' );
		$this.parent()
			.show()
			.animate( { 'width' : '13em' }, 'fast' )
			.prev()
			.animate( { 'marginRight': '-13em'}, 'fast' )
			.children()
			.animate( {'marginRight': '13em'}, 'fast');
		/* 
		 * TODO: incorporate the cookie for saving toc position
		 * $.cookie( 'wikiEditor-' + context.instance + '-toc-width', context.modules.$toc.data( 'openWidth' ) );
		*/
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
		function buildCollapseControls() {
			var $collapseControl = $( '<div />' ), $expandControl = $( '<div />' );
			$collapseControl
				.addClass( 'tab' )
				.addClass( 'tab-toc' )
				.append( '<a href="#" />' )
				.bind( 'click.wikiEditor-toc', function() { 
						context.modules.$toc.trigger( 'collapse' ); 
						return false; 
					})
				.find( 'a' )
				.text( gM( 'wikieditor-toc-hide' ) );
			$expandControl
				.addClass( 'wikiEditor-ui-toc-expandControl')
				.append( '<a href="#" />' )
				.bind( 'click.wikiEditor-toc', function() { 
						context.modules.$toc.trigger( 'expand' ); 
						return false; 
					})
				.hide()
				.find( 'a' )
				.text( gM( 'wikieditor-toc-show' ) );
			$collapseControl.insertBefore( context.modules.$toc );
			context.$ui.find( '.wikiEditor-ui-left .wikiEditor-ui-top' ).append( $expandControl );
		}
		function buildResizeControls() {
			context.$ui.find( '.ui-resizable-e' )
				.removeClass( 'ui-resizable-e' )
				.addClass( 'ui-resizable-w' )
				.addClass( 'wikiEditor-ui-toc-resize-grip' )
				.appendTo( context.$ui.find( '.wikiEditor-ui-right' ) );
			context.modules.$toc
				.bind( 'collapse.wikiEditor-toc', $.wikiEditor.modules.toc.fn.collapse )
				.bind( 'expand.wikiEditor-toc', $.wikiEditor.modules.toc.fn.expand  );
			var $collapseControl = $( '<div />' )
				.addClass( 'tab' )
				.addClass( 'tab-toc' )
				.append( '<a href="#" />' );
			if( $.cookie( 'wikiEditor-' + context.instance + '-toc-width' ) != 1 ) {
				$collapseControl.bind( 'click.wikiEditor-toc', function() { 
						context.modules.$toc.trigger( 'collapse' ); return false; 
					})
					.find( 'a' ).text( gM( 'wikieditor-toc-hide' ) );
			} else {
				$collapseControl.bind( 'click.wikiEditor-toc', function() { 
						context.modules.$toc.trigger( 'expand' ); return false; 
					})
					.find( 'a' ).text( gM( 'wikieditor-toc-show' ) );
			}
			
			if( !context.modules.$toc.data( 'openWidth' ) ) {
				context.modules.$toc.data( 'openWidth', context.initialWidth == 1 ?
					$.wikiEditor.modules.toc.defaultWidth : context.initialWidth );
			}
			if ( context.initialWidth == 1 )
				$.wikiEditor.modules.toc.fn.collapse( { data: context } );
			return "";
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
			context.$ui.find( '.wikiEditor-ui-right' )
			.data( 'wikiEditor-ui-left', context.$ui.find( '.wikiEditor-ui-left' ))
			.resizable( {handles: 'w,e', minWidth: 50,
				start: function( e, ui ) {
					$( '<div />' ).addClass( 'wikiEditor-ui-resize-mask' )
					.css( 'position', 'absolute' )
					.css( 'z-index', 2 )
					.css( 'left', 0 ).css( 'top', 0 ).css( 'bottom', 0 ).css( 'right', 0 )
					.appendTo(context.$ui.find( '.wikiEditor-ui-left' ));
				},
				resize: function( e, ui ) {
					/*
					 * FIXME: Currently setting a heigh property on the resizable with ever mousemove event
					 * which breaks our height resizing code in jquery.wikiEditor.toolbar.js
					 */
					
					// for some odd reason, ui.size.width seems a step ahead of what the *actual* width of 
					// the resizable is
					$( this ).css( 'width' , ui.size.width )
					.data( 'wikiEditor-ui-left' ).css( 'marginRight', ( -1 * ui.size.width ) )
					.children( ).css( 'marginRight', ui.size.width );
				},
				stop: function ( e, ui ) {
					context.$ui.find( '.wikiEditor-ui-resize-mask' ).remove();
					if( ui.size.width < 70 ){
						// collapse
					}
				}
			});
			
			context.modules.$toc.append( buildResizeControls() );
			buildCollapseControls();
		}
		context.modules.$toc.find( 'div' ).autoEllipse( { 'position': 'right', 'tooltip': true } );
		// Cache the outline for later use
		context.data.outline = outline;
	}
}

}; } ) ( jQuery );
