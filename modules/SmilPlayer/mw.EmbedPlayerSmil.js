/**
* Extends EmbedPlayer to wrap smil playback in the html5 video tag abstraction. 
*/

//Get all our message text
mw.includeAllModuleMessages();

// Setup the EmbedPlayerSmil object:
mw.EmbedPlayerSmil = {

	// Instance Name
	instanceOf: 'Smil',
	
	// Player supported feature set
	supports: {
		'playHead' : true,
		'pause' : true,
		'fullscreen' : true,
		'timeDisplay' : true,
		'volumeControl' : true,
		
		'overlays' : true
	},	
	 	
	/**
	* Put the embed player into the container
	*/
	doEmbedPlayer: function() {
		var _this = this;
		// Set "loading" here:
		$j( this ).html( 	
			$j( '<div />')
			.attr('id', 'smilCanvas_' + this.id )
			.css( {
				'width' : '100%',
				'height' : '100%',
				'position' : 'relative'
			})	
		);			
				
		// Update the embed player
		this.getSmil( function( smil ){				
			// XXX might want to move this into mw.SMIL
			$j( _this ).html( 
				smil.getHtmlDOM( {
					'width': _this.getWidth(), 
					'height': _this.getHeight() 
				}, 0 )
			)
		});		
	},
	
	/** 
	 * The monitor function updates smil states with the current time
	 * monitor is called every 250ms so animations are handled between 
	 * update time
	 */
	monitor: function(){
		var _this = this;
		// update current time and set monitor interval 
		_this.parent_monitor();
		
		// probably need a smil "update" function
		_this.getSmil(function( smil ){
			smil.syncWithTime( _this.currentTime );			
		});
	},
	
	/**
	* Get the smil object. If the smil object does not exist create one with the source url:
	* @param callback 
	*/
	getSmil: function( callback ){
		if( this.smil ){
			callback( this.smil );
			return ;
		}
		// Create the Smil engine object 
		this.smil = new mw.Smil();
		
		// Load the smil 
		this.smil.loadFromUrl( this.getSrc(), function(){
			callback( this.smil ); 
		});		
	},
	
	/**
	* Get the duration of smil document. 
	*/
	getDuration: function(){
		if( this.smil ){
			return this.smil.getDuration();
		}
	},
	
	/**
	* Return the virtual canvas element
	*/ 
	getPlayerElement: function(){
		// return the virtual canvas
		return $j( 'smilCanvas_' + this.id ).get(0);
	},
	
	/**
	* Update the thumbnail html
	*/
	updateThumbnailHTML: function() {
		// If we have a "poster" use that;		
		if(  this.poster ){
			this.parent_updateThumbnailHTML();
			return ;
		}
		// If no thumb could be generated use the first frame of smil: 
		this.doEmbedPlayer(); 
	},

	
	/**
	*  Play function starts the virtual clock time
	*/
	play: function() {
		mw.log(" parent: " + this.parent_play);		
		// call the parent to update interface
		this.parent_play();

		mw.log( 'f:play: smilEmbed' );
		var ct = new Date();
		this.clockStartTime = ct.getTime();

		// Start up monitor:
		this.monitor();
	},		
	
	/**
	* Stops the playback
	*/
	stop:function() {
		this.currentTime = 0;
		this.pause();		
	},
	
	/**
	* Preserves the pause time across for timed playback 
	*/
	pause:function() {
		mw.log( 'f:pause: smilEmbed' );
		var ct = new Date();
		this.pauseTime = this.currentTime;
		mw.log( 'pause time: ' + this.pauseTime );
		
		window.clearInterval( this.monitorTimerId );
	},
	
	/**
	* Get the embed player time
	*/
	getPlayerElementTime: function() {
		//mw.log('html:monitor: '+ this.currentTime);		
		var ct = new Date();
		var currentTime = ( ( ct.getTime() - this.clockStartTime ) / 1000 ) + this.pauseTime;		
		return currentTime;
	},
	

	/**
	* Seeks to a given percent and updates the pauseTime
	* 
	* NOTE in most cases you will want setCurrentTime with callback
	*
	* @param {Float} perc Percentage to seek into the virtual player
	*/
	doSeek: function( perc ) {
		this.pauseTime = perc * this.getDuration();
		this.play();
	},
	
	/**
	 * Seeks to the requested time and issues a callback when assets are loaded / displayed
	 * @param {float} time Time in seconds to seek to
	 * @param {function} callback Function called once currentTime is loaded and displayed 
	 */
	setCurrentTime : function( time, callback ) {
		var _this = this;
		// Update the embed player
		this.getSmil( function( smil ){				
			// XXX might want to move this into mw.SMIL
			$j( _this ).html( 
				smil.getHtmlDOM( {
					'width': _this.getWidth(), 
					'height': _this.getHeight() 
				}, time, callback)
			)
		});		
	}
}
