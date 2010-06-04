/**
 * The Smil Player 
 *
 * @copyright kaltura
 * @author: Michael Dale  mdale@wikimedia.org
 * @license GPL2
 * 
 * Sequence player wraps smil into the video tag
 * 
 * It lets you controls smil timeline like you would an html5 video tag element
 *
 * It supports frame by frame rendering of "smil"
 * Its supports basic drop frame live playback of "smil" 
 * 
 * Extends the "embedPlayer" and represents the playlist as a single video stream
 * 
 */
 
 /* Add the hooks needed for playback */
mw.SmilPlayer = {
 	addPlayerHooks: function() {
		// Bind some hooks to every player: 
		$j( mw ).bind( 'newEmbedPlayerEvent', function( event, swapedPlayerId ) {		
			// Setup local reference to embedPlayer interface
			var embedPlayer = $j( '#' + swapedPlayerId ).get(0);				
			
			// Add the swarmTransport playerType	
			mw.EmbedTypes.players.defaultPlayers[ 'application/smil' ] = [ 'smilEmbed' ];
			
			// Build the swarm Transport "player"
			var smilMediaPlayer = new mediaPlayer( 'smilPlayer', [ 'application/smil' ], 'smilEmbed' );
			
			// Add the swarmTransport "player"
			mw.EmbedTypes.players.addPlayer( smilMediaPlayer );								
					
		} );		
 	}
}
mw.SmilPlayer.addPlayerHooks();

  
/**
* Extends EmbedPlayer to wrap smil playback in the html5 video tag abstraction. 
*/
var smilEmbed = {

	// Instance Name
	instanceOf: 'SequencePlayer',
	
	// Player supported feature set
	supports: {
		'play_head': true,
		'pause': true,
		'fullscreen': true,
		'time_display': true,
		'volume_control': true,		
		'overlays': true
	},
	 	
	/**
	* Put the embed player into the container
	*/
	doEmbedPlayer: function() {
		var _this = this;
		// Set "loading" here:
		$j(this).text( 	'smilPlayer here'	);
		
		// Get the clips in range
		
		// Start loading all the assets
		
	},
	
	/**
	* Get the thumbnail html
	*/
	getThumbnailHTML: function() {
		// If we have a "poster" use that;		
		return 'thumb html';
	},

	/**
	 * Seeks to the requested time and issues a callback when ready / displayed
	 * @param {float} time Time in seconds to seek to
	 * @param {function} callback Function to be called once currentTime is loaded and displayed 
	 */
	setCurrentTime : function( time, callback ) {
	
	}
}
