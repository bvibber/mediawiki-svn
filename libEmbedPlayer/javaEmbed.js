/*
* List of domains and hosted location of cortado. Lets clients avoid the security warning for cross domain cortado
*/
window.cortadoDomainLocations = {
	'upload.wikimedia.org' : 'http://upload.wikimedia.org/jars/cortado.jar',
	'tinyvid.tv' : 'http://tinyvid.tv/static/cortado.jar',
	'media.tinyvid.tv' : 'http://media.tinyvid.tv/cortado.jar'
}

var javaEmbed = {

	// instance name:
	instanceOf:'javaEmbed',
	
	// Supported feature set of the cortado applet: 		
	supports: {
		'play_head':true,
		'pause':true,
		'stop':true,
		'fullscreen':false,
		'time_display':true,
		'volume_control':false
	},
	
	/**
	* Wraps the embed object html output:
	*/
	getEmbedHTML: function () {
		// big delay on embed html cuz its just for status updates and ie6 is crazy. 
		if ( this.controls )
			setTimeout( 'document.getElementById(\'' + this.id + '\').postEmbedJS()', 500 );
		// set a default duration of 30 seconds: cortao should detect duration.
		return this.wrapEmebedContainer( this.getEmbedObj() );
	},
	
	/**
	* Get the embed html code:
	*/
	getEmbedObj: function() {
		mw.log( "java play url:" + this.getSrc( this.seek_time_sec ) );
		// get the duration
		this.getDuration();
		// if still unset set to an arbitrary time 60 seconds: 
		if ( !this.duration )this.duration = 60;
		// @@todo we should have src property in our base embed object
		var mediaSrc = this.getSrc();
		
		if ( mediaSrc.indexOf( '://' ) != -1 & mw.parseUri( document.URL ).host !=  mw.parseUri( mediaSrc ).host ) {
			if ( window.cortadoDomainLocations[mw.parseUri( mediaSrc ).host] ) {
				applet_loc =  window.cortadoDomainLocations[mw.parseUri( mediaSrc ).host];
			} else {
				applet_loc  = 'http://theora.org/cortado.jar';
			}
		} else {
			// should be identical to cortado.jar
			applet_loc = mw.getMwEmbedPath() + 'libEmbedPlayer/binPlayers/cortado/cortado-ovt-stripped-0.5.0.jar';
		}
			// load directly in the page..
			// (media must be on the same server or applet must be signed)
			var appplet_code = '' +
			'<applet id="' + this.pid + '" code="com.fluendo.player.Cortado.class" archive="' + applet_loc + '" width="' + this.width + '" height="' + this.height + '">	' + "\n" +
				'<param name="url" value="' + mediaSrc + '" /> ' + "\n" +
				'<param name="local" value="false"/>' + "\n" +
				'<param name="keepaspect" value="true" />' + "\n" +
				'<param name="video" value="true" />' + "\n" +
				'<param name="showStatus" value="hide" />' + "\n" +
				'<param name="audio" value="true" />' + "\n" +
				'<param name="seekable" value="true" />' + "\n" +
				'<param name="duration" value="' + this.duration + '" />' + "\n" +
				'<param name="bufferSize" value="4096" />' + "\n" +
			'</applet>';
									
			// Wrap it in an iframe to avoid hanging the event thread in FF 2/3 and similar
			// Doesn't work in MSIE or Safari/Mac or Opera 9.5
			if ( embedTypes.mozilla ) {
				var iframe = document.createElement( 'iframe' );
				iframe.setAttribute( 'width', params.width );
				iframe.setAttribute( 'height', playerHeight );
				iframe.setAttribute( 'scrolling', 'no' );
				iframe.setAttribute( 'frameborder', 0 );
				iframe.setAttribute( 'marginWidth', 0 );
				iframe.setAttribute( 'marginHeight', 0 );
				iframe.setAttribute( 'id', 'cframe_' + this.id )
				elt.appendChild( iframe );
				var newDoc = iframe.contentDocument;
				newDoc.open();
				newDoc.write( '<html><body>' + appplet_code + '</body></html>' );
				newDoc.close(); // spurious error in some versions of FF, no workaround known
			} else {
				return appplet_code;
			}
	},
	
	/**
	* Once the applet has been embed start monitoring playback
	*/
	postEmbedJS:function() {		
		// start monitor: 
		this.monitor();
	},
	
	/**
	* Monitor applet playback, and update currentTime 
	*/	
	monitor:function() {
		this.getPlayerElement();
		if ( this.isPlaying() ) {
			if ( this.playerElement && this.playerElement.getPlayPosition ) {
				try {
				   // java reads ogg media time.. so no need to add the start or seek offset:
				   // mw.log(' ct: ' + this.playerElement.getPlayPosition() + ' ' +  this.supportsURLTimeEncoding());												   
				   this.currentTime = this.playerElement.getPlayPosition();
				   if ( this.playerElement.getPlayPosition() < 0 ) {
				   		mw.log( 'pp:' + this.playerElement.getPlayPosition() );
						// probably reached clip end					
						this.onClipDone();
				   }
				} catch ( e ) {
				   mw.log( 'could not get time from jPlayer: ' + e );
				}
			}
		}
		// once currentTime is updated call parent_monitor 
		this.parent_monitor();
	},
	
	/**
	* Seek in the ogg stream 
	* (Cortado seek does not seem to work very well)  
	* @param {Float} percentage Percentage to seek into the stream
	*/
	doSeek:function( percentage ) {	
		mw.log( 'java:seek:p: ' + percentage + ' : '  + this.supportsURLTimeEncoding() + ' dur: ' + this.getDuration() + ' sts:' + this.seek_time_sec );
		this.getPlayerElement();
		
		if ( this.supportsURLTimeEncoding() ) {
			this.parent_doSeek( percentage );
			// this.seek_time_sec = mw.npt2seconds( this.start_ntp ) + parseFloat( percentage * this.getDuration() );						
		   // this.playerElement.setParam('url', this.getSrc( this.seek_time_sec ))
			// this.playerElement.restart();
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
		if ( embedTypes.mozilla ) {
			this.playerElement = window.frames['cframe_' + this.id ].document.getElementById( this.pid );
		} else {
			this.playerElement = $j( '#' + this.pid ).get( 0 );
		}
	},
	
	/**
	* Show the Thumbnail
	*/
	showThumbnail:function() {
		// empty out player html (jquery with java applets does mix) :			
		var pelm = document.getElementById( 'dc_' + this.id );
		if ( pelm ) {
			pelm.innerHTML = '';
		}
		this.parent_showThumbnail();
	},
	
	/**
	* Issue the doPlay request to the playerElement
	*	calls parent_play to update interface
	*/
	play:function() {
		this.getPlayerElement();
		this.parent_play();
		if ( this.playerElement )
			this.playerElement.doPlay();
	},
	
	/**
	* Pause playback
	* 	calls parent_pause to update interface
	*/	
	pause:function() {
		this.getPlayerElement();
		this.parent_pause();
		if ( this.playerElement )
			this.playerElement.doPause();
	}
};
