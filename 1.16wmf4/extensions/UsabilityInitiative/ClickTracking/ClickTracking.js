( function( $ ) {
	if ( !wgClickTrackingIsThrottled ) {
		// Create 'track action' function to call the clicktracking API and send the ID
		$.trackAction = function( id ) {
			$j.post(
					wgScriptPath + '/api.php', { 'action': 'clicktracking', 'eventid': id, 'token': wgTrackingToken }
			);
		};
		
		$.trackActionWithInfo = function( id, info ) {
			$j.post(
					wgScriptPath + '/api.php', { 'action': 'clicktracking', 'eventid': id, 'token': wgTrackingToken, 'additional': info }
			);
		};
		
		
		// Add click tracking hooks to the sidebar
		$j(document).ready( function() {
			$( '#p-logo a, #p-navigation a, #p-interaction a, #p-tb a' ).each( function() {
				var href = $(this).attr( 'href' );
				// Only modify local URLs
				if ( href.length > 0 && href[0] == '/' && ( href.length == 1 || href[1] != '/' ) ) {
					var id = 'leftnav-' + skin + '-' + ( $(this).attr( 'id' ) || $(this).parent().attr( 'id' ) );
					href = wgScriptPath + '/api.php?action=clicktracking' +
						'&eventid=' + id + '&token=' + wgTrackingToken + '&redirectto=' + escape( href );
					$(this).attr( 'href', href );
				}
			} );
		} );
	}
} )( jQuery );
