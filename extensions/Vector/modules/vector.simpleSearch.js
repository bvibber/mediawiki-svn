/* JavaScript for SimpleSearch extension */

// Disable mwsuggest.js's effect on searchInput 
if ( typeof os_autoload_inputs !== 'undefined' && os_autoload_forms !== 'undefined' ) {
	os_autoload_inputs = [];
	os_autoload_forms = [];
}

$( document ).ready( function() {
	
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
	if ( !$.wikiEditor.isSupported( mod ) ) {
		return true;
	}
	
	// Placeholder text
	// if the placeholder attribute is supported, use it
	if ( 'placeholder' in document.createElement( 'input' ) ) {
		$( 'div#simpleSearch > input#searchInput' )
			.attr( 'placeholder', mw.usability.getMsg( 'vector-simplesearch-search' ) );
	} else {
		$( 'div#simpleSearch > input#searchInput' )
			.each( function() {
				var $input = $( this );
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
	$( '#searchInput, #searchInput2, #powerSearchText, #searchText' ).suggestions( {
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
			select: function( $textbox ) {
				$textbox.closest( 'form' ).submit();
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
	$( '#searchInput' ).suggestions( {
		result: {
			select: function( $textbox ) {
				$textbox.closest( 'form' ).submit();
			}
		},
		special: {
			render: function( query ) {
				if ( $(this).children().size() == 0  ) {
					$(this).show()
					$label = $( '<div />' )
						.addClass( 'special-label' )
						.text( mw.usability.getMsg( 'vector-simplesearch-containing' ) )
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
			select: function( $textbox ) {
				$textbox.closest( 'form' ).append(
					$( '<input />' ).attr( { 'type': 'hidden', 'name': 'fulltext', 'value': 1 } )
				);
				$textbox.closest( 'form' ).submit();
			}
		},
		$region: $( '#simpleSearch' )
	} );
});
