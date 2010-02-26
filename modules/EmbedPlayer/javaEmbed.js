/*
* List of domains and hosted location of cortado. Lets clients avoid the security warning for cross domain cortado
*/
window.cortadoDomainLocations = {
	'upload.wikimedia.org' : 'http://upload.wikimedia.org/jars/cortado.jar'
}

var javaEmbed = {

	// instance name:
	instanceOf:'javaEmbed',
	
	// Supported feature set of the cortado applet: 		
	supports: {
		'playHead' : true,
		'pause' : true,
		'stop' : true,
		'fullscreen' : false,
		'timeDisplay' : true,
		'volumeControl' : false
	},
	
	/**
	* Output the the embed html
	*/
	doEmbedHTML: function () {
		var _this = this;
		mw.log( "java play url:" + this.getSrc( this.seek_time_sec ) );
		// get the duration
		this.getDuration();
		// if still unset set to an arbitrary time 60 seconds: 
		if ( !this.duration )this.duration = 60;
		
		var applet_loc = this.getAppletLocation();				
		
		mw.log('Applet location: ' + applet_loc );
		mw.log('Play media: ' + this.getSrc() );
		
		// load directly in the page..
		// (media must be on the same server or applet must be signed)
		var appletCode = '' +
		'<applet id="' + this.pid + '" code="com.fluendo.player.Cortado.class" archive="' + applet_loc + '" width="' + this.getWidth() + '" height="' + this.getHeight() + '">	' + "\n" +
			'<param name="url" value="' + this.getSrc() + '" /> ' + "\n" +
			'<param name="local" value="false"/>' + "\n" +
			'<param name="keepaspect" value="true" />' + "\n" +
			'<param name="video" value="true" />' + "\n" +
			'<param name="showStatus" value="hide" />' + "\n" +
			'<param name="audio" value="true" />' + "\n" +
			'<param name="seekable" value="true" />' + "\n" +
			'<param name="duration" value="' + this.duration + '" />' + "\n" +
			'<param name="bufferSize" value="4096" />' + "\n" +
		'</applet>';					
		$j( this ).html( appletCode );
				
		// Wrap it in an iframe to avoid hanging the event thread in FF 2/3 and similar
		// NOTE:  This breaks refrence to the applet so disabled for now: 
		/*if ( $j.browser.mozilla ) {
			var iframe = document.createElement( 'iframe' );
			iframe.setAttribute( 'width', this.getWidth() );
			iframe.setAttribute( 'height', this.getHeight() );
			iframe.setAttribute( 'scrolling', 'no' );
			iframe.setAttribute( 'frameborder', 0 );
			iframe.setAttribute( 'marginWidth', 0 );
			iframe.setAttribute( 'marginHeight', 0 );
			iframe.setAttribute( 'id', 'cframe_' + this.id )
			
			// Append the iframe to the embed object: 
			$j( this ).html( iframe );
			
			// Write out the iframe content: 
			var newDoc = iframe.contentDocument;
			newDoc.open();
			newDoc.write( '<html><body>' + appletCode + '</body></html>' );
			// spurious error in some versions of FF, no workaround known
			newDoc.close(); 
		} else {*/
			$j( this ).html( appletCode );
		//}	
		
		// Start the monitor: 
		this.monitor();
	},
	
	/**
	* Get the applet location
	*/
	getAppletLocation: function() {
		var mediaSrc = this.getSrc()
		if ( mediaSrc.indexOf( '://' ) != -1 & !mw.isLocalDomain( mediaSrc ) ) {
			if ( window.cortadoDomainLocations[ mw.parseUri( mediaSrc ).host ] ) {
				applet_loc =  window.cortadoDomainLocations[mw.parseUri( mediaSrc ).host];
			} else {
				applet_loc  = 'http://theora.org/cortado.jar';
			}
		} else {
			// should be identical to cortado.jar
			applet_loc = mw.getMwEmbedPath() + 'modules/EmbedPlayer/binPlayers/cortado/cortado-ovt-stripped-0.5.0.jar';
		}
		return applet_loc;
	},
	
	/**
	* Monitor applet playback, and update currentTime 
	*/	
	monitor: function() {
		this.getPlayerElement();		
		if ( this.playerElement ) {
				try {
				   // java reads ogg media time.. so no need to add the start or seek offset:
				   mw.log(' ct: ' + this.playerElement.getPlayPosition() + ' ' +  this.supportsURLTimeEncoding());												   
				   this.currentTime = this.playerElement.getPlayPosition();
				   if ( this.playerElement.getPlayPosition() < 0 ) {
				   		mw.log( 'pp:' + this.playerElement.getPlayPosition() );
						// Probably reached clip end					
						this.onClipDone();
				   }
				} catch ( e ) {
				   mw.log( 'could not get time from jPlayer: ' + e );
				}
		}else{
			mw.log(" could not find playerElement " );
		}			
		// Once currentTime is updated call parent_monitor 
		this.parent_monitor();
	},
	
	/**
	* Seek in the ogg stream 
	* ( Cortado seek does not seem to work very well )  
	* @param {Float} percentage Percentage to seek into the stream
	*/
	doSeek:function( percentage ) {	
		mw.log( 'java:seek:p: ' + percentage + ' : '  + this.supportsURLTimeEncoding() + ' dur: ' + this.getDuration() + ' sts:' + this.seek_time_sec );
		this.getPlayerElement();
		
		if ( this.supportsURLTimeEncoding() ) {
			this.parent_doSeek( percentage );			
		} else if ( this.playerElement ) {
		   // do a (generally broken) local seek:   
		   mw.log( "cortado javascript seems to always fail ... but here we go... doSeek(" + ( percentage * parseFloat( this.getDuration() ) ) );
		   this.playerElement.doSeek( percentage * parseFloat( this.getDuration() )  );
		} else {
			this.doPlayThenSeek( percentage );
		}
	},
	
	/**
	* Issue a play request then seek to a percentage point in the stream
	* @param {Float} percentage Percentage to seek into the stream
	*/	
	doPlayThenSeek: function( percentage ) {
		mw.log( 'doPlayThenSeek' );
		var _this = this;
		this.play();
		var rfsCount = 0;
		var readyForSeek = function() {
			_this.getPlayerElement();
			// if we have .jre ~in theory~ we can seek (but probably not) 
			if ( _this.playerElement ) {
				_this.doSeek( perc );
			} else {
				// try to get player for 10 seconds: 
				if ( rfsCount < 200 ) {
					setTimeout( readyForSeek, 50 );
					rfsCount++;
				} else {
					mw.log( 'error:doPlayThenSeek failed' );
				}
			}
		}
		readyForSeek();
	},
	
	/**
	* Update the playerElement instance with a pointer to the embed object 
	*/
	getPlayerElement:function() {
		//if ( $j.browser.mozilla ) {
		//	this.playerElement  = $j('#cframe_' + this.id).contents().find( '#' +  this.pid );							
		//} else {
			this.playerElement = $j( '#' + this.pid ).get( 0 );
		//}
	},	
	
	/**
	* Issue the doPlay request to the playerElement
	*	calls parent_play to update interface
	*/
	play: function() {
		this.getPlayerElement();
		this.parent_play();
		if ( this.playerElement && this.playerElement.play ) {
			this.playerElement.play();
		}
	},
	
	/**
	* Pause playback
	* 	calls parent_pause to update interface
	*/	
	pause:function() {
		this.getPlayerElement();
		// Update the interface
		this.parent_pause();
		// Call the pause function if it exists:		
		if ( this.playerElement && this.playerElement.pause ) {
			this.playerElement.pause();
		}
	}
};
