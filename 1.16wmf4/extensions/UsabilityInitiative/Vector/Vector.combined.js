/* Prototype code to show collapsing left nav options */
/* First draft and will be changing greatly */

$j(document).ready( function() {
	if ( !wgVectorEnabledModules.collapsiblenav ) {
		return true;
	}

	/* Browser Support */

	var map = {
		// Left-to-right languages
		'ltr': {
			// Collapsible Nav is broken in Opera < 9.6 and Konqueror < 4
			'msie': [['>=', 7]],
			'opera': [['>=', 9.6]],
			'konqueror': [['>=', 4.0]],
			'blackberry': false,
			'ipod': false,
			'iphone': false,
			'ps3': false
		},
		// Right-to-left languages
		'rtl': {
			'msie': [['>=', 7]],
			'opera': [['>=', 9.6]],
			'konqueror': [['>=', 4.0]],
			'blackberry': false,
			'ipod': false,
			'iphone': false,
			'ps3': false
		}
	};
	if ( !mw.usability.testBrowser( map ) ) {
		return true;
	}

	/* Bucket Testing */

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

	/* Special Language Portal Handling */

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
			if ( state == 'true' || ( state == null && i < 1 ) || ( state == null && version == 1 && id == 'p-lang' ) ) {
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

	/* Tab Indexing */

	var $headings = $j( '#mw-panel > div.portal:not(.persistent) > h5' );
	// Get the highest tab index
	var tabIndex = mw.usability.getMaxTabIndex() + 1;
	// Fix the search not having a tabindex
	$j( '#searchInput' ).attr( 'tabindex', tabIndex++ );
	// Make it keyboard accessible
	$headings.each( function() {
		$j(this).attr( 'tabindex', tabIndex++ );
	} );
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
$j(document).ready( function() {
	// Check if CollapsibleTabs is enabled
	if ( !wgVectorEnabledModules.collapsibletabs ) {
		return true;
	}
	
	var rtl = $j( 'body' ).is( '.rtl' );
	
	// Overloading the moveToCollapsed function to animate the transition 
	$j.collapsibleTabs.moveToCollapsed = function( ele ) {
		var $moving = $j( ele );
		$j.collapsibleTabs.getSettings( $j( $j.collapsibleTabs.getSettings( $moving ).expandedContainer ) ).shifting = true;
		var data = $j.collapsibleTabs.getSettings( $moving );
		// Remove the element from where it's at and put it in the dropdown menu
		var target = data.collapsedContainer;
		$moving.css( "position", "relative" )
			.css( ( rtl ? 'left' : 'right' ), 0 )
			.animate( { width: '1px' }, "normal", function() {
				$j( this ).hide();
				// add the placeholder
				$j( '<span class="placeholder" style="display:none;"></span>' ).insertAfter( this );
				$j( this ).remove().prependTo( target ).data( 'collapsibleTabsSettings', data );
				$j( this ).attr( 'style', 'display:list-item;' );
				$j.collapsibleTabs.getSettings( $j( $j.collapsibleTabs.getSettings( $j( ele ) ).expandedContainer ) )
					.shifting = false;
				$j.collapsibleTabs.handleResize();
			} );
	};
	
	// Overloading the moveToExpanded function to animate the transition
	$j.collapsibleTabs.moveToExpanded = function( ele ) {
		var $moving = $j( ele );
		$j.collapsibleTabs.getSettings( $j( $j.collapsibleTabs.getSettings( $moving ).expandedContainer ) ).shifting = true;
		var data = $j.collapsibleTabs.getSettings( $moving );
		// grab the next appearing placeholder so we can use it for replacing
		var $target = $j( data.expandedContainer ).find( 'span.placeholder:first' );
		var expandedWidth = data.expandedWidth;
		$moving.css( "position", "relative" ).css( ( rtl ? 'right' : 'left' ), 0 ).css( 'width', '1px' );
		$target.replaceWith( $moving.remove().css( 'width', '1px' ).data( 'collapsibleTabsSettings', data )
			.animate( { width: expandedWidth+"px" }, "normal", function() {
				$j( this ).attr( 'style', 'display:block;' );
				$j.collapsibleTabs.getSettings( $j( $j.collapsibleTabs.getSettings( $moving ).expandedContainer ) )
					.shifting = false;
				$j.collapsibleTabs.handleResize();
			} ) );
	};
	
	// Bind callback functions to animate our drop down menu in and out
	// and then call the collapsibleTabs function on the menu 
	$j( '#p-views ul' ).bind( "beforeTabCollapse", function() {
		if( $j( '#p-cactions' ).css( 'display' ) == 'none' )
		$j( "#p-cactions" ).addClass( "filledPortlet" ).removeClass( "emptyPortlet" )
			.find( 'h5' ).css( 'width','1px' ).animate( { 'width':'26px' }, 390 );
	}).bind( "beforeTabExpand", function() {
		if( $j( '#p-cactions li' ).length == 1 )
		$j( "#p-cactions h5" ).animate( { 'width':'1px' }, 370, function() {
			$j( this ).attr( 'style', '' ).parent().addClass( "emptyPortlet" ).removeClass( "filledPortlet" );
		});
	}).collapsibleTabs( {
		expandCondition: function( eleWidth ) {
			if( rtl ){
				return ( $j( '#right-navigation' ).position().left + $j( '#right-navigation' ).width() + 1 )
					< ( $j( '#left-navigation' ).position().left - eleWidth );
			} else {
				return ( $j( '#left-navigation' ).position().left + $j( '#left-navigation' ).width() + 1 )
					< ( $j( '#right-navigation' ).position().left - eleWidth );
			}
		},
		collapseCondition: function() {
			if( rtl ) {
				return ( $j( '#right-navigation' ).position().left + $j( '#right-navigation' ).width() )
					> $j( '#left-navigation' ).position().left;
			} else {
				return ( $j( '#left-navigation' ).position().left + $j( '#left-navigation' ).width() )
					> $j( '#right-navigation' ).position().left;
			}
		}
	} );
} );
/* JavaScript for EditWarning extension */

$j(document).ready( function() {
	// Check if EditWarning is enabled and if we need it
	if ( !wgVectorEnabledModules.editwarning || $j( '#wpTextbox1' ).size() == 0 ) {
		return true;
	}
	// Get the original values of some form elements
	$j( '#wpTextbox1, #wpSummary' ).each( function() {
		$j(this).data( 'origtext', $j(this).val() );
	});
	// Attach our own handler for onbeforeunload which respects the current one
	var fallbackWindowOnBeforeUnload = window.onbeforeunload;
	var ourWindowOnBeforeUnload = function() {
		var fallbackResult = undefined;
		var retval = undefined;
		// Check if someone already set on onbeforeunload hook
		if ( fallbackWindowOnBeforeUnload ) {
			// Get the result of their onbeforeunload hook
			fallbackResult = fallbackWindowOnBeforeUnload();
		}
		// Check if their onbeforeunload hook returned something
		if ( fallbackResult !== undefined ) {
			// Exit here, returning their message
			retval = fallbackResult;
		} else {
			// Check if the current values of some form elements are the same as
			// the original values
			if (
				wgAction == 'submit' ||
				$j( '#wpTextbox1' ).data( 'origtext' ) != $j( '#wpTextbox1' ).val() ||
				$j( '#wpSummary' ).data( 'origtext' ) != $j( '#wpSummary' ).val()
			) {
				// Return our message
				retval = mw.usability.getMsg( 'vector-editwarning-warning' );
			}
		}
		
		// Unset the onbeforeunload handler so we don't break page caching in Firefox
		window.onbeforeunload = null;
		if ( retval !== undefined ) {
			return retval;
		}
	};
	var pageShowHandler = function() {
		// Re-add onbeforeunload handler
		window.onbeforeunload = ourWindowOnBeforeUnload;
	};
	pageShowHandler();
	if ( window.addEventListener ) {
		window.addEventListener('pageshow', pageShowHandler, false);
	} else if ( window.attachEvent ) {
		window.attachEvent( 'pageshow', pageShowHandler );
	}
	
	// Add form submission handler
	$j( 'form' ).submit( function() {
		// Restore whatever previous onbeforeload hook existed
		window.onbeforeunload = fallbackWindowOnBeforeUnload;
	});
});
//Global storage of fallback for onbeforeunload hook
var fallbackWindowOnBeforeUnload = null;
/* JavaScript for SimpleSearch extension */

// Disable mwsuggest.js on searchInput 
if ( wgVectorEnabledModules.simplesearch && skin == 'vector' && typeof os_autoload_inputs !== 'undefined' &&
		os_autoload_forms !== 'undefined' ) {
	os_autoload_inputs = [];
	os_autoload_forms = [];
}

$j(document).ready( function() {
	// Only use this function in conjuction with the Vector skin
	if( !wgVectorEnabledModules.simplesearch || wgVectorPreferences.simplesearch.disablesuggest || skin != 'vector' ) {
		return true;
	}
	var mod = {
		'browsers': {
			// Left-to-right languages
			'ltr': {
				// SimpleSearch is broken in Opera < 9.6
				'opera': [['>=', 9.6]],
				'blackberry': false,
				'ipod': false,
				'iphone': false
			},
			// Right-to-left languages
			'rtl': {
				'opera': [['>=', 9.6]],
				'blackberry': false,
				'ipod': false,
				'iphone': false
			}
		}
	};
	if ( !$j.wikiEditor.isSupported( mod ) ) {
		return true;
	}
	
	// Add form submission handler
	$j( 'div#simpleSearch > input#searchInput' )
		.each( function() {
			$j( '<label />' )
				.text( mw.usability.getMsg( 'vector-simplesearch-search' ) )
				.css({
					'display': 'none',
					'position' : 'absolute',
					'color': '#999999',
					'cursor': 'text',
					'margin': '0 4px',
					'top': '6px',
					'line-height': '13px'
				})
				.css( ( $j( 'body' ).is( '.rtl' ) ? 'right' : 'left' ), 0 )
				.mousedown( function() {
					$j(this).parent().find( 'input#searchInput' ).focus();
					return false;
				})
				.appendTo( $j(this).parent() );
			if ( $j(this).val() == '' ) {
				$j(this).parent().find( 'label' ).fadeIn( 100 );
			}
		})
		.bind( 'keypress', function() {
			// just in case the text field was focus before our handler was bound to it
			if ( $j(this).parent().find( 'label:visible' ).size() > 0 )
				$j(this).parent().find( 'label' ).fadeOut( 100 );
		})
		.focus( function() {
			$j(this).parent().find( 'label' ).fadeOut( 100 );
		})
		.blur( function() {
			if ( $j(this).val() == '' ) {
				$j(this).parent().find( 'label' ).fadeIn( 100 );
			}
		});
		// listen for dragend events in order to clear the label from the search field if
		// text is dragged into it. Only works for mozilla
		$j( document ).bind( 'dragend', function( event ) {
			if ( $j( 'div#simpleSearch > label:visible' ).size() > 0 
				&& $j( 'div#simpleSearch > input#searchInput' ).val().length > 0 )
					$j( 'div#simpleSearch > label' ).fadeOut( 100 );
		} );
	$j( '#searchInput, #searchInput2, #powerSearchText, #searchText' ).suggestions( {
		fetch: function( query ) {
			var $this = $j(this);
			var request = $j.ajax( {
				url: wgScriptPath + '/api.php',
				data: {
					'action': 'opensearch',
					'search': query,
					'namespace': 0,
					'suggest': ''
				},
				dataType: 'json',
				success: function( data ) {
					$this.suggestions( 'suggestions', data[1] );
				}
			});
			$j(this).data( 'request', request );
		},
		cancel: function () {
			var request = $j(this).data( 'request' );
			// If the delay setting has caused the fetch to have not even happend yet, the request object will
			// have never been set
			if ( request && typeof request.abort == 'function' ) {
				request.abort();
				$j(this).removeData( 'request' );
			}
		},
		result: {
			select: function( $textbox ) {
				$textbox.closest( 'form' ).submit();
			}
		},
		delay: 120,
		positionFromLeft: $j( 'body' ).is( '.rtl' ),
		highlightInput: true
	} )
		.bind( 'paste cut', function( e ) {
			// make sure paste and cut events from the mouse trigger the keypress handler and cause the suggestions to update
			$j( this ).trigger( 'keypress' );
		} );
	$j( '#searchInput' ).suggestions( {
		result: {
			select: function( $textbox ) {
				$textbox.closest( 'form' ).submit();
			}
		},
		special: {
			render: function( query ) {
				if ( $j(this).children().size() == 0  ) {
					$j(this).show()
					$label = $j( '<div />' )
						.addClass( 'special-label' )
						.text( mw.usability.getMsg( 'vector-simplesearch-containing' ) )
						.appendTo( $j(this) );
					$query = $j( '<div />' )
						.addClass( 'special-query' )
						.text( query )
						.appendTo( $j(this) );
					$query.autoEllipsis();
				} else {
					$j(this).find( '.special-query' )
						.empty()
						.text( query )
						.autoEllipsis();
				}
			},
			select: function( $textbox ) {
				$textbox.closest( 'form' ).append(
					$j( '<input />' ).attr( { 'type': 'hidden', 'name': 'fulltext', 'value': 1 } )
				);
				$textbox.closest( 'form' ).submit();
			}
		},
		$region: $j( '#simpleSearch' )
	} );
});
