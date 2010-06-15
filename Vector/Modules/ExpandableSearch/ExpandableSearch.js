/* JavaScript for ExpandableSearch extension */
$j( document ).ready( function() {
	
	// Only use this function in conjuction with the Vector skin
	if( !wgVectorEnabledModules.expandablesearch || skin != 'vector' ) {
		return true;
	}
	
	/* Browser Support */

	var map = {
		// Left-to-right languages
		'ltr': {
			// Collapsible Nav is broken in Opera < 9.6 and Konqueror < 4
			'msie': [['>=', 8]],
			'blackberry': false,
			'ipod': false,
			'iphone': false,
			'ps3': false
		},
		// Right-to-left languages
		'rtl': {
			'msie': [['>=', 8]],
			'blackberry': false,
			'ipod': false,
			'iphone': false,
			'ps3': false
		}
	};
	if ( !mw.usability.testBrowser( map ) ) {
		return true;
	}
	
	$j( '#searchInput' )
		.expandableField( { 
			'beforeExpand': function( context ) {
				// animate the containers border
				$j( this )
					.parent()
					.animate( {
						'borderTopColor': '#a0d8ff',
						'borderLeftColor': '#a0d8ff',
						'borderRightColor': '#a0d8ff',
						'borderBottomColor': '#a0d8ff' }, 'fast' );
			},
			'beforeCondense': function( context ) {
				// animate the containers border
				$j( this )
					.parent()
					.animate( {
						'borderTopColor': '#aaaaaa',
						'borderLeftColor': '#aaaaaa',
						'borderRightColor': '#aaaaaa',
						'borderBottomColor': '#aaaaaa' }, 'fast' );
			},
			'afterExpand': function( context ) {
				//trigger the collapsible tabs resize handler
				if ( typeof $j.collapsibleTabs != 'undefined' ){
					$j.collapsibleTabs.handleResize();
				}
			},
			'afterCondense': function( context ) {
				//trigger the collapsible tabs resize handler
				if ( typeof $j.collapsibleTabs != 'undefined' ){
					$j.collapsibleTabs.handleResize();
				}
			},
			'expandToLeft': ! $j( 'body' ).is( '.rtl' )
		} )
		.css( 'float', $j( 'body' ).is( '.rtl' ) ? 'right' : 'left' )
		.siblings( 'button' )
		.css( 'float', $j( 'body' ).is( '.rtl' ) ? 'left' : 'right' );
});
