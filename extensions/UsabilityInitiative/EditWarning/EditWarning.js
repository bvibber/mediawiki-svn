/* JavaScript for EditWarning extension */

$( document ).ready( function() {
	$( 'textarea#wpTextbox1, input#wpSummary' )
		.bind(
			'change paste cut keydown',
			function () {
				if ( !( 'onbeforeunload' in window ) ) {
					window.onbeforeunload = function() {
						return gM('editwarning-warning' );
					}
				}
			}
		);
	$( 'form' ).submit( function() {
		window.onbeforeunload = function () {};
	});
});