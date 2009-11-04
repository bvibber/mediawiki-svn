(function($) {
	if( !wgClickTrackingIsThrottled ) {
		// creates 'track action' function to call the clicktracking API and send the ID
		$.trackAction = function ( id ) {
			$j.post( wgScriptPath + '/api.php', { 'action': 'clicktracking', 'eventid': id, 'token': wgTrackingToken } );
		};
		
		// Clicktrack the left sidebar links
		$(document).ready( function() {
			$( '#p-logo a, #p-navigation a, #p-tb a' ).click( function() {
				var id = 'leftnav-' + skin + '-' +
					( $(this).attr( 'id' ) || $(this).parent().attr( 'id' ) );
				window.location =  wgScriptPath +
					'/api.php?action=clicktracking&eventid=' + id + '&token=' +
					wgTrackingToken +
					'&redirectto=' + escape( $(this).attr( 'href' ) );
			});
		});
	}

})(jQuery);