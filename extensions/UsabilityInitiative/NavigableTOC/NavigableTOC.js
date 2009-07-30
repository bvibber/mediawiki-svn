/* JavaScript for NavigableTOC extension */

js2AddOnloadHook( function() {
	var list = $j( '<div></div>' )
		.attr( 'id', 'edit-toc' )
		.appendTo( $j( 'div#edit-ui-right' ) );
	$j( '#wpTextbox1' )
		.eachAsync( {
			bulk: 0,
			loop: function() {
				$j(this)
					.parseOutline()
					.buildOutline( list )
					.updateOutline( list );
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
