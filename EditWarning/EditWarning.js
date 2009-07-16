/* JavaScript for EditWarning extension */

$( document ).ready( function() {
	// Only use this function in conjuction with the Vector skin
	if( skin != 'vector' )
		return;
	
	// Get the original values of some form elements
	$( '#wpTextbox1, #wpSummary' ).each( function() {
		$(this).data( 'origtext', $(this).val() );
	});
	
	fallbackWindowOnBeforeUnload = window.onbeforeunload;
	window.onbeforeunload = function() {
		var fallbackResult = null;
		// Check if someone already set on onbeforunload hook
		if ( fallbackWindowOnBeforeUnload ) {
			// Get the result of their onbeforeunload hook
			fallbackResult = fallbackWindowOnBeforeUnload();
		}
		// Check if their onbeforeunload hook returned something
		if ( fallbackResult !== null ) {
			// Exit here, returning their message
			return fallbackResult;
		}
		// Check if the current values of some form elements are the same as
		// the original values
		if(
			$( '#wpTextbox1' ).data( 'origtext' ) != $( '#wpTextbox1' ).val() ||
			$( '#wpSummary' ).data( 'origtext' ) != $( '#wpSummary' ).val()
		) {
			// Return our message
			return gM( 'editwarning-warning' );
		}
	}
	// Add form submission handler
	$( 'form' ).submit( function() {
		// Restore whatever previous onbeforeload hook existed
		window.onbeforeunload = fallbackWindowOnBeforeUnload;
	});
});
//Global storage of fallback for onbeforeunload hook
var fallbackWindowOnBeforeUnload = null;