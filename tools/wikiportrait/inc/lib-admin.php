<?php
// Library for the admin page
function admin_action( $action ) {
	global $image;

	// Start with a header
	$mail = sprintf( ___( "RESPONSE_HEADER" ), $image['name'] );

	// Add specific text for this action
	switch( $action ) {
		case "good":
			$mail .= sprintf( ___( "RESPONSE_ACTION_CONFIRMATION" ), $image['title'] );
			break;
		case "google":
			$mail .= ___( "RESPONSE_ACTION_GOOGLE" );
			break;
		case "invalid_email":
			$mail .= ___( "RESPONSE_ACTION_INVALID_EMAIL" );
			break;
		case "bad_quality":
			$mail .= ___( "RESPONSE_ACTION_BAD_QUALITY" );
			break;
		case "not_relevant":
			$mail .= ___( "RESPONSE_ACTION_NOT_RELEVANT" );
	}

	// add footer and disclaimer
	$mail .= ___( "RESPONSE_FOOTER" );
	$mail .= ___( "RESPONSE_DISCLAIMER" );

	return $mail;
}
