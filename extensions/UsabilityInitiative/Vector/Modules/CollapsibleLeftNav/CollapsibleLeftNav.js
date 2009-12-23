/* Prototype code to show collapsing left nav options */
/* First draft and will be changing greatly */

/* To enable add the following line to LocalSettings.php */
/* $wgVectorUseCollapsibleLeftNav = true; */

mw.addOnloadHook( function() {
	if( !wgVectorUseCollapsibleLeftNav )
		return;
	$j( '#panel' ).addClass( 'collapsible-nav' );
	$j( '#panel > div.portal' ).toggleClass( 'collapsed' );
	$j( '#panel > div.portal:first' )
		.toggleClass( 'expanded' )
		.toggleClass( 'collapsed' )
		.find( 'div.body' )
		.slideToggle( 'fast' );
	// Toggle the selected menu's class and expand or collapse the menu
	$j( '#panel > div.portal > h5' ).click( function() {
		$j( this )
			.parent()
			.toggleClass( 'expanded' )
			.toggleClass( 'collapsed' )
			.find( 'div.body' )
			.slideToggle( 'fast' );
	} );
} );
