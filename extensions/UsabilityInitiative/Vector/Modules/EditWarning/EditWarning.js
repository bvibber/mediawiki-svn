/* JavaScript for EditWarning extension */

$j(document).ready( function() {
	// Check if EditWarning is enabled and if we need it
	if ( !wgVectorEnabledModules.editwarning || $j( '#wpTextbox1' ).size() == 0 ) {
		return true;
	}
	// Get the original values of some form elements
	$j( '#wpTextbox1, #wpSummary' ).each( function() {
		$j(this).data( 'origtext', $j(this).val() );
	});
	// Attach our own handler for onbeforeunload which respects the current one
	var fallbackWindowOnBeforeUnload = window.onbeforeunload;
	var ourWindowOnBeforeUnload = function() {
		var fallbackResult = undefined;
		var retval = undefined;
		// Check if someone already set on onbeforeunload hook
		if ( fallbackWindowOnBeforeUnload ) {
			// Get the result of their onbeforeunload hook
			fallbackResult = fallbackWindowOnBeforeUnload();
		}
		// Check if their onbeforeunload hook returned something
		if ( fallbackResult !== undefined ) {
			// Exit here, returning their message
			retval = fallbackResult;
		} else {
			// Check if the current values of some form elements are the same as
			// the original values
			if (
				wgAction == 'submit' ||
				$j( '#wpTextbox1' ).data( 'origtext' ) != $j( '#wpTextbox1' ).val() ||
				$j( '#wpSummary' ).data( 'origtext' ) != $j( '#wpSummary' ).val()
			) {
				// Return our message
				retval = mw.usability.getMsg( 'vector-editwarning-warning' );
			}
		}
		
		// Unset the onbeforeunload handler so we don't break page caching in Firefox
		window.onbeforeunload = null;
		if ( retval !== undefined ) {
			return retval;
		}
	};
	var pageShowHandler = function() {
		// Re-add onbeforeunload handler
		window.onbeforeunload = ourWindowOnBeforeUnload;
	};
	pageShowHandler();
	if ( window.addEventListener ) {
		window.addEventListener('pageshow', pageShowHandler, false);
	} else if ( window.attachEvent ) {
		window.attachEvent( 'pageshow', pageShowHandler );
	}
	
	// Add form submission handler
	$j( 'form' ).submit( function() {
		// Restore whatever previous onbeforeload hook existed
		window.onbeforeunload = fallbackWindowOnBeforeUnload;
	});
});
//Global storage of fallback for onbeforeunload hook
var fallbackWindowOnBeforeUnload = null;
