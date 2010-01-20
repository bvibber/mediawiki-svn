/* TOC Module for wikiEditor */
( function( $ ) { $.wikiEditor.modules.toc = {

/**
 * Configuration
 */
cfg: {
	// Default width of table of contents
	defaultWidth: '166px',
	// Minimum width to allow resizing to before collapsing the table of contents - used when resizing and collapsing
	minimumWidth: '70px',
	// Minimum width of the wikiText area
	textMinimumWidth: '450px',
	// Boolean var indicating text direction
	rtl: false
},
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
	change: function( context, event ) {
		$.wikiEditor.modules.toc.fn.update( context );
	},
	ready: function( context, event ) {
		// Add the TOC to the document
		$.wikiEditor.modules.toc.fn.build( context );
		context.$content.parent()
			.blur( function() {
				var context = event.data.context;
				$.wikiEditor.modules.toc.fn.unhighlight( context );
			});
		$.wikiEditor.modules.toc.evt.resize( context );
	},
	resize: function( context, event ) {
		var availableWidth = context.$wikitext.width() - parseFloat( $.wikiEditor.modules.toc.cfg.textMinimumWidth ),
			totalMinWidth = parseFloat( $.wikiEditor.modules.toc.cfg.minimumWidth ) + 
				parseFloat( $.wikiEditor.modules.toc.cfg.textMinimumWidth );
		context.$ui.find( '.wikiEditor-ui-right' )
			.resizable( 'option', 'maxWidth', availableWidth );
		if ( context.modules.toc.$toc.data( 'positionMode' ) != 'disabled' && 
			context.$wikitext.width() < totalMinWidth ) {
				$.wikiEditor.modules.toc.fn.disable( context );
		} else if ( context.modules.toc.$toc.data( 'positionMode' ) == 'disabled'  &&
			context.$wikitext.width() >  totalMinWidth ) {
				$.wikiEditor.modules.toc.fn.enable( context );
		} else if ( context.modules.toc.$toc.data( 'positionMode' ) == 'regular'  &&
			context.$ui.find( '.wikiEditor-ui-right' ).width() > availableWidth ) {
			//switch mode
			$.wikiEditor.modules.toc.fn.switchLayout( context );
		} else if ( context.modules.toc.$toc.data( 'positionMode' ) == 'goofy'  &&
			context.modules.toc.$toc.data( 'previousWidth' ) < context.$wikitext.width() ) {
			//switch mode
			$.wikiEditor.modules.toc.fn.switchLayout( context );
		}
		if ( context.modules.toc.$toc.data( 'positionMode' ) == 'goofy' ) {
			context.modules.toc.$toc.find( 'div' ).autoEllipsis( { 'position': 'right', 'tooltip': true, 'restoreText': true } );
		}
		// reset the height of the TOC
		context.modules.toc.$toc.height(
			context.$ui.find( '.wikiEditor-ui-left' ).height() - 
			context.$ui.find( '.tab-toc' ).outerHeight()
		);
		// store the width of the view for comparison on next resize
		context.modules.toc.$toc.data( 'previousWidth', context.$wikitext.width() );
	},
	mark: function( context, event ) {
		var markers = context.modules.highlight.markers;
		var tokenArray = context.modules.highlight.tokenArray;
		var outline = context.data.outline = [];
		var h = 0;
		for ( var i = 0; i < tokenArray.length; i++ ) {
			if ( tokenArray[i].label != 'TOC_HEADER' ) {
				continue;
			}
			h++;
			markers.push( {
				index: h,
				start: tokenArray[i].tokenStart,
				end: tokenArray[i].offset,
				anchor: 'before',
				afterWrap: function( node ) {
					var marker = $( node ).data( 'marker' );
					$( node ).addClass( 'wikiEditor-toc-header' )
						.addClass( 'wikiEditor-toc-section-' + marker.index )
						.data( 'section', marker.index );
				},
				onSkip: function( node ) {
					var marker = $( node ).data( 'marker' );
					$( node )
						.removeClass( 'wikiEditor-toc-section-' + $( node ).data( 'section' ) )
						.addClass( 'wikiEditor-toc-section-' + marker.index )
						.data( 'section', marker.index );
				},
				getAnchor: function( ca1, ca2 ) {
					return $( ca1.previousSibling ).is( 'div.wikiEditor-toc-header' ) ?
						ca1.previousSibling : null;
				}
			} );
			outline.push ( {
				'text': tokenArray[i].match[2],
				'level': tokenArray[i].match[1].length,
				'index': h
			} );
		}
		$.wikiEditor.modules.toc.fn.build( context );
		$.wikiEditor.modules.toc.fn.update( context );
	}
},
exp: [
	{ 'regex': /^(={1,6})(.+?)\1\s*$/m, 'label': 'TOC_HEADER', 'markAfter': true }
],
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
		if ( '$toc' in context.modules.toc ) {
			return;
		}
		$.wikiEditor.modules.toc.cfg.rtl = config.rtl;
		
		var height = context.$ui.find( '.wikiEditor-ui-left' ).height();
		context.modules.toc.$toc = $( '<div />' )
			.addClass( 'wikiEditor-ui-toc' )
			.data( 'context', context )
			.data( 'positionMode', 'regular' )
			.data( 'collapsed', false );
			/* context.$ui.find( '.wikiEditor-ui-right' )
				.css( 'width', $.wikiEditor.modules.toc.cfg.defaultWidth )
				.append( context.modules.toc.$toc );
			context.modules.toc.$toc.height(
				context.$ui.find( '.wikiEditor-ui-left' ).height()
			);
			context.$ui.find( '.wikiEditor-ui-left' )
				.css( 'marginRight', "-" + $.wikiEditor.modules.toc.cfg.defaultWidth )
				.children()
				.css( 'marginRight', $.wikiEditor.modules.toc.cfg.defaultWidth );
			*/
			context.$ui.find( '.wikiEditor-ui-right' )
				.append( context.modules.toc.$toc );
			context.modules.toc.$toc.height(
				context.$ui.find( '.wikiEditor-ui-left' ).height()
			);
			$.wikiEditor.modules.toc.fn.redraw( context, $.wikiEditor.modules.toc.cfg.defaultWidth );
		},
		
	
	redraw: function( context, fixedWidth ) {
		var fixedWidth = parseFloat( fixedWidth );
		if( context.modules.toc.$toc.data( 'positionMode' ) == 'regular' ) {
			context.$ui.find( '.wikiEditor-ui-right' )
			.css( 'width', fixedWidth + 'px' );
			context.$ui.find( '.wikiEditor-ui-left' )
				.css( 'marginRight', ( -1 * fixedWidth ) + 'px' )
				.children()
				.css( 'marginRight', fixedWidth + 'px' );
		} else if( context.modules.toc.$toc.data( 'positionMode' ) == 'goofy' ) {
			context.$ui.find( '.wikiEditor-ui-left' )
				.css( 'width', fixedWidth );
			context.$ui.find( '.wikiEditor-ui-right' )
				.css( $.wikiEditor.modules.toc.cfg.rtl ? 'right': 'left', fixedWidth );
			context.$wikitext.css( 'height', context.$ui.find( '.wikiEditor-ui-right' ).height() );
		}
	},
	switchLayout: function( context ) {
		var width,
			height = context.$ui.find( '.wikiEditor-ui-right' ).height();
		if( context.modules.toc.$toc.data( 'positionMode' ) == 'regular'
		 	&& !context.modules.toc.$toc.data( 'collapsed' ) ) {
			// store position mode
			context.modules.toc.$toc.data( 'positionMode', 'goofy' );
			// store the width of the TOC, to ensure we dont allow it to be larger than this when switching back
			context.modules.toc.$toc.data( 'positionModeChangeAt', 
				context.$ui.find( '.wikiEditor-ui-right' ).width() );
			width = $.wikiEditor.modules.toc.cfg.textMinimumWidth;
			// set our styles for goofy mode
			context.$ui.find( '.wikiEditor-ui-left' )
				.css( { 'marginRight': '', 'position': 'absolute', 'float': 'none',
					'left': $.wikiEditor.modules.toc.cfg.rtl ? 'auto': 0, 
					'right' : $.wikiEditor.modules.toc.cfg.rtl ? 0 : 'auto' } )
				.children()
				.css( 'marginRight', '' );
			context.$ui.find( '.wikiEditor-ui-right' )
				.css( { 'width': 'auto', 'position': 'absolute', 'float': 'none',
				'right': $.wikiEditor.modules.toc.cfg.rtl ? 'auto': 0, 
				'left' : $.wikiEditor.modules.toc.cfg.rtl ? 0 : 'auto' } );
			context.$wikitext
				.css( 'position', 'relative' );
		} else if ( context.modules.toc.$toc.data( 'positionMode' ) == 'goofy' ) {
			// store position mode
			context.modules.toc.$toc.data( 'positionMode', 'regular' );
			// set width
			width = context.$wikitext.width() - context.$ui.find( '.wikiEditor-ui-left' ).width();
			if ( width > context.modules.toc.$toc.data( 'positionModeChangeAt' ) ) {
				width = context.modules.toc.$toc.data( 'positionModeChangeAt' );
			}
			// set our styles for regular mode
			context.$wikitext
				.css( { 'position': '', 'height': '' } );
			context.$ui.find( '.wikiEditor-ui-right' )
				.css( { 'marginRight': '', 'position': '', 'left': '', 'right': '', 'float': '', 'top': '', 'height': '' } );
			context.$ui.find( '.wikiEditor-ui-left' )
				.css( { 'width': '', 'position': '', 'left': '', 'float': '', 'right': '' } );
		}
		$.wikiEditor.modules.toc.fn.redraw( context, width );
	},
	disable: function( context ) {
		if ( context.modules.toc.$toc.data( 'collapsed' ) ) {
			context.$ui.find( '.wikiEditor-ui-toc-expandControl' ).hide();
		} else {
			if( context.modules.toc.$toc.data( 'positionMode' ) == 'goofy' ) {
				$.wikiEditor.modules.toc.fn.switchLayout( context );
			}
			context.$ui.find( '.wikiEditor-ui-right' ).hide();
			context.$ui.find( '.wikiEditor-ui-left' )
				.css( 'marginRight', '' )
				.children()
				.css( 'marginRight', '' );
		}
		context.modules.toc.$toc.data( 'positionMode', 'disabled' );
	},
	enable: function( context ) {
		context.modules.toc.$toc.data( 'positionMode', 'regular' );
		if ( context.modules.toc.$toc.data( 'collapsed' ) ) {
			context.$ui.find( '.wikiEditor-ui-toc-expandControl' ).show();
		} else {
			context.$ui.find( '.wikiEditor-ui-right' ).show();
			$.wikiEditor.modules.toc.fn.redraw( context, $.wikiEditor.modules.toc.cfg.minimumWidth );
			context.modules.toc.$toc.find( 'div' ).autoEllipsis( { 'position': 'right', 'tooltip': true, 'restoreText': true } );
		}
	},
	unhighlight: function( context ) {
		context.modules.toc.$toc.find( 'div' ).removeClass( 'current' );
	},
	/**
	 * Highlight the section the cursor is currently within
	 *
	 * @param {Object} context
	 */
	update: function( context ) {
		$.wikiEditor.modules.toc.fn.unhighlight( context );
		
		var div = context.fn.beforeSelection( 'div.wikiEditor-toc-header' );
		var section = div.data( 'section' ) || 0;
		if ( context.data.outline.length > 0 ) {
			var sectionLink = context.modules.toc.$toc.find( 'div.section-' + section );
			sectionLink.addClass( 'current' );
			
			// Scroll the highlighted link into view if necessary
			var relTop = sectionLink.offset().top - context.modules.toc.$toc.offset().top;
			var scrollTop = context.modules.toc.$toc.scrollTop();
			var divHeight = context.modules.toc.$toc.height();
			var sectionHeight = sectionLink.height();
			if ( relTop < 0 )
				// Scroll up
				context.modules.toc.$toc.scrollTop( scrollTop + relTop );
			else if ( relTop + sectionHeight > divHeight )
				// Scroll down
				context.modules.toc.$toc.scrollTop( scrollTop + relTop + sectionHeight - divHeight );
		}
	},
	
	/**
	 * Collapse the contents module
	 *
	 * @param {Object} event Event object with context as data
	 */
	collapse: function( event ) {
		var $this = $( this ), 
			context = $this.data( 'context' );
		if( context.modules.toc.$toc.data( 'positionMode' ) == 'goofy' ) {
			$.wikiEditor.modules.toc.fn.switchLayout( context );
		}
		var pT = $this.parent().position().top - 1;
		context.modules.toc.$toc.data( 'collapsed', true );
		context.$ui.find( '.wikiEditor-ui-left' )
			.animate( { 'marginRight': '-1px' }, 'fast', function() {
				$( this ).css( 'marginRight', 0 );
			} )
			.children()
			.animate( { 'marginRight': '1px' }, 'fast',  function() { 
				$( this ).css( 'marginRight', 0 ); 
			} );
		context.$ui.find( '.wikiEditor-ui-right' )
			.css( { 
				'marginTop' : '1px', 
				'position' : 'absolute', 
				'left' : $.wikiEditor.modules.toc.cfg.rtl ? 0 : 'auto', 
				'right' : $.wikiEditor.modules.toc.cfg.rtl ? 'auto' : 0, 
				'top' : pT } )
			.fadeOut( 'fast', function() {
				$( this ).hide()
				.css( { 'marginTop': '0', 'width': '1px' } );
				context.$ui.find( '.wikiEditor-ui-toc-expandControl' ).fadeIn( 'fast' );
				// Let the UI know things have moved around
				context.fn.trigger( 'tocCollapse' );
				context.fn.trigger( 'resize' );
			 } );
			
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
			openWidth = parseFloat( context.modules.toc.$toc.data( 'openWidth' ) ),
			availableSpace = context.$wikitext.width() - parseFloat( $.wikiEditor.modules.toc.cfg.textMinimumWidth );
		if ( availableSpace < $.wikiEditor.modules.toc.cfg.textMinmumWidth ) return false;
		context.modules.toc.$toc.data( 'collapsed', false );
		// check if we've got enough room to open to our stored width
		if ( availableSpace < openWidth ) openWidth = availableSpace;
		context.$ui.find( '.wikiEditor-ui-toc-expandControl' ).hide();
		context.$ui.find( '.wikiEditor-ui-left' )
			.animate( { 'marginRight': ( parseFloat( openWidth ) * -1 ) }, 'fast' )
			.children()
			.animate( { 'marginRight': openWidth }, 'fast' );
		context.$ui.find( '.wikiEditor-ui-right' )
			.show()
			.css( 'marginTop', '1px' )
			.animate( { 'width' : openWidth }, 'fast', function() {
				context.$content.trigger( 'mouseup' );
				$( this ).css( {
					'marginTop' : '0',
					'position' : 'relative',
					'right' : 'auto',
					'left' : 'auto',
					'top': 'auto' } );
					context.fn.trigger( 'tocExpand' );
					context.fn.trigger( 'resize' );
			 } );
		$.cookie( 'wikiEditor-' + context.instance + '-toc-width',
			context.modules.toc.$toc.data( 'openWidth' ) );
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
		 * Builds unordered list HTML object from structured outline
		 *
		 * @param {Object} structure Structured outline
		 */
		function buildList( structure ) {
			var list = $( '<ul />' );
			for ( i in structure ) {
				var wrapper = context.$content.find( '.wikiEditor-toc-section-' + structure[i].index );
				if ( wrapper.size() == 0 )
					wrapper = context.$content;
				var div = $( '<div />' )
					.addClass( 'section-' + structure[i].index )
					.data( 'wrapper', wrapper )
					.click( function( event ) {
						context.fn.scrollToTop( $( this ).data( 'wrapper' ), true );
						context.$textarea.textSelection( 'setSelection', {
							'start': 0,
							'startContainer': $(this).data( 'wrapper' )
						} );
						
						// Highlight the clicked link
						$.wikiEditor.modules.toc.fn.unhighlight( context );
						$( this ).addClass( 'current' );
						
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
					context.modules.toc.$toc.trigger( 'collapse.wikiEditor-toc' ); return false;
				} )
				.find( 'a' )
				.text( gM( 'wikieditor-toc-hide' ) );
			$expandControl
				.addClass( 'wikiEditor-ui-toc-expandControl' )
				.append( '<a href="#" />' )
				.bind( 'click.wikiEditor-toc', function() {
					context.modules.toc.$toc.trigger( 'expand.wikiEditor-toc' ); return false;
				} )
				.hide()
				.find( 'a' )
				.text( gM( 'wikieditor-toc-show' ) );
			$collapseControl.insertBefore( context.modules.toc.$toc );
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
				.resizable( { handles: 'w,e', preventPositionLeftChange: true, 
					minWidth: parseFloat( $.wikiEditor.modules.toc.cfg.minimumWidth ),
					start: function( e, ui ) {
						var $this = $( this );
						// Toss a transparent cover over our iframe
						$( '<div />' )
							.addClass( 'wikiEditor-ui-resize-mask' )
							.css( {
								'position': 'absolute',
								'z-index': 2,
								'left': 0,
								'top': 0,
								'bottom': 0,
								'right': 0
							} )
							.appendTo( context.$ui.find( '.wikiEditor-ui-left' ) );
						$this.resizable( 'option', 'maxWidth', $this.parent().width() - 
							parseFloat( $.wikiEditor.modules.toc.cfg.textMinimumWidth ) );
						if(context.modules.toc.$toc.data( 'positionMode' ) == 'goofy' ) {
							$.wikiEditor.modules.toc.fn.switchLayout( context );
						}
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
						if( ui.size.width < parseFloat( $.wikiEditor.modules.toc.cfg.minimumWidth ) ) {
							context.modules.toc.$toc.trigger( 'collapse.wikiEditor-toc' );
						} else {
							context.modules.toc.$toc.find( 'div' ).autoEllipsis( { 'position': 'right', 'tooltip': true, 'restoreText': true } );
							context.modules.toc.$toc.data( 'openWidth', ui.size.width );
							$.cookie( 'wikiEditor-' + context.instance + '-toc-width', ui.size.width );
						}
						// Let the UI know things have moved around
						context.fn.trigger( 'resize' );
					}
				});
			// Convert our east resize handle into a secondary west resize handle
			var handle = $.wikiEditor.modules.toc.cfg.rtl ? 'w' : 'e';
			context.$ui.find( '.ui-resizable-' + handle )
				.removeClass( 'ui-resizable-' + handle )
				.addClass( 'ui-resizable-' + ( handle == 'w' ? 'e' : 'w' ) )
				.addClass( 'wikiEditor-ui-toc-resize-grip' );
			// Bind collapse and expand event handlers to the TOC
			context.modules.toc.$toc
				.bind( 'collapse.wikiEditor-toc', $.wikiEditor.modules.toc.fn.collapse )
				.bind( 'expand.wikiEditor-toc', $.wikiEditor.modules.toc.fn.expand  );
			context.modules.toc.$toc.data( 'openWidth', $.wikiEditor.modules.toc.cfg.defaultWidth );
			// If the toc-width cookie is set, reset the widths based upon that
			if ( $.cookie( 'wikiEditor-' + context.instance + '-toc-width' ) == 0 ) {
				context.modules.toc.$toc.trigger( 'collapse.wikiEditor-toc', { data: context } );
			} else if ( $.cookie( 'wikiEditor-' + context.instance + '-toc-width' ) > 0 ) {
				var initialWidth = $.cookie( 'wikiEditor-' + context.instance + '-toc-width' );
				if( initialWidth < parseFloat( $.wikiEditor.modules.toc.cfg.minimumWidth ) )
					initialWidth = parseFloat( $.wikiEditor.modules.toc.cfg.minimumWidth ) + 1;
				context.modules.toc.$toc.data( 'openWidth', initialWidth + 'px' );
				$.wikiEditor.modules.toc.fn.redraw( context, initialWidth );
			}
		}
		
		// Normalize heading levels for list creation
		// This is based on Linker::generateTOC(), so it should behave like the
		// TOC on rendered articles does - which is considdered to be correct
		// at this point in time.
		if ( context.data.outline ) {
			var outline = context.data.outline;
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
				structure.unshift( { 'text': wgPageName.replace( /_/g, ' ' ), 'level': 1, 'index': 0 } );
			}
			context.modules.toc.$toc.html( buildList( structure ) );
			
			if ( wgNavigableTOCResizable && !context.$ui.data( 'resizableDone' ) ) {
				buildResizeControls();
				buildCollapseControls();
			}
			context.modules.toc.$toc.find( 'div' ).autoEllipsis( { 'position': 'right', 'tooltip': true, 'restoreText': true } );
		}
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
