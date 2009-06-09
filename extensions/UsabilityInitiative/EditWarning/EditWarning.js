/* JavaScript for EditWarning extension */

$( document ).ready( function() {
	$( 'textarea#wpTextbox1, input#wpSummary' )
		.bind(
			'change select paste cut',
			function () {
				$( window ).bind(
					'beforeunload',
					function( event ) {
						if( !confirm( gM('editwarning-warning' ) ) ) {
							event.preventDefault();							
						}
						event.stopImmediatePropagation();
					}
				);
			}
		);
	}
);
