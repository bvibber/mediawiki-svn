/* JavaScript for OptIn extension */

$( document ).ready( function() {
	$( '.optin-other-select' ).hide();
	$( 'select.optin-need-other' ).change( function() {
		if( $(this).val() == 'other' )
			$( '#' + $(this).attr( 'id' ) + '-other' ).show();
		else
			$( '#' + $(this).attr( 'id' ) + '-other' ).hide();
	});
	$( '.optin-other-radios, .optin-other-checks' ).click( function() {
		$(this).prev().prev().click();
	});
	// Detect screen resolution
	if ( screen.width && screen.height ) {
		$( '.optin-resolution-x' ).val( screen.width );
		$( '.optin-resolution-y' ).val( screen.height );
		// Hide the fields?
	}
	// Detect browser and version
	// BEWARE: this depends on the order of browsers in $wgOptInSurvey
	var browserIndex = -1;
	switch ( $.browser.name ) {
		case 'msie':
			browserIndex = $.browser.versionNumber - 5;
		break;
		case 'firefox':
			browserIndex = $.browser.versionNumber + 3;
		break;
		case 'chrome':
			browserIndex = $.browser.versionNumber + 7;
		break;
		case 'safari':
			browserIndex = $.browser.versionNumber + 7;
		break;
		case 'opera':
			if ( parseInt( $.browser.versionNumber ) == 9 ) {
				if ( $.browser.version.substr( 0, 3 ) == '9.5' )
					browserIndex = 13;
				else
					browserIndex = 12;
			} else if ( parseInt( $.browser.versionNumber ) == 10 )
				browserIndex = 14;
		break;
	}

	var osIndex = -1;
	switch ( $.os.name ) {
		case 'win':
			osIndex = 0;
		break;
		case 'mac':
			osIndex = 1;
		break;
		case 'linux':
			osIndex = 2;
		break;
	}

	if ( browserIndex == -1 )
		$( '#survey-1' ).val( 'other' );
	else
		$( '#survey-1' ).val( parseInt( browserIndex ) );
	if ( osIndex == -1 )
		$( '#survey-2' ).val( 'other' );
	else
		$( '#survey-2' ).val( osIndex );

});
