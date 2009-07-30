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
						'label': "\u00c1",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00c1"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00e1",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00e1"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00c0",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00c0"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00e0",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00e0"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00c2",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00c2"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00e2",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00e2"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00c4",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00c4"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00e4",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00e4"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00c3",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00c3"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00e3",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00e3"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u01cd",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u01cd"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u01ce",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u01ce"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0100",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0100"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0101",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0101"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0102",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0102"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0103",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0103"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0104",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0104"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0105",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0105"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00c5",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00c5"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00e5",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00e5"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0106",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0106"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0107",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0107"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0108",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0108"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0109",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0109"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00c7",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00c7"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00e7",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00e7"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u010c",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u010c"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u010d",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u010d"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u010a",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u010a"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u010b",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u010b"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0110",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0110"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0111",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0111"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u010e",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u010e"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u010f",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u010f"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u1e0c",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u1e0c"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u1e0d",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u1e0d"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00c9",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00c9"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00e9",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00e9"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00c8",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00c8"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00e8",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00e8"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00ca",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00ca"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00ea",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00ea"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00cb",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00cb"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00eb",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00eb"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u1ebc",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u1ebc"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u1ebd",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u1ebd"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u011a",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u011a"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u011b",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u011b"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0112",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0112"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0113",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0113"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0114",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0114"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0115",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0115"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0116",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0116"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0117",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0117"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0118",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0118"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0119",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0119"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u011c",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u011c"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u011d",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u011d"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0122",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0122"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0123",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0123"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u011e",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u011e"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u011f",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u011f"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0120",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0120"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0121",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0121"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0124",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0124"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0125",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0125"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u1e24",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u1e24"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u1e25",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u1e25"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0126",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0126"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0127",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0127"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00cd",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00cd"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00ed",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00ed"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00cc",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00cc"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00ec",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00ec"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00ce",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00ce"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00ee",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00ee"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00cf",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00cf"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00ef",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00ef"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0128",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0128"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0129",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0129"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u01cf",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u01cf"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u01d0",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u01d0"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u012a",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u012a"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u012b",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u012b"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u012c",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u012c"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u012d",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u012d"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0130",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0130"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0131",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0131"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u012e",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u012e"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u012f",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u012f"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0134",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0134"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0135",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0135"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0136",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0136"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0137",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0137"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0139",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0139"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u013a",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u013a"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u013b",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u013b"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u013c",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u013c"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u013d",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u013d"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u013e",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u013e"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u1e36",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u1e36"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u1e37",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u1e37"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u1e38",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u1e38"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u1e39",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u1e39"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0141",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0141"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0142",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0142"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u013f",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u013f"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0140",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0140"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u1e42",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u1e42"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u1e43",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u1e43"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0143",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0143"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0144",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0144"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00d1",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00d1"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00f1",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00f1"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0145",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0145"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0146",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0146"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0147",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0147"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0148",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0148"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u1e46",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u1e46"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u1e47",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u1e47"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00d3",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00d3"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00f3",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00f3"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00d2",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00d2"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00f2",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00f2"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00d4",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00d4"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00f4",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00f4"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00d6",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00d6"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00f6",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00f6"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00d5",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00d5"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00f5",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00f5"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u01d1",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u01d1"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u01d2",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u01d2"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u014c",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u014c"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u014d",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u014d"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u014e",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u014e"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u014f",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u014f"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u01ea",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u01ea"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u01eb",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u01eb"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0150",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0150"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0151",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0151"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0154",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0154"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0155",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0155"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0156",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0156"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0157",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0157"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0158",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0158"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0159",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0159"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u1e5a",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u1e5a"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u1e5b",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u1e5b"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u1e5c",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u1e5c"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u1e5d",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u1e5d"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u015a",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u015a"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u015b",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u015b"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u015c",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u015c"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u015d",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u015d"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u015e",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u015e"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u015f",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u015f"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0160",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0160"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0161",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0161"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u1e62",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u1e62"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u1e63",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u1e63"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0162",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0162"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0163",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0163"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0164",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0164"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0165",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0165"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u1e6c",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u1e6c"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u1e6d",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u1e6d"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00da",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00da"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00fa",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00fa"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00d9",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00d9"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00f9",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00f9"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00db",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00db"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00fb",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00fb"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00dc",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00dc"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00fc",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00fc"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0168",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0168"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0169",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0169"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u016e",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u016e"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u016f",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u016f"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u01d3",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u01d3"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u01d4",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u01d4"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u016a",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u016a"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u016b",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u016b"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u01d6",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u01d6"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u01d8",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u01d8"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u01da",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u01da"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u01dc",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u01dc"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u016c",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u016c"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u016d",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u016d"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0172",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0172"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0173",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0173"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0170",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0170"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0171",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0171"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0174",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0174"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0175",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0175"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00dd",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00dd"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00fd",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00fd"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0176",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0176"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0177",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0177"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0178",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0178"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00ff",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00ff"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u1ef8",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u1ef8"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u1ef9",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u1ef9"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0232",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0232"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0233",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0233"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0179",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0179"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u017a",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u017a"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u017d",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u017d"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u017e",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u017e"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u017b",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u017b"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u017c",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u017c"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00c6",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00c6"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00e6",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00e6"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u01e2",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u01e2"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u01e3",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u01e3"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00d8",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00d8"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00f8",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00f8"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0152",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0152"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0153",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0153"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00df",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00df"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00f0",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00f0"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00de",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00de"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u00fe",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00fe"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u018f",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u018f"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0259",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0259"
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
						'label': "\u0391",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0391"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0386",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0386"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03b1",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03b1"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03ac",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03ac"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0392",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0392"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03b2",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03b2"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0393",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0393"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03b3",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03b3"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0394",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0394"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03b4",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03b4"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0395",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0395"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0388",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0388"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03b5",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03b5"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03ad",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03ad"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0396",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0396"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03b6",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03b6"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0397",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0397"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0389",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0389"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03b7",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03b7"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03ae",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03ae"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0398",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0398"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03b8",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03b8"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u0399",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u0399"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u038a",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u038a"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03b9",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03b9"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03af",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03af"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u039a",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u039a"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03ba",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03ba"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u039b",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u039b"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03bb",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03bb"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u039c",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u039c"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03bc",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03bc"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u039d",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u039d"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03bd",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03bd"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u039e",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u039e"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03be",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03be"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u039f",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u039f"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u038c",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u038c"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03bf",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03bf"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03cc",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03cc"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03a0",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03a0"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03c0",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03c0"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03a1",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03a1"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03c1",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03c1"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03a3",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03a3"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03c3",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03c3"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03c2",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03c2"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03a4",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03a4"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03c4",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03c4"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03a5",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03a5"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u038e",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u038e"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03c5",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03c5"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03cd",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03cd"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03a6",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03a6"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03c6",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03c6"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03a7",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03a7"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03c7",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03c7"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03a8",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03a8"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03c8",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03c8"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03a9",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03a9"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u038f",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u038f"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03c9",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03c9"
							}
						}
					},
					{
						'type': 'link',
						'label': "\u03ce",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u03ce"
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
