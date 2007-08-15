
// This is a global configuration object which can embed multiple video instances
var wgOggPlayer = {
	'detectionDone': false,
	'vlcActiveX': false,

	// List of players in order of preference
	// Downpreffed VLC because it crashes my browser all the damn time -- TS
	'players': ['videoElement', 'oggPlugin', 'cortado', 'vlcPlugin', 'vlcActiveX'],

	'clientSupports': {},

	// Configuration from MW
	'msg': {},
	'cortadoUrl' : '',
	'showPlayerSelect': true,

	// Main entry point: initialise a video player
	// Player will be created as a child of the given ID
	// There may be multiple players in a document
	'init': function ( player, id, videoUrl, width, height, length ) {
		var elt = document.getElementById( id );

		this.detect( elt );

		if ( !player ) {
			// See if there is a cookie specifying a preferred player
			var cookieName = "ogg_player=";
			var cookiePos = document.cookie.indexOf(cookieName);
			if (cookiePos > -1) {
				player = document.cookie.substr( cookiePos + cookieName.length );
				var semicolon = player.indexOf( ";" );
				if ( semicolon > -1 ) {
					player = player.substr( cookiePos, semicolon );
				}
			}
		}

		if ( !player || !this.clientSupports[player] ) {
			for ( var i = 0; i < this.players.length; i++ ) {
				if ( this.clientSupports[this.players[i]] ) {
					player = this.players[i];
					break;
				}
			}
		}

		elt.innerHTML = '';

		switch ( player ) {
			case 'videoElement':
				this.embedVideoElement( elt, videoUrl, width, height, length );
				break;
			case 'oggPlugin':
				this.embedOggPlugin( elt, videoUrl, width, height, length );
				break;
			case 'vlcPlugin':
				this.embedVlcPlugin( elt, videoUrl, width, height, length );
				break;
			case 'vlcActiveX':
				this.embedVlcActiveX( elt, videoUrl, width, height, length );
				break;
			case 'cortado':
				this.embedCortado( elt, videoUrl, width, height, length );
				break;
			default:
				elt.innerHTML = this.msg['ogg-no-player'] + '<br/>';
		}
		if ( this.showPlayerSelect ) {
			elt.appendChild( document.createElement( 'br' ) );
			var label = document.createElement( 'label' );
			label.setAttribute( 'class', 'ogg_player_using' );
			label.appendChild( document.createTextNode( ' ' + this.msg['ogg-using-player'] ) );
			label.appendChild( this.makePlayerSelect( player, id, videoUrl, width, height, length ) );
			elt.appendChild( label );
		}
	},

	// Detect client capabilities
	'detect': function( elt ) {
		if (this.detectionDone) {
			return;
		}
		this.detectionDone = true;

		// MSIE VLC
		try {
			var vlcObj = new ActiveXObject( "VideoLAN.VLCPlugin.2" );
			this.clientSupports['vlcActiveX'] = true;
		} catch ( e ) {}

		// <video> element
		elt.innerHTML = '<video id="testvideo"></video>\n';
		var testvideo = document.getElementById('testvideo');
		if (testvideo && testvideo.play) {
			this.clientSupports['videoElement'] = true;
		}

		// Mozilla plugins
		if(navigator.mimeTypes && navigator.mimeTypes.length > 0) {
			for ( var i = 0; i < navigator.mimeTypes.length; i++) {
				var type = navigator.mimeTypes[i].type;
				var pluginName = navigator.mimeTypes[i].enabledPlugin ? navigator.mimeTypes[i].enabledPlugin.name : '';
				if(type.indexOf("application/ogg") > -1 && 
					pluginName != "VLC multimedia plugin" && pluginName != "VLC Multimedia Plugin") 
				{
					this.clientSupports['oggPlugin'] = true;
				}
				if(navigator.mimeTypes[i].type.indexOf("application/x-vlc-plugin") > -1) {
					this.clientSupports['vlcPlugin'] = true;
				}
			}
		}

		// Java
		this.clientSupports['cortado'] = navigator.javaEnabled();
	},

	'makePlayerSelect' : function ( selectedPlayer, id, videoUrl, width, height, length ) {
		var select = document.createElement( 'select' );
		for ( var player in this.clientSupports ) {
			var option = document.createElement( 'option' );
			option.value = player;
			option.appendChild( document.createTextNode( this.msg['ogg-player-' + player] ) );
			if ( selectedPlayer == player ) {
				option.selected = true;
			}
			select.appendChild( option );
		}
		select.value = selectedPlayer;

		var me = this;
		select.onchange = function () {
			var player = select.value;
			document.cookie = "ogg_player=" + player;
			me.init( player, id, videoUrl, width, height, length );
		};
		return select;
	},

	'newButton': function ( caption, callback ) {
		var elt = document.createElement('input');
		elt.type = 'button';
		elt.value = this.msg[caption];
		elt.onclick = callback;
		return elt;
	},

	'newPlayButton': function ( videoElt ) {
		return this.newButton( 'ogg-play', function () { videoElt.play(); } );
	},

	'newPauseButton': function ( videoElt ) {
		return this.newButton( 'ogg-pause', function () { videoElt.pause(); } );
	},

	'newStopButton': function ( videoElt ) {
		return this.newButton( 'ogg-stop', function () { videoElt.stop(); } );
	},

	'embedVideoElement': function ( elt, videoUrl, width, height, length ) {
		var videoElt = document.createElement('video');
		videoElt.setAttribute('width', width);
		videoElt.setAttribute('height', height);
		videoElt.setAttribute('src', videoUrl);
		videoElt.setAttribute('autoplay', '1');
		videoElt.setAttribute('controls', '1');
		elt.appendChild(videoElt);

		// Try to detect implementations that don't support controls
		// This works for the Opera test build
		if (!videoElt.controls) {
			elt.appendChild( document.createElement( 'br' ) );
			elt.appendChild( this.newPlayButton( videoElt ) );
			elt.appendChild( this.newPauseButton( videoElt ) );
			elt.appendChild( this.newStopButton( videoElt ) );
			//videoElt.play();
		}
	},

	'embedOggPlugin': function ( elt, videoUrl, width, height, length ) {
		var videoElt = document.createElement( 'object' );
		videoElt.setAttribute( 'type', 'application/ogg' );
		videoElt.setAttribute( 'width', width );
		videoElt.setAttribute( 'height', height );
		videoElt.setAttribute( 'data', videoUrl );
		elt.appendChild(videoElt);
	},

	'embedVlcPlugin' : function ( elt, videoUrl, width, height, length ) {
		var videoElt = document.createElement( 'object' );
		videoElt.setAttribute( 'type', 'application/x-vlc-plugin' );
		videoElt.setAttribute( 'width', width );
		videoElt.setAttribute( 'height', height );
		videoElt.setAttribute( 'data', videoUrl );
		elt.appendChild(videoElt);
		elt.appendChild( document.createElement( 'br' ) );
		// TODO: seek bar
		elt.appendChild( this.newPlayButton( videoElt ) );
		elt.appendChild( this.newPauseButton( videoElt ) );
		elt.appendChild( this.newStopButton( videoElt ) );
	},

	'embedVlcActiveX' : function ( elt, videoUrl, width, height, length ) {
		var id = elt.id + "_obj";

		// I ran into some hairy object initialisation issues when trying to 
		// create this object with DOM functions. If anyone knows a better way 
		// than innerHTML, please let me know. -- TS
		elt.innerHTML += 
			"<object id='" + id + "'" + 
			" classid='clsid:9BE31822-FDAD-461B-AD51-BE1D1C159921'" + 
			" codebase='http://downloads.videolan.org/pub/videolan/vlc/latest/win32/axvlc.cab#Version=0,8,6,0'" + 
			" width='" + width + "'" + 
			" height='" + height + "'>" + 
			"<param name='mrl' value='" + videoUrl.replace(/&/, '&amp;') + "'/>" + 
			"</object>" ;

		var videoElt = document.getElementById( id );
		elt.appendChild( document.createElement( 'br' ) );
		// TODO: seek bar
		elt.appendChild( this.newPlayButton( videoElt.playlist ) );
		// FIXME: playlist.pause() doesn't work
		elt.appendChild( this.newStopButton( videoElt.playlist ) );
	},

	'embedCortado' : function ( elt, videoUrl, width, height, length ) {
		var videoElt;
		if ( false ) {
			// Use <object>
			videoElt = document.createElement( 'object' );
			videoElt.setAttribute( 'codetype', 'application/x-java-applet' );
			videoElt.setAttribute( 'classid', 'com.fluendo.player.Cortado.class' );
		} else {
			// Use <applet>
			videoElt = document.createElement( 'applet' );
			videoElt.setAttribute( 'code', 'com.fluendo.player.Cortado.class' );
		}
		videoElt.setAttribute( 'width', width );
		videoElt.setAttribute( 'height', height );
		videoElt.setAttribute( 'archive', this.cortadoUrl );

		var param = document.createElement( 'param' );
		this.addParam( videoElt, 'url', videoUrl );
		this.addParam( videoElt, 'duration', length );
		this.addParam( videoElt, 'seekable', 'true' );
		this.addParam( videoElt, 'autoPlay', 'true' );
		this.addParam( videoElt, 'showStatus', 'show' );
		this.addParam( videoElt, 'statusHeight', 18 );
		elt.appendChild( videoElt );
	},

	'addParam': function ( elt, name, value ) {
		var param = document.createElement( 'param' );
		param.setAttribute( 'name', name );
		param.setAttribute( 'value', value );
		elt.appendChild( param );
	}
};
	


