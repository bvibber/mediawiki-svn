/* JavaScript for EditWarning extension */

mw.addOnloadHook( function() {
	// Check if EditWarning is enabled
	if ( !wgVectorEnabledModules.editwarning ) {
		return true;
	}
	// Get the original values of some form elements
	$j( '#wpTextbox1, #wpSummary' ).each( function() {
		$j(this).data( 'origtext', $j(this).val() );
	});
	// Attach our own handler for onbeforeunload which respects the current one
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
			wgAction == 'submit' ||
			$j( '#wpTextbox1' ).data( 'origtext' ) != $j( '#wpTextbox1' ).val() ||
			$j( '#wpSummary' ).data( 'origtext' ) != $j( '#wpSummary' ).val()
		) {
			// Return our message
			return gM( 'vector-editwarning-warning' );
		}
	}
	// Add form submission handler
	$j( 'form' ).submit( function() {
		// Restore whatever previous onbeforeload hook existed
		window.onbeforeunload = fallbackWindowOnBeforeUnload;
	});
});
//Global storage of fallback for onbeforeunload hook
var fallbackWindowOnBeforeUnload = null;
