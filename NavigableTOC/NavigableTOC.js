/* JavaScript for NavigableTOC extension */

jQuery( document ).ready( function() {
	$( 'div#edit-ui-right' ).append(
		$( '<div></div>' )
			.attr( 'id', 'edit-toc' )
	);
	jQuery( '#wpTextbox1' ).parseOutline();
	jQuery( '#wpTextbox1' )
		.buildOutline( jQuery( '#edit-toc' ) )
		.updateOutline( jQuery( '#edit-toc' ) )
		.bind( 'keyup', { 'list': jQuery( '#edit-toc' ) }, function( event ) {
			jQuery(this).parseOutline();
			jQuery(this).buildOutline( event.data.list );
		} )
		.bind( 'keyup mouseup scrollToPosition', function() {
			jQuery(this).updateOutline( jQuery( '#edit-toc' ) );
		} );
});
