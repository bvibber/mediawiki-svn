/*
 * JavaScript for Click Tracking
 */

( function( $ ) {
	// Add click tracking hooks to the sidebar
	$(document).ready( function() {
		$( '#p-logo a, #p-navigation a, #p-interaction a, #p-tb a' ).each( function() {
			var href = $(this).attr( 'href' );
			var token = mediaWiki.config.get( 'wgTrackingToken' );
			// Only modify local URLs
			if ( href.length > 0 && href[0] == '/' && ( href.length == 1 || href[1] != '/' ) ) {
				var id = 'leftnav-' + skin + '-' + ( $(this).attr( 'id' ) || $(this).parent().attr( 'id' ) );
				href = wgScriptPath + '/api.php?action=clicktracking' +
					'&eventid=' + id + '&token=' + token + '&redirectto=' + escape( href );
				$(this).attr( 'href', href );
			}
		} );
	} );
} )( jQuery );
