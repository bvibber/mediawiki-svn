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
		} );
	// Use the same function for all navigation headings - don't repeat yourself
	function toggle( $element ) {
		$j.cookie( 'vector-nav-' + $element.parent().attr( 'id' ), $element.parent().is( '.collapsed' ) );
		$element
			.parent()
			.toggleClass( 'expanded' )
			.toggleClass( 'collapsed' )
			.find( 'div.body' )
			.slideToggle( 'fast' );
	}
	var $headings = $j( '#panel > div.portal > h5' );
	var tabindex = 32767 - $headings.length;
	// Toggle the selected menu's class and expand or collapse the menu
	$headings
		// Make it keyboard accessible
		.each( function() { $j(this).attr( 'tabindex', tabindex++ ); } )
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
