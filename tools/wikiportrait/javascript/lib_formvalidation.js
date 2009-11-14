// This library is used for form validaton
function FormValidation( form ) {
	// public variables
	var result = { }
	result.validated = false;
	result.error = '';

	function hasRequiredValues( requiredValues ) {
		var emptyValues = [];

		for ( var i in requiredValues ) {
			var requiredValue = requiredValues[i];
			if ( form[requiredValue].value == "" ) {
				// form empty, add to the list
				emptyValues.push( requiredValue );
			} else {
				// remove any classes if available
				$( "#" + requiredValue ).removeClass( "warning" );
			}
		}

		if ( emptyValues.length > 0 ) { // there are empty values
			// first generate the error string
			for ( var i in emptyValues ) {
				// Get the correct name for this field from the <label> assigned to it
				result.error += $( "label[for=" + emptyValues[i] + "]" ).text() + ", ";

				// also light up the specific fields
				$( "#" + emptyValues[i] ).addClass( "warning" );
			}
			// we cut off the last 2 characters from the string to prevent the ', ' at the end of the string
			result.error = messages.EMPTY_VALUE + result.error.substr( 0, ( result.error.length - 2 ) );
			return false;
		} else {
			// no empty values, so this form has all the required values
			return true;
		}
	}

	function hasValidEmail() {
		var emailRegEx =/^[\w][\w\.\-]*@(?:[a-zA-Z0-9\-]+\.)+[a-zA-Z]{2,4}$/
		
		if (form.email.value.search(emailRegEx) == -1) {
			// email not valid, change the error message
			result.error = messages.INVALID_EMAIL;
			$( "#email" ).addClass( "warning" );
			return false;
		} else {
			$( "#email" ).removeClass( "warning" );
			return true;
		}
	}

	function hasGoodEmailProvider( providers ) {
		// searches the email string for any occurences of unwanted e-mail providers (e.g. hotmail, yahoo, gmail)
		for ( var i in providers ) {
			var provider = providers[i];
			if ( form.email.value.toLowerCase().indexOf( provider ) != -1 ) {
				// bad email provider
				result.error = messages.INVALID_EMAIL_PROVIDER + provider;
				$( "#email" ).addClass( "warning" );
				return false;
			}
		}
		$( "#email" ).removeClass( "warning" );
		return true;
	}

	function hasCheckedDisclaimer() {
		if ( form. disclaimerAgree.checked ) {
			$( "#disclaimerAgreeBox" ).removeClass( "warning" );
			return true;
		} else {
			result.error = messages.DISCLAIMER_NOT_AGREED;
			$( "#disclaimerAgreeBox" ).addClass( "warning" );
			return false;
		}
	}

	function hasValidFiletype( filetypes ) {
		for ( var i in filetypes ) {
			var filetype = filetypes[i];
			if ( form.file.value.toLowerCase().indexOf( filetype ) != -1 ) {
				$( "#file" ).removeClass( "warning" );
				return true;
			}
		}

		// no valid filetypes found
		result.error = messages.INVALID_FILETYPE;
		$( "#file" ).addClass( "warning" );
		return false;
	}

	// The whole routine
	if ( hasRequiredValues( ["file", "title", "source", "name", "email"] ) == false ) return result;
	if ( hasValidEmail() == false ) return result;
	if ( hasGoodEmailProvider( ['hotmail.com', 'gmail.com', 'yahoo.com', 'live.com', 'live.nl', 'yahoo.ca', 'msn.com'] ) == false ) return result;
	if ( hasCheckedDisclaimer() == false ) return result;
	if ( hasValidFiletype( ['.png', '.jpg', '.jpeg', '.gif'] ) == false ) return result;

	// all checks passed, upload the photograph
	result.validated = true;
	return result;
}
