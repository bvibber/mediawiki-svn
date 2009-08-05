/* JavaScript for SimpleSearch extension */

js2AddOnloadHook( function() {
	// Only use this function in conjuction with the Vector skin
	if( skin != 'vector' )
		return;
	
	// Adds form submission handler
	$j( 'div#simpleSearch > input#searchInput' )
		.each( function() {
			$j( '<label></label>' )
				.text( gM( 'simplesearch-search' ) )
				.css({
					'display': 'none',
					'position' : 'absolute',
					'bottom': 0,
					'padding': '0.25em',
					'color': '#999999',
					'cursor': 'text'
				})
				.css( ( $j( 'body.rtl' ).size() > 0 ? 'right' : 'left' ), 0 )
				.click( function() {
					$j(this).parent().find( 'input#searchInput' ).focus();
				})
				.appendTo( $j(this).parent() );
			if ( $j(this).val() == '' ) {
				$j(this).parent().find( 'label' ).show();
			}
		})
		.focus( function() {
			$j(this).parent().find( 'label' ).hide();
		})
		.blur( function() {
			if ( $j(this).val() == '' ) {
				$j(this).parent().find( 'label' ).show();
			}
		});
});