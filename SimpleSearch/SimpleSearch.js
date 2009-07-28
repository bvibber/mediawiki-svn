/* JavaScript for SimpleSearch extension */

$( document ).ready( function() {
	// Only use this function in conjuction with the Vector skin
	if( skin != 'vector' )
		return;
	
	// Adds form submission handler
	$( 'div#simpleSearch > input#searchInput' )
		.each( function() {
			$( '<label>' + gM( 'simplesearch-search' ) + '</label>' )
				.css({
					'display': 'none',
					'position' : 'absolute',
					'bottom': 0,
					'padding': '0.25em',
					'color': '#999999',
					'cursor': 'text'
				})
				.css( ( $( 'body.rtl' ).size() > 0 ? 'right' : 'left' ), 0 )
				.click( function() {
					$(this).parent().find( 'input#searchInput' ).focus();
				})
				.appendTo( $(this).parent() );
			if ( $(this).val() == '' ) {
				$(this).parent().find( 'label' ).show();
			}
		})
		.focus( function() {
			$(this).parent().find( 'label' ).hide();
		})
		.blur( function() {
			if ( $(this).val() == '' ) {
				$(this).parent().find( 'label' ).show();
			}
		});
});