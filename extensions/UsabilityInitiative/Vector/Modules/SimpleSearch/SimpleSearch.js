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
				'docomo': false,
				'blackberry': false,
				'ipod': false,
				'iphone': false
			},
			// Right-to-left languages
			'rtl': {
				'opera': [['>=', 9.6]],
				'docomo': false,
				'blackberry': false,
				'ipod': false,
				'iphone': false
			}
		}
	};
	if ( !$j.wikiEditor.isSupported( mod ) ) {
		return true;
	}
	
	// Placeholder text
	// if the placeholder attribute is supported, use it
	if ( 'placeholder' in document.createElement( 'input' ) ) {
		$j( 'div#simpleSearch > input#searchInput' )
			.attr( 'placeholder', mw.usability.getMsg( 'vector-simplesearch-search' ) );
	} else {
		$j( 'div#simpleSearch > input#searchInput' )
			.each( function() {
				var $input = $j( this );
				$input
					.bind( 'blur', function() {
						if ( $input.val().length == 0 ) {
							$input
								.val( mw.usability.getMsg( 'vector-simplesearch-search' ) )
								.addClass( 'placeholder' );
							}
						} )
					.bind( 'focus', function() {
						if ( $input.hasClass( 'placeholder' ) ) {
							$input.val( '' ).removeClass( 'placeholder' );
						}
					} )
					.parents( 'form' )
						.bind( 'submit', function() {
							$input.trigger( 'focus' );
						} );
				if ( $input.val() == '' ) {
					$input.trigger( 'blur' );
				}
			} );
	}
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
