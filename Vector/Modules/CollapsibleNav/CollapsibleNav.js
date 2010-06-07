/* Prototype code to show collapsing left nav options */
/* First draft and will be changing greatly */

$j(document).ready( function() {
	if( !wgVectorEnabledModules.collapsiblenav ) {
		return true;
	}
	var mod = {
		'browsers': {
			// Left-to-right languages
			'ltr': {
				// Collapsible Nav is broken in Opera < 9.6 and Konqueror < 4
				'opera': [['>=', 9.6]],
				'konqueror': [['>=', 4.0]],
				'blackberry': false,
				'ipod': false,
				'iphone': false
			},
			// Right-to-left languages
			'rtl': {
				'opera': [['>=', 9.6]],
				'konqueror': [['>=', 4.0]],
				'blackberry': false,
				'ipod': false,
				'iphone': false
			}
		}
	};
	if ( !$j.wikiEditor.isSupported( mod ) ) {
		return true;
	}
	// Create a new portal for overflow languages
	$j( '#p-lang' )
		.after( '<div id="p-lang-more" class="portal"><h5></h5><div class="body"><ul></ul></div></div>' )
		.addClass( 'persistent' );
	$j( '#panel > div.portal:first' )
		.addClass( 'first persistent' );
	$j( '#p-lang-more h5' ).text( mw.usability.getMsg( 'vector-collapsiblenav-more' ) );
	// Apply a class to the entire panel to activate styles
	$j( '#panel' ).addClass( 'collapsible-nav' );
	// Use cookie data to restore preferences of what to show and hide
	$j( '#panel > div.portal:not(.persistent)' )
		.each( function( i ) {
			var state = $j.cookie( 'vector-nav-' + $j(this).attr( 'id' ) );
			if ( state == 'true' || ( state == null && i < 1 ) ) {
				$j(this)
					.addClass( 'expanded' )
					.find( 'div.body' )
					.show();
			} else {
				$j(this).addClass( 'collapsed' );
			}
			// Re-save cookie
			if ( state != null ) {
				$j.cookie( 'vector-nav-' + $j(this).attr( 'id' ), state, { expires: 30, path: '/' } );
			}
		} );
	
	// Use the same function for all navigation headings - don't repeat yourself
	function toggle( $element ) {
		$j.cookie( 'vector-nav-' + $element.parent().attr( 'id' ), $element.parent().is( '.collapsed' ), { expires: 30, path: '/' } );
		$element
			.parent()
			.toggleClass( 'expanded' )
			.toggleClass( 'collapsed' )
			.find( 'div.body' )
			.slideToggle( 'fast' );
	}
	var $headings = $j( '#panel > div.portal:not(.persistent) > h5' );
	/** Copy-pasted from jquery.wikiEditor.dialogs - :( */
	// Find the highest tabindex in use
	var maxTI = 0;
	$j( '[tabindex]' ).each( function() {
		var ti = parseInt( $j(this).attr( 'tabindex' ) );
		if ( ti > maxTI )
			maxTI = ti;
	});
	var tabIndex = maxTI + 1;
	// Fix the search not having a tabindex
	$j( '#searchInput' ).attr( 'tabindex', tabIndex++ );
	// Make it keyboard accessible
	$headings.each( function() {
		$j(this).attr( 'tabindex', tabIndex++ );
	} );
	/** End of copy-pasted section */
	// Toggle the selected menu's class and expand or collapse the menu
	$headings
		// Make the space and enter keys act as a click
		.keydown( function( event ) {
			if ( event.which == 13 /* Enter */ || event.which == 32 /* Space */ ) {
				toggle( $j(this) );
			}
		} )
		.mousedown( function() {
			toggle( $j(this) );
			$j(this).blur();
			return false;
		} );
	// Split the language lists, showing the first 5 in the original portal and all others in the overflow portal
	var limit = 5;
	var count = 0;
	$more = $j( '#p-lang-more ul' );
	$j( '#p-lang li' ).each( function() {
		if ( count++ >= limit ) {
			$j(this).remove().appendTo( $more );
		}
	} );
	/*
	 * It may be clever to use something like this to steer which languages get shown by default...
	var wikipediaProjectSizes = {
		'en': 1, 'fr': 2, 'de': 3, 'es': 4, 'pt': 5, 'it': 6, 'ru': 7, 'ja': 8, 'nl': 9, 'pl': 10, 'zh': 11, 'sv': 12,
		'ar': 13, 'tr': 14, 'uk': 15, 'fi': 16, 'no': 17, 'ca': 18, 'ro': 19, 'hu': 20, 'ksh': 21, 'id': 22, 'he': 23,
		'cs': 24, 'vi': 25, 'ko': 26, 'sr': 27, 'fa': 28, 'da': 29, 'eo': 30, 'sk': 31, 'th': 32, 'lt': 33, 'vo': 34,
		'bg': 35, 'sl': 36, 'hr': 37, 'hi': 38, 'et': 39, 'mk': 40, 'simple': 41, 'new': 42, 'ms': 43, 'nn': 44,
		'gl': 45, 'el': 46, 'eu': 47, 'ka': 48, 'tl': 49, 'bn': 50, 'lv': 51, 'ml': 52, 'bs': 53, 'te': 54, 'la': 55,
		'az': 56, 'sh': 57, 'war': 58, 'br': 59, 'is': 60, 'mr': 61, 'be-x-old': 62, 'sq': 63, 'cy': 64, 'lb': 65,
		'ta': 66, 'zh-classical': 67, 'an': 68, 'jv': 69, 'ht': 70, 'oc': 71, 'bpy': 72, 'ceb': 73, 'ur': 74,
		'zh-yue': 75, 'pms': 76, 'scn': 77, 'be': 78, 'roa-rup': 79, 'qu': 80, 'af': 81, 'sw': 82, 'nds': 83, 'fy': 84,
		'lmo': 85, 'wa': 86, 'ku': 87, 'hy': 88, 'su': 89, 'yi': 90, 'io': 91, 'os': 92, 'ga': 93, 'ast': 94, 'nap': 95,
		'vec': 96, 'gu': 97, 'cv': 98, 'bat-smg': 99, 'kn': 100, 'uz': 101, 'zh-min-nan': 102, 'si': 103, 'als': 104,
		'yo': 105, 'li': 106, 'gan': 107, 'arz': 108, 'sah': 109, 'tt': 110, 'bar': 111, 'gd': 112, 'tg': 113,
		'kk': 114, 'pam': 115, 'hsb': 116, 'roa-tara': 117, 'nah': 118, 'mn': 119, 'vls': 120, 'gv': 121, 'mi': 122,
		'am': 123, 'ia': 124, 'co': 125, 'ne': 126, 'fo': 127, 'nds-nl': 128, 'glk': 129, 'mt': 130, 'ang': 131,
		'wuu': 132, 'dv': 133, 'km': 134, 'sco': 135, 'bcl': 136, 'mg': 137, 'my': 138, 'diq': 139, 'tk': 140,
		'szl': 141, 'ug': 142, 'fiu-vro': 143, 'sc': 144, 'rm': 145, 'nrm': 146, 'ps': 147, 'nv': 148, 'hif': 149,
		'bo': 150, 'se': 151, 'sa': 152, 'pnb': 153, 'map-bms': 154, 'lad': 155, 'lij': 156, 'crh': 157, 'fur': 158,
		'kw': 159, 'to': 160, 'pa': 161, 'jbo': 162, 'ba': 163, 'ilo': 164, 'csb': 165, 'wo': 166, 'xal': 167,
		'krc': 168, 'ckb': 169, 'pag': 170, 'ln': 171, 'frp': 172, 'mzn': 173, 'ce': 174, 'nov': 175, 'kv': 176,
		'eml': 177, 'gn': 178, 'ky': 179, 'pdc': 180, 'lo': 181, 'haw': 182, 'mhr': 183, 'dsb': 184, 'stq': 185,
		'tpi': 186, 'arc': 187, 'hak': 188, 'ie': 189, 'so': 190, 'bh': 191, 'ext': 192, 'mwl': 193, 'sd': 194,
		'ig': 195, 'myv': 196, 'ay': 197, 'iu': 198, 'na': 199, 'cu': 200, 'pi': 201, 'kl': 202, 'ty': 203, 'lbe': 204,
		'ab': 205, 'got': 206, 'sm': 207, 'as': 208, 'mo': 209, 'ee': 210, 'zea': 211, 'av': 212, 'ace': 213, 'kg': 214,
		'bm': 215, 'cdo': 216, 'cbk-zam': 217, 'kab': 218, 'om': 219, 'chr': 220, 'pap': 221, 'udm': 222, 'ks': 223,
		'zu': 224, 'rmy': 225, 'cr': 226, 'ch': 227, 'st': 228, 'ik': 229, 'mdf': 230, 'kaa': 231, 'aa': 232, 'fj': 233,
		'srn': 234, 'tet': 235, 'or': 236, 'pnt': 237, 'bug': 238, 'ss': 239, 'ts': 240, 'pcd': 241, 'pih': 242,
		'za': 243, 'sg': 244, 'lg': 245, 'bxr': 246, 'xh': 247, 'ak': 248, 'ha': 249, 'bi': 250, 've': 251, 'tn': 252,
		'ff': 253, 'dz': 254, 'ti': 255, 'ki': 256, 'ny': 257, 'rw': 258, 'chy': 259, 'tw': 260, 'sn': 261, 'tum': 262,
		'ng': 263, 'rn': 264, 'mh': 265, 'ii': 266, 'cho': 267, 'hz': 268, 'kr': 269, 'ho': 270, 'mus': 271, 'kj': 272
	};
	*/
	 */
} );
