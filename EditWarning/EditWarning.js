/* JavaScript for EditWarning extension */

$( document ).ready( function() {
	// Checks if the skin is not vector
	if( skin != 'vector' )
		// Exits
		return;
	// Gets the original values of some form elements
	$( '#wpTextbox1, #wpSummary' ).each( function() {
		$(this).data( 'origtext', $(this).val() );
	});
	
	fallbackWindowOnBeforeUnload = window.onbeforeunload;
	window.onbeforeunload = function() {
		var fallbackResult = null;
		// Checks if someone already set on onbeforunload hook
		if ( fallbackWindowOnBeforeUnload ) {
			// Gets the result of their onbeforeunload hook
			fallbackResult = fallbackWindowOnBeforeUnload();
		}
		// Checks if their onbeforeunload hook returned something
		if ( fallbackResult !== null ) {
			// Exits here, returning their message
			return fallbackResult;
		}
		// Checks if the current values of some form elements are the same as
		// the original values
		if(
			$( '#wpTextbox1' ).data( 'origtext' ) != $( '#wpTextbox1' ).val() ||
			$( '#wpSummary' ).data( 'origtext' ) != $( '#wpSummary' ).val()
		) {
			// Returns our message
			return gM( 'editwarning-warning' );
		}
	}
	// Adds form submission handler
	$( 'form' ).submit( function() {
		// Restores whatever previous onbeforeload hook existed
		window.onbeforeunload = fallbackWindowOnBeforeUnload;
	});
});
//Global storage of fallback for onbeforeunload hook
var fallbackWindowOnBeforeUnload = null;