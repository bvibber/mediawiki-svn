/*
 * The Core timed Text interface object
 * 
 * handles class mapings for: 
 * 	menu display ( jquery.ui themable )
 * 	timed text loading request
 *  timed text edit requests
 * 	timed text search & seek interface ( version 2 ) 
 */ 

// Bind to mw (for uncluttered global namespace) 
( function( $ ) {	
	$.timedText = function ( embedPlayer, options ){
		return new TimedText( embedPlayer, options );
	}
	/** 
	 * Timed Text Object
	 * @param embedPlayer Host player for timedText interfaces 
	 */
	TimedText = function( embedPlayer, options ) {
		return this.init( embedPlayer, options);
	}
	TimedText.prototype = {
		timedTextProvider:{
			'commons':{
				'api_url': mw.commons_api_url
			}				
		},		
			
		/**
		 * @constructor
		 * @param {Object} embedPlayer Host player for timedText interfaces 
		 * 
		 */
		init: function( embedPlayer, options ){
			this.embedPlayer = embedPlayer;			
		},
		
		/**
		 * Show the timed text menu
		 * @param {Object} jQuery selector to display the target
		 */
		showMenu: function( $target ){
			mw.log("TimedText:ShowMenu");
			// Get local refrence to all timed text sources
			var cat = this.embedPlayer;
			//var sources = this.embedPlayer.mediaElement.getSources( 'text' );
			// 	
		}
		
	}
} )( window.mw );

/**
* jQuery entry point for timedText interface:
*/
( function( $ ) {
	$.fn.timedText = function ( options ){
		var embedPlayer = $j( this.selector ).get(0);
		if( ! embedPlayer.TimedText )
			embedPlayer.TimedText = new mw.timedText( embedPlayer, options);
		// else just apply the options action:
		 
		//do the default "showMenu" action:
		embedPlayer.TimedText.showMenu(); 
	}
} )( jQuery );