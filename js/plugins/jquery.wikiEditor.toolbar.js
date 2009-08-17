/**
 * Toolbar module for wikiEditor
 */
( function( $ ) { $.wikiEditor.modules.toolbar = {

/**
 * Path to images - this is a bit messy, and it would need to change if
 * this code (and images) gets moved into the core - or anywhere for
 * that matter...
 */
imgPath : wgScriptPath + '/extensions/UsabilityInitiative/images/wikiEditor/toolbar/',
/**
 * API accessible functions
 */
api : {
	addToToolbar : function( context, data ) {
	for ( type in data ) {
		switch ( type ) {
			case 'sections':
				var $sections = context.modules.$toolbar
				.find( 'div.sections' );
				var $tabs = context.modules.$toolbar
				.find( 'div.tabs' );
				for ( section in data[type] ) {
					if ( section == 'main' ) {
						// Section
						context.modules.$toolbar
						.prepend(
							$.wikiEditor.modules.toolbar.fn.buildSection(
								context, section, data[type][section]
							)
						);
						continue;
					}
					// Section
					$sections.append(
						$.wikiEditor.modules.toolbar.fn.buildSection( context, section, data[type][section] )
					);
					// Tab
					$tabs.append( $.wikiEditor.modules.toolbar.fn.buildTab( context, section, data[type][section] ) );
				}
				break;
			case 'groups':
				if ( ! ( 'section' in data ) ) {
					continue;
				}
				var $section = context.modules.$toolbar
				.find( 'div[rel=' + data.section + '].section' );
				for ( group in data[type] ) {
					// Group
					$section
					.append( $.wikiEditor.modules.toolbar.fn.buildGroup( context, group, data[type][group] ) );
				}
				break;
			case 'tools':
				if ( ! ( 'section' in data && 'group' in data ) ) {
					continue;
				}
				var $group = context.modules.$toolbar
				.find( 'div[rel=' + data.section + '].section ' + 'div[rel=' + data.group + '].group' );
				for ( tool in data[type] ) {
					// Tool
					$group.append( $.wikiEditor.modules.toolbar.fn.buildTool( context, tool,data[type][tool] ) );
				}
				break;
			case 'pages':
				if ( ! ( 'section' in data ) ) {
					continue;
				}
				var $pages = context.modules.$toolbar
				.find( 'div[rel=' + data.section + '].section .pages' );
				var $index = context.modules.$toolbar
				.find( 'div[rel=' + data.section + '].section .index' );
				for ( page in data[type] ) {
					// Page
					$pages.append( $.wikiEditor.modules.toolbar.fn.buildPage( context, page, data[type][page] ) );
					// Index
					$index.append( $.wikiEditor.modules.toolbar.fn.buildBookmark( context, page, data[type][page] ) );
				}
				$.wikiEditor.modules.toolbar.fn.updateBookletSelection( context, page, $pages, $index );
				break;
			case 'rows':
				if ( ! ( 'section' in data && 'page' in data ) ) {
					continue;
				}
				var $table = context.modules.$toolbar.find(
					'div[rel=' + data.section + '].section ' + 'div[rel=' + data.page + '].page table'
				);
				for ( row in data[type] ) {
					// Row
					$table.append( $.wikiEditor.modules.toolbar.fn.buildRow( context, data[type][row] ) );
				}
				break;
			case 'characters':
				if ( ! ( 'section' in data && 'page' in data ) ) {
					continue;
				}
				$characters = context.modules.$toolbar.find(
					'div[rel=' + data.section + '].section ' + 'div[rel=' + data.page + '].page div'
				);
				var actions = $characters.data( 'actions' );
				for ( character in data[type] ) {
					// Character
					$characters
					.append(
						$( $.wikiEditor.modules.toolbar.fn.buildCharacter( data[type][character], actions ) )
							.click( function() {
								$.wikiEditor.modules.toolbar.fn.doAction( $(this).parent().data( 'context' ),
								$(this).parent().data( 'actions' )[$(this).attr( 'rel' )] );
								return false;
							} )
					);
				}
				break;
			default: break;
		}
	}
},
removeFromToolbar : function( context, data ) {
	if ( typeof data.section == 'string' ) {
		// Section
		var tab = 'div.tabs span[rel=' + data.section + '].tab';
		var target = 'div[rel=' + data.section + '].section';
		if ( typeof data.group == 'string' ) {
			// Toolbar group
			target += ' div[rel=' + data.group + '].group';
			if ( typeof data.tool == 'string' ) {
				// Tool
				target += ' div[rel=' + data.tool + '].tool';
			}
		} else if ( typeof data.page == 'string' ) {
			// Booklet page
			var index = target + ' div.index div[rel=' + data.page + ']';
			target += ' div.pages div[rel=' + data.page + '].page';
			if ( typeof data.character == 'string' ) {
				// Character
				target += ' a[rel=' + data.character + ']';
			} else if ( typeof data.row == 'number' ) {
				// Table row
				target += ' table tr:not(:has(th)):eq(' + data.row + ')';
			} else {
				// Just a page, remove the index too!
				context.modules.$toolbar.find( index ).remove();
				$.wikiEditor.modules.toolbar.fn.updateBookletSelection(
					context,
					null,
					context.modules.$toolbar.find( target ),
					context.modules.$toolbar.find( index )
				);
			}
		} else {
			// Just a section, remove the tab too!
				context.modules.$toolbar.find( tab ).remove();
			}
			context.modules.$toolbar.find( target ).remove();
		}
	}
},
/**
 * Internally used functions
 */
fn : {
	// Wraps gM from js2, but allows raw text to supercede
	autoMsg : function( object, property ) {
		if ( property in object ) {
			return object[property];
		} else if ( property + 'Msg' in object ) {
			return gM( object[property + 'Msg'] );
		} else {
			return '';
		}
	},
	/**
	 * Creates a toolbar module within a wikiEditor
	 * 
	 * @param {Object} context Context object of editor to create module in
	 * @param {Object} config Configuration object to create module from
	 */
	create : function( context, config ) {
		if ( '$toolbar' in context.modules ) {
			return;
		}
		context.modules.$toolbar = $( '<div />' )
			.addClass( 'wikiEditor-ui-toolbar' )
			.attr( 'id', 'wikiEditor-ui-toolbar' );
		$.wikiEditor.modules.toolbar.fn.build( context, config );
		context.$ui.find( '.wikiEditor-ui-top' ).append( context.modules.$toolbar );
	},
	/**
	 * Performs an operation based on parameters
	 * 
	 * @param {Object} context
	 * @param {Object} action
	 */
	doAction : function( context, action ) {
		switch ( action.type ) {
			case 'replace':
			case 'encapsulate':
				var parts = { 'pre' : '', 'peri' : '', 'post' : '' };
				for ( part in parts ) {
					if ( part + 'Msg' in action.options ) {
						parts[part] = gM( action.options[part + 'Msg'], ( action.options[part] || null ) );
					} else {
						parts[part] = ( action.options[part] || '' )
					}
				}
				context.$textarea.encapsulateSelection(
					parts.pre, parts.peri, parts.post, action.options.ownline, action.type == 'replace'
				);
				break;
			case 'dialog':
				if ( $j( '#' + action.id ).size() == 0 ) {
					var dialogConf = action.dialog;
					// Add some stuff to dialogConf
					dialogConf.bgiframe = true;
					dialogConf.autoOpen = false;
					dialogConf.modal = true;
					dialogConf.title = gM( action.titleMsg );
					
					// Transform messages in keys
					// Stupid JS won't let us do stuff like
					// foo = { gM ('bar'): baz }
					for ( msg in dialogConf.buttons ) {
						dialogConf.buttons[gM( msg )] = dialogConf.buttons[msg];
						delete dialogConf.buttons[msg];
					}
					
					// Create the dialog <div>
					$j( '<div /> ' )
						.attr( 'id', action.id )
						.html( action.html )
						.data( 'context', context )
						.appendTo( $j( 'body' ) )
						.each( action.init ).dialog( dialogConf );
				}
				$j( '#' + action.id ).dialog( 'open' );
				break;
			default: break;
		}
	},
	buildGroup : function( context, id, group ) {
		var $group = $( '<div />' ).attr( { 'class' : 'group group-' + id, 'rel' : id } );
		var label = $.wikiEditor.modules.toolbar.fn.autoMsg( group, 'label' );
		if ( label ) {
			$group.append( '<div class="label">' + label + '</div>' )
		}
		if ( 'tools' in group ) {
			for ( tool in group.tools ) {
				$group.append( $.wikiEditor.modules.toolbar.fn.buildTool( context, tool, group.tools[tool] ) );
			}
		}
		return $group;
	},
	buildTool : function( context, id, tool ) {
		if ( 'filters' in tool ) {
			for ( filter in tool.filters ) {
				if ( $( tool.filters[filter] ).size() == 0 ) {
					return null;
				}
			}
		}
		var label = $.wikiEditor.modules.toolbar.fn.autoMsg( tool, 'label' );
		switch ( tool.type ) {
			case 'button':
				var src = tool.icon;
				if ( src.indexOf( 'http://' ) !== 0 && src.indexOf( 'https://' ) !== 0 ) {
					src = $.wikiEditor.modules.toolbar.imgPath + src;
				}
				$button = $( '<img />' ).attr( {
					'src' : src,
					'width' : 22,
					'height' : 22,
					'alt' : label,
					'title' : label,
					'rel' : id,
					'class' : 'tool tool-' + id
				} );
				if ( 'action' in tool ) {
					$button
						.data( 'action', tool.action )
						.data( 'context', context )
						.click( function() {
							$.wikiEditor.modules.toolbar.fn.doAction(
								$(this).data( 'context' ), $(this).data( 'action' )
							);
							return false;
						} );
				}
				return $button;
			case 'select':
				var $select = $( '<select />' ).attr( { 'rel' : id, 'class' : 'tool tool-' + id } );
				$select.append( $( '<option />' ).text( label ) )
				if ( 'list' in tool ) {
					$select
						.data( 'list', tool.list )
						.data( 'context', context )
						.click( function() {
							var list = $(this).data( 'list' );
							var val = $(this).val();
							if ( val in list && 'action' in list[val] ) {
								$.wikiEditor.modules.toolbar.fn.doAction(
									$(this).data( 'context' ), list[val].action
								);
							}
							$(this)
								.find( ":selected" )
								.attr( 'selected', false )
								.find( ":first" )
								.attr( 'selected', true );
							return false;
						} );
					for ( option in tool.list ) {
						var optionLabel = $.wikiEditor.modules.toolbar.fn.autoMsg( tool.list[option], 'label' );
						$select.append( $( '<option />' ).text( optionLabel ).attr( 'value', option ) );
					}
				}
				return $select;
			default:
				return null;
		}
	},
	buildBookmark : function( context, id, page ) {
		var label = $.wikiEditor.modules.toolbar.fn.autoMsg( page,
		'label' );
		return $( '<div />' )
			.text( label )
			.attr( 'rel', id )
			.data( 'context', context )
			.click(
				function() {
					$(this).parent().parent().find( '.page' ).hide();
					$(this).parent().parent().find( '.page-' + $(this).attr( 'rel' ) ).show();
					$(this).siblings().removeClass( 'current' );
					$(this).addClass( 'current' );
					var section = $(this).parent().parent().attr( 'rel' );
					$.cookie(
						'wikiEditor-' + $(this).data( 'context' ).instance + '-booklet-' + section + '-page',
						$(this).attr( 'rel' )
					);
				} );
	},
	buildPage : function( context, id, page ) {
		var $page = $( '<div />' ).attr( {
			'class' : 'page page-' + id,
			'rel' : id
		} );
		switch ( page.layout ) {
			case 'table':
				$page.addClass( 'page-table' );
				var html =
					'<table cellpadding=0 cellspacing=0 ' + 'border=0 width="100%" class="table table-"' + id + '">';
				if ( 'headings' in page ) {
					html += $.wikiEditor.modules.toolbar.fn.buildHeading( context, page.headings )
				}
				if ( 'rows' in page ) {
					for ( row in page.rows ) {
						html += $.wikiEditor.modules.toolbar.fn.buildRow( context, page.rows[row] )
					}
				}
				$page.html( html );
				break;
			case 'characters':
				$page.addClass( 'page-characters' );
				$characters = $( '<div />' ).data( 'context', context ).data( 'actions', {} );
				var actions = $characters.data( 'actions' );
				if ( 'language' in page ) {
					$characters.attr( 'lang', page.language );
				}
				if ( 'direction' in page ) {
					$characters.attr( 'dir', page.direction );
				}
				if ( 'characters' in page ) {
					var html = '';
					for ( character in page.characters ) {
						html += $.wikiEditor.modules.toolbar.fn.buildCharacter( page.characters[character], actions );
					}
					$characters
						.html( html )
						.children()
						.click( function() {
							$.wikiEditor.modules.toolbar.fn.doAction(
								$(this).parent().data( 'context' ),
								$(this).parent().data( 'actions' )[$(this).attr( 'rel' )]
							);
							return false;
						} );
				}
				$page.append( $characters );
				break;
		}
		return $page;
	},
	buildHeading : function( context, headings ) {
		var html = '<tr>';
		for ( heading in headings ) {
			html += '<th>' + $.wikiEditor.modules.toolbar.fn.autoMsg( headings[heading], 'content' ) + '</th>';
		}
		return html;
	},
	buildRow : function( context, row ) {
		var html = '<tr>';
		for ( cell in row ) {
			html += '<td class="cell cell-' + cell + '" valign="top"><span>' +
				$.wikiEditor.modules.toolbar.fn.autoMsg( row[cell], 'content' ) + '</span></td>';
		}
		html += '</tr>';
		return html;
	},
	buildCharacter : function( character, actions ) {
		if ( typeof character == 'string' ) {
			character = {
				'label' : character,
				'action' : {
					'type' : 'encapsulate',
					'options' : {
						'pre' : character
					}
				}
			};
		} else if ( 0 in character && 1 in character ) {
			character = {
				'label' : character[0],
				'action' : {
					'type' : 'encapsulate',
					'options' : {
						'pre' : character[1]
					}
				}
			};
		}
		if ( 'action' in character && 'label' in character ) {
			actions[character.label] = character.action;
			return '<a rel="' + character.label + '" href="#">' + character.label + '</a>';
		}
	},
	buildTab : function( context, id, section ) {
		var selected = $
		.cookie( 'wikiEditor-' + context.instance + '-toolbar-section' );
		return $( '<span />' )
		.attr( { 'class' : 'tab tab-' + id, 'rel' : id } )
		.append(
			$( '<a />' )
				.addClass( selected == id ? 'current' : null )
				.attr( 'href', '#' )
				.text( $.wikiEditor.modules.toolbar.fn.autoMsg( section, 'label' ) )
				.data( 'context', context )
				.click( function() {
					var $section =
						$(this).data( 'context' ).$ui.find( '.section-' + $(this).parent().attr( 'rel' ) );
					$(this).blur();
					var show = $section.css( 'display' ) == 'none';
					$section.parent().children().hide();
					$(this).parent().parent().find( 'a' ).removeClass( 'current' );
					if ( show ) {
						$section.show();
						$(this).addClass( 'current' );
					}
					$.cookie(
						'wikiEditor-' + $(this).data( 'context' ).instance + '-toolbar-section',
						show ? $section.attr( 'rel' ) : null
					);
					return false;
				} )
		);
	},
	buildSection : function( context, id, section ) {
		var selected = $
		.cookie( 'wikiEditor-' + context.instance + '-toolbar-section' );
		var $section;
		switch ( section.type ) {
			case 'toolbar':
				var $section = $( '<div />' ).attr( { 'class' : 'toolbar section section-' + id, 'rel' : id } );
				if ( 'groups' in section ) {
					for ( group in section.groups ) {
						$section.append(
							$.wikiEditor.modules.toolbar.fn.buildGroup( context, group, section.groups[group] )
						);
					}
				}
				break;
			case 'booklet':
				var $pages = $( '<div />' ).addClass( 'pages' );
				var $index = $( '<div />' ).addClass( 'index' );
				if ( 'pages' in section ) {
					for ( page in section.pages ) {
						$pages.append(
							$.wikiEditor.modules.toolbar.fn.buildPage( context, page, section.pages[page] )
						);
						$index.append(
							$.wikiEditor.modules.toolbar.fn.buildBookmark( context, page, section.pages[page] )
						);
					}
				}
				$section = $( '<div />' ).attr( { 'class' : 'booklet section section-' + id, 'rel' : id } )
					.append( $index )
					.append( $pages );
				$.wikiEditor.modules.toolbar.fn.updateBookletSelection( context, page, $pages, $index );
				break;
		}
		if ( $section !== null && id !== 'main' ) {
			$section.css( 'display', selected == id ? 'block' : 'none' );
		}
		return $section;
	},
	updateBookletSelection : function( context, id, $pages, $index ) {
		var cookie = 'wikiEditor-' + context.instance + '-booklet-' + id + '-page';
		var selected = $.cookie( cookie );
		var $selectedIndex = $index.find( '*[rel=' + selected + ']' );
		if ( $selectedIndex.size() == 0 ) {
			selected = $index.children().eq( 0 ).attr( 'rel' );
			$.cookie( cookie, selected );
		}
		$pages.children().hide();
		$pages.find( '*[rel=' + selected + ']' ).show();
		$index.children().removeClass( 'current' );
		$selectedIndex.addClass( 'current' );
	},
	build : function( context, config ) {
		var $tabs = $( '<div />' ).addClass( 'tabs' ).appendTo( context.modules.$toolbar );
		var $sections = $( '<div />' ).addClass( 'sections' ).appendTo( context.modules.$toolbar );
		context.modules.$toolbar.append( $( '<div />' ).css( 'clear', 'both' ) );
		var sectionQueue = [];
		for ( section in config ) {
			if ( section == 'main' ) {
				context.modules.$toolbar.prepend(
					$.wikiEditor.modules.toolbar.fn.buildSection( context, section, config[section] )
				);
			} else {
				sectionQueue.push( {
					'$sections' : $sections,
					'context' : context,
					'id' : section,
					'config' : config[section]
				} );
				$tabs.append( $.wikiEditor.modules.toolbar.fn.buildTab( context, section, config[section] ) );
			}
		}
		$.eachAsync( sectionQueue, {
			'bulk' : 0,
			'end' : function() {
				// HACK: Opera doesn't seem to want to redraw after
				// these bits
				// are added to the DOM, so we can just FORCE it!
				$( 'body' ).css( 'position', 'static' );
				$( 'body' ).css( 'position', 'relative' );
			},
			'loop' : function( i, s ) {
				s.$sections.append( $.wikiEditor.modules.toolbar.fn.buildSection( s.context, s.id, s.config ) );
			}
		} );
	}
}

}; } )( jQuery );
