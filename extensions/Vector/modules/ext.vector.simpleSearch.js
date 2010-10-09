/* JavaScript for SimpleSearch extension */

// XXX: Disable mwsuggest.js's effect on searchInput 
if ( typeof os_autoload_inputs !== 'undefined' && os_autoload_forms !== 'undefined' ) {
	os_autoload_inputs = [];
	os_autoload_forms = [];
}

$( document ).ready( function() {
	
	// Compatibility map
	var map = {
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
	if ( !$.client.test( map ) ) {
		return true;
	}
	
	// Placeholder text for SimpleSearch box
	$( 'div#simpleSearch > input#searchInput' ).placeholder( mediaWiki.msg.get( 'vector-simplesearch-search' ) );
	
	// General suggestions functionality for all search boxes
	$( '#searchInput, #searchInput2, #powerSearchText, #searchText' )
		.suggestions( {
			fetch: function( query ) {
				var $this = $(this);
				var request = $.ajax( {
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
				$(this).data( 'request', request );
			},
			cancel: function () {
				var request = $(this).data( 'request' );
				// If the delay setting has caused the fetch to have not even happend yet, the request object will
				// have never been set
				if ( request && typeof request.abort == 'function' ) {
					request.abort();
					$(this).removeData( 'request' );
				}
			},
			result: {
				select: function( $input ) {
					$input.closest( 'form' ).submit();
				}
			},
			delay: 120,
			positionFromLeft: $( 'body' ).is( '.rtl' ),
			highlightInput: true
		} )
		.bind( 'paste cut', function( e ) {
			// make sure paste and cut events from the mouse trigger the keypress handler and cause the suggestions to update
			$( this ).trigger( 'keypress' );
		} );
	// Special suggestions functionality for skin-provided search box
	$( '#searchInput' ).suggestions( {
		result: {
			select: function( $input ) {
				$input.closest( 'form' ).submit();
			}
		},
		special: {
			render: function( query ) {
				if ( $(this).children().size() == 0  ) {
					$(this).show()
					$label = $( '<div />' )
						.addClass( 'special-label' )
						.text( mediaWiki.msg.get( 'vector-simplesearch-containing' ) )
						.appendTo( $(this) );
					$query = $( '<div />' )
						.addClass( 'special-query' )
						.text( query )
						.appendTo( $(this) );
					$query.autoEllipsis();
				} else {
					$(this).find( '.special-query' )
						.empty()
						.text( query )
						.autoEllipsis();
				}
			},
			select: function( $input ) {
				$input.closest( 'form' ).append(
					$( '<input />' ).attr( { 'type': 'hidden', 'name': 'fulltext', 'value': 1 } )
				);
				$input.closest( 'form' ).submit();
			}
		},
		$region: $( '#simpleSearch' )
	} );
});
