/* JavaScript for EditToolbar extension */

js2AddOnloadHook( function() {
	$j( 'textarea#wpTextbox1' ).wrap(
		$j( '<div></div>' )
			.attr( 'id', 'edit-ui' )
	);
	$j( 'textarea#wpTextbox1' ).wrap(
		$j( '<div></div>' )
			.attr( 'id', 'edit-ui-left' )
	);
	$j( 'div#edit-ui' ).append(
		$j( '<div></div>' )
			.attr( 'id', 'edit-ui-right' )
	);
	$j( 'div#edit-ui-left' ).prepend(
		$j( '<div></div>' )
			.attr( 'id', 'edit-toolbar' )
	);
	$j( 'div#edit-toolbar' ).toolbar(
		$j( 'textarea#wpTextbox1' ),
		editToolbarConfiguration
	);
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
						labelMsg: 'edittoolbar-tool-bold',
						type: 'button',
						icon: 'format-bold.png',
						action: {
							type: 'encapsulate',
							options: {
								pre: "'''",
								periMsg: 'edittoolbar-tool-bold-example',
								post: "'''"
							}
						}
					},
					'italic': {
						section: 'main',
						group: 'format',
						id: 'italic',
						labelMsg: 'edittoolbar-tool-italic',
						type: 'button',
						icon: 'format-italic.png',
						action: {
							type: 'encapsulate',
							options: {
								pre: "''",
								periMsg: 'edittoolbar-tool-italic-example',
								post: "''"
							}
						}
					}
				}
			},
			'insert': {
				tools: {
					'xlink': {
						labelMsg: 'edittoolbar-tool-xlink',
						type: 'button',
						icon: 'insert-xlink.png',
						action: {
							type: 'encapsulate',
							options: {
								pre: "[",
								periMsg: 'edittoolbar-tool-xlink-example',
								post: "]"
							}
						}
					},
					'ilink': {
						labelMsg: 'edittoolbar-tool-ilink',
						type: 'button',
						icon: 'insert-ilink.png',
						action: {
							type: 'encapsulate',
							options: {
								pre: "[[",
								periMsg: 'edittoolbar-tool-ilink-example',
								post: "]]"
							}
						}
					},
					'file': {
						labelMsg: 'edittoolbar-tool-file',
						type: 'button',
						icon: 'insert-file.png',
						action: {
							type: 'encapsulate',
							options: {
								pre: "[[",
								preMsg: 'edittoolbar-tool-file-pre',
								periMsg: 'edittoolbar-tool-file-example',
								post: "]]"
							}
						}
					},
					'reference': {
						labelMsg: 'edittoolbar-tool-reference',
						filters: [ 'body.ns-subject' ],
						type: 'button',
						icon: 'insert-reference.png',
						action: {
							type: 'encapsulate',
							options: {
								pre: "<ref>",
								periMsg: 'edittoolbar-tool-reference-example',
								post: "</ref>"
							}
						}
					},
					'signature': {
						labelMsg: 'edittoolbar-tool-signature',
						filters: [ 'body:not(.ns-0)' ],
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
	'advanced': {
		labelMsg: 'edittoolbar-section-advanced',
		type: 'toolbar',
		groups: {
			'heading': {
				tools: {
					'heading': {
						labelMsg: 'edittoolbar-tool-heading',
						type: 'select',
						list: {
							'heading-1' : {
								labelMsg: 'edittoolbar-tool-heading-1',
								action: {
									type: 'encapsulate',
									options: {
										pre: "=",
										periMsg: 'edittoolbar-tool-heading-example',
										post: "="
									}
								}
							},
							'heading-2' : {
								labelMsg: 'edittoolbar-tool-heading-2',
								action: {
									type: 'encapsulate',
									options: {
										pre: "==",
										periMsg: 'edittoolbar-tool-heading-example',
										post: "=="
									}
								}
							},
							'heading-3' : {
								labelMsg: 'edittoolbar-tool-heading-3',
								action: {
									type: 'encapsulate',
									options: {
										pre: "===",
										periMsg: 'edittoolbar-tool-heading-example',
										post: "==="
									}
								}
							},
							'heading-4' : {
								labelMsg: 'edittoolbar-tool-heading-4',
								action: {
									type: 'encapsulate',
									options: {
										pre: "====",
										periMsg: 'edittoolbar-tool-heading-example',
										post: "===="
									}
								}
							},
							'heading-5' : {
								labelMsg: 'edittoolbar-tool-heading-5',
								action: {
									type: 'encapsulate',
									options: {
										pre: "=====",
										periMsg: 'edittoolbar-tool-heading-example',
										post: "====="
									}
								}
							}
						}
					}
				}
			},
			'list': {
				labelMsg: 'edittoolbar-group-list',
				tools: {
					'ulist': {
						labelMsg: 'edittoolbar-tool-ulist',
						type: 'button',
						icon: 'format-ulist.png',
						action: {
							type: 'encapsulate',
							options: {
								pre: "* ",
								periMsg: 'edittoolbar-tool-ulist-example',
								post: ""
							}
						}
					},
					'olist': {
						labelMsg: 'edittoolbar-tool-olist',
						type: 'button',
						icon: 'format-olist.png',
						action: {
							type: 'encapsulate',
							options: {
								pre: "# ",
								periMsg: 'edittoolbar-tool-olist-example',
								post: ""
							}
						}
					}
				}
			},
			'size': {
				labelMsg: 'edittoolbar-group-size',
				tools: {
					'big': {
						labelMsg: 'edittoolbar-tool-big',
						type: 'button',
						icon: 'format-big.png',
						action: {
							type: 'encapsulate',
							options: {
								pre: "<big>",
								periMsg: 'edittoolbar-tool-big-example',
								post: "</big>"
							}
						}
					},
					'small': {
						labelMsg: 'edittoolbar-tool-small',
						type: 'button',
						icon: 'format-small.png',
						action: {
							type: 'encapsulate',
							options: {
								pre: "<small>",
								periMsg: 'edittoolbar-tool-small-example',
								post: "</small>"
							}
						}
					}
				}
			},
			'baseline': {
				labelMsg: 'edittoolbar-group-baseline',
				tools: {
					'superscript': {
						labelMsg: 'edittoolbar-tool-superscript',
						type: 'button',
						icon: 'format-superscript.png',
						action: {
							type: 'encapsulate',
							options: {
								pre: "<sup>",
								periMsg: 'edittoolbar-tool-superscript-example',
								post: "</sup>"
							}
						}
					},
					'subscript': {
						labelMsg: 'edittoolbar-tool-subscript',
						type: 'button',
						icon: 'format-subscript.png',
						action: {
							type: 'encapsulate',
							options: {
								pre: "<sub>",
								periMsg: 'edittoolbar-tool-subscript-example',
								post: "</sub>"
							}
						}
					}
				}
			},
			'insert': {
				labelMsg: 'edittoolbar-group-insert',
				tools: {
					'gallery': {
						labelMsg: 'edittoolbar-tool-gallery',
						type: 'button',
						icon: 'insert-gallery.png',
						action: {
							type: 'encapsulate',
							options: {
								pre: "<gallery>\n",
								periMsg: 'edittoolbar-tool-gallery-example',
								post: "\n</gallery>"
							}
						}
					},
					'table': {
						labelMsg: 'edittoolbar-tool-table',
						type: 'button',
						icon: 'insert-table.png',
						action: {
							type: 'encapsulate',
							options: {
								pre: "{| class=\"wikitable\" border=\"1\"\n|",
								periMsg: 'edittoolbar-tool-table-example',
								post: "\n|}"
							}
						}
					},
					'newline': {
						labelMsg: 'edittoolbar-tool-newline',
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
		pages: {
			'latin': {
				'labelMsg': 'edittoolbar-characters-page-latin',
				'layout': 'characters',
				'characters': [
					{
						'type': 'link',
						'label': 'Á',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Á',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'á',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'á',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ć',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ć',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ć',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ć',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'É',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'É',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'é',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'é',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Í',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Í',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'í',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'í',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ĺ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ĺ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ĺ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ĺ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ń',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ń',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ń',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ń',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ó',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ó',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ó',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ó',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ŕ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ŕ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ŕ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ŕ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ś',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ś',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ś',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ś',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ú',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ú',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ú',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ú',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ý',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ý',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ý',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ý',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ź',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ź',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ź',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ź',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'À',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'À',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'à',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'à',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'È',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'È',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'è',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'è',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ì',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ì',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ì',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ì',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ò',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ò',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ò',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ò',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ù',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ù',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ù',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ù',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Â',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Â',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'â',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'â',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ĉ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ĉ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ĉ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ĉ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ê',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ê',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ê',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ê',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ĝ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ĝ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ĝ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ĝ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ĥ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ĥ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ĥ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ĥ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Î',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Î',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'î',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'î',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ĵ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ĵ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ĵ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ĵ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ô',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ô',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ô',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ô',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ŝ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ŝ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ŝ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ŝ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Û',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Û',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'û',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'û',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ŵ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ŵ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ŵ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ŵ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ŷ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ŷ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ŷ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ŷ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ä',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ä',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ä',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ä',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ë',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ë',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ë',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ë',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ï',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ï',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ï',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ï',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ö',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ö',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ö',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ö',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ü',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ü',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ü',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ü',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ÿ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ÿ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ÿ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ÿ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ß',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ß',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ã',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ã',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ã',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ã',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ẽ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ẽ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ẽ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ẽ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ĩ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ĩ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ĩ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ĩ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ñ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ñ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ñ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ñ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Õ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Õ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'õ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'õ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ũ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ũ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ũ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ũ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ỹ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ỹ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ỹ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ỹ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ç',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ç',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ç',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ç',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ģ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ģ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ģ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ģ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ķ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ķ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ķ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ķ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ļ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ļ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ļ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ļ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ņ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ņ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ņ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ņ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ŗ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ŗ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ŗ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ŗ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ş',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ş',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ş',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ş',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ţ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ţ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ţ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ţ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Đ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Đ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'đ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'đ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ů',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ů',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ů',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ů',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ǎ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ǎ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ǎ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ǎ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Č',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Č',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'č',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'č',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ď',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ď',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ď',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ď',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ě',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ě',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ě',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ě',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ǐ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ǐ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ǐ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ǐ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ľ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ľ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ľ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ľ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ň',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ň',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ň',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ň',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ǒ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ǒ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ǒ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ǒ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ř',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ř',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ř',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ř',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Š',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Š',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'š',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'š',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ť',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ť',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ť',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ť',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ǔ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ǔ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ǔ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ǔ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ž',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ž',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ž',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ž',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ā',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ā',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ā',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ā',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ē',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ē',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ē',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ē',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ī',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ī',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ī',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ī',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ō',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ō',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ō',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ō',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ū',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ū',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ū',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ū',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ȳ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ȳ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ȳ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ȳ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ǣ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ǣ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ǣ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ǣ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ǖ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ǖ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ǘ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ǘ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ǚ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ǚ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ǜ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ǜ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ă',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ă',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ă',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ă',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ĕ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ĕ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ĕ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ĕ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ğ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ğ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ğ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ğ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ĭ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ĭ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ĭ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ĭ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ŏ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ŏ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ŏ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ŏ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ŭ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ŭ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ŭ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ŭ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ċ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ċ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ċ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ċ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ė',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ė',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ė',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ė',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ġ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ġ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ġ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ġ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'İ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'İ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ı',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ı',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ż',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ż',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ż',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ż',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ą',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ą',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ą',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ą',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ę',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ę',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ę',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ę',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Į',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Į',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'į',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'į',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ǫ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ǫ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ǫ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ǫ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ų',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ų',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ų',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ų',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ḍ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ḍ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ḍ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ḍ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ḥ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ḥ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ḥ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ḥ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ḷ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ḷ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ḷ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ḷ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ḹ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ḹ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ḹ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ḹ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ṃ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ṃ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ṃ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ṃ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ṇ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ṇ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ṇ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ṇ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ṛ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ṛ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ṛ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ṛ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ṝ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ṝ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ṝ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ṝ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ṣ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ṣ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ṣ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ṣ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ṭ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ṭ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ṭ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ṭ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ł',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ł',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ł',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ł',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ő',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ő',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ő',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ő',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ű',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ű',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ű',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ű',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ŀ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ŀ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ŀ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ŀ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ħ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ħ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ħ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ħ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ð',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ð',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ð',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ð',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Þ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Þ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'þ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'þ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Œ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Œ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'œ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'œ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Æ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Æ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'æ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'æ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ø',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ø',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ø',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ø',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Å',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Å',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'å',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'å',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ə',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ə',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ə',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ə',
								'post': ''
							}
						}
					}
				]
			},
			'ipa': {
				labelMsg: 'edittoolbar-characters-page-ipa',
				layout: 'characters',
				characters: [
					{
						type: 'link',
						label: 'p',
						action: {
							type: 'encapsulate',
							options: {
								pre: 'p'
							}
						}
					},
					{
						type: 'link',
						label: 't̪',
						action: {
							type: 'encapsulate',
							options: {
								pre: 't̪'
							}
						}
					},
					{
						type: 'link',
						label: 't',
						action: {
							type: 'encapsulate',
							options: {
								pre: 't'
							}
						}
					},
					{
						type: 'link',
						label: 'ʈ',
						action: {
							type: 'encapsulate',
							'options': {
								'pre': 'ʈ'
							}
						}
					},
					{
						'type': 'link',
						'label': 'c',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'c'
							}
						}
					},
					{
						'type': 'link',
						'label': 'k',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'k'
							}
						}
					},
					{
						'type': 'link',
						'label': 'q',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'q'
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʡ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʡ'
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʔ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʔ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'b',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'b',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'd̪',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'd̪',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'd',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'd',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɖ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɖ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɟ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɟ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɡ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɡ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɢ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɢ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɓ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɓ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɗ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɗ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʄ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʄ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɠ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɠ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʛ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʛ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 't͡s',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 't͡s',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 't͡ʃ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 't͡ʃ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 't͡ɕ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 't͡ɕ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'd͡z',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'd͡z',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'd͡ʒ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'd͡ʒ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'd͡ʑ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'd͡ʑ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɸ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɸ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'f',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'f',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'θ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'θ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 's',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 's',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʃ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʃ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʅ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʅ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʆ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʆ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʂ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʂ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɕ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɕ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ç',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ç',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɧ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɧ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'x',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'x',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'χ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'χ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ħ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ħ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʜ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʜ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'h',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'h',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'β',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'β',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'v',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'v',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʍ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʍ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ð',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ð',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'z',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'z',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʒ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʒ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʓ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʓ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʐ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʐ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʑ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʑ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʝ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʝ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɣ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɣ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʁ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʁ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʕ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʕ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʖ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʖ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʢ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʢ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɦ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɦ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɬ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɬ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɮ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɮ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'm',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'm',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'm̩',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'm̩',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɱ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɱ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɱ̩',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɱ̩',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɱ̍',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɱ̍',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'n̪',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'n̪',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'n̪̍',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'n̪̍',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'n',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'n',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'n̩',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'n̩',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɳ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɳ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɳ̩',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɳ̩',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɲ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɲ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɲ̩',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɲ̩',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ŋ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ŋ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ŋ̍',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ŋ̍',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ŋ̩',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ŋ̩',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɴ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɴ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɴ̩',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɴ̩',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʙ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʙ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʙ̩',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʙ̩',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'r',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'r',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'r̩',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'r̩',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʀ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʀ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʀ̩',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʀ̩',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɾ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɾ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɽ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɽ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɿ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɿ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɺ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɺ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'l̪',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'l̪',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'l̪̩',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'l̪̩',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'l',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'l',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'l̩',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'l̩',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɫ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɫ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɫ̩',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɫ̩',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɭ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɭ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɭ̩',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɭ̩',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʎ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʎ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʎ̩',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʎ̩',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʟ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʟ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʟ̩',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʟ̩',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'w',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'w',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɥ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɥ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʋ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʋ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɹ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɹ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɻ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɻ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'j',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'j',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɰ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɰ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʘ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʘ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ǂ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ǂ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ǀ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ǀ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '!',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '!',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ǁ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ǁ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʰ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʰ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʱ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʱ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʷ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʷ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʸ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʸ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʲ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʲ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʳ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʳ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ⁿ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ⁿ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ˡ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ˡ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʴ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʴ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʵ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʵ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ˢ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ˢ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ˣ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ˣ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ˠ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ˠ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʶ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʶ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ˤ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ˤ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ˁ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ˁ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ˀ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ˀ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʼ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʼ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'i',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'i',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'i̯',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'i̯',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ĩ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ĩ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'y',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'y',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'y̯',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'y̯',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ỹ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ỹ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɪ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɪ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɪ̯',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɪ̯',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɪ̃',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɪ̃',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʏ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʏ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʏ̯',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʏ̯',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʏ̃',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʏ̃',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɨ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɨ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɨ̯',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɨ̯',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɨ̃',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɨ̃',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʉ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʉ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʉ̯',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʉ̯',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʉ̃',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʉ̃',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɯ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɯ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɯ̯',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɯ̯',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɯ̃',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɯ̃',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'u',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'u',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'u̯',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'u̯',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ũ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ũ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʊ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʊ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʊ̯',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʊ̯',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʊ̃',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʊ̃',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'e',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'e',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'e̯',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'e̯',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ẽ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ẽ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ø',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ø',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ø̯',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ø̯',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ø̃',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ø̃',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɘ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɘ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɘ̯',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɘ̯',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɘ̃',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɘ̃',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɵ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɵ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɵ̯',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɵ̯',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɵ̃',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɵ̃',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɤ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɤ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɤ̯',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɤ̯',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɤ̃',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɤ̃',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'o',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'o',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'o̯',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'o̯',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'õ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'õ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɛ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɛ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɛ̯',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɛ̯',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɛ̃',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɛ̃',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'œ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'œ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'œ̯',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'œ̯',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'œ̃',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'œ̃',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɜ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɜ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɜ̯',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɜ̯',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɜ̃',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɜ̃',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ə',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ə',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ə̯',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ə̯',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ə̃',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ə̃',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɞ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɞ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɞ̯',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɞ̯',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɞ̃',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɞ̃',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʌ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʌ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʌ̯',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʌ̯',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ʌ̃',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ʌ̃',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɔ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɔ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɔ̯',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɔ̯',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɔ̃',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɔ̃',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'æ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'æ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'æ̯',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'æ̯',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'æ̃',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'æ̃',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɶ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɶ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɶ̯',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɶ̯',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɶ̃',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɶ̃',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'a',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'a',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'a̯',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'a̯',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ã',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ã',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɐ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɐ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɐ̯',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɐ̯',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɐ̃',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɐ̃',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɑ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɑ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɑ̯',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɑ̯',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɑ̃',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɑ̃',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɒ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɒ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɒ̯',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɒ̯',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ɒ̃',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ɒ̃',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ˈ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ˈ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ˌ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ˌ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ː',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ː',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ˑ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ˑ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '˘',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '˘',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '.',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '.',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '‿',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '‿',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '|',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '|',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '‖',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '‖',
								'post': ''
							}
						}
					}
				]
			},
			'symbols': {
				'labelMsg': 'edittoolbar-characters-page-symbols',
				'layout': 'characters',
				'characters': [
					{
						'type': 'link',
						'label': '~',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '~',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '|',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '|',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '¡',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '¡',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '¿',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '¿',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '†',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '†',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '‡',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '‡',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '↔',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '↔',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '↑',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '↑',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '↓',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '↓',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '•',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '•',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '¶',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '¶',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '#',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '#',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '½',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '½',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '⅓',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '⅓',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '⅔',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '⅔',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '¼',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '¼',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '¾',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '¾',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '⅛',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '⅛',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '⅜',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '⅜',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '⅝',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '⅝',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '⅞',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '⅞',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '∞',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '∞',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '‘',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '‘',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '“',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '“',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '’',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '’',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '”',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '”',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '«»',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '«',
								'post': '»'
							}
						}
					},
					{
						'type': 'link',
						'label': '¤',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '¤',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '₳',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '₳',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '฿',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '฿',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '₵',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '₵',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '¢',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '¢',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '₡',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '₡',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '₢',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '₢',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '$',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '$',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '₫',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '₫',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '₯',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '₯',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '€',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '€',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '₠',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '₠',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '₣',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '₣',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ƒ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ƒ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '₴',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '₴',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '₭',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '₭',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '₤',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '₤',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ℳ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ℳ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '₥',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '₥',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '₦',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '₦',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '№',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '№',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '₧',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '₧',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '₰',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '₰',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '£',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '£',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '៛',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '៛',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '₨',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '₨',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '₪',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '₪',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '৳',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '৳',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '₮',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '₮',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '₩',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '₩',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '¥',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '¥',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '♠',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '♠',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '♣',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '♣',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '♥',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '♥',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '♦',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '♦',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'm²',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'm²',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'm³',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'm³',
								'post': ''
							}
						}
					}
				]
			},
			'greek':{
				'labelMsg': 'edittoolbar-characters-page-greek',
				'layout': 'characters',
				'attributes': {
					'lang': 'hl'
				},
				'characters': [
					{
						'type': 'link',
						'label': 'Α',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Α',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ά',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ά',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Β',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Β',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Γ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Γ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Δ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Δ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ε',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ε',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Έ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Έ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ζ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ζ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Η',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Η',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ή',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ή',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Θ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Θ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ι',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ι',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ί',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ί',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Κ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Κ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Λ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Λ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Μ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Μ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ν',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ν',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ξ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ξ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ο',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ο',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ό',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ό',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Π',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Π',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ρ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ρ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Σ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Σ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Τ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Τ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Υ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Υ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ύ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ύ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Φ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Φ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Χ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Χ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ψ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ψ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ω',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ω',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ώ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ώ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'α',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'α',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ά',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ά',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'β',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'β',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'γ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'γ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'δ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'δ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ε',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ε',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'έ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'έ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ζ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ζ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'η',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'η',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ή',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ή',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'θ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'θ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ι',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ι',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ί',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ί',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'κ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'κ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'λ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'λ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'μ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'μ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ν',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ν',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ξ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ξ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ο',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ο',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ό',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ό',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'π',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'π',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ρ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ρ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'σ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'σ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ς',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ς',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'τ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'τ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'υ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'υ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ύ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ύ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'φ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'φ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'χ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'χ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ψ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ψ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ω',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ω',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ώ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ώ',
								'post': ''
							}
						}
					}
				]
			},
			'cyrillic': {
				'labelMsg': 'edittoolbar-characters-page-cyrillic',
				'layout': 'characters',
				'characters': [
					{
						'type': 'link',
						'label': 'А',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'А',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ә',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ә',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Б',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Б',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'В',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'В',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Г',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Г',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ґ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ґ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ѓ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ѓ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ғ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ғ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Д',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Д',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ђ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ђ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Е',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Е',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Є',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Є',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ё',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ё',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ж',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ж',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'З',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'З',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ѕ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ѕ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'И',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'И',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'І',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'І',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ї',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ї',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'İ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'İ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Й',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Й',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ӣ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ӣ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ј',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ј',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'К',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'К',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ќ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ќ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Қ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Қ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Л',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Л',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Љ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Љ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'М',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'М',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Н',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Н',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Њ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Њ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ң',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ң',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'О',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'О',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ө',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ө',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'П',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'П',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Р',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Р',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'С',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'С',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Т',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Т',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ћ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ћ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'У',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'У',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ў',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ў',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ӯ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ӯ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ұ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ұ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ү',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ү',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ф',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ф',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Х',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Х',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ҳ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ҳ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Һ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Һ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ц',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ц',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ч',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ч',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ҷ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ҷ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Џ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Џ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ш',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ш',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Щ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Щ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ъ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ъ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ы',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ы',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ь',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ь',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Э',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Э',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Ю',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Ю',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'Я',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'Я',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'а',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'а',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ә',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ә',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'б',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'б',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'в',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'в',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'г',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'г',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ґ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ґ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ѓ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ѓ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ғ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ғ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'д',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'д',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ђ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ђ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'е',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'е',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'є',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'є',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ё',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ё',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ж',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ж',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'з',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'з',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ѕ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ѕ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'и',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'и',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'і',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'і',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ї',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ї',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'й',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'й',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ӣ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ӣ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ј',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ј',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'к',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'к',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ќ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ќ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'қ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'қ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'л',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'л',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'љ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'љ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'м',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'м',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'н',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'н',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'њ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'њ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ң',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ң',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'о',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'о',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ө',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ө',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'п',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'п',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'р',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'р',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'с',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'с',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'т',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'т',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ћ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ћ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'у',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'у',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ў',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ў',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ӯ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ӯ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ұ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ұ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ү',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ү',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ф',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ф',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'х',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'х',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ҳ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ҳ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'һ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'һ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ц',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ц',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ч',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ч',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ҷ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ҷ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'џ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'џ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ш',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ш',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'щ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'щ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ъ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ъ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ы',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ы',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ь',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ь',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'э',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'э',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ю',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ю',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'я',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'я',
								'post': ''
							}
						}
					}
				]
			},
			'arabic': {
				'labelMsg': 'edittoolbar-characters-page-arabic',
				'layout': 'characters',
				'attributes': {
					'lang': 'ar',
					'class': 'rtl'
				},
				'styles': {
					'font-size': '1.25em'
				},
				'characters': [
					{
						'type': 'link',
						'label': '؛',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '؛',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '؟',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '؟',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ء',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ء',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'آ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'آ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'أ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'أ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ؤ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ؤ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'إ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'إ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ئ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ئ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ا',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ا',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ب',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ب',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ة',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ة',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ت',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ت',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ث',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ث',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ج',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ج',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ح',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ح',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'خ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'خ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'د',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'د',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ذ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ذ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ر',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ر',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ز',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ز',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'س',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'س',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ش',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ش',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ص',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ص',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ض',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ض',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ط',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ط',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ظ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ظ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ع',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ع',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'غ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'غ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ف',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ف',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ق',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ق',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ك',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ك',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ل',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ل',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'م',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'م',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ن',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ن',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ه',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ه',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'و',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'و',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ى',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ى',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ي',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ي',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '،',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '،',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'پ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'پ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'چ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'چ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ژ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ژ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'گ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'گ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ڭ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ڭ',
								'post': ''
							}
						}
					}
				]
			},
			'hebrew': {
				'labelMsg': 'edittoolbar-characters-page-hebrew',
				'layout': 'characters',
				'attributes': {
					'class': 'rtl'
				},
				'characters': [
					{
						'type': 'link',
						'label': 'א',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'א',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ב',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ב',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ג',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ג',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ד',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ד',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ה',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ה',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ו',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ו',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ז',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ז',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ח',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ח',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ט',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ט',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'י',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'י',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ך',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ך',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'כ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'כ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ל',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ל',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ם',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ם',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'מ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'מ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ן',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ן',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'נ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'נ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ס',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ס',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ע',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ע',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ף',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ף',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'פ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'פ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ץ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ץ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'צ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'צ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ק',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ק',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ר',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ר',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ש',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ש',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ת',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ת',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '׳',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '׳',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': '״',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': '״',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'װ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'װ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ױ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ױ',
								'post': ''
							}
						}
					},
					{
						'type': 'link',
						'label': 'ײ',
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': 'ײ',
								'post': ''
							}
						}
					}
				]
			}
		}
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
