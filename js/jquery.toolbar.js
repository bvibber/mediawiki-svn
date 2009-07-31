/**
 * This is the toolbar plugin, which can be used like
 * $j( 'div#edittoolbar' ).toolbar( '#wpTextbox1', tools );
 * Where tools is an array of objects which describe each tool (see below for
 * specific examples) (THIS NEEDS BETTER DOCUMENTATION WHEN I HAVE TIME)
 */
(function($){ $.fn.extend({
/**
 * Builds toolbar
 * @param {Object} textbox
 * @param {Object} tools
 */
toolbar: function( textbox, tools ) {
	return this.each(function() {
		if ( 'main' in tools ) {
			$(this).addToolbarSection( tools.main, textbox, 'main' );
		}
		var tabDiv = $( '<div></div>' )
			.attr( 'class', 'tabs' );
		var sectionsDiv = $( '<div></div>' )
			.attr( 'class', 'sections' );
		var sectionCookie = 'edittoolbar-' + $(this).attr( 'id' ) + '-section';
		var sectionQueue = [];
		for ( section in tools ) {
			if ( section == 'main' ) {
				continue;
			}
			var sectionDiv = $( '<div></div>')
				.attr( {
					'class': 'section',
					'id': $(this).attr( 'id' ) + '-section-' + section
				} )
				.addClass( 'loading' )
				.append(
					$( '<div></div>' )
						.addClass( 'progress' )
						.text( gM( 'edittoolbar-loading' )
					)
				)
				.appendTo( sectionsDiv );
			var current = false;
			if ( $.cookie( sectionCookie ) == sectionDiv.attr( 'id' ) ) {
				sectionDiv.attr( 'style', 'display:block' );
				current = true;
			}
			sectionQueue[sectionQueue.length] = {
				'sectionDiv': sectionDiv,
				'tools': tools[section],
				'textbox': textbox
			};
			tabDiv.append(
				$( '<span></span>' )
					.attr( 'class', 'tab' )
					.append(
						$( '<a></a>' )
							.text(
								tools[section].label ||
								gM( tools[section].labelMsg )
							)
							.attr( {
								'href': '#',
								'rel': section,
								'class': current ? 'current' : null
							} )
							.data( 'sectionDiv', sectionDiv )
							.data( 'sectionCookie', sectionCookie )
							.data( 'textbox', textbox )
							.click( function() {
								$(this).blur();
								var show = (
									$(this).data( 'sectionDiv' )
										.css( 'display' ) == 'none'
								);
								$(this).data( 'sectionDiv' )
									.parent().children().hide();
								$(this)
									.parent()
									.parent()
									.find( 'a' )
									.removeClass( 'current' );
								if ( show ) {
									$(this).data( 'sectionDiv' ).show();
									$(this).addClass( 'current' );
								}
								$.cookie(
									$(this).data( 'sectionCookie' ),
									show ?
										$(this).data( 'sectionDiv' )
											.attr( 'id' )
										: null
								);
								return false;
							})
					)
			);
		}
		$(this).append( tabDiv )
			.append( sectionsDiv )
			.append( $( '<div></div>' ).addClass( 'break' ) );
		$.eachAsync( sectionQueue, {
			bulk: 0,
			loop: function( index, value ) {
				value.sectionDiv.addToolbarSection(
					value.tools, value.textbox, index
				);
				value.sectionDiv.removeClass( 'loading' )
			}
		} )
	});
},
/**
 * Adds a toolbar section to a containing div
 * @param {Object} section Section data to build toolbar from
 * @param {Object} textbox
 * @param {String} section ID (used for cookies)
 */
addToolbarSection: function( section, textbox, id ) {
	// Path to images (THIS WILL HAVE TO CHANGE IF YOU MOVE THIS INTO CORE)
	var imagePath = wgScriptPath +
		'/extensions/UsabilityInitiative/EditToolbar/images/';
	function msgSet( object, property ) {
		return property in object || property + 'Msg' in object;
	}
	function msg( object, property ) {
		return object[property] || gM( object[property + 'Msg'] );
	}
	var action = function( event ) {
		$(this).useTool(
			$(this).data( 'context' ).tool,
			$(this).data( 'context' ).textbox
		);
		event.preventDefault();
	};
	switch ( section.type ) {
		case 'toolbar':
			if ( !( 'groups' in section ) ) {
				return;
			}
			for ( group in section.groups ) {
				var groupDiv = $( '<div></div>' )
					.attr( 'class', 'group' );
				if ( msgSet( section.groups[group], 'label' ) ) {
					groupDiv.append(
						$( '<div></div>' )
							.attr( 'class', 'label' )
							.text( msg( section.groups[group], 'label' ) )
					)
				}
				for ( tool in section.groups[group].tools ) {
					if ( 'filters' in section.groups[group].tools[tool] ) {
						var filters = section.groups[group].tools[tool].filters;
						var skip = false;
						for ( filter in filters ) {
							if ( $( filters[filter] ).size() == 0 ) {
								skip = true;
							}
						}
						if ( skip ) {
							continue;
						}
					}
					var context = {
						'tool': section.groups[group].tools[tool],
						'textbox': textbox
					};
					var label =
						msg( section.groups[group].tools[tool], 'label' );
					switch ( section.groups[group].tools[tool].type ) {
						case 'button':
							groupDiv.append(
								$( '<input />' )
								.attr( {
									src: imagePath +
										section.groups[group].tools[tool].icon,
									alt: label,
									title: label,
									'class': 'tool',
									'type': 'image'
								} )
								.data( 'context', context )
								.click( action )
							);
						break;
						case 'select':
							var selectDiv = $( '<select></select>' )
								.data( 'context', context )
								.change( action )
								.append(
									$( '<option></option>' ) .text( label )
								)
								.appendTo( groupDiv );
							for (
								option in section.groups[group].tools[tool].list
							) {
								selectDiv.append(
									$( '<option></option>' )
										.text(
											msg(
												section.groups[group]
												    .tools[tool].list[option],
												'label'
											)
										)
										.attr( 'value', option )
								);
							}
						break;
						default: break;
					}
				}
				$(this).append( groupDiv );
			}
		break;
		case 'booklet':
			if ( !( 'pages' in section ) ) {
				return;
			}
			var indexDiv = $( '<div></div>' )
				.attr( 'class', 'index' );
			var bookletCookie =
				'edittoolbar-' + $(this).attr( 'id' ) + '-booklet-' + id;
			var selectedID = $.cookie( bookletCookie ); 
			for ( page in section.pages ) {
				if ( selectedID === null ) {
					selectedID = page;
				}
				indexDiv.append(
					$( '<div></div>' )
						.attr( 'class', page === selectedID ? 'current' : null )
						.text( msg( section.pages[page], 'label' ) )
						.data( 'page', page )
						.data( 'cookie', bookletCookie )
						.click( function() {
							$(this)
								.parent()
								.parent()
								.find( 'div.pages > div.page' )
								.hide()
								.end()
								.parent()
								.find( 'div' )
								.removeClass( 'current' )
								.end()
								.parent()
								.parent()
								.find(
									'div.pages > div.page-' +
										$(this).data( 'page' )
								)
								.show();
							$(this).addClass( 'current' );
							$.cookie(
								$(this).data( 'cookie'), $(this).data( 'page' )
							);
						} )
				);
			}
			var pagesDiv = $( '<div></div>' )
				.attr( 'class', 'pages' );
			for ( page in section.pages ) {
				var pageDiv = $( '<div></div>' )
					.attr( 'class', 'page page-' + page )
					.css( 'display', page === selectedID ? 'block' : 'none' );
				switch ( section.pages[page].layout ) {
					case 'table':
						var contentTable = $( '<table></table>' )
							.attr( {
								'cellpadding': '0',
								'cellspacing': '0',
								'border': '0',
								'width': '100%'
							} );
						var headingRow = $( '<tr></tr>' );
						for ( heading in section.pages[page].headings ) {
							$( '<th></th>' )
								.text(
									msg(
										section.pages[page]
											.headings[heading],
										'content'
									)
								)
								.appendTo( headingRow );
						}
						contentTable.append( headingRow );
						for ( row in section.pages[page].rows ) {
							var contentRow = $( '<tr></tr>' );
							for ( cell in section.pages[page].rows[row] ) {
								$( '<td></td>' )
									.attr( {
										'class': cell,
										'valign': 'top'
									} )
									.append(
										$( '<span></span>' )
											.text(
												msg(
													section.pages[page]
													    .rows[row][cell],
													'content'
												)
											)
									)
									.appendTo( contentRow );
							}
							contentTable.append( contentRow );
						}
						pageDiv.append( contentTable );
					break;
					case 'characters':
						var charsDiv = $( '<div />' )
							.attr( section.pages[page].attributes )
							.css( section.pages[page].styles );
						for ( character in section.pages[page].characters ) {
							switch (
								section.pages[page].characters[character].type
							) {
								case 'link':
									var context = {
										'tool' : section.pages[page]
										             .characters[character],
										'textbox': textbox
									};
									charsDiv.append(
										$( '<a />' )
											.attr( 'href', '#' )
											.text(
												section.pages[page]
												    .characters[character].label
											)
											.data( 'context', context)
											.click( action )
									);
								break;
							}
						}
						pageDiv.append( charsDiv );
					break;
					default: break;
				}
				pagesDiv.append( pageDiv );
			}
			$(this).append( indexDiv )
				.append( pagesDiv );
		break;
		default: break;
	}
},
/**
 * Performs action on a textbox using a tool
 * @param {Object} tool
 * @param {Object} textbox
 */
useTool: function( tool, textbox ) {
	function performAction( action, textbox ) {
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
				textbox.encapsulateSelection(
					parts.pre, parts.peri, parts.post
				);
			break;
			default: break;
		}
	}
	switch ( tool.type ) {
		case 'button':
		case 'link':
			performAction( tool.action, textbox );
		break;
		case 'select':
			if ( $(this).val() in tool.list ) {
				performAction( tool.list[$(this).val()].action, textbox );
			}
			$(this).find(":selected").attr( 'selected', false );
			$(this).find(":first").attr( 'selected', true );
		break;
		default: break;
	}
},

/**
 * Converts a charinsert array like the one used on dewiki to
 * the format expected in editToolbarConfiguration
 */
parseCharinsert: function( charinsert ) {
	var retval = {};
	for( page in charinsert ) {
		var pageKey = page.replace(/[^A-Za-z]/g, '-');
		var characters = [], attributes = {}, styles = {};
		var i = 0;
		for( line in charinsert[page] ) {
			if( !( charinsert[page][line] instanceof Array ) ) {
				for( attr in charinsert[page][line] ) {
					switch( attr ) {
						case 'class':
						case 'lang':
							attributes[attr] = charinsert[page][line][attr];
						break;
						default:
							styles[attr] = charinsert[page][line][attr];
					}
				}
				continue;
			}
			for( character in charinsert[page][line] ) {
				 var tool = {
				 	type: 'link',
				 	label: '',
				 	action: {
				 		type: 'encapsulate',
				 		options: {
				 			pre: '',
				 			post: ''
				 		}
				 	}
				 };
				 if( charinsert[page][line][character] instanceof Array ) {
				 	tool.action.options.pre =
				 		charinsert[page][line][character][0];
				 	tool.action.options.post =
				 		charinsert[page][line][character][1];
				 } else {
				 	tool.action.options.pre = charinsert[page][line][character];
				 }
				tool.label = tool.action.options.pre + tool.action.options.post;
				characters[i++] = tool;
			}
		}
		retval[pageKey] = {
			label: page,
			layout: 'characters',
			attributes: attributes,
			styles: styles,
			characters: characters
		};
	}
	return retval;
}

}); })(jQuery);