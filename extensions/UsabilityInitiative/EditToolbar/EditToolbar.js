/* JavaScript for EditToolbar extension */

/**
 * This is the toolbar plugin, which can be used like
 * $( 'div#edittoolbar' ).toolbar( '#wpTextbox1', tools );
 * Where tools is an array of objects which describe each tool (see below for
 * specific examples) (THIS NEEDS BETTER DOCUMENTATION WHEN I HAVE TIME)
 */
(function($){
	$.fn.extend({
		/**
		 * 
		 * @param {Object} textbox
		 * @param {Object} tools
		 */
		toolbar: function( textbox, tools ) {
			return this.each(function() {
				// Checks if main section is in the structure
				if ( 'main' in tools ) {
					// Adds main section to toolbar
					$(this).addToolbarSection( tools.main, textbox, 'main' );
				}
				// Appends additional section tabs
				var tabDiv = $( '<div />' )
					.attr( 'class', 'tabs' )
					.appendTo( $(this) );
				// Appends additional section
				var sectionsDiv = $( '<div />' )
					.attr( 'class', 'sections' )
					.appendTo( $(this) );
				// Appends float-clearing div
				$(this).append( $( '<div style="clear:both"></div>' ) );
				// Cookie name for storing section state
				var sectionCookie = 'edittoolbar-' + $(this).attr( 'id' ) + '-section';
				// Queue for sections to be built asynchonously
				var sectionQueue = [];
				// Loops over each section
				for ( section in tools ) {
					// Skips over main (was handled as special case already)
					if ( section == 'main' ) {
						continue;
					}
					// Appends section content
					var sectionDiv = $( '<div />')
						.attr( { 'class': 'section', 'id': $(this).attr( 'id' ) + '-section-' + section } )
						.appendTo( sectionsDiv )
						.addClass( 'loading' )
						.append(
							$( '<div />' )
								.addClass( 'progress' )
								.text( gM( 'edittoolbar-loading' )
							)
						);
					// Respects state
					var current = false;
					if ( $.cookie( sectionCookie ) == sectionDiv.attr( 'id' ) ) {
						sectionDiv.attr( 'style', 'display:block' );
						current = true;
					}
					// Appends toolbar to section div
					sectionQueue[sectionQueue.length] = {
						'sectionDiv': sectionDiv,
						'tools': tools[section],
						'textbox': textbox
					};
					// Appends section tab
					tabDiv.append(
						$( '<div />' )
							.attr( 'class', 'tab' )
							.append(
								$( '<a />' )
									.text( tools[section].label || gM( tools[section].labelMsg ) )
									.attr( { 'href': '#', 'rel': section, 'class': current ? 'current' : null } )
									.data( 'sectionDiv', sectionDiv )
									.data( 'sectionCookie', sectionCookie )
									.data( 'textbox', textbox )
									.click( function() {
										$(this).blur();
										var show = ( $(this).data( 'sectionDiv' ).css( 'display' ) == 'none' );
										$(this).data( 'sectionDiv' ).parent().children().hide('fast');
										$(this).parent().parent().find( 'a' ).removeClass( 'current' );
										if ( show ) {
											$(this).data( 'sectionDiv' ).show('fast');
											$(this).addClass( 'current' );
										}
										// Sets or deletes cookie when sections are shown or hidden
										$.cookie(
											$(this).data( 'sectionCookie' ),
											show ? $(this).data( 'sectionDiv' ).attr( 'id' ) : null
										);
										$(this).data( 'textbox' ).focus();
										return false;
									})
							)
					);
				}
				$.eachAsync( sectionQueue, {
					bulk: 0,
					loop: function( index, value ) {
						value.sectionDiv.addToolbarSection( value.tools, value.textbox, index );
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
			// Creates generic action
			var action = function() {
				$(this).useTool(
					$(this).data( 'context' ).tool,
					$(this).data( 'context' ).textbox
				);
			};
			switch ( section.type ) {
				case 'toolbar':
					// Check for groups
					if ( !( 'groups' in section ) ) {
						return;
					}
					// Loops over each group
					for ( group in section.groups ) {
						// Appends group
						var groupDiv = $( '<div />' )
							.attr( 'class', 'group' )
							.appendTo( $(this) );
						// Checks if there's a label for this group
						if ( msgSet( section.groups[group], 'label' ) ) {
							groupDiv.append(
								$( '<div />' )
									.attr( 'class', 'label' )
									.text( msg( section.groups[group], 'label' ) )
							)
						}
						// Loops over each tool
						for ( tool in section.groups[group].tools ) {
							// Filters are jQuery selectors which must select 1 or more
							// elements for this tool to apear. This is especailly
							// useful for restricting some tools to certain namespaces
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
							// Creates context for use in action
							var context = { 'tool': section.groups[group].tools[tool], 'textbox': textbox };
							// Creates the label of the tool
							var label = msg( section.groups[group].tools[tool], 'label' );
							switch ( section.groups[group].tools[tool].type ) {
								case 'button':
									// Appends button
									groupDiv.append(
										$( '<img />' )
										.attr( {
											src: imagePath + section.groups[group].tools[tool].icon,
											alt: label,
											title: label
										} )
										.data( 'context', context )
										.click( action )
									);
								break;
								case 'select':
									// Appends select
									var selectDiv = $( '<select />' )
										.data( 'context', context )
										.change( action )
										.append(
											$( '<option />' ) .text( label )
										)
										.appendTo( groupDiv );
									// Appends options
									for ( option in section.groups[group].tools[tool].list ) {
										selectDiv.append(
											$( '<option/>' )
												.text( msg( section.groups[group].tools[tool].list[option], 'label' ) )
												.attr( 'value', option )
										);
									}
								break;
								default: break;
							}
						}
					}
				break;
				case 'booklet':
					// Check for pages
					if ( !( 'pages' in section ) ) {
						return;
					}
					// Appends index
					var indexDiv = $( '<div />' )
						.attr( 'class', 'index' )
						.appendTo( $(this) );

					var bookletCookie = 'edittoolbar-' + $(this).attr( 'id' ) + '-booklet-' + id;
					var selectedID = $.cookie( bookletCookie ); 
					
					// Loops over each page
					for ( page in section.pages ) {
						// Appends index entry
						indexDiv.append(
							$( '<div />' )
								.attr( 'class', page == selectedID ? 'current' : null )
								.text( msg( section.pages[page], 'label' ) )
								.data( 'page', page )
								.data( 'cookie', bookletCookie )
								.click( function() {
									$(this).parent().parent().find( 'div.pages > div.page' ).hide();
									$(this).parent().find( 'div' ).removeClass( 'current' );
									$(this).parent().parent().find( 'div.pages > div.page-' + $(this).data( 'page' ) ).show();
									$(this).addClass( 'current' );
									
									// Update cookie
									$.cookie( $(this).data( 'cookie'), $(this).data( 'page' ) );
								} )
						);
					}
					var pagesDiv = $( '<div />' )
						.attr( 'class', 'pages' )
						.appendTo( $(this) );
					// Loops over each page
					for ( page in section.pages ) {
						// Appends page
						var pageDiv = $( '<div />' )
							.attr( 'class', 'page page-' + page )
							.appendTo( pagesDiv );
						// Checks if there's content for this page
						switch ( section.pages[page].layout ) {
							case 'table':
								// Appends table to page
								var contentTable = $( '<table />' )
									.attr( {
										'cellpadding': '0',
										'cellspacing': '0',
										'border': '0',
										'width': '100%'
									} )
									.appendTo( pageDiv );
								var headingRow = $( '<tr />' )
									.appendTo( contentTable );
								// Appends headings to table
								for ( heading in section.pages[page].headings ) {
									headingRow.append(
										$( '<th />' )
											.text( msg( section.pages[page].headings[heading], 'content' ) )
									);
								}
								// Appends rows to table
								for ( row in section.pages[page].rows ) {
									var contentRow = $( '<tr />' )
										.appendTo( contentTable );
									for ( cell in section.pages[page].rows[row] ) {
										contentRow.append(
											$( '<td />' )
												.attr( {
													'class': cell,
													'valign': 'top'
												} )
												.append(
													$( '<span>' + msg( section.pages[page].rows[row][cell], 'content' ) + '</span>' )
												)
										);
									}
								}
							break;
							case 'characters':
								// Appends special character adders
								var charsDiv = $( '<div />' )
									.attr( section.pages[page].attributes )
									.css( section.pages[page].styles )
									.appendTo( pageDiv );
								for ( character in section.pages[page].characters ) {
									switch( section.pages[page].characters[character].type ) {
										case 'link':
											var context = {
												'tool' : section.pages[page].characters[character],
												'textbox': textbox
											};
											charsDiv.append(
												$( '<a />' )
													.attr( 'href', '#' )
													.text( section.pages[page].characters[character].label )
													.data( 'context', context)
													.click( action )
													.click( function() { return false; } )
											);
										break;
									}
								}
							break;
							default: break;
						}
					}
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
								parts[part] = gM( action.options[part + 'Msg'], ( action.options[part] || null ) );
							} else {
								parts[part] = ( action.options[part] || '' )
							}
						}
						textbox.encapsulateSelection( parts.pre, parts.peri, parts.post );
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
						 	tool.action.options.pre = charinsert[page][line][character][0];
						 	tool.action.options.post = charinsert[page][line][character][1];
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
	});
})(jQuery);
/**
 * This initializes an edit toolbar on div#edittoolbar and connects it to
 * textarea#wpTextbox1 - which needs to be done after the document is loaded.
 */
jQuery( document ).ready( function() {
	jQuery( 'div#edittoolbar' ).toolbar( $( 'textarea#wpTextbox1' ), editToolbarConfiguration );
});
/**
 * This enormous structure is what makes the toolbar what it is. Customization
 * of this structure prior to the document being ready and thus executing the
 * initialization procedure for the toolbar will result in a custom toolbar.
 */
var editToolbarConfiguration = {
	// Main section
	'main': {
		type: 'toolbar',
		groups: {
			'format': {
				tools: {
					'bold': {
						labelMsg: 'edittoolbar-tool-format-bold',
						type: 'button',
						icon: 'format-bold.png',
						action: {
							type: 'encapsulate',
							options: {
								pre: "'''",
								periMsg: 'edittoolbar-tool-format-bold-example',
								post: "'''"
							}
						}
					},
					'italic': {
						section: 'main',
						group: 'format',
						id: 'italic',
						labelMsg: 'edittoolbar-tool-format-italic',
						type: 'button',
						icon: 'format-italic.png',
						action: {
							type: 'encapsulate',
							options: {
								pre: "''",
								periMsg: 'edittoolbar-tool-format-italic-example',
								post: "''"
							}
						}
					}
				}
			},
			'insert': {
				tools: {
					'xlink': {
						labelMsg: 'edittoolbar-tool-insert-xlink',
						type: 'button',
						icon: 'insert-xlink.png',
						action: {
							type: 'encapsulate',
							options: {
								pre: "[",
								periMsg: 'edittoolbar-tool-insert-xlink-example',
								post: "]"
							}
						}
					},
					'ilink': {
						labelMsg: 'edittoolbar-tool-insert-ilink',
						type: 'button',
						icon: 'insert-ilink.png',
						action: {
							type: 'encapsulate',
							options: {
								pre: "[[",
								periMsg: 'edittoolbar-tool-insert-ilink-example',
								post: "]]"
							}
						}
					},
					'file': {
						labelMsg: 'edittoolbar-tool-insert-file',
						type: 'button',
						icon: 'insert-file.png',
						action: {
							type: 'encapsulate',
							options: {
								pre: "[[",
								preMsg: 'edittoolbar-tool-insert-file-pre',
								periMsg: 'edittoolbar-tool-insert-file-example',
								post: "]]"
							}
						}
					},
					'reference': {
						labelMsg: 'edittoolbar-tool-insert-reference',
						filters: [ 'body.ns-subject' ],
						type: 'button',
						icon: 'insert-reference.png',
						action: {
							type: 'encapsulate',
							options: {
								pre: "<ref>",
								periMsg: 'edittoolbar-tool-insert-reference-example',
								post: "</ref>"
							}
						}
					},
					'signature': {
						labelMsg: 'edittoolbar-tool-insert-signature',
						filters: [ 'body.ns-talk' ],
						type: 'button',
						icon: 'insert-signature.png',
						action: {
							type: 'encapsulate',
							options: {
								post: "--~~~~"
							}
						}
					}
				}
			}
		}
	},
	// Format section
	'format': {
		labelMsg: 'edittoolbar-section-format',
		type: 'toolbar',
		groups: {
			'heading': {
				tools: {
					'heading': {
						labelMsg: 'edittoolbar-tool-format-heading',
						type: 'select',
						list: {
							'heading-1' : {
								labelMsg: 'edittoolbar-tool-format-heading-1',
								action: {
									type: 'encapsulate',
									options: {
										pre: "=",
										periMsg: 'edittoolbar-tool-format-heading-example',
										post: "="
									}
								}
							},
							'heading-2' : {
								labelMsg: 'edittoolbar-tool-format-heading-2',
								action: {
									type: 'encapsulate',
									options: {
										pre: "==",
										periMsg: 'edittoolbar-tool-format-heading-example',
										post: "=="
									}
								}
							},
							'heading-3' : {
								labelMsg: 'edittoolbar-tool-format-heading-3',
								action: {
									type: 'encapsulate',
									options: {
										pre: "===",
										periMsg: 'edittoolbar-tool-format-heading-example',
										post: "==="
									}
								}
							},
							'heading-4' : {
								labelMsg: 'edittoolbar-tool-format-heading-4',
								action: {
									type: 'encapsulate',
									options: {
										pre: "====",
										periMsg: 'edittoolbar-tool-format-heading-example',
										post: "===="
									}
								}
							},
							'heading-5' : {
								labelMsg: 'edittoolbar-tool-format-heading-5',
								action: {
									type: 'encapsulate',
									options: {
										pre: "=====",
										periMsg: 'edittoolbar-tool-format-heading-example',
										post: "====="
									}
								}
							}
						}
					}
				}
			},
			'list': {
				labelMsg: 'edittoolbar-group-format-list',
				tools: {
					'ulist': {
						labelMsg: 'edittoolbar-tool-format-ulist',
						type: 'button',
						icon: 'format-ulist.png',
						action: {
							type: 'encapsulate',
							options: {
								pre: "* ",
								periMsg: 'edittoolbar-tool-format-ulist-example',
								post: ""
							}
						}
					},
					'olist': {
						labelMsg: 'edittoolbar-tool-format-olist',
						type: 'button',
						icon: 'format-olist.png',
						action: {
							type: 'encapsulate',
							options: {
								pre: "# ",
								periMsg: 'edittoolbar-tool-format-olist-example',
								post: ""
							}
						}
					}
				}
			},
			'size': {
				labelMsg: 'edittoolbar-group-format-size',
				tools: {
					'big': {
						labelMsg: 'edittoolbar-tool-format-big',
						type: 'button',
						icon: 'format-big.png',
						action: {
							type: 'encapsulate',
							options: {
								pre: "<big>",
								periMsg: 'edittoolbar-tool-format-big-example',
								post: "</big>"
							}
						}
					},
					'small': {
						labelMsg: 'edittoolbar-tool-format-small',
						type: 'button',
						icon: 'format-small.png',
						action: {
							type: 'encapsulate',
							options: {
								pre: "<small>",
								periMsg: 'edittoolbar-tool-format-small-example',
								post: "</small>"
							}
						}
					}
				}
			},
			'baseline': {
				labelMsg: 'edittoolbar-group-format-baseline',
				tools: {
					'superscript': {
						labelMsg: 'edittoolbar-tool-format-superscript',
						type: 'button',
						icon: 'format-superscript.png',
						action: {
							type: 'encapsulate',
							options: {
								pre: "<sup>",
								periMsg: 'edittoolbar-tool-format-superscript-example',
								post: "</sup>"
							}
						}
					},
					'subscript': {
						labelMsg: 'edittoolbar-tool-format-subscript',
						type: 'button',
						icon: 'format-subscript.png',
						action: {
							type: 'encapsulate',
							options: {
								pre: "<sub>",
								periMsg: 'edittoolbar-tool-format-subscript-example',
								post: "</sub>"
							}
						}
					}
				}
			}
		}
	},
	// Insert section
	'insert': {
		labelMsg: 'edittoolbar-section-insert',
		type: 'toolbar',
		groups: {
			'media': {
				labelMsg: 'edittoolbar-group-insert-media',
				tools: {
					'file': {
						labelMsg: 'edittoolbar-tool-insert-file',
						type: 'button',
						icon: 'insert-file.png',
						action: {
							type: 'encapsulate',
							options: {
								pre: "[[",
								preMsg: 'edittoolbar-tool-insert-file-pre',
								periMsg: 'edittoolbar-tool-insert-file-example',
								post: "]]"
							}
						}
					},
					'gallery': {
						labelMsg: 'edittoolbar-tool-insert-gallery',
						type: 'button',
						icon: 'insert-gallery.png',
						action: {
							type: 'encapsulate',
							options: {
								pre: "<gallery>\n",
								periMsg: 'edittoolbar-tool-insert-gallery-example',
								post: "\n</gallery>"
							}
						}
					}
				}
			},
			'whitespace': {
				labelMsg: 'edittoolbar-group-insert-whitespace',
				tools: {
					'newline': {
						labelMsg: 'edittoolbar-tool-insert-newline',
						type: 'button',
						icon: 'insert-newline.png',
						action: {
							type: 'encapsulate',
							options: {
								pre: "<br />\n"
							}
						}
					}
				}
			}
		}
	},
	'characters': {
		labelMsg: 'edittoolbar-section-characters',
		type: 'booklet',
		pages: {} // Set by the document.ready handler
	},
	'help': {
		labelMsg: 'edittoolbar-section-help',
		type: 'booklet',
		pages: {
			'format': {
				labelMsg: 'edittoolbar-help-page-format',
				layout: 'table',
				headings: [
					{ contentMsg: 'edittoolbar-help-heading-description' },
					{ contentMsg: 'edittoolbar-help-heading-syntax' },
					{ contentMsg: 'edittoolbar-help-heading-result' }
				],
				rows: [
					{
						'description': { contentMsg: 'edittoolbar-help-content-italic-description' },
						'syntax': { contentMsg: 'edittoolbar-help-content-italic-syntax' },
						'result': { contentMsg: 'edittoolbar-help-content-italic-result' }
					},
					{
						'description': { contentMsg: 'edittoolbar-help-content-bold-description' },
						'syntax': { contentMsg: 'edittoolbar-help-content-bold-syntax' },
						'result': { contentMsg: 'edittoolbar-help-content-bold-result' }
					},
					{
						'description': { contentMsg: 'edittoolbar-help-content-bolditalic-description' },
						'syntax': { contentMsg: 'edittoolbar-help-content-bolditalic-syntax' },
						'result': { contentMsg: 'edittoolbar-help-content-bolditalic-result' }
					}
				]
			},
			'link': {
				labelMsg: 'edittoolbar-help-page-link',
				layout: 'table',
				headings: [
					{ contentMsg: 'edittoolbar-help-heading-description' },
					{ contentMsg: 'edittoolbar-help-heading-syntax' },
					{ contentMsg: 'edittoolbar-help-heading-result' }
				],
				rows: [
					{
						'description': { contentMsg: 'edittoolbar-help-content-ilink-description' },
						'syntax': { contentMsg: 'edittoolbar-help-content-ilink-syntax' },
						'result': { contentMsg: 'edittoolbar-help-content-ilink-result' }
					},
					{
						'description': { contentMsg: 'edittoolbar-help-content-xlink-description' },
						'syntax': { contentMsg: 'edittoolbar-help-content-xlink-syntax' },
						'result': { contentMsg: 'edittoolbar-help-content-xlink-result' }
					}
				]
			},
			'heading': {
				labelMsg: 'edittoolbar-help-page-heading',
				layout: 'table',
				headings: [
					{ contentMsg: 'edittoolbar-help-heading-description' },
					{ contentMsg: 'edittoolbar-help-heading-syntax' },
					{ contentMsg: 'edittoolbar-help-heading-result' }
				],
				rows: [
					{
						'description': { contentMsg: 'edittoolbar-help-content-heading1-description' },
						'syntax': { contentMsg: 'edittoolbar-help-content-heading1-syntax' },
						'result': { contentMsg: 'edittoolbar-help-content-heading1-result' }
					},
					{
						'description': { contentMsg: 'edittoolbar-help-content-heading2-description' },
						'syntax': { contentMsg: 'edittoolbar-help-content-heading2-syntax' },
						'result': { contentMsg: 'edittoolbar-help-content-heading2-result' }
					},
					{
						'description': { contentMsg: 'edittoolbar-help-content-heading3-description' },
						'syntax': { contentMsg: 'edittoolbar-help-content-heading3-syntax' },
						'result': { contentMsg: 'edittoolbar-help-content-heading3-result' }
					},
					{
						'description': { contentMsg: 'edittoolbar-help-content-heading4-description' },
						'syntax': { contentMsg: 'edittoolbar-help-content-heading4-syntax' },
						'result': { contentMsg: 'edittoolbar-help-content-heading4-result' }
					},
					{
						'description': { contentMsg: 'edittoolbar-help-content-heading5-description' },
						'syntax': { contentMsg: 'edittoolbar-help-content-heading5-syntax' },
						'result': { contentMsg: 'edittoolbar-help-content-heading5-result' }
					}
				]
			},
			'list': {
				labelMsg: 'edittoolbar-help-page-list',
				layout: 'table',
				headings: [
					{ contentMsg: 'edittoolbar-help-heading-description' },
					{ contentMsg: 'edittoolbar-help-heading-syntax' },
					{ contentMsg: 'edittoolbar-help-heading-result' }
				],
				rows: [
					{
						'description': { contentMsg: 'edittoolbar-help-content-ulist-description' },
						'syntax': { contentMsg: 'edittoolbar-help-content-ulist-syntax' },
						'result': { contentMsg: 'edittoolbar-help-content-ulist-result' }
					},
					{
						'description': { contentMsg: 'edittoolbar-help-content-olist-description' },
						'syntax': { contentMsg: 'edittoolbar-help-content-olist-syntax' },
						'result': { contentMsg: 'edittoolbar-help-content-olist-result' }
					}
				]
			},
			'file': {
				labelMsg: 'edittoolbar-help-page-file',
				layout: 'table',
				headings: [
					{ contentMsg: 'edittoolbar-help-heading-description' },
					{ contentMsg: 'edittoolbar-help-heading-syntax' },
					{ contentMsg: 'edittoolbar-help-heading-result' }
				],
				rows: [
					{
						'description': { contentMsg: 'edittoolbar-help-content-file-description' },
						'syntax': { contentMsg: 'edittoolbar-help-content-file-syntax' },
						'result': { contentMsg: 'edittoolbar-help-content-file-result' }
					}
				]
			},
			'reference': {
				labelMsg: 'edittoolbar-help-page-reference',
				layout: 'table',
				headings: [
					{ contentMsg: 'edittoolbar-help-heading-description' },
					{ contentMsg: 'edittoolbar-help-heading-syntax' },
					{ contentMsg: 'edittoolbar-help-heading-result' }
				],
				rows: [
					{
						'description': { contentMsg: 'edittoolbar-help-content-reference-description' },
						'syntax': { contentMsg: 'edittoolbar-help-content-reference-syntax' },
						'result': { contentMsg: 'edittoolbar-help-content-reference-result' }
					},
					{
						'description': { contentMsg: 'edittoolbar-help-content-rereference-description' },
						'syntax': { contentMsg: 'edittoolbar-help-content-rereference-syntax' },
						'result': { contentMsg: 'edittoolbar-help-content-rereference-result' }
					},
					{
						'description': { contentMsg: 'edittoolbar-help-content-showreferences-description' },
						'syntax': { contentMsg: 'edittoolbar-help-content-showreferences-syntax' },
						'result': { contentMsg: 'edittoolbar-help-content-showreferences-result' }
					}
				]
			},
			'discussion': {
				labelMsg: 'edittoolbar-help-page-discussion',
				layout: 'table',
				headings: [
					{ contentMsg: 'edittoolbar-help-heading-description' },
					{ contentMsg: 'edittoolbar-help-heading-syntax' },
					{ contentMsg: 'edittoolbar-help-heading-result' }
				],
				rows: [
					{
						'description': { contentMsg: 'edittoolbar-help-content-signaturetimestamp-description' },
						'syntax': { contentMsg: 'edittoolbar-help-content-signaturetimestamp-syntax' },
						'result': { contentMsg: 'edittoolbar-help-content-signaturetimestamp-result' }
					},
					{
						'description': { contentMsg: 'edittoolbar-help-content-signature-description' },
						'syntax': { contentMsg: 'edittoolbar-help-content-signature-syntax' },
						'result': { contentMsg: 'edittoolbar-help-content-signature-result' }
					},
					{
						'description': { contentMsg: 'edittoolbar-help-content-indent-description' },
						'syntax': { contentMsg: 'edittoolbar-help-content-indent-syntax' },
						'result': { contentMsg: 'edittoolbar-help-content-indent-result' }
					}
				]
			}
		}
	}
};