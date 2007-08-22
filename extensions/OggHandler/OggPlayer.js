
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
	'controlsHeightGuess': 20, 

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
				player = 'none';
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

		// navigator.javaEnabled() only tells us about preferences, we need to
		// search navigator.mimeTypes to see if it's installed
		var javaEnabled = navigator.javaEnabled();

		// MSIE VLC
		if ( this.testActiveX( 'VideoLAN.VLCPlugin.2' ) ) {
			this.clientSupports['vlcActiveX'] = true;
		}

		// MSIE Java
		if ( javaEnabled && this.testActiveX( 'JavaPlugin' ) ) {
			this.clientSupports['cortado'] = true;
		}

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
				if (javaEnabled && 
					navigator.mimeTypes[i].type.indexOf("application/x-java-applet") > -1) 
				{
					this.clientSupports['cortado'] = true;
				}
			}
		}
	},

	'testActiveX' : function ( name ) {
		var hasObj = true;
		try {
			// No IE, not a class called "name", it's a variable
			var obj = new ActiveXObject( '' + name );
		} catch ( e ) {
			hasObj = false;
		}
		return hasObj;
	},

	'addOption' : function ( select, value, text, selected ) {
			var option = document.createElement( 'option' );
			option.value = value;
			option.appendChild( document.createTextNode( text ) );
			if ( selected ) {
				option.selected = true;
			}
			select.appendChild( option );
	},

	'hx' : function ( s ) {
		if ( typeof s != 'String' ) {
			s = s.toString();
		}
		return s.replace( /&/g, '&amp;' )
			. replace( /</g, '&lt;' )
			. replace( />/g, '&gt;' );
	},

	'hq' : function ( s ) {
		return '"' + this.hx( s ) + '"';
	},

	'makePlayerSelect' : function ( selectedPlayer, id, videoUrl, width, height, length ) {
		var select = document.createElement( 'select' );
		if ( selectedPlayer == 'none' ) {
			this.addOption( select, 'none', this.msg['ogg-player-none'], true );
		}
		for ( var player in this.clientSupports ) {
			this.addOption( select, player, this.msg['ogg-player-' + player], selectedPlayer == player );
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
		videoElt.setAttribute( 'width', width );
		videoElt.setAttribute( 'height', height + this.controlsHeightGuess );
		videoElt.setAttribute( 'src', videoUrl );
		videoElt.setAttribute( 'autoplay', '1' );
		videoElt.setAttribute( 'controls', '1' );
		elt.appendChild( videoElt );

		// Try to detect implementations that don't support controls
		// This works for the Opera test build
		if ( !videoElt.controls ) {
			elt.appendChild( document.createElement( 'br' ) );
			elt.appendChild( this.newPlayButton( videoElt ) );
			elt.appendChild( this.newPauseButton( videoElt ) );
			elt.appendChild( this.newStopButton( videoElt ) );
			//videoElt.play();
		}
	},

	'embedOggPlugin': function ( elt, videoUrl, width, height, length ) {
		var id = elt.id + "_obj";
		elt.innerHTML += 
			"<object id=" + this.hq( id ) + 
			" type='application/ogg'" +
			" width=" + this.hq( width ) + 
			" height=" + this.hq( height + this.controlsHeightGuess ) + 
			" data=" + this.hq( videoUrl ) + "></object>";
	},

	'embedVlcPlugin' : function ( elt, videoUrl, width, height, length ) {
		var id = elt.id + "_obj";
		elt.innerHTML += 	
			"<object id=" + this.hq( id ) + 
			" type='application/x-vlc-plugin'" +
			" width=" + this.hq( width ) + 
			" height=" + this.hq( height ) + 
			" data=" + this.hq( videoUrl ) + "></object>";
		
		var videoElt = document.getElementById( id );
		elt.appendChild( document.createElement( 'br' ) );
		// TODO: seek bar
		elt.appendChild( this.newPlayButton( videoElt ) );
		elt.appendChild( this.newPauseButton( videoElt ) );
		elt.appendChild( this.newStopButton( videoElt ) );
	},

	'embedVlcActiveX' : function ( elt, videoUrl, width, height, length ) {
		var id = elt.id + "_obj";

		var html = 
			'<object id=' + this.hq( id ) + 
			' classid="clsid:9BE31822-FDAD-461B-AD51-BE1D1C159921"' + 
			' codebase="http://downloads.videolan.org/pub/videolan/vlc/latest/win32/axvlc.cab#Version=0,8,6,0"' + 
			' width=' + this.hq( width ) + 
			' height=' + this.hq( height ) + 
			' style="width: ' + this.hx( width ) + 'px; height: ' + this.hx( height ) + 'px;"' +
			">" + 
			'<param name="mrl" value=' + this.hq( videoUrl ) + '/>' + 
			'</object>';
		elt.innerHTML += html;

		var videoElt = document.getElementById( id );

		// IE says "sorry, I wasn't listening, what were the dimensions again?"
		if ( width && height ) {
			videoElt.width = width;
			videoElt.height = height;
			videoElt.style.width = width + 'px';
			videoElt.style.height = height + 'px';
		}

		elt.appendChild( document.createElement( 'br' ) );
		// TODO: seek bar
		elt.appendChild( this.newButton( 'ogg-play', function() { videoElt.playlist.play(); } ) );
		// FIXME: playlist.pause() doesn't work
		elt.appendChild( this.newButton( 'ogg-stop', function() { videoElt.playlist.stop(); } ) );
	},

	'embedCortado' : function ( elt, videoUrl, width, height, length ) {
		var statusHeight = 18;
		// Given extra vertical space, cortado centres the video and then overlays the status 
		// line, leaving an ugly black bar at the top. So we don't give it any.
		var playerHeight = height < statusHeight ? statusHeight : height;

		// Create the applet all at once
		// In Opera, document.createElement('applet') immediately creates
		// a non-working applet with unchangeable parameters, similar to the 
		// problem with IE and ActiveX. 
		elt.innerHTML = 
		    '<applet code="com.fluendo.player.Cortado.class" ' +
		    '      width=' + this.hq( width ) +
		    '      height=' + this.hq( playerHeight ) + 
		    '      archive=' + this.hq( this.cortadoUrl ) + '>' +
		    '  <param name="url"  value=' + this.hq( videoUrl ) + '/>' +
		    '  <param name="duration"  value=' + this.hq( length ) + '/>' +
		    '  <param name="seekable"  value="true"/>' +
		    '  <param name="autoPlay" value="true"/>' +
		    '  <param name="showStatus"  value="show"/>' +
		    '  <param name="statusHeight"  value="' + statusHeight + '"/>' +
		    '</applet>';

		// Disable autoPlay in the DOM right now, to prevent Mozilla from 
		// restarting an arbitrary number of applet instances on a back button click.
		// Unfortunately this means that some clients (e.g. Opera) won't autoplay at all
		var videoElt = elt.getElementsByTagName( 'applet' )[0];
		var params = videoElt.getElementsByTagName( 'param' );
		for ( var i = 0; i < params.length; i++ ) {
			if ( params[i].name == 'autoPlay' ) {
				params[i].value = '';
				break;
			}
		}
	},

	'addParam': function ( elt, name, value ) {
		var param = document.createElement( 'param' );
		param.setAttribute( 'name', name );
		param.setAttribute( 'value', value );
		elt.appendChild( param );
	}
};

// vim: ts=4 sw=4 noet cindent :

