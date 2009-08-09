/* JavaScript for EditToolbar extension */

js2AddOnloadHook( function() {
	// Allow user/site JS to customize editToolbarConfiguration
	$j( document ).trigger( 'toolbarConfig' );
	$j( 'textarea#wpTextbox1' ).wikiEditor(
		{ 'toolbar': editToolbarConfiguration }
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
				'characters': [ "\u00c1",
					"\u00e1", "\u00c0", "\u00e0", "\u00c2",
					"\u00e2", "\u00c4", "\u00e4", "\u00c3",
					"\u00e3", "\u01cd", "\u01ce", "\u0100",
					"\u0101", "\u0102", "\u0103", "\u0104",
					"\u0105", "\u00c5", "\u00e5", "\u0106",
					"\u0107", "\u0108", "\u0109", "\u00c7",
					"\u00e7", "\u010c", "\u010d", "\u010a",
					"\u010b", "\u0110", "\u0111", "\u010e",
					"\u010f", "\u00c9", "\u00e9", "\u00c8",
					"\u00e8", "\u00ca", "\u00ea", "\u00cb",
					"\u00eb", "\u011a", "\u011b", "\u0112",
					"\u0113", "\u0114", "\u0115", "\u0116",
					"\u0117", "\u0118", "\u0119", "\u011c",
					"\u011d", "\u0122", "\u0123", "\u011e",
					"\u011f", "\u0120", "\u0121", "\u0124",
					"\u0125", "\u0126", "\u0127", "\u00cd",
					"\u00ed", "\u00cc", "\u00ec", "\u00ce",
					"\u00ee", "\u00cf", "\u00ef", "\u0128",
					"\u0129", "\u01cf", "\u01d0", "\u012a",
					"\u012b", "\u012c", "\u012d", "\u0130",
					"\u0131", "\u012e", "\u012f", "\u0134",
					"\u0135", "\u0136", "\u0137", "\u0139",
					"\u013a", "\u013b", "\u013c", "\u013d",
					"\u013e", "\u0141", "\u0142", "\u013f",
					"\u0140", "\u0143", "\u0144", "\u00d1",
					"\u00f1", "\u0145", "\u0146", "\u0147",
					"\u0148", "\u00d3", "\u00f3", "\u00d2",
					"\u00f2", "\u00d4", "\u00f4", "\u00d6",
					"\u00f6", "\u00d5", "\u00f5", "\u01d1",
					"\u01d2", "\u014c", "\u014d", "\u014e",
					"\u014f", "\u01ea", "\u01eb", "\u0150",
					"\u0151", "\u0154", "\u0155", "\u0156",
					"\u0157", "\u0158", "\u0159", "\u015a",
					"\u015b", "\u015c", "\u015d", "\u015e",
					"\u015f", "\u0160", "\u0161", "\u0162",
					"\u0163", "\u0164", "\u0165", "\u00da",
					"\u00fa", "\u00d9", "\u00f9", "\u00db",
					"\u00fb", "\u00dc", "\u00fc", "\u0168",
					"\u0169", "\u016e", "\u016f", "\u01d3",
					"\u01d4", "\u016a", "\u016b", "\u01d6",
					"\u01d8", "\u01da", "\u01dc", "\u016c",
					"\u016d", "\u0172", "\u0173", "\u0170",
					"\u0171", "\u0174", "\u0175", "\u00dd",
					"\u00fd", "\u0176", "\u0177", "\u0178",
					"\u00ff", "\u0232", "\u0233", "\u0179",
					"\u017a", "\u017d", "\u017e", "\u017b",
					"\u017c", "\u00c6", "\u00e6", "\u01e2",
					"\u01e3", "\u00d8", "\u00f8", "\u0152",
					"\u0153", "\u00df", "\u00f0", "\u00de",
					"\u00fe", "\u018f", "\u0259" ]
			},
			'latinextended': {
				'labelMsg': 'edittoolbar-characters-page-latinextended',
				'layout': 'characters',
				'characters': [ "\u1e00", "\u1e01", "\u1e9a",
					"\u1ea0", "\u1ea1", "\u1ea2", "\u1ea3",
					"\u1ea4", "\u1ea5", "\u1ea6", "\u1ea7",
					"\u1ea8", "\u1ea9", "\u1eaa", "\u1eab",
					"\u1eac", "\u1ead", "\u1eae", "\u1eaf",
					"\u1eb0", "\u1eb1", "\u1eb2", "\u1eb3",
					"\u1eb4", "\u1eb5", "\u1eb6", "\u1eb7",
					"\u1e02", "\u1e03", "\u1e04", "\u1e05",
					"\u1e06", "\u1e07", "\u1e08", "\u1e09",
					"\u1e0a", "\u1e0b", "\u1e0c", "\u1e0d",
					"\u1e0e", "\u1e0f", "\u1e10", "\u1e11",
					"\u1e12", "\u1e13", "\u1e14", "\u1e15",
					"\u1e16", "\u1e17", "\u1e18", "\u1e19", 
					"\u1e1a", "\u1e1b", "\u1e1c", "\u1e1d",
					"\u1eb8", "\u1eb9", "\u1eba", "\u1ebb",
					"\u1ebc", "\u1ebd", "\u1ebe", "\u1ebf",
					"\u1ec0", "\u1ec1", "\u1ec2", "\u1ec3",
					"\u1ec4", "\u1ec5", "\u1ec6", "\u1ec7",
					"\u1e1e", "\u1e1f", "\u1e20", "\u1e21",
					"\u1e22", "\u1e23", "\u1e24", "\u1e25",
					"\u1e26", "\u1e27", "\u1e28", "\u1e29",
					"\u1e2a", "\u1e2b", "\u1e96", "\u1e2c",
					"\u1e2d", "\u1e2e", "\u1e2f", "\u1ec8",
					"\u1ec9", "\u1eca", "\u1ecb", "\u1e30",
					"\u1e31", "\u1e32", "\u1e33", "\u1e34",
					"\u1e35", "\u1e36", "\u1e37", "\u1e38",
					"\u1e39", "\u1e3a", "\u1e3b", "\u1e3c",
					"\u1e3d", "\u1efa", "\u1efb", "\u1e3e",
					"\u1e3f", "\u1e40", "\u1e41", "\u1e42",
					"\u1e43", "\u1e44", "\u1e45", "\u1e46",
					"\u1e47", "\u1e48", "\u1e49", "\u1e4a",
					"\u1e4b", "\u1e4c", "\u1e4d", "\u1e4e",
					"\u1e4f", "\u1e50", "\u1e51", "\u1e52",
					"\u1e53", "\u1ecc", "\u1ecd", "\u1ece",
					"\u1ecf", "\u1ed0", "\u1ed1", "\u1ed2",
					"\u1ed3", "\u1ed4", "\u1ed5", "\u1ed6",
					"\u1ed7", "\u1ed8", "\u1ed9", "\u1eda",
					"\u1edb", "\u1edc", "\u1edd", "\u1ede",
					"\u1edf", "\u1ee0", "\u1ee1", "\u1ee2",
					"\u1ee3", "\u1e54", "\u1e55", "\u1e56",
					"\u1e57", "\u1e58", "\u1e59", "\u1e5a",
					"\u1e5b", "\u1e5c", "\u1e5d", "\u1e5e",
					"\u1e5f", "\u1e60", "\u1e61", "\u1e9b",
					"\u1e62", "\u1e63", "\u1e64", "\u1e65",
					"\u1e66", "\u1e67", "\u1e68", "\u1e69",
					"\u1e9c", "\u1e9d", "\u1e6a", "\u1e6b",
					"\u1e6c", "\u1e6d", "\u1e6e", "\u1e6f",
					"\u1e70", "\u1e71", "\u1e97", "\u1e72",
					"\u1e73", "\u1e74", "\u1e75", "\u1e76",
					"\u1e77", "\u1e78", "\u1e79", "\u1e7a",
					"\u1e7b", "\u1ee4", "\u1ee5", "\u1ee6",
					"\u1ee7", "\u1ee8", "\u1ee9", "\u1eea",
					"\u1eeb", "\u1eec", "\u1eed", "\u1eee",
					"\u1eef", "\u1ef0", "\u1ef1", "\u1e7c",
					"\u1e7d", "\u1e7e", "\u1e7f", "\u1efc",
					"\u1efd", "\u1e80", "\u1e81", "\u1e82",
					"\u1e83", "\u1e84", "\u1e85", "\u1e86",
					"\u1e87", "\u1e88", "\u1e89", "\u1e98",
					"\u1e8a", "\u1e8b", "\u1e8c", "\u1e8d",
					"\u1e8e", "\u1e8f", "\u1e99", "\u1ef2",
					"\u1ef3", "\u1ef4", "\u1ef5", "\u1ef6",
					"\u1ef7", "\u1ef8", "\u1ef9", "\u1efe",
					"\u1eff", "\u1e90", "\u1e91", "\u1e92",
					"\u1e93", "\u1e94", "\u1e95", "\u1e9e",
					"\u1e9f" ]
			},
			'ipa': {
				labelMsg: 'edittoolbar-characters-page-ipa',
				layout: 'characters',
				characters: [ "p",
					"t\u032a", "t", "\u0288", "c", "k",
					"q", "\u02a1", "\u0294", "b",
					"d\u032a", "d", "\u0256", "\u025f",
					"\u0261", "\u0262", "\u0253", "\u0257",
					"\u0284", "\u0260", "\u029b",
					"t\u0361s", "t\u0361\u0283",
					"t\u0361\u0255", "d\u0361z",
					"d\u0361\u0292", "d\u0361\u0291",
					"\u0278", "f", "\u03b8", "s", "\u0283",
					"\u0285", "\u0286", "\u0282", "\u0255",
					"\u00e7", "\u0267", "x", "\u03c7",
					"\u0127", "\u029c", "h", "\u03b2", "v",
					"\u028d", "\u00f0", "z", "\u0292",
					"\u0293", "\u0290", "\u0291", "\u029d",
					"\u0263", "\u0281", "\u0295", "\u0296",
					"\u02a2", "\u0266", "\u026c", "\u026e",
					"m", "m\u0329", "\u0271",
					"\u0271\u0329", "\u0271\u030d",
					"n\u032a", "n\u032a\u030d", "n",
					"n\u0329", "\u0273", "\u0273\u0329",
					"\u0272", "\u0272\u0329", "\u014b",
					"\u014b\u030d", "\u014b\u0329",
					"\u0274", "\u0274\u0329", "\u0299",
					"\u0299\u0329", "r", "r\u0329",
					"\u0280", "\u0280\u0329", "\u027e",
					"\u027d", "\u027f", "\u027a",
					"l\u032a", "l\u032a\u0329", "l",
					"l\u0329", "\u026b", "\u026b\u0329",
					"\u026d", "\u026d\u0329", "\u028e",
					"\u028e\u0329", "\u029f",
					"\u029f\u0329", "w", "\u0265",
					"\u028b", "\u0279", "\u027b", "j",
					"\u0270", "\u0298", "\u01c2", "\u01c0",
					"!", "\u01c1", "\u02b0", "\u02b1",
					"\u02b7", "\u02b8", "\u02b2", "\u02b3",
					"\u207f", "\u02e1", "\u02b4", "\u02b5",
					"\u02e2", "\u02e3", "\u02e0", "\u02b6",
					"\u02e4", "\u02c1", "\u02c0", "\u02bc",
					"i", "i\u032f", "\u0129", "y",
					"y\u032f", "\u1ef9", "\u026a",
					"\u026a\u032f", "\u026a\u0303",
					"\u028f", "\u028f\u032f",
					"\u028f\u0303", "\u0268",
					"\u0268\u032f", "\u0268\u0303",
					"\u0289", "\u0289\u032f",
					"\u0289\u0303", "\u026f",
					"\u026f\u032f", "\u026f\u0303", "u",
					"u\u032f", "\u0169", "\u028a",
					"\u028a\u032f", "\u028a\u0303", "e",
					"e\u032f", "\u1ebd", "\u00f8",
					"\u00f8\u032f", "\u00f8\u0303",
					"\u0258", "\u0258\u032f",
					"\u0258\u0303", "\u0275",
					"\u0275\u032f", "\u0275\u0303",
					"\u0264", "\u0264\u032f",
					"\u0264\u0303", "o", "o\u032f",
					"\u00f5", "\u025b", "\u025b\u032f",
					"\u025b\u0303", "\u0153",
					"\u0153\u032f", "\u0153\u0303",
					"\u025c", "\u025c\u032f",
					"\u025c\u0303", "\u0259",
					"\u0259\u032f", "\u0259\u0303",
					"\u025e", "\u025e\u032f",
					"\u025e\u0303", "\u028c",
					"\u028c\u032f", "\u028c\u0303",
					"\u0254", "\u0254\u032f",
					"\u0254\u0303", "\u00e6",
					"\u00e6\u032f", "\u00e6\u0303",
					"\u0276", "\u0276\u032f",
					"\u0276\u0303", "a", "a\u032f",
					"\u00e3", "\u0250", "\u0250\u032f",
					"\u0250\u0303", "\u0251",
					"\u0251\u032f", "\u0251\u0303",
					"\u0252", "\u0252\u032f",
					"\u0252\u0303", "\u02c8", "\u02cc",
					"\u02d0", "\u02d1", "\u02d8", ".",
					"\u203f", "|", "\u2016" ]
			},
			'symbols': {
				'labelMsg': 'edittoolbar-characters-page-symbols',
				'layout': 'characters',
				'characters': [ "~", "|",
					"\u00a1", "\u00bf", "\u2020", "\u2021",
					"\u2194", "\u2191", "\u2193", "\u2022",
					"\u00b6", "#", "\u00bd", "\u2153",
					"\u2154", "\u00bc", "\u00be", "\u215b",
					"\u215c", "\u215d", "\u215e", "\u221e",
					"\u2018", "\u201c", "\u2019",
					"\u201d", {
						'label': "\u00ab\u00bb",
						'action': {
							'type': 'encapsulate',
							'options': {
								'pre': "\u00ab",
								'post': "\u00bb"
							}
						}
					},
					"\u00a4", "\u20b3", "\u0e3f", "\u20b5",
					"\u00a2", "\u20a1", "\u20a2", "$",
					"\u20ab", "\u20af", "\u20ac", "\u20a0",
					"\u20a3", "\u0192", "\u20b4", "\u20ad",
					"\u20a4", "\u2133", "\u20a5", "\u20a6",
					"\u2116", "\u20a7", "\u20b0", "\u00a3",
					"\u17db", "\u20a8", "\u20aa", "\u09f3",
					"\u20ae", "\u20a9", "\u00a5", "\u2660",
					"\u2663", "\u2665", "\u2666",
					"m\u00b2", "m\u00b3", "\u2013",
					"\u2014", "\u2026", "\u2018", "\u201c",
					"\u2019", "\u201d", "\u00b0", "\u2033",
					"\u2032", "\u2248", "\u2260", "\u2264",
					"\u2265", "\u00b1", "\u2212", "\u00d7",
					"\u00f7", "\u2190", "\u2192", "\u00b7",
					"\u00a7" ]
			},
			'greek': {
				'labelMsg': 'edittoolbar-characters-page-greek',
				'layout': 'characters',
				'language': 'hl',
				'characters': [ "\u0391",
					"\u0386", "\u03b1", "\u03ac", "\u0392",
					"\u03b2", "\u0393", "\u03b3", "\u0394",
					"\u03b4", "\u0395", "\u0388", "\u03b5",
					"\u03ad", "\u0396", "\u03b6", "\u0397",
					"\u0389", "\u03b7", "\u03ae", "\u0398",
					"\u03b8", "\u0399", "\u038a", "\u03b9",
					"\u03af", "\u039a", "\u03ba", "\u039b",
					"\u03bb", "\u039c", "\u03bc", "\u039d",
					"\u03bd", "\u039e", "\u03be", "\u039f",
					"\u038c", "\u03bf", "\u03cc", "\u03a0",
					"\u03c0", "\u03a1", "\u03c1", "\u03a3",
					"\u03c3", "\u03c2", "\u03a4", "\u03c4",
					"\u03a5", "\u038e", "\u03c5", "\u03cd",
					"\u03a6", "\u03c6", "\u03a7", "\u03c7",
					"\u03a8", "\u03c8", "\u03a9", "\u038f",
					"\u03c9", "\u03ce" ]
			},
			'cyrillic': {
				'labelMsg': 'edittoolbar-characters-page-cyrillic',
				'layout': 'characters',
				'characters': [ "\u0410",
					"\u0430", "\u04d8", "\u04d9", "\u0411",
					"\u0431", "\u0412", "\u0432", "\u0413",
					"\u0433", "\u0490", "\u0491", "\u0403",
					"\u0453", "\u0492", "\u0493", "\u0414",
					"\u0434", "\u0402", "\u0452", "\u0415",
					"\u0435", "\u0404", "\u0454", "\u0401",
					"\u0451", "\u0416", "\u0436", "\u0417",
					"\u0437", "\u0405", "\u0455", "\u0418",
					"\u0438", "\u0406", "\u0456", "\u0407",
					"\u0457", "\u0130", "\u0419", "\u0439",
					"\u04e2", "\u04e3", "\u0408", "\u0458",
					"\u041a", "\u043a", "\u040c", "\u045c",
					"\u049a", "\u049b", "\u041b", "\u043b",
					"\u0409", "\u0459", "\u041c", "\u043c",
					"\u041d", "\u043d", "\u040a", "\u045a",
					"\u04a2", "\u04a3", "\u041e", "\u043e",
					"\u04e8", "\u04e9", "\u041f", "\u043f",
					"\u0420", "\u0440", "\u0421", "\u0441",
					"\u0422", "\u0442", "\u040b", "\u045b",
					"\u0423", "\u0443", "\u040e", "\u045e",
					"\u04ee", "\u04ef", "\u04b0", "\u04b1",
					"\u04ae", "\u04af", "\u0424", "\u0444",
					"\u0425", "\u0445", "\u04b2", "\u04b3",
					"\u04ba", "\u04bb", "\u0426", "\u0446",
					"\u0427", "\u0447", "\u04b6", "\u04b7",
					"\u040f", "\u045f", "\u0428", "\u0448",
					"\u0429", "\u0449", "\u042a", "\u044a",
					"\u042b", "\u044b", "\u042c", "\u044c",
					"\u042d", "\u044d", "\u042e", "\u044e",
					"\u042f", "\u044f" ]
			},
			'arabic': {
				'labelMsg': 'edittoolbar-characters-page-arabic',
				'layout': 'characters',
				'language': 'ar',
				'direction': 'rtl',
				'characters': [ "\u061b",
					"\u061f", "\u0621", "\u0622", "\u0623",
					"\u0624", "\u0625", "\u0626", "\u0627",
					"\u0628", "\u0629", "\u062a", "\u062b",
					"\u062c", "\u062d", "\u062e", "\u062f",
					"\u0630", "\u0631", "\u0632", "\u0633",
					"\u0634", "\u0635", "\u0636", "\u0637",
					"\u0638", "\u0639", "\u063a", "\u0641",
					"\u0642", "\u0643", "\u0644", "\u0645",
					"\u0646", "\u0647", "\u0648", "\u0649",
					"\u064a", "\u060c", "\u067e", "\u0686",
					"\u0698", "\u06af", "\u06ad" ]
			},
			'hebrew': {
				'labelMsg': 'edittoolbar-characters-page-hebrew',
				'layout': 'characters',
				'direction': 'rtl',
				'characters': [ "\u05d0",
					"\u05d1", "\u05d2", "\u05d3", "\u05d4",
					"\u05d5", "\u05d6", "\u05d7", "\u05d8",
					"\u05d9", "\u05db", "\u05da", "\u05dc",
					"\u05de", "\u05dd", "\u05e0", "\u05df",
					"\u05e1", "\u05e2", "\u05e4", "\u05e3",
					"\u05e6", "\u05e5", "\u05e7", "\u05e8",
					"\u05e9", "\u05ea", "\u05f3", "\u05f4",
					"\u05f0", "\u05f1", "\u05f2", "\u05d0",
					"\u05d3", "\u05d4", "\u05d5", "\u05d6",
					"\u05d7", "\u05d8", "\u05d9", "\u05da",
					"\u05db", "\u05dc", "\u05dd", "\u05de",
					"\u05df", "\u05e0", "\u05e1", "\u05e2",
					"\u05e3", "\u05e4", "\u05be", "\u05f3",
					"\u05f4", [ "\u05b0\u25cc", "\u05b0" ],
					[ "\u05b1\u25cc", "\u05b1" ],
					[ "\u05b2\u25cc", "\u05b2" ],
					[ "\u05b3\u25cc", "\u05b3" ],
					[ "\u05b4\u25cc", "\u05b4" ],
					[ "\u05b5\u25cc", "\u05b5" ],
					[ "\u05b6\u25cc", "\u05b6" ],
					[ "\u05b7\u25cc", "\u05b7" ],
					[ "\u05b8\u25cc", "\u05b8" ],
					[ "\u05b9\u25cc", "\u05b9" ],
					[ "\u05bb\u25cc", "\u05bb" ],
					[ "\u05bc\u25cc", "\u05bc" ],
					[ "\u05c1\u25cc", "\u05c1" ],
					[ "\u05c2\u25cc", "\u05c2" ],
					[ "\u05c7\u25cc", "\u05c7" ],
					[ "\u0591\u25cc", "\u0591" ],
					[ "\u0592\u25cc", "\u0592" ],
					[ "\u0593\u25cc", "\u0593" ],
					[ "\u0594\u25cc", "\u0594" ],
					[ "\u0595\u25cc", "\u0595" ],
					[ "\u0596\u25cc", "\u0596" ],
					[ "\u0597\u25cc", "\u0597" ],
					[ "\u0598\u25cc", "\u0598" ],
					[ "\u0599\u25cc", "\u0599" ],
					[ "\u059a\u25cc", "\u059a" ],
					[ "\u059b\u25cc", "\u059b" ],
					[ "\u059c\u25cc", "\u059c" ],
					[ "\u059d\u25cc", "\u059d" ],
					[ "\u059e\u25cc", "\u059e" ],
					[ "\u059f\u25cc", "\u059f" ],
					[ "\u05a0\u25cc", "\u05a0" ],
					[ "\u05a1\u25cc", "\u05a1" ],
					[ "\u05a2\u25cc", "\u05a2" ],
					[ "\u05a3\u25cc", "\u05a3" ],
					[ "\u05a4\u25cc", "\u05a4" ],
					[ "\u05a5\u25cc", "\u05a5" ],
					[ "\u05a6\u25cc", "\u05a6" ],
					[ "\u05a7\u25cc", "\u05a7" ],
					[ "\u05a8\u25cc", "\u05a8" ],
					[ "\u05a9\u25cc", "\u05a9" ],
					[ "\u05aa\u25cc", "\u05aa" ],
					[ "\u05ab\u25cc", "\u05ab" ],
					[ "\u05ac\u25cc", "\u05ac" ],
					[ "\u05ad\u25cc", "\u05ad" ],
					[ "\u05ae\u25cc", "\u05ae" ],
					[ "\u05af\u25cc", "\u05af" ],
					[ "\u05bf\u25cc", "\u05bf" ],
					[ "\u05c0\u25cc", "\u05c0" ],
					[ "\u05c3\u25cc", "\u05c3" ]
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
