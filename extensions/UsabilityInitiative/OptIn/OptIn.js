/* JavaScript for OptIn extension */

js2AddOnloadHook( function() {
	$j( '.optin-other-select' ).parent().hide();
	$j( 'select.optin-need-other' ).change( function() {
		if( $j(this).val() == 'other' )
			$j( '#' + $j(this).attr( 'id' ) + '-other' ).parent().slideDown( 'fast' );
		else
			$j( '#' + $j(this).attr( 'id' ) + '-other' ).parent().slideUp( 'fast' );
	});
	$j( '.optin-other-radios, .optin-other-checks' ).click( function() {
		$j(this).prev().prev().attr( 'checked', true );
	});
	$j( '.survey-ifyes, .survey-ifno' ).hide();
	$j( '.survey-yes, .survey-no' ).change( function() {
		yesrow = $j( '#' + $j(this).attr( 'name' ) + '-ifyes-row' );
		norow = $j( '#' + $j(this).attr( 'name' ) + '-ifno-row' );
		if( $j(this).is( '.survey-yes:checked' ) ) {
			yesrow.slideDown( 'fast' );
			norow.slideUp( 'fast' );
		} else if( $j(this).is( '.survey-no:checked' ) ) {
			yesrow.slideUp( 'fast' );
			norow.slideDown( 'fast' );
		}
	});
	// Load initial state
	$j( '.survey-yes, .survey-no' ).change();
	// Detect browser
	var browserIndex = 'other';
	switch ( $j.browser.name ) {
		case 'msie':
			browserIndex = 'ie'+ parseInt( $j.browser.versionNumber );
		break;
		case 'firefox':
			browserIndex = 'ff' + parseInt( $j.browser.versionNumber );
		break;
		case 'chrome':
			// FIXME: Chrome Beta?
			browserIndex = 'c' + parseInt( $j.browser.versionNumber );
		break;
		case 'safari':
			browserIndex = 's' + parseInt( $j.browser.versionNumber );
		break;
		case 'opera':
			if ( parseInt( $j.browser.versionNumber ) == 9 ) {
				if ( $j.browser.version.substr( 0, 3 ) == '9.5' )
					browserIndex = 'o9.5';
				else
					browserIndex = 'o9';
			} else if ( parseInt( $j.browser.versionNumber ) == 10 )
				browserIndex = 'o10';
		break;
	}
	$j( '#survey-browser' ).val( browserIndex );
	// Detect operating system
	var osIndex = 'other';
	switch ( $j.os.name ) {
		case 'win':
			osIndex = 'windows';
		break;
		case 'mac':
			osIndex = 'macos';
		break;
		case 'linux':
			osIndex = 'linux';
		break;
	}
	$j( '#survey-os' ).val( osIndex );
	// Detect screen dimensions
	if ( screen.width && screen.height ) {
		$j( '.optin-resolution-x' ).val( screen.width );
		$j( '.optin-resolution-y' ).val( screen.height );
	}
});
