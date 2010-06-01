/* Prototype code to show collapsing left nav options */
/* First draft and will be changing greatly */

$j(document).ready( function() {
	if( !wgVectorEnabledModules.collapsiblenav ) {
		return true;
	}
	$j( '#panel' ).addClass( 'collapsible-nav' );
	// Always show the first portal
	$j( '#panel > div.portal:first' )
		.addClass( 'expanded' )
		.find( 'div.body' )
		.show();
	// Remember which portals to hide and show
	$j( '#panel > div.portal:not(:first)' )
		.each( function( i ) {
			var state = $j.cookie( 'vector-nav-' + $j(this).attr( 'id' ) );
			if ( state == 'true' || ( state == null && i < 1 ) ) {
				$j(this)
					.addClass( 'expanded' )
					.find( 'div.body' )
					.show();
			} else {
				$j(this).addClass( 'collapsed' );
			}
			// Re-save cookie
			if ( state != null ) {
				$j.cookie( 'vector-nav-' + $j(this).attr( 'id' ), state, { expires: 30 } );
			}
		} );
	// Use the same function for all navigation headings - don't repeat yourself
	function toggle( $element ) {
		$j.cookie( 'vector-nav-' + $element.parent().attr( 'id' ), $element.parent().is( '.collapsed' ), { expires: 30 } );
		$element
			.parent()
			.toggleClass( 'expanded' )
			.toggleClass( 'collapsed' )
			.find( 'div.body' )
			.slideToggle( 'fast' );
	}
	var $headings = $j( '#panel > div.portal > h5' );
	/** Copy-pasted from jquery.wikiEditor.dialogs - :( */
	// Find the highest tabindex in use
	var maxTI = 0;
	$j( '[tabindex]' ).each( function() {
		var ti = parseInt( $j(this).attr( 'tabindex' ) );
		if ( ti > maxTI )
			maxTI = ti;
	});
	var tabIndex = maxTI + 1;
	// Fix the search not having a tabindex
	$j( '#searchInput' ).attr( 'tabindex', tabIndex++ );
	// Make it keyboard accessible
	$headings.each( function() {
		$j(this).attr( 'tabindex', tabIndex++ );
	} );
	/** End of copy-pasted section */
	// Toggle the selected menu's class and expand or collapse the menu
	$headings
		// Make the space and enter keys act as a click
		.keydown( function( event ) {
			if ( event.which == 13 /* Enter */ || event.which == 32 /* Space */ ) {
				toggle( $j(this) );
			}
		} )
		.mousedown( function() {
			toggle( $j(this) );
			$j(this).blur();
			return false;
		} );
} );
