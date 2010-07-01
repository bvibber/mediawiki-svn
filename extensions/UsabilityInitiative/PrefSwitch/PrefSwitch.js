/* JavaScript for PrefSwitch extension */

$j(document).ready( function() {
	function detect() {
		// Detect browser
		var browser = 'other';
		switch ( $j.browser.name ) {
			case 'msie':
				var v = parseInt( $j.browser.versionNumber );
				// IE8 supports Document mode while IE7 does not support it - other versions don't lie about their age
				browser = ( v == 7 ? ( document.documentMode ? 'ie8' : 'ie7' ) : 'ie' + v );
				break;
			case 'firefox': browser = 'ff' + parseInt( $j.browser.versionNumber ); break;
			case 'chrome': browser = 'c' + parseInt( $j.browser.versionNumber ); break;
			case 'safari': browser = 's' + parseInt( $j.browser.versionNumber ); break;
			case 'opera':
				if ( parseInt( $j.browser.versionNumber ) == 9 ) {
					if ( $j.browser.version.substr( 0, 3 ) == '9.5' ) {
						browser = 'o9.5';
					} else {
						browser = 'o9';
					}
				} else if ( parseInt( $j.browser.versionNumber ) == 10 ) {
					browser = 'o10';
				}
				break;
		}
		// Detect operating system
		var os = 'other';
		switch ( $j.os.name ) {
			case 'win': os = 'windows'; break;
			case 'mac': os = 'macos'; break;
			case 'linux': os = 'linux'; break;
		}
		switch ( $j.browser.name ) {
			case 'iemobile': os = 'windowsmobile'; break;
			case 'iphone': os = 'iphoneos'; break;
			case 'ipod': os = 'iphoneos'; break;
			case 'ipad': os = 'iphoneos'; break;
		}
		return {
			'survey-browser': browser, 'survey-os': os, 'survey-res-x': screen.width, 'survey-res-y': screen.height
		};
	}
	// Auto-hide/show "other" explaination fields for selects
	$j( '.prefswitch-survey-other-select' ).parent().hide();
	$j( 'select.prefswitch-survey-need-other' ).change( function() {
		if ( $j(this).val() == 'other' ) {
			$j( '#' + $j(this).attr( 'id' ) + '-other' ).parent().slideDown( 'fast' );
		} else {
			$j( '#' + $j(this).attr( 'id' ) + '-other' ).parent().slideUp( 'fast' );
		}
	});
	// Auto-select the check or radio next to an "other" explaination on click
	$j( '.prefswitch-survey-other-radios, .prefswitch-survey-other-checks' ).click( function() {
		$j(this).prev().prev().attr( 'checked', true );
	});
	// Auto-hide/show explaination fields for boolean
	$j( '.prefswitch-survey-iftrue, .prefswitch-survey-iffalse' ).hide();
	$j( '.prefswitch-survey-true, .prefswitch-survey-false' ).change( function() {
		$ifTrueRow = $j( '#' + $j(this).attr( 'name' ) + '-iftrue-row' );
		$ifFalseRow = $j( '#' + $j(this).attr( 'name' ) + '-iffalse-row' );
		if ( $j(this).is( '.prefswitch-survey-true:checked' ) ) {
			$ifTrueRow.slideDown( 'fast' );
			$ifFalseRow.slideUp( 'fast' );
		} else if ( $j(this).is( '.prefswitch-survey-false:checked' ) ) {
			$ifTrueRow.slideUp( 'fast' );
			$ifFalseRow.slideDown( 'fast' );
		}
	} );
	$j( '.prefswitch-survey-yes, .prefswitch-survey-no' ).change();
	// Auto-detect browser, os and screen size
	var detected = detect();
	$j( '#prefswitch-survey-browser' ).val( detected['survey-browser'] );
	$j( '#prefswitch-survey-os' ).val( detected['survey-os'] );
	if ( detected['survey-res-x'] && detected['survey-res-y'] ) {
		$j( '#prefswitch-survey-res-x' ).val( detected['survey-res-x'] );
		$j( '#prefswitch-survey-res-y' ).val( detected['survey-res-y'] );
	}
});
