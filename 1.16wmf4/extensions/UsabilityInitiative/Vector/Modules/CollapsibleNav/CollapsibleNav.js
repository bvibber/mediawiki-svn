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
				'iphone': false,
				'ps3': false
			},
			// Right-to-left languages
			'rtl': {
				'opera': [['>=', 9.6]],
				'konqueror': [['>=', 4.0]],
				'blackberry': false,
				'ipod': false,
				'iphone': false,
				'ps3': false
			}
		}
	};
	if ( !$j.wikiEditor.isSupported( mod ) ) {
		return true;
	}
	// Fallback to old version
	var version = 1;
	// Allow new version override
	if ( wgCollapsibleNavForceNewVersion ) {
		version = 2;
	} else {
		// Make bucket testing optional
		if ( wgCollapsibleNavBucketTest ) {
			// This is be determined randomly, and then stored in a cookie
			version = $j.cookie( 'vector-nav-pref-version' );
			// If the cookie didn't exist, or the value is out of range, generate a new one and save it
			if ( version == null ) {
				// 50% of the people will get the new version
				version = Math.round( Math.random() + 1 );
				$j.cookie( 'vector-nav-pref-version', version, { 'expires': 30, 'path': '/' } );
			}
		}
	}
	// Language portal splitting feature (if it's turned on)
	if ( version == 2 ) {
		// How many links to show in the primary languages portal
		var limit = 5;
		// How many links there must be in the secondary portal to justify having a secondary portal
		var threshold = 3;
		// Make the interwiki language links list a secondary list, and create a new list before it as primary list
		$j( '#p-lang ul' ).addClass( 'secondary' ).before( '<ul class="primary"></ul>' );
		// This is a list of languages in order of Wikipedia project size. This is the lowest fallback for choosing
		// which links to show in the primary list. Ideally the browser's accept-language headers should steer this
		// list, and we should fallback on a site configured (MediaWiki:Common.js) list of prefered languages.
		var languages = [
			'en', 'fr', 'de', 'es', 'pt', 'it', 'ru', 'ja', 'nl', 'pl', 'zh', 'sv', 'ar', 'tr', 'uk', 'fi', 'no', 'ca',
			'ro', 'hu', 'ksh', 'id',  'he', 'cs', 'vi', 'ko', 'sr', 'fa', 'da', 'eo', 'sk', 'th', 'lt', 'vo', 'bg',
			'sl', 'hr', 'hi', 'et', 'mk', 'simple', 'new', 'ms', 'nn', 'gl', 'el', 'eu', 'ka', 'tl', 'bn', 'lv', 'ml',
			'bs', 'te', 'la', 'az', 'sh', 'war', 'br', 'is', 'mr', 'be-x-old', 'sq', 'cy', 'lb', 'ta', 'zh-classical',
			'an', 'jv', 'ht', 'oc', 'bpy', 'ceb', 'ur', 'zh-yue', 'pms', 'scn', 'be', 'roa-rup', 'qu', 'af', 'sw',
			'nds', 'fy', 'lmo', 'wa', 'ku', 'hy', 'su', 'yi', 'io', 'os', 'ga', 'ast', 'nap', 'vec', 'gu', 'cv',
			'bat-smg', 'kn', 'uz', 'zh-min-nan', 'si', 'als', 'yo', 'li', 'gan', 'arz', 'sah', 'tt', 'bar', 'gd', 'tg',
			'kk', 'pam', 'hsb', 'roa-tara', 'nah', 'mn', 'vls', 'gv', 'mi', 'am', 'ia', 'co', 'ne', 'fo', 'nds-nl',
			'glk', 'mt', 'ang', 'wuu', 'dv', 'km', 'sco', 'bcl', 'mg', 'my', 'diq', 'tk', 'szl', 'ug', 'fiu-vro', 'sc',
			'rm', 'nrm', 'ps', 'nv', 'hif', 'bo', 'se', 'sa', 'pnb', 'map-bms', 'lad', 'lij', 'crh', 'fur', 'kw', 'to',
			'pa', 'jbo', 'ba', 'ilo', 'csb', 'wo', 'xal', 'krc', 'ckb', 'pag', 'ln', 'frp', 'mzn', 'ce', 'nov', 'kv',
			'eml', 'gn', 'ky', 'pdc', 'lo', 'haw', 'mhr', 'dsb', 'stq', 'tpi', 'arc', 'hak', 'ie', 'so', 'bh', 'ext',
			'mwl', 'sd', 'ig', 'myv', 'ay', 'iu', 'na', 'cu', 'pi', 'kl', 'ty', 'lbe', 'ab', 'got', 'sm', 'as', 'mo',
			'ee', 'zea', 'av', 'ace', 'kg', 'bm', 'cdo', 'cbk-zam', 'kab', 'om', 'chr', 'pap', 'udm', 'ks', 'zu', 'rmy',
			'cr', 'ch', 'st', 'ik', 'mdf', 'kaa', 'aa', 'fj', 'srn', 'tet', 'or', 'pnt', 'bug', 'ss', 'ts', 'pcd',
			'pih', 'za', 'sg', 'lg', 'bxr', 'xh', 'ak', 'ha', 'bi', 've', 'tn', 'ff', 'dz', 'ti', 'ki', 'ny', 'rw',
			'chy', 'tw', 'sn', 'tum', 'ng', 'rn', 'mh', 'ii', 'cho', 'hz', 'kr', 'ho', 'mus', 'kj'
		];
		// If the user has an Accept-Language cookie, use it. Otherwise, set it asynchronously but keep the default behavior
		// for this page view.
		var acceptLangCookie = $j.cookie( 'accept-language' );
		if ( acceptLangCookie != null ) {
			// Put the user's accepted languages before the list ordered by wiki size
			if ( acceptLangCookie != '' ) {
				languages = acceptLangCookie.split( ',' ).concat( languages );
			}
		} else {
			$j.getJSON( wgScriptPath + '/api.php?action=query&meta=userinfo&uiprop=acceptlang&format=json', function( data ) {
				var langs = [];
				if ( typeof data.query != 'undefined' && typeof data.query.userinfo != 'undefined' &&
						typeof data.query.userinfo.acceptlang != 'undefined' ) {
					for ( var j = 0; j < data.query.userinfo.acceptlang.length; j++ ) {
						if ( data.query.userinfo.acceptlang[j].q != 0 ) {
							langs.push( data.query.userinfo.acceptlang[j]['*'] );
						}
					}
				}
				$j.cookie( 'accept-language', langs.join( ',' ), { 'path': '/', 'expires': 30 } );
			} );
		}
		// Shortcuts to the two lists
		var $primary = $j( '#p-lang ul.primary' );
		var $secondary = $j( '#p-lang ul.secondary' );
		// Adjust the limit based on the threshold
		if ( $secondary.children().length < limit + threshold ) {
			limit += threshold;
		}
		// Move up to 5 of the links into the primary list, based on the priorities set forth in the languages list
		var count = 0;
		for ( var i = 0; i < languages.length; i++ ) {
			var $link = $secondary.find( '.interwiki-' + languages[i] );
			if ( $link.length ) {
				if ( count++ < limit ) {
					$link.appendTo( $primary );
				} else {
					break;
				}
			}
		}
		// If there's still links in the secondary list and we havn't filled the primary list to it's limit yet, move links
		// into the primary list in order of appearance
		if ( count < limit ) {
			$secondary.children().each( function() {
				if ( count++ < limit ) {
					$j(this).appendTo( $primary );
				} else {
					return false;
				}
			} );
		}
		// Hide the more portal if it's now empty, otherwise make the list into it's very own portal
		if ( $secondary.children().length == 0 ) {
			$secondary.remove();
		} else {
			$j( '#p-lang' ).after( '<div id="p-lang-more" class="portal"><h5></h5><div class="body"></div></div>' );
			$j( '#p-lang-more h5' ).text( mw.usability.getMsg( 'vector-collapsiblenav-more' ) );
			$secondary.appendTo( $j( '#p-lang-more div.body' ) );
		}
		// Always show the primary interwiki language portal
		$j( '#p-lang' ).addClass( 'persistent' );
	}
	
	/* General Portal Modification */
	
	// Always show the first portal
	$j( '#mw-panel > div.portal:first' ).addClass( 'first persistent' );
	// Apply a class to the entire panel to activate styles
	$j( '#mw-panel' ).addClass( 'collapsible-nav' );
	// Use cookie data to restore preferences of what to show and hide
	$j( '#mw-panel > div.portal:not(.persistent)' )
		.each( function( i ) {
			var id = $j(this).attr( 'id' );
			var state = $j.cookie( 'vector-nav-' + id );
			// In the case that we are not showing the new version, let's show the languages by default
			if ( state == 'true' || ( state == null && i < 1 ) || version == 1 && id == 'p-lang' ) {
				$j(this)
					.addClass( 'expanded' )
					.find( 'div.body' )
					.show();
			} else {
				$j(this).addClass( 'collapsed' );
			}
			// Re-save cookie
			if ( state != null ) {
				$j.cookie( 'vector-nav-' + $j(this).attr( 'id' ), state, { 'expires': 30, 'path': '/' } );
			}
		} );
	// Use the same function for all navigation headings - don't repeat yourself
	function toggle( $element ) {
		$j.cookie(
			'vector-nav-' + $element.parent().attr( 'id' ),
			$element.parent().is( '.collapsed' ),
			{ 'expires': 30, 'path': '/' }
		);
		$element
			.parent()
			.toggleClass( 'expanded' )
			.toggleClass( 'collapsed' )
			.find( 'div.body' )
			.slideToggle( 'fast' );
	}
	var $headings = $j( '#mw-panel > div.portal:not(.persistent) > h5' );
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
} );
