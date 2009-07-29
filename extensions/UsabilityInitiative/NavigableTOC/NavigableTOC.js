/* JavaScript for NavigableTOC extension */

jQuery( document ).ready( function() {
	var list = $( '<div></div>' )
		.attr( 'id', 'edit-toc' )
		.appendTo( $( 'div#edit-ui-right' ) );
	$( '#wpTextbox1' )
		.eachAsync( {
			bulk: 0,
			loop: function() {
				$(this)
					.parseOutline()
					.buildOutline( list )
					.updateOutline( list );
			}
		} )
		.bind( 'keyup encapsulateSelection', { 'list': list },
			function( event ) {
				$(this).eachAsync( {
					bulk: 0,
					loop: function() {
						$(this)
							.parseOutline()
							.buildOutline( event.data.list )
							.updateOutline( event.data.list );
					}
				} );
			}
		)
		.bind( 'mouseup scrollToPosition', { 'list': list },
			function( event ) {
				$(this).eachAsync( {
					bulk: 0,
					loop: function() {
						$(this).updateOutline( event.data.list )
					}
				} );
			}
		);
});
