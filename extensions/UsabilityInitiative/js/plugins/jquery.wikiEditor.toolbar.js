/**
 * Toolbar module for wikiEditor
 */

(function($) { $.wikiEditor.modules.toolbar = {
/**
 * Path to images - this is a bit messy, and it would need to change if this
 * code (and images) gets moved into the core - or anywhere for that matter...
 */
imgPath: wgScriptPath +
	'/extensions/UsabilityInitiative/images/wikiEditor/toolbar/',
/**
 * API accessible functions
 */
api: {
	addToToolbar: function( context, data ) {
		//
	},
	modifyToolbar: function( context, data ) {
		// 
	},
	removeFromToolbar: function( context, data ) {
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
fn: {
	// Wraps gM from js2, but allows raw text to supercede
	autoMsg: function( object, property ) {
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
	create: function( context, config ) {
		if ( '$toolbar' in context.modules ) {
			return;
		}
		context.modules.$toolbar = $( '<div></div>' )
			.addClass( 'wikiEditor-ui-toolbar' )
			.attr( 'id', 'wikiEditor-ui-toolbar' );
		$.wikiEditor.modules.toolbar.fn.build( context, config );
		context.$ui.find( '.wikiEditor-ui-top' )
			.append( context.modules.$toolbar );
	},
	/**
	 * Performs an operation based on parameters
	 * 
	 * @param {Object} context
	 * @param {Object} action
	 */
	doAction: function( context, action ) {
		switch ( action.type ) {
			case 'encapsulate':
				var parts = { 'pre': '', 'peri': '', 'post': '' };
				for ( part in parts ) {
					if ( part + 'Msg' in action.options ) {
						parts[part] = gM(
							action.options[part + 'Msg'],
							( action.options[part] || null )
						);
					} else {
						parts[part] = ( action.options[part] || '' )
					}
				}
				context.$textarea.encapsulateSelection(
					parts.pre, parts.peri, parts.post,
					action.options.ownline
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
						dialogConf.buttons[ gM( msg ) ] = dialogConf.buttons[msg];
						delete dialogConf.buttons[msg];
					}
					
					// Create the dialog <div>
					if ( typeof action.html == 'function' )
						action.html = action.html();
					$j( '<div></div> ')
						.attr( 'id', action.id )
						.html( action.html )
						.data( 'context', context )
						.appendTo( $j( 'body' ) )
						.dialog( dialogConf );
				}
				$j( '#' + action.id ).dialog( 'open' );
			break;
			default: break;
		}
	},
	buildSection: function( context, id, section ) {
		switch ( section.type ) {
			case 'toolbar':
				return $.wikiEditor.modules.toolbar.fn.buildToolbar(
					context, id, section
				);
			case 'booklet':
				return $.wikiEditor.modules.toolbar.fn.buildBooklet(
					context, id, section
				);
			default: return null;
		}
	},
	buildToolbar: function( context, id, toolbar ) {
		var $toolbar = $( '<div></div>' ).attr( {
			'class': 'toolbar section section-' + id,
			'rel': id
		} );
		if ( 'groups' in toolbar ) {
			for ( group in toolbar.groups ) {
				$toolbar.append(
					$.wikiEditor.modules.toolbar.fn.buildGroup(
						context, group, toolbar.groups[group]
					)
				);
			}
		}
		return $toolbar;
	},
	buildGroup: function( context, id, group ) {
		var $group = $( '<div></div>' ).attr( {
			'class': 'group group-' + id,
			'rel': id
		} );
		var label = $.wikiEditor.modules.toolbar.fn.autoMsg( group, 'label' );
		if ( label ) {
			$group.append(
				$( '<div></div>' ).text( label ).addClass( 'label' )
			)
		}
		if ( 'tools' in group ) {
			for ( tool in group.tools ) {
				$group.append(
					$.wikiEditor.modules.toolbar.fn.buildTool(
						context, tool, group.tools[tool]
					)
				);
			}
		}
		return $group;
	},
	buildTool: function( context, id, tool ) {
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
				$button = $( '<img />' ).attr( {
					'src': $.wikiEditor.modules.toolbar.imgPath + tool.icon,
					'alt': label,
					'title': label,
					'rel': id,
					'class': 'tool tool-' + id
				} );
				if ( 'action' in tool ) {
					$button
						.data( 'action', tool.action )
						.data( 'context', context )
						.click( function() {
							$.wikiEditor.modules.toolbar.fn.doAction(
								$(this).data( 'context' ),
								$(this).data( 'action' )
							);
							return false;
						} );
				}
				return $button;
			case 'select':
				var $select = $( '<select></select>' ).attr( {
					'rel': id,
					'class': 'tool tool-' + id
				} );
				$select.append( $( '<option></option>' ).text( label ) )
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
								.find(":selected").attr( 'selected', false )
								.find(":first").attr( 'selected', true );
							return false;
						} );
					for ( option in tool.list ) {
						var optionLabel =
							$.wikiEditor.modules.toolbar.fn.autoMsg(
								tool.list[option], 'label'
							);
						$select.append(
							$( '<option></option>' )
								.text( optionLabel )
								.attr( 'value', option )
						);
					}
				}
				return $select;
			default: return null;
		}
	},
	buildBooklet: function( context, id, booklet ) {
		var selected = $.cookie(
			'wikiEditor-' + context.instance + '-booklet-' + id + '-page'
		);
		var $booklet = $( '<div></div>' ).attr( {
			'class': 'booklet section section-' + id,
			'rel': id
		} );
		var $pages = $( '<div></div>' ).attr( 'class', 'pages' );
		var $index = $( '<div></div>' ).attr( 'class', 'index' );
		if ( 'pages' in booklet ) {
			if ( !( selected in booklet.pages ) ) {
				selected = null;
			}
			for ( page in booklet.pages ) {
				if ( selected === null ) {
					selected = page;
				}
				var $page = $.wikiEditor.modules.toolbar.fn.buildPage(
					context, page, booklet.pages[page]
				);
				var $bookmark = $.wikiEditor.modules.toolbar.fn.buildBookmark(
					context, page, booklet.pages[page]
				);
				if ( selected == page ) {
					$page.show();
					$bookmark.addClass( 'current' );
				} else {
					$page.hide();
				}
				$pages.append( $page );
				$index.append( $bookmark );
			}
		}
		return $booklet.append( $index ).append( $pages );
	},
	buildBookmark: function( context, id, page ) {
		var label = $.wikiEditor.modules.toolbar.fn.autoMsg( page, 'label' );
		return $( '<div></div>' )
			.text( label )
			.attr( 'rel', id )
			.data( 'context', context )
			.click( function() {
				$(this)
					.parent()
					.parent()
					.find( '.page' )
					.hide();
				$(this)
					.parent()
					.parent()
					.find( '.page-' + $(this).attr( 'rel' ) )
					.show();
				$(this).siblings().removeClass( 'current' );
				$(this).addClass( 'current' );
				var section = $(this).parent().parent().attr( 'rel' );
				$.cookie(
					'wikiEditor-' + $(this).data( 'context' ).instance +
						'-booklet-' + section + '-page',
					$(this).attr( 'rel' )
				);
			} );
	},
	buildPage: function( context, id, page ) {
		var $page = $( '<div></div>' ).attr( {
			'class': 'page page-' + id,
			'rel': id
		} );
		switch( page.layout ) {
			case 'table':
				$page.addClass( 'page-table' );
				var $table = $( '<table></table>' ).attr( {
					'cellpadding': '0',
					'cellspacing': '0',
					'border': '0',
					'width': '100%',
					'class': 'table table-' + id
				} );
				if ( 'headings' in page ) {
					var $headings = $( '<tr></tr>' );
					for ( heading in page.headings ) {
						var content =
							$.wikiEditor.modules.toolbar.fn.autoMsg(
									page.headings[heading], 'content'
							);
						$headings.append(
							$( '<th></th>' ).text( content )
						);
					}
					$table.append( $headings );
				}
				if ( 'rows' in page ) {
					for ( row in page.rows ) {
						var $row = $( '<tr></tr>' );
						for ( cell in page.rows[row] ) {
							var $cell = $( '<td></td>' ).attr( {
								'class': 'cell cell-' + cell,
								'valign': 'top'
							} );
							var content =
								$.wikiEditor.modules.toolbar.fn.autoMsg(
										page.rows[row][cell], 'content'
								);
							$cell.append(
								$( '<span></span>' ).html( content )
							);
							$row.append( $cell );
						}
						$table.append( $row );
					}
				}
				$page.append( $table );
				break;
			case 'characters':
				$page.addClass( 'page-characters' );
				$characters = $( '<div></div>' );
				if ( 'language' in page ) {
					$characters.attr( 'lang', page.language );
				}
				if ( 'direction' in page ) {
					$characters.attr( 'dir', page.direction );
				}
				if ( 'characters' in page ) {
					for ( character in page.characters ) {
						var tool = page.characters[character];
						if ( typeof tool == 'string' ) {
							tool = {
								'label': tool,
								'action': {
									'type': 'encapsulate',
									'options': { 'pre': tool }
								}
							};
						} else if ( 0 in tool && 1 in tool ) {
							tool = {
								'label': tool[0],
								'action': {
									'type': 'encapsulate',
									'options': { 'pre': tool[1] }
								}
							};
						}
						if ( 'action' in tool && 'label' in tool ) {
							var $character = $( '<a></a>' )
								.attr( 'rel', tool.label )
								.attr( 'href', '#' )
								.text( tool.label )
								.data( 'context', context )
								.data( 'action', tool.action )
								.click( function() {
									$.wikiEditor.modules.toolbar.fn.doAction(
										$(this).data( 'context' ),
										$(this).data( 'action' )
									);
									return false;
								} );
							$characters.append( $character );
						}
					}
					$page.append( $characters );
				}
				break;
		}
		return $page;
	},
	build: function( context, config ) {
		var $tabs = $( '<div></div>' )
			.addClass( 'tabs' )
			.appendTo( context.modules.$toolbar );
		var $sections = $( '<div></div>' )
			.addClass( 'sections' )
			.appendTo( context.modules.$toolbar );
		context.modules.$toolbar.append(
			$( '<div></div>' ).addClass( 'break' )
		);
		var selected = $.cookie(
			'wikiEditor-' + context.instance + '-toolbar-section'
		);
		var sectionQueue = [];
		for ( section in config ) {
			if ( section == 'main' ) {
				context.modules.$toolbar.prepend(
					$.wikiEditor.modules.toolbar.fn.buildSection(
						context, section, config[section]
					)
				);
			} else {
				s = {
					'context': context,
					'$sections': $sections,
					'$tabs': $tabs,
					'section': section,
					'config': config[section],
					'selected': ( selected == section )
				};
				sectionQueue[sectionQueue.length] = s;
				s.$tabs.append(
					$( '<span></span>' )
						.attr( {
							'class': 'tab tab-' + s.section,
							'rel': s.section
						} )
						.append(
							$( '<a></a>' )
							.addClass( s.selected ? 'current' : null )
							.attr( 'href', '#' )
							.text(
								$.wikiEditor.modules.toolbar.fn.autoMsg(
									s.config, 'label'
								)
							)
							.data( 'context', s.context )
							.click( function() {
								var $section =
									$(this).data( 'context' ).$ui.find(
											'.section-' +
											$(this).parent().attr( 'rel' )
									);
								$(this).blur();
								var show = $section.css( 'display' ) == 'none';
								$section.parent().children().hide();
								$(this)
									.parent()
									.parent()
									.find( 'a' )
									.removeClass( 'current' );
								if ( show ) {
									$section.show();
									$(this).addClass( 'current' );
								}
								$.cookie(
									'wikiEditor-' +
										$(this).data( 'context' ).instance +
										'-toolbar-section',
									show ? $section.attr( 'rel' ) : null
								);
								return false;
							} )
						)
				);
			}
		}
		$.eachAsync( sectionQueue, {
			'bulk': 0,
			'end': function() {
				// HACK: Opera doesn't seem to want to redraw after these bits
				// are added to the DOM, so we can just FORCE it!
				$( 'body' ).css( 'position', 'static' );
				$( 'body' ).css( 'position', 'relative' );
			},
			'loop': function( i, s ) {
				s.$sections.append(
					$.wikiEditor.modules.toolbar.fn.buildSection(
						s.context, s.section, s.config
					)
					.css( 'display', s.selected ? 'block' : 'none' )
				);
				
			}
		} );
	}
}

};})(jQuery);