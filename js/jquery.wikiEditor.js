/**
 * This is the toolbar plugin, which can be used like
 * $j( 'div#edittoolbar' ).toolbar( '#wpTextbox1', tools );
 * Where tools is an array of objects which describe each tool (see below for
 * specific examples) (THIS NEEDS BETTER DOCUMENTATION WHEN I HAVE TIME)
 */
( function( $ ) { $.fn.wikiEditor = function() {

/* Configuration */

// Path to images (THIS WILL HAVE TO CHANGE IF YOU MOVE THIS INTO CORE)
var imgPath = wgScriptPath +
	'/extensions/UsabilityInitiative/images/wikiEditor/toolbar/';

/* Initialization */

// The wikiEditor context is stored in the element, so when this function
// gets called again we can pick up where we left off
var context = $(this).data( 'context' );

/* API */

// The first time this is called, we expect context to be undefined, meaning
// the editing ui has not yet been, and still needs to be built, however each
// additional call after that is expected to be an API call, which contains a
// string as the first argument which corrosponds to a supported api call
if ( typeof context !== 'undefined' ) {
	// Since javascript gives arugments as an object, we need to convert them
	// so they can be used more easily
	arguments = $.makeArray( arguments );
	if ( arguments.length > 0 ) {
		// Handle API calls
		var call = arguments.shift();
		if ( call in context.api ) {
			context.api[call]( arguments );
		}
		// Store the context for next time and return
		return $(this).data( 'context', context );
	}
	// Nothing to do, just return
	return $(this);
}

/* Construction */

context = { '$textarea': $(this), 'modules': {}, 'data': {} };
// Encapsulate the textarea with some containers for layout
$(this)
	.wrap( $( '<div></div>' ).addClass( 'wikiEditor-ui' ) )
	.wrap( $( '<div></div>' ).addClass( 'wikiEditor-ui-bottom' ) )
	.wrap( $( '<div></div>' ).addClass( 'wikiEditor-ui-text' ) );
// Get a refrence to the outter container
context.$ui = $(this).parent().parent().parent();
// Attach a container in the top
context.$ui.prepend( $( '<div></div>' ).addClass( 'wikiEditor-ui-top' ) );
// Build functionality for modules - perhaps this could be done on the fly
// since not all modules will always be in use all the time
context.modules.toolbar = {
	/**
	 * Performs an operation based on parameters
	 * @param {Object} action
	 */
	performAction: function( action ) {
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
	 * @param {Object} $section Container to add section content to
	 * @param {Object} section Configuration to build toolbar from
	 * @param {String} sectionId Unique identifier of this section
	 */
	addSection: function( $section, section, sectionId ) {
		/**
		 * Wraps performAction with tool specific UI interaction
		 */
		var useTool = function() {
			var tool = $(this).data( 'tool' );
			if ( 'type' in tool ) {
				console.log( tool.type );
				switch ( tool.type ) {
					case 'button':
					case 'link':
						if ( 'action' in tool ) {
							context.modules.toolbar.performAction(
								tool.action
							);
						}
						break;
					case 'select':
						if ( 'list' in tool && $(this).val() in tool.list ) {
							context.modules.toolbar.performAction(
								tool.list[$(this).val()].action
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
								'src': imgPath + tools[tool].icon,
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
	 * @param {Object} textbox
	 * @param {Object} configuration
	 */
	build: function( configuration, editorId ) {
		if ( 'main' in configuration ) {
			// Handle the main specially both for layout purposes and
			// so that it is rendered immediately while the other sections are
			// rendered asynchronously and possibly much later
			context.modules.toolbar.addSection(
				context.modules.$toolbar, configuration.main, 'main'
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
				context.modules.toolbar.addSection(
					section.$section, section.tools, section.id
				);
				// When addSection is done, we can remove the loading
				// class to hide the spinner and reveal the content
				section.$section.removeClass( 'loading' )
			}
		} );
	},
	create: function( configuration ) {
		context.modules.$toolbar = $( '<div></div>' )
			.addClass( 'wikiEditor-ui-toolbar' );
		context.modules.toolbar.build( configuration );
		context.$ui.find( '.wikiEditor-ui-top' )
			.append( context.modules.$toolbar );
	}
};
context.modules.toc = {
	/**
	 * Highlight the section the cursor is currently within
	 * 
	 * @param target jQuery selection of element of containers with links to update
	 */
	update: function() {
		context.modules.$toc.find( 'a' ).removeClass( 'currentSelection' );
		var position = context.$textarea.getCaretPosition();
		var section = 0;
		if ( context.data.outline.length > 0 ) {
			// If the caret is before the first heading, you must be in section
			// 0, and there is no need to look any farther - otherwise check
			// that the caret is before each section, and when it's not, we now
			// know what section it is in
			if ( !( position < context.data.outline[0].position - 1 ) ) {
				while (
					section < context.data.outline.length &&
					context.data.outline[section].position - 1 < position
				) {
					section++;
				}
				section = Math.max( 0, section );
			}
			context.modules.$toc.find( 'a.section-' + section )
				.addClass( 'currentSelection' );
		}
	},
	/**
	 * Builds table of contents
	 * @param {Object} configuration
	 * @param {String} editorId
	 */
	build: function( configuration, editorId ) {
		var outline = [];
		// Extract headings from wikitext
		var wikitext = '\n' + context.$textarea.val() + '\n';
		var headings = wikitext.match( /\n={1,5}.*={1,5}(?=\n)/g );
		var offset = 0;
		for ( var h = 0; h < headings.length; h++ ) {
			text = headings[h];
			// Get position of first occurence
			var position = wikitext.indexOf( text, offset );
			// Update offset to avoid stumbling on duplicate headings
			if ( position > offset ) {
				offset = position;
			} else if ( position == -1 ) {
				// Not sure this is possible, or what should happen
				continue;
			}
			// Trim off whitespace
			text = $.trim( text );
			// Detect the starting and ending heading levels
			var startLevel = 0;
			for ( var c = 0; c < text.length; c++ ) {
				if ( text.charAt( c ) == '=' ) {
					startLevel++;
				} else {
					break;
				}
			}
			var endLevel = 0;
			for ( var c = text.length - 1; c >= 0; c-- ) {
				if ( text.charAt( c ) == '=' ) {
					endLevel++;
				} else {
					break;
				}
			}
			// Use the lowest common denominator as the actual level
			var level = Math.min( startLevel, endLevel );
			text = $.trim( text.substr( level, text.length - ( level * 2 ) ) );
			// Add the heading data to the outline
			outline[h] = {
				'text': text,
				'position': position,
				'level': level,
				'index': h + 1
			};
		}
		// Normalize levels, adding an nLevel parameter to each node
		var level = 1;
		var trunc = 0;
		for ( var i = 0; i < outline.length; i++ ) {
			if ( i > 0 ) {
				if ( outline[i].level > outline[i - 1].level ) {
					level++;
				} else if ( outline[i].level < outline[i - 1].level ) {
					if ( trunc <= 1 ) {
						level -= Math.max(
							1, outline[i - 1].level - outline[i].level
						);
					}
				}
				trunc = outline[i].level - outline[i - 1].level;
			}
			outline[i].nLevel = level;
		}
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
		function buildList( structure ) {
			var list = $( '<ul></ul>' );
			for ( i in structure ) {
				var item = $( '<li></li>' )
					.append(
						$( '<a></a>' )
							.attr( 'href', '#' )
							.addClass( 'section-' + structure[i].index )
							.data( 'textbox', context.$textarea )
							.data( 'position', structure[i].position )
							.click( function( event ) {
								$(this).data( 'textbox' )
									.scrollToCaretPosition(
											$(this).data( 'position' )
									);
								event.preventDefault();
							} )
							.text( structure[i].text )
					);
				if ( structure[i].sections !== undefined ) {
					item.append( buildList( structure[i].sections ) );
				}
				list.append( item );
			}
			return list;
		}
		// Recursively build the structure and adds special item for section 0
		var structure = buildStructure( outline );
		structure.unshift(
			{ 'text': wgTitle, 'level': 1, 'index': 0, 'position': 0 }
		);
		context.modules.$toc.html( buildList( structure ) );
		// Cache the outline for later use
		context.data.outline = outline;
	},
	create: function( configuration ) {
		context.modules.$toc = $( '<div></div>' )
			.addClass( 'wikiEditor-ui-toc' );
		context.modules.toc.build( configuration );
		context.$ui.find( '.wikiEditor-ui-bottom' )
			.append( context.modules.$toc );
		context.modules.$toc.height(
			context.$ui.find( '.wikiEditor-ui-bottom' ).height()
		);
		// Make some css modifications to make room for the toc on the right...
		// Perhaps this could be configurable?
		context.modules.$toc.css( 'width', '12em' );
		context.$ui.find( '.wikiEditor-ui-text' ).css( 'marginRight', '12em' );
		// Add the TOC to the document
		context.modules.toc.build();
		context.modules.toc.update();
		context.$textarea
			.bind( 'keyup encapsulateSelection',
				function( event ) {
					var context = $(this).data( 'context' );
					$(this).eachAsync( {
						bulk: 0,
						loop: function() {
							context.modules.toc.build();
							context.modules.toc.update();
						}
					} );
				}
			)
			.bind( 'mouseup scrollToPosition',
				function( event ) {
					var context = $(this).data( 'context' );
					$(this).eachAsync( {
						bulk: 0,
						loop: function() {
							context.modules.toc.update();
						}
					} );
				}
			);
	}
};
// Create a set of standard methods for internal and external use
context.api = {
	addModule: function() {
		if ( arguments.length >= 1 && arguments[0].length >= 1 ) {
			var module = arguments[0][0];
			var configuration = ( arguments[0][1] ? arguments[0][1] : {} );
			// Check if the module is supported and that there's a create
			// method available for it
			if (
				module in context.modules &&
				'create' in context.modules[module]
			) {
				context.modules[module].create( configuration );
			}
		}
	}
};
// Store the context for next time (and also for the API calls about to happen
// just below this
$(this).data( 'context', context );
//Each browser seems to do this differently, so let's keep our editor
//consistent by allways starting at the begining
context.$textarea.scrollToCaretPosition( 0 );
// If there was a configuration passed, we can get started adding
// modules right away - which is done using the same API that could be used
// explicitly by the user
if ( arguments.length > 0 && typeof arguments[0] == 'object' ) {
	if ( 'modules' in arguments[0] ) {
		for ( module in arguments[0].modules ) {
			context.api.addModule( [module, arguments[0].modules[module]] );
		}
	}
}

return $(this);

} } )(jQuery);