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
 * Creates a toolbar module within a wikiEditor
 * 
 * @param {Object} context Context object of editor to create module in
 * @param {Object} configuration Configuration object to create module from
 */
create: function( context, configuration ) {
	if ( '$toolbar' in context.modules ) {
		return;
	}
	context.modules.$toolbar = $( '<div></div>' )
		.addClass( 'wikiEditor-ui-toolbar' );
	$.wikiEditor.modules.toolbar.build( context, configuration );
	context.$ui.find( '.wikiEditor-ui-top' )
		.append( context.modules.$toolbar );
},
/**
 * Performs an operation based on parameters
 * 
 * @param {Object} action
 */
performAction: function( context, action ) {
	switch ( action.type) {
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
				parts.pre, parts.peri, parts.post
			);
		break;
		default: break;
	}
},
/**
 * Adds a toolbar section to a containing div
 * 
 * @param {Object} $section Container to add section content to
 * @param {Object} section Configuration to build toolbar from
 * @param {String} sectionId Unique identifier of this section
 */
addSection: function( context, $section, section, sectionId ) {
	/**
	 * Wraps performAction with tool specific UI interaction
	 */
	var useTool = function() {
		var tool = $(this).data( 'tool' );
		if ( 'type' in tool ) {
			switch ( tool.type ) {
				case 'button':
				case 'link':
					if ( 'action' in tool ) {
						$.wikiEditor.modules.toolbar.performAction(
							context, tool.action
						);
					}
					break;
				case 'select':
					if ( 'list' in tool && $(this).val() in tool.list ) {
						$.wikiEditor.modules.toolbar.performAction(
							context, tool.list[$(this).val()].action
						);
					}
					$(this).find(":selected").attr( 'selected', false );
					$(this).find(":first").attr( 'selected', true );
					break;
			}
		}
		return false;
	}
	/**
	 * Adds tools to a group
	 * 
	 * @param {Object} $group jQuery selection to add tools to
	 * @param {Object} tools Configurations for tools
	 * @param {String} sectionId Unique ID of section
	 */
	function addTools( $group, tools, sectionId ) {
		for ( tool in section.groups[group].tools ) {
			// Filters are the input to a jQuery selector. The tool will
			// only be shown if the selection contains one or more elements
			if ( 'filters' in tools[tool] ) {
				var skip = false;
				for ( filter in tools[tool].filters ) {
					if ( $( tools[tool].filters[filter] ).size() == 0 ) {
						skip = true;
					}
				}
				if ( skip ) {
					continue;
				}
			}
			var label = msg( tools[tool], 'label' );
			switch ( tools[tool].type ) {
				case 'button':
					$group.append(
						$( '<input />' )
						.attr( {
							'src': $.wikiEditor.modules.toolbar.imgPath +
								tools[tool].icon,
							'alt': label,
							'title': label,
							'type': 'image'
						} )
						.addClass( 'tool' )
						.addClass( 'tool-' + tool )
						.data( 'tool', tools[tool] )
						.click( useTool )
					);
					break;
				case 'select':
					var $select = $( '<select></select>' )
						.data( 'tool', tools[tool] )
						.change( useTool )
						.append( $( '<option></option>' ).text( label ) )
						.appendTo( $group );
					for ( option in tools[tool].list ) {
						$select.append(
							$( '<option></option>' )
								.text(
									msg( tools[tool].list[option], 'label' )
								)
								.attr( 'value', option )
						);
					}
					break;
			}
		}
	}
	/**
	 * Adds pages to a booklet
	 * 
	 * @param {Object} $index jQuery selection to add index entry to
	 * @param {Object} $pages jQuery selection to add pages to
	 * @param {Object} pages Configurations for pages
	 * @param {String} sectionId Unique ID of section
	 */
	function addPages( $index, $pages, pages, sectionId ) {
		var selected = $.cookie( sectionId ); 
		// The pages may have changed since the user was last here, so we
		// must check that the page they want to default to still exists
		if ( !( selected in pages ) ) {
			selected = null;
		}
		for ( page in pages ) {
			// If there's no layout property, we can just skip over this one
			if ( !( 'layout' in pages[page] ) ) {
				continue;
			}
			// When no page state is present, deafult to the first page
			if ( selected == null ) {
				selected = page;
			}
			// Add an entry to the index of pages so the user can navigate
			// from one to another
			$index.append(
				$( '<div></div>' )
					.attr( 'class', page === selected ? 'current' : null )
					.text( msg( pages[page], 'label' ) )
					.data( 'page', page )
					.data( 'sectionId', sectionId )
					.click( function() {
						$(this)
							.parent()
							.parent()
							.find( '.page' )
							.hide()
							.end()
							.parent()
							.find( 'div' )
							.removeClass( 'current' )
							.end()
							.parent()
							.parent()
							.find( '.page-' + $(this).data( 'page' ) )
							.show();
						$(this).addClass( 'current' );
						// Store the state each time the user changes pages
						$.cookie(
							$(this).data( 'sectionId'),
							$(this).data( 'page' )
						);
					} )
			);
			// Add the content of the page and only show the selected one
			var $page = $( '<div></div>' )
				.addClass( 'page' )
				.addClass( 'page-' + page )
				.css( 'display', page == selected ? 'block' : 'none' )
				.appendTo( $pages );
			// Depending on the layout, we can render different page types
			switch ( pages[page].layout ) {
				case 'table':
					var $table = $( '<table></table>' )
						.attr( {
							'cellpadding': '0',
							'cellspacing': '0',
							'border': '0',
							'width': '100%'
						} )
						.appendTo( $page );
					if (
						'headings' in pages[page] &&
						typeof pages[page].headings == 'object'
					) {
						var $headings = $( '<tr></tr>' ).appendTo( $table );
						for ( heading in pages[page].headings ) {
							var content = msg(
								pages[page].headings[heading], 'content'
							);
							$( '<th></th>' )
								.text( content )
								.appendTo( $headings );
						}
					}
					if (
						'rows' in pages[page] &&
						typeof pages[page].rows == 'object'
					) {
						for ( row in pages[page].rows ) {
							var $row = $( '<tr></tr>' ).appendTo( $table );
							for ( cell in pages[page].rows[row] ) {
								var content = msg(
									pages[page].rows[row][cell], 'content'
								);
								$( '<td></td>' )
									.addClass( cell )
									.attr( 'valign', 'top' )
									.append(
										$( '<span></span>' ).html( content )
									)
									.appendTo( $row );
							}
						}
					}
				break;
				case 'characters':
					var $characters = $( '<div></div>' )
						.attr( pages[page].attributes )
						.css( pages[page].styles )
						.appendTo( $page );
					if (
						'characters' in pages[page] &&
						typeof pages[page].characters == 'object'
					) {
						for ( character in pages[page].characters ) {
							var char = pages[page].characters[character];
							var tool = {};
							/*
							 * The contents of char may be a string, or an
							 * object. If it's a string the string is both
							 * the label and the inserted value treated as
							 * a pre parameter to the encapsulateSelection
							 * action. If it's an object, the object must
							 * contain a label or it will be skipped - and
							 * the entire object is passed through as the
							 * tool configuration so it must contain valid
							 * tool configuration content as well.
							 */
							if ( typeof char == 'string' ) {
								tool = {
									'type': 'link',
									'label': char,
									'action': {
										'type': 'encapsulate',
										'options': {
											'pre': char
										}
									}
								};
							} else if ( typeof char == 'object' ) {
								tool = char;
							} else {
								continue;
							}
							if ( !( 'label' in tool ) ) {
								continue;
							}
							$characters.append(
								$( '<a></a>' )
									.attr( 'href', '#' )
									.text( tool.label )
									.data( 'tool', tool )
									.click( useTool )
							);
						}
					}
					break;
			}
		}
	}
	// Wraps gM from js2, but allows raw text to supercede
	function msg( object, property ) {
		return object[property] || gM( object[property + 'Msg'] );
	}
	// Checks if a message of any kind is in an object
	function objHasMsg( object, property ) {
		return property in object || property + 'Msg' in object;
	}
	switch ( section.type ) {
		case 'toolbar':
			// Tools must be in groups, so if there're no groups this part
			// of the configuration is not valid and we need to skip over it
			if ( !( 'groups' in section ) ) {
				return;
			}
			for ( group in section.groups ) {
				var $group = $( '<div></div>' )
					.attr( 'class', 'group' )
					.appendTo( $section );
				if ( objHasMsg( section.groups[group], 'label' ) ) {
					$group.append(
						$( '<div></div>' )
							.attr( 'class', 'label' )
							.text( msg( section.groups[group], 'label' ) )
					)
				}
				addTools( $group, section.groups[group].tools, sectionId );
			}
		break;
		case 'booklet':
			if ( !( 'pages' in section ) ) {
				return;
			}
			var $index = $( '<div></div>' )
				.attr( 'class', 'index' )
				.appendTo( $section );
			var $pages = $( '<div></div>' )
				.attr( 'class', 'pages' )
				.appendTo( $section );
			addPages( $index, $pages, section.pages, sectionId );
			break;
	}
},
/**
 * Builds toolbar
 * 
 * @param {Object} textbox
 * @param {Object} configuration
 */
build: function( context, configuration, editorId ) {
	if ( 'main' in configuration ) {
		// Handle the main specially both for layout purposes and
		// so that it is rendered immediately while the other sections are
		// rendered asynchronously and possibly much later
		$.wikiEditor.modules.toolbar.addSection(
			context, context.modules.$toolbar, configuration.main, 'main'
		);
	}
	// Create a base name for keys that will be stored in a cookie which
	// maintain the state of which sections are open and closed
	var sectionIdBase = editorId + '-wikiEditor-ui-toolbar-section';
	// Create some containers for various elements and append them
	var $tabs = $( '<div></div>' )
		.addClass( 'tabs' )
		.appendTo( context.modules.$toolbar );
	var $sections = $( '<div></div>' )
		.addClass( 'sections' )
		.appendTo( context.modules.$toolbar );
	context.modules.$toolbar.append(
		$( '<div></div>' ).addClass( 'break' )
	);
	// To prevent slow page rendering times, we store the individual
	// section configurations in a queue to be built asynchrnously later on
	var sectionQueue = [];
	for ( section in configuration ) {
		// Skip over main section since it's been handled specially above
		if ( section == 'main' ) {
			continue;
		}
		// Add section container, initially in loading class - but that will
		// get removed once the section is done being built
		var sectionCookie = editorId + '-section';
		var sectionId = sectionCookie + '-' + section.type + '-' + section;
		var $section = $( '<div></div>' )
			.addClass( 'section loading' )
			.addClass( 'section-' + configuration[section].type )
			.addClass(
				'section-' + configuration[section].type + '-' + section
			)
			.attr( 'id', sectionId )
			.append(
				$( '<div></div>' )
					.addClass( 'spinner' )
					.text( gM( 'edittoolbar-loading' ) )
			)
			.appendTo( $sections );
		// Recall the state from cookie
		var current = false;
		if ( $.cookie( sectionCookie ) == sectionId ) {
			$section.attr( 'style', 'display:block' );
			current = true;
		}
		// Add section to queue for later processing
		sectionQueue[sectionQueue.length] = {
			'$section': $section,
			'tools': configuration[section],
			'id': sectionId
		};
		// Add a tab the user can click to hide and show the section
		$tabs.append(
			$( '<span></span>' )
				.attr( 'class', 'tab' )
				.append(
					$( '<a></a>' )
						.text(
							configuration[section].label ||
							gM( configuration[section].labelMsg )
						)
						.attr( { 'href': '#', 'rel': section } )
						.addClass( current ? 'current' : null )
						.data( '$section', $section )
						.data( 'sectionCookie', sectionCookie )
						.click( function() {
							var $section = $(this).data( '$section' );
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
								$(this).data( 'sectionCookie' ),
								show ? $section.attr( 'id' ) : null
							);
							return false;
						} )
				)
		);
	}
	// Process the section queue
	$.eachAsync( sectionQueue, {
		bulk: 0,
		loop: function( index, section ) {
			$.wikiEditor.modules.toolbar.addSection(
				context, section.$section, section.tools, section.id
			);
			// When addSection is done, we can remove the loading
			// class to hide the spinner and reveal the content
			section.$section.removeClass( 'loading' )
		}
	} );
}

};})(jQuery);