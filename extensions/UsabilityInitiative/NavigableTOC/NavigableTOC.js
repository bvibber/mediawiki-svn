/* JavaScript for NavigableTOC extension */

/*
 * This function should be called on the text area to map out the section
 * character positions by scanning for headings, and the resulting data will
 * be stored as $(this).data( 'sections',  { ... } )
 */
jQuery.fn.mapSections = function() {
	return this.each( function() {
		// WRITE CODE HERE :)
	} );
};
/*
 * This function should be called on the text area with a selected UL element
 * to perform the list update on, where it will match the current cursor
 * position to an item on the outline and classify that li as 'current'
 */
jQuery.fn.updateSectionsList = function( list ) {
	return this.each( function( list ) {
		// WRITE CODE HERE :)
	} );
};

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
		link = $(this).children( 'a:visible' );
		if ( typeof link.data( 'offset') == 'undefined' ) {
			link.hide();
			$(this).prepend( link.html() );
		}
	});

	$( '.toc:last * li a' ).click( function(e) {
		if( typeof $(this).data( 'offset' ) != 'undefined' )
			$( '#wpTextbox1' ).scrollToPosition( $(this).data( 'offset' ) );
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
