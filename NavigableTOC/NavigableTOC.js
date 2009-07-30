/* JavaScript for NavigableTOC extension */

js2AddOnloadHook( function() {
	var list = $j( '<div></div>' ).attr( 'id', 'edit-ui-toc' );
	$j( 'div#edit-ui-bottom' ).append( list );
	$j( 'div#edit-ui-toc' ).height( $j( 'div#edit-ui-bottom' ).height() );
	$j( 'textarea#wpTextbox1' )
		.eachAsync( {
			bulk: 0,
			loop: function() {
				$j(this)
					.scrollToCaretPosition( 0 )
					.parseOutline()
					.buildOutline( list )
					.updateOutline( list );
					$j( 'div#edit-ui' ).trigger( 'layout' );
			}
		} )
		.bind( 'keyup encapsulateSelection', { 'list': list },
			function( event ) {
				$j(this).eachAsync( {
					bulk: 0,
					loop: function() {
						$j(this)
							.parseOutline()
							.buildOutline( event.data.list )
							.updateOutline( event.data.list );
					}
				} );
			}
		)
		.bind( 'mouseup scrollToPosition', { 'list': list },
			function( event ) {
				$j(this).eachAsync( {
					bulk: 0,
					loop: function() {
						$j(this).updateOutline( event.data.list )
					}
				} );
			}
		);
});
