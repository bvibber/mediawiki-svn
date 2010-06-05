
/**
* Extends EmbedPlayer to wrap smil playback in the html5 video tag abstraction. 
*/
var mw.EmbedPlayerSmil = {

	// Instance Name
	instanceOf: 'Smil',
	
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
		$j(this).html( 	
			$j( '<div />')
			.attr('id', 'smilCanvas_' + this.id )
			.css( {
				'width' : '100%',
				'height' : '100%',
				'position' : 'relative'
			})
		);			
		
		// Create the smilPlayer
		this.smil = new mw.SmilPlayer( this );
		
		
	},
	$j( this ).trigger( 'mediaLoaded' );
	/**
	* Return the virtual canvas element
	*/ 
	getPlayerElement: function(){
		// return the virtual canvaus
		return $j( 'smilCanvas_' + this.id ).get(0);
	},
	
	/**
	* update the thumbnail html
	*/
	updateThumbnailHTML: function() {
		// If we have a "poster" use that;		
		if(  this.poster ){
			this.parent_updateThumbnailHTML();
			return ;
		}
		// If no thumb could be gennerated use the first frame of smil: 
		this.doEmbedPlayer(); 
	},

	/**
	 * Seeks to the requested time and issues a callback when ready / displayed
	 * @param {float} time Time in seconds to seek to
	 * @param {function} callback Function to be called once currentTime is loaded and displayed 
	 */
	setCurrentTime : function( time, callback ) {
		
	}
}
