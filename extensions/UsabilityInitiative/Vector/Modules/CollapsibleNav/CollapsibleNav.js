/* Prototype code to show collapsing left nav options */
/* First draft and will be changing greatly */

mw.addOnloadHook( function() {
	if( !wgVectorEnabledModules.collapsibleleftnav ) {
		return true;
	}
	$j( '#panel' ).addClass( 'collapsible-nav' );
	$j( '#panel > div.portal:first' )
		.addClass( 'expanded' )
		.find( 'div.body' )
		.show();
	$j( '#panel > div.portal:not(:first)' )
		.addClass( 'collapsed' );
	// Toggle the selected menu's class and expand or collapse the menu
	$j( '#panel > div.portal > h5' ).click( function() {
		$j(this)
			.parent()
			.toggleClass( 'expanded' )
			.toggleClass( 'collapsed' )
			.find( 'div.body' )
			.slideToggle( 'fast' );
		return false;
	} );
} );
