/* JavaScript for NavigableTOC extension */

$( document ).ready( function() {
	if ( $.section == '' ) {
		// Full page edit
		// Tell the section links what their offsets are
		for ( i = 0; i < $.sectionOffsets.length; i++ )
			$( '.tocsection-' + ( i + 1 ) ).children( 'a' )
				.data( 'offset', $.sectionOffsets[i] );
	} else if ( $.section != 'new' && $.section != 0 ) {
		// Existing section edit
		// Set adjusted offsets on the usable links
		$.section = parseInt( $.section );
		for ( i = 0; i < $.sectionOffsets.length; i++ )
			$( '.tocsection-' + ( i + $.section ) ).children( 'a' )
				.data( 'offset', $.sectionOffsets[i] -
					$.sectionOffsets[0] );
	}
	// Unlink all section links that didn't get an offset
	$( '.toc:last * li' ).each( function() {
		link = $(this).children( 'a' );
		if ( typeof link.data( 'offset') == 'undefined' &&
				link.is( ':visible' ) ) {
			link.hide();
			$(this).prepend( link.html() );
		}
	});

	$( '.toc:last * li a' ).click( function(e) {
		if( typeof jQuery(this).data( 'offset' ) != 'undefined' )
			jQuery( '#wpTextbox1' ).scrollToPosition( jQuery(this).data( 'offset' ) );
			e.preventDefault();
	});
	
	function styleCurrentSection() {
		// FIXME: Try to dynamically adjust section offsets when user
		// enters/removes stuff
		// Find the section we're in
		bytePos = $( '#wpTextbox1' ).bytePos();
		i = 0;
		while ( i < $.sectionOffsets.length &&
				$.sectionOffsets[i] <= bytePos )
			i++;
		sectionLink = $( '.tocsection-' + i ).children( 'a' );
		if ( !sectionLink.hasClass( 'currentSection' ) ) {
			$( '.currentSection' ).removeClass( 'currentSection' );
			sectionLink.addClass( 'currentSection' );
		}
	}
	
	$( '#wpTextbox1' ).bind( 'keydown mousedown scrollToPosition', function() {
		// Run styleCurrentSelection() after event processing is done
		// If we run it directly, we'll get an out-of-date byte position
		// This is ugly as hell
		setTimeout(styleCurrentSection, 0);
	});

});
