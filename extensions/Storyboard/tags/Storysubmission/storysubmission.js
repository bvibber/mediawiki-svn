/**
 * JavaScript for <storysubmission> tags.
 * 
 * @author Jeroen De Dauw
 * @ingroup Storyboard
 */

function stbValidateStory( textarea, lowerLimit, upperLimit, infodiv, submissionButton ) {
	var button = document.getElementById( submissionButton );
	button.disabled = !stbLimitChars( textarea, lowerLimit, upperLimit, infodiv );
}

function stbLimitChars(textarea, lowerLimit, upperLimit, infodiv) {
	var text = textarea.value; 
	var textlength = text.length;
	var info = document.getElementById( infodiv );
	
	if(textlength > upperLimit) {
		info.innerHTML = -( upperLimit - textlength ) + ' characters to many!'; // TODO: i18n
		return false;
	} else if (textlength < lowerLimit) {
		info.innerHTML = '('+ ( lowerLimit - textlength ) + ' more characters needed)'; // TODO: i18n
		return false;
	} else {
		info.innerHTML = '(' + ( upperLimit - textlength ) + ' characters left)'; // TODO: i18n
		return true;
	}
}

function stbValidateSubmission( termsCheckbox ) {
	var agreementValid = document.getElementById( termsCheckbox ).checked;
	if (!agreementValid) {
		alert( 'You need to agree to the publication of your story to submit it.' ); // TODO: i18n
	}
	return agreementValid;
}

addOnloadHook( function() { 
	document.getElementById( 'storysubmission-button' ).disabled = true;
} );