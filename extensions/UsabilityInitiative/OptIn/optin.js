/* JavaScript for OptIn extension */

$( document ).ready( function() {
	$( '.optin-other-select' ).hide();
	$( 'select.optin-need-other' ).change( function() {
		if( $(this).val() == 'other' )
			$( '#' + $(this).attr( 'id' ) + '-other' ).show();
		else
			$( '#' + $(this).attr( 'id' ) + '-other' ).hide();
	});
	$( '.optin-other-radios' ).click( function() {
		$(this).prev().prev().click();
	});
});