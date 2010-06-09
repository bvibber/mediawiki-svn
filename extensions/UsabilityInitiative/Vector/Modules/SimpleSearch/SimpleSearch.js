/* JavaScript for SimpleSearch extension */

// Disable mwsuggest.js on searchInput 
if ( wgVectorEnabledModules.simplesearch && skin == 'vector' && typeof os_autoload_inputs !== 'undefined' &&
		os_autoload_forms !== 'undefined' ) {
	os_autoload_inputs = [];
	os_autoload_forms = [];
}

$j(document).ready( function() {
	// Only use this function in conjuction with the Vector skin
	if( !wgVectorEnabledModules.simplesearch || skin != 'vector' ) {
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
					'bottom': 0,
					'padding': '0.25em',
					'color': '#999999',
					'cursor': 'text'
				})
				.css( ( $j( 'body' ).is( '.rtl' ) ? 'right' : 'left' ), 0 )
				.click( function() {
					$j(this).parent().find( 'input#searchInput' ).focus();
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
		.bind( 'paste cut click', function() {
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
	} )
		.bind( 'paste cut click', function() {
			$j( this ).trigger( 'keypress' );
		} );
});
