/*
* The "kaltura player" embedPlayer interface for fallback h.264 and flv video format support
*/
var kplayerEmbed = {
	
	// Instance name: 
	instanceOf:'kplayerEmbed',
	
	// List of supported features: 
	supports: {
		'play_head':true,
		'pause':true,
		'stop':true,
		'time_display':true,
		'volume_control':true,
		'overlay':false,
		'fullscreen':false
	},
	
	/*
	* Get the Embed html by wraping the embed code in the embed container:
	*/
	getEmbedHTML : function () {
		var embed_code =  this.getEmbedObj();		
		var _this = this;
		setTimeout(function(){
			_this.postEmbedJS();
		}, 50);
		mw.log( "return embed html: " + embed_code );
		return this.wrapEmebedContainer( embed_code );
	},
	
	/**
	* Get the plugin embed html
	*/
	getEmbedObj:function() {	
		var player_path = mw.getMwEmbedPath() + 'libEmbedPlayer/binPlayers/kaltura-player';
		return '<object width="' + this.width + '" height="' + this.height + '" '+ 
			 'data="' + player_path + '/wrapper.swf" allowfullscreen="true" '+ 
			 'allownetworking="all" allowscriptaccess="always" '+
			 'type="application/x-shockwave-flash" '+ 
			 'id="' + this.pid + '" name="' + this.pid + '">'+
				'<param value="always" name="allowScriptAccess"/>'+
				'<param value="all" name="allowNetworking"/>'+
			  	'<param value="true" name="allowFullScreen"/>'+
			  	'<param value="#000000" name="bgcolor"/>'+
			  	'<param value="wrapper.swf" name="movie"/>'+
			  	'<param value="' + 
			  		'kdpUrl=' + player_path + '/kdp.swf' +
			  		'&ks=dummy&partner_id=0&subp_id=0' +
			  		'&uid=0&emptyF=onKdpEmpty&readyF=onKdpReady' +
			  		'" ' + 
			  		'name="flashVars"/>'+
			  '<param value="opaque" name="wmode"/>'+
			 '</object>';		
	},
	
	/**
	* javascript run post player embeding
	*/
	postEmbedJS:function() {
		var _this = this;
		this.getPlayerElement();	
		//alert( 	this.playerElement );
		if( this.playerElement && this.playerElement.insertMedia){
			// Add KDP listeners
			
			//this.playerElement.addJsListener("doPlay","kdpDoOnPlay");
			//this.playerElement.addJsListener("doStop","kdpDoOnStop");
			//myKdp.addJsListener("fastForward","kdpDoOnFF");
						
			_this.bindPlayerFunction( 'doPause', 'onPause' );
			_this.bindPlayerFunction( 'doPlay', 'play' );
			_this.bindPlayerFunction( 'playerPlayEnd', 'onClipDone' );
						
			// KDP player likes an absolute url for the src:
			var src = mw.absoluteUrl( _this.getSrc() );
			mw.log('play src: ' + src);
			
			// Insert the src:	
			this.playerElement.insertMedia( "-1", src, 'true' );			
			this.playerElement.dispatchKdpEvent( 'doPlay' );
			
			// Start the monitor
			this.monitor();
		}else{
			mw.log('insert media: not defiend:' + typeof this.playerElement.insertMedia );
			setTimeout( function(){
				_this.postEmbedJS();
			}, 25);
		}		
	},	
	
	/**
	* Bind a Player Function, 
	* 
	* Does some tricker to bind to "this" player instance:
	* 
	* @param {String} flash binding name
	* @param {String} function callback name
	*/
	bindPlayerFunction:function( bName, fName ){
		var cbid = fName + '_cb_' + this.id.replace(' ', '_');
		eval( 'window[ \'' + cbid +'\' ] = function(){$j(\'#' + this.id + '\').get(0).'+ fName +'();}' );
		this.playerElement.addJsListener( bName , cbid);
	},
	
	/**
	* on Pause callback from the kaltura flash player
	*  calls parent_pause to update the interface
	*/
	onPause:function(){		
		this.parent_pause();
	},
	
	/**
	* play method
	*  calls parent_play to update the interface 
	*/
	play:function() {
		if( this.playerElement && this.playerElement.dispatchKdpEvent )
			this.playerElement.dispatchKdpEvent('doPlay');
		this.parent_play();
	},
	
	/**
	* pause method
	*  calls parent_pause to update the interface 
	*/
	pause:function() {
		this.playerElement.dispatchKdpEvent('doPause');
		this.parent_pause();
	},
	
	/**
	* Issues a seek to the playerElement	
	*/ 
	doSeek:function( prec ){
		var _this = this;
		if( this.playerElement ){
			var seek_time = prec * this.getDuration(); 
			this.playerElement.dispatchKdpEvent('doSeek',  seek_time);
			// Kdp is missing seek done callback
			setTimeout(function(){
				_this.seeking= false;
			},500);
		}
		this.monitor();
	},
	
	/**
	* Issues a volume update to the playerElement
	*/
	updateVolumen:function( percentage ) {
		if( this.playerElement && this.playerElement.dispatchKdpEvent )
			this.playerElement.dispatchKdpEvent('volumeChange', percentage);
	},
	
	/**
	* Monitors playback updating the current Time
	*/
	monitor:function() {	
		if( this.playerElement && this.playerElement.getMediaSeekTime ){
			this.currentTime = this.playerElement.getMediaSeekTime();
		}
		this.parent_monitor();
	},
	
	/**
	* Get the embed fla object player Element
	*/
	getPlayerElement: function () {
		this.playerElement = document.getElementById( this.pid );
	}
}
/**
* function called once player is ready.
* 
* NOTE: playerID is not always passed so we can't use this: 
*/
function onKdpReady( playerId ) {
 	 mw.log( "player is ready::" + playerId); 	 
}
