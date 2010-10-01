/*
 * JavaScript for ClickTracking jQuery plugin
 */

( function( $ ) {
	if ( !$.cookie( 'clicktracking-session' ) ) {
		/*
		 * Very simple hashing of date, why simple?
		 * 1. This is based on the date, not the user, so security is not an issue.
		 * 2. This is for statistics gathering a large scales, in the very unlikley event that two users end up with the
		 *    same token, it will only introduce a tiny and acceptable amount of noise.
		 * 3. Because it's much more problematic to sent tons of JavaScript to the client than to cope with 1 and 2.
		 */
		var token = '',
			dict = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',
			date = new Date().getTime();
		while ( token.length <= 32 ) {
		    token += dict.charAt( ( ( Math.random() * date ) + token.length + date ) % dict.length );
		}
		$.cookie( 'clicktracking-session', token );
	}
	/**
	 * Performs click tracking API call
	 * 
	 * @param {string} id event identifier
	 */
	$.trackAction = function( id ) {
		$.post(
			mediaWiki.config.get( 'wgScriptPath' ) + '/api.php', {
				'action': 'clicktracking',
				'eventid': id,
				'token': $.cookie( 'clicktracking-session' )
			}
		);
	};
	/**
	 * Performs click tracking API call
	 * 
	 * @param {string} id event identifier
	 * @param {string} info additional information to be stored with the click
	 */
	$.trackActionWithInfo = function( id, info ) {
		$.post(
			mediaWiki.config.get( 'wgScriptPath' ) + '/api.php', {
				'action': 'clicktracking',
				'eventid': id,
				'token': $.cookie( 'clicktracking-session' ),
				'additional': info
			}
		);
	};
} )( jQuery );
