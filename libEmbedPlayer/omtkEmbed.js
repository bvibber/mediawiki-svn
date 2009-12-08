/*
* omtk media player supports ogg vorbis playback.
* omtk is not feature complete and fails on some ogg vorbis streams.
*
* This script will be depreciated unless the omtk flash applet improves in quality 
*/
var omtkEmbed = {
	
	// Instance name
	instanceOf:'omtkEmbed',
	
	// Supported player features  
	supports: {
		'pause':true,
		'time_display':true
	},
	
	/**
	* Wrap the embed code
	*/
	getEmbedHTML : function () {
	 	var _this = this;
		var embed_code =  this.getEmbedObj();
		// Need omtk to fire an onReady event.
		setTimeout( function(){
			_this.postEmbedJS();
		}, 2000 );
		return this.wrapEmebedContainer( embed_code );
	},
	
	/**
	* Get the embed object html
	*/
	getEmbedObj:function() {
		var player_path = mw.getMwEmbedPath() + 'libEmbedPlayer/binPlayers/omtk-fx/omtkp.swf';
		// player_path = 'omtkp.swf';
		js_log( "player path: " + player_path );
		return  '<object id="' + this.pid + '" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="1" height="1">' +
					'<param name="movie" value="' + player_path + '" />' + "\n" +
					'<!--[if !IE]>-->' + "\n" +
						'<object id="' + this.pid + '_ie" type="application/x-shockwave-flash" data="' + player_path + '" width="1" height="1">' + "\n" +
					'<!--<![endif]-->' + "\n" +
						  '<p>Error with Display of Flash Plugin</p>' + "\n" +
					'<!--[if !IE]>-->' + "\n" +
						'</object>' + "\n" +
					'<!--<![endif]-->' + "\n" +
				  '</object>';
	},
	
	/**
	* Run post embed javascript
	*/ 
	postEmbedJS:function() {
		this.getPlayerElement();
		// play the url: 
		js_log( "play: pid:" + this.pid + ' src:' + this.src );
				
		this.playerElement.play( this.src );
		
		this.monitor();
		// $j('#omtk_player').get(0).play(this.src);
		// $j('#'+this.pid).get(0).play( this.src );
	},
	
	/**
	* omtk does not support pause, issue the "stop" request
	*/
	pause:function() {
		this.stop();
	},
	
	/**
	* Monitor the audio playback and update the position
	*/
	monitor:function() {
		if ( this.playerElement.getPosition )
			this.currentTime = this.playerElement.getPosition() / 1000;
		
		this.parent_monitor();
	},
	
	/**
	* Update the playerElement pointer
	*/
	getPlayerElement : function () {
		this.playerElement = $j( '#' + this.pid ).get( 0 );
		if ( !this.playerElement.play )
			this.playerElement = $j( '#' + this.pid + '_ie' ).get( 0 );
		
		if ( this.playerElement.play ) {
			// js_log('omtk obj is missing .play (probably not omtk obj)');
		}
	},
}
// Some auto-called globals (bad) 
function OMTK_P_complete() {
	js_log( 'OMTK_P_complete' );
}

function OMTK_P_metadataUpdate() {
	js_log( 'OMTK_P_metadataUpdate' );
}
