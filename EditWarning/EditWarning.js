/* JavaScript for EditWarning extension */

$( document ).ready( function() {
	if( skin != 'vector' )
		return;
	
	$( '#wpTextbox1, #wpSummary' ).each( function() {
		$(this).data( 'origtext', $(this).val() );
	});
	if( !( 'onbeforeunload' in window ) )
		window.onbeforeunload = function() {
			if( $( '#wpTextbox1' ).data( 'origtext' ) != $( '#wpTextbox1' ).val() ||
					$( '#wpSummary' ).data( 'origtext' ) != $( '#wpSummary' ).val() )
				return gM( 'editwarning-warning' );
	};
	$( 'form' ).submit( function() {
		window.onbeforeunload = function() {};
	});
});
