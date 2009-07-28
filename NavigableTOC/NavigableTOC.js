/* JavaScript for NavigableTOC extension */

jQuery( document ).ready( function() {
	jQuery( '#wpTextbox1' ).parseOutline();
	jQuery( '#wpTextbox1' )
		.buildOutline( jQuery( '#navigableTOC' ) )
		.updateOutline( jQuery( '#navigableTOC' ) )
		.bind( 'keyup', { 'list': jQuery( '#navigableTOC' ) }, function( event ) {
			jQuery(this).parseOutline();
			jQuery(this).buildOutline( event.data.list );
		} )
		.bind( 'keyup mouseup scrollToPosition', function() {
			jQuery(this).updateOutline( jQuery( '#navigableTOC' ) );
		} );
});
