// This is a global configuration object which can embed multiple video instances
var wgOggPlayer = {
	'detectionDone': false,
	'vlc-activex': false,

	// List of players in order of preference
	// Downpreffed VLC because it crashes my browser all the damn time -- TS
	'players': ['videoElement', 'oggPlugin', 'cortado', 'quicktime-mozilla', 'quicktime-activex', 'vlc-mozilla', 'vlc-activex'],

	'clientSupports': { 'thumbnail' : true },
	'savedThumbs': {},

	// Configuration from MW
	'msg': {},
	'cortadoUrl' : '',
	'smallFileUrl' : '',
	'showPlayerSelect': true,
	'controlsHeightGuess': 20, 

	// Main entry point: initialise a video player
	// Player will be created as a child of the given ID
	// There may be multiple players in a document
	'init': function ( player, id, videoUrl, width, height, length, linkUrl ) {
		elt = document.getElementById( id );

		// Save still image HTML
		if ( !(id in this.savedThumbs) ) {
			var thumb = document.createDocumentFragment();
			thumb.appendChild( elt.cloneNode( true ) );
			this.savedThumbs[id] = thumb;
		}

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
					if ( player == 'thumbnail' ) {
						// Can't select this one permanently
						player = false;
					}
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
			case 'vlc-mozilla':
				this.embedVlcPlugin( elt, videoUrl, width, height, length );
				break;
			case 'vlc-activex':
				this.embedVlcActiveX( elt, videoUrl, width, height, length );
				break;
			case 'cortado':
				this.embedCortado( elt, videoUrl, width, height, length );
				break;
			case 'quicktime-mozilla':
				this.embedQuicktimePlugin( elt, videoUrl, width, height, length );
				break;
			case 'thumbnail':
				if ( id in this.savedThumbs ) {
					elt.appendChild( this.savedThumbs[id].cloneNode( true ) );
				} else {
					elt.appendChild( document.createTextNode( 'Missing saved thumbnail for ' + id ) );
				}
				break;
			default:
				elt.innerHTML = this.msg['ogg-no-player'] + '<br/>';
				player = 'none';
		}
		if ( player != 'thumbnail' ) {
			var optionsBox = this.makeOptionsBox( player, id, videoUrl, width, height, length, linkUrl );
			var optionsLink = this.makeOptionsLink( id );
			elt.appendChild( document.createElement( 'br' ) );
			elt.appendChild( optionsBox );
			elt.appendChild( optionsLink );
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

		// ActiveX plugins
		// VLC
		if ( this.testActiveX( 'VideoLAN.VLCPlugin.2' ) ) {
			this.clientSupports['vlc-activex'] = true;
		}
		// Java
		if ( javaEnabled && this.testActiveX( 'JavaWebStart.isInstalled' ) ) {
			this.clientSupports['cortado'] = true;
		}
		// QuickTime
		/*
		if ( this.testActiveX( 'QuickTimeCheckObject.QuickTimeCheck.1' ) ) {
			// TODO: Determine if it has XiphQT somehow...
		*/

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
				if ( type == 'application/ogg' && pluginName.toLowerCase() == 'vlc multimedia plugin' ) {
					this.clientSupports['vlc-mozilla'] = true;
					continue;
				}
				if ( type == 'application/ogg' && pluginName.indexOf( 'QuickTime' ) > -1 ) {
					this.clientSupports['quicktime-mozilla'] = true;
					continue;
				}
				if ( javaEnabled && type == 'application/x-java-applet' ) {
					this.clientSupports['cortado'] = true;
					continue;
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

	'getMsg': function ( key ) {
		if ( ! (key in this.msg) ) {
			return '<' + key + '>';
		} else {
			return this.msg[key];
		}
	},

	'makeOptionsBox' : function ( selectedPlayer, id, videoUrl, width, height, length, linkUrl ) {
		var div, p, a, ul, li, button;

		div = document.createElement( 'div' );
		div.style.cssText = "width: " + ( width - 10 ) + "px; display: none;";
		div.className = 'ogg-player-options';
		div.id = id + '_options_box';
		div.align = 'center';

		ul = document.createElement( 'ul' );

		// Description page link
		if ( linkUrl ) {
			li = document.createElement( 'li' );
			a = document.createElement( 'a' );
			a.href = linkUrl;
			a.appendChild( document.createTextNode( this.msg['ogg-desc-link'] ) );
			li.appendChild( a );
			ul.appendChild( li );
		}

		// Download link
		li = document.createElement( 'li' );
		a = document.createElement( 'a' );
		a.href = videoUrl;
		a.appendChild( document.createTextNode( this.msg['ogg-download'] ) );
		li.appendChild( a );
		ul.appendChild( li );
		
		div.appendChild( ul );

		// Player list caption
		p = document.createElement( 'p' );
		p.appendChild( document.createTextNode( this.msg['ogg-use-player'] ) );
		div.appendChild( p );

		// Make player list
		ul = document.createElement( 'ul' );
		for ( var i = 0; i < this.players.length + 1; i++ ) {
			var player;
			if ( i == this.players.length ) {
				player = 'thumbnail';
			} else {
				player = this.players[i];
				// Skip unsupported players
				if ( ! this.clientSupports[player] ) {
					continue;
				}
			}

			// Make list item
			li = document.createElement( 'li' );
			if ( player == selectedPlayer ) {
				var strong = document.createElement( 'strong' );
				strong.appendChild( document.createTextNode( 
					this.msg['ogg-player-' + player] + ' ' + this.msg['ogg-player-selected'] ) );
				li.appendChild( strong );
			} else {
				a = document.createElement( 'a' );
				a.href = 'javascript:void("' + player + '")';
				a.onclick = this.makePlayerFunction( player, id, videoUrl, width, height, length, linkUrl );
				a.appendChild( document.createTextNode( this.msg['ogg-player-' + player] ) );
				li.appendChild( a );
			}
			ul.appendChild( li );
		}
		div.appendChild( ul );
		
		div2 = document.createElement( 'div' );
		div2.style.cssText = 'text-align: center;';
		button = document.createElement( 'button' );
		button.appendChild( document.createTextNode( this.msg['ogg-dismiss'] ) );
		button.onclick = this.makeDismissFunction( id );
		div2.appendChild( button );
		div.appendChild( div2 );

		return div;
	},

	'makeOptionsLink' : function ( id ) {
		var a = document.createElement( 'a' );
		a.href = 'javascript:void("options")';
		a.id = id + '_options_link';
		a.onclick = this.makeDisplayOptionsFunction( id );
		a.appendChild( document.createTextNode( this.msg['ogg-more'] ) );
		return a;
	},

	'setCssProperty' : function ( elt, prop, value ) {
		// Could use style.setProperty() here if it worked in IE
		var re = new RegExp( prop + ':[^;](;|$)' );
		if ( elt.style.cssText.search( re ) > -1 ) {
			elt.style.cssText = elt.style.cssText.replace( re, prop + ':' + value + '$1' );
		} else if ( elt.style.cssText == '' ) {
			elt.style.cssText = prop + ':' + value + ';';
		} else if ( elt.style.cssText[elt.style.cssText.length - 1] == ';' ) {
			elt.style.cssText += prop + ':' + value + ';';
		} else {
			elt.style.cssText += ';' + prop + ':' + value + ';';
		}
	},

	'makeDismissFunction' : function ( id ) {
		var this_ = this;
		return function () {
			var optionsLink = document.getElementById( id + '_options_link' );
			var optionsBox = document.getElementById( id + '_options_box' );
			this_.setCssProperty( optionsLink, 'display', 'inline' );
			this_.setCssProperty( optionsBox, 'display', 'none' );
		}
	},

	'makeDisplayOptionsFunction' : function ( id ) {
		var this_ = this;
		return function () {
			var optionsLink = document.getElementById( id + '_options_link' );
			var optionsBox = document.getElementById( id + '_options_box' );
			this_.setCssProperty( optionsLink, 'display', 'none' );
			this_.setCssProperty( optionsBox, 'display', 'block' );
		}
	},

	'makePlayerFunction' : function ( player, id, videoUrl, width, height, length, linkUrl ) {
		var this_ = this;
		return function () {
			if ( player != 'thumbnail' ) {
				document.cookie = "ogg_player=" + player;
			}
			this_.init( player, id, videoUrl, width, height, length, linkUrl );
		};
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
		this.setParam( videoElt, 'autoPlay', '' );
	},

	'embedQuicktimePlugin': function ( elt, videoUrl, width, height, length ) {
		var id = elt.id + "_obj";
		var controllerHeight = 16; // by observation
		elt.innerHTML += 
			"<object id=" + this.hq( id ) + 
			" type='video/quicktime'" +
			" width=" + this.hq( width ) + 
			" height=" + this.hq( height + controllerHeight ) + 
			
			// Use QTSRC parameter instead of data attribute to allow progressive download
			// The data attribute and src parameter point to a small file, as recommended in
			// http://developer.apple.com/documentation/QuickTime/Conceptual/QTScripting_HTML/QTScripting_HTML_Document/chapter_1000_section_6.html
			" data=" + this.hq( this.smallFileUrl ) +
			">" + 
			// Scale, don't clip
			"<param name='SCALE' value='Aspect'/>" + 
			"<param name='AUTOPLAY' value='True'/>" +
			"<param name='src' value=" + this.hq( this.smallFileUrl ) +  "/>" +
			"<param name='QTSRC' value=" + this.hq( videoUrl ) + "/>" +
			"</object>";

		// Disable autoplay on back button
		var this_ = this;
		window.setTimeout( 
			function () {
				var videoElt = document.getElementById( id );
				this_.setParam( videoElt, 'AUTOPLAY', 'False' );
			}, 3000 );
	},

	'addParam': function ( elt, name, value ) {
		var param = document.createElement( 'param' );
		param.setAttribute( 'name', name );
		param.setAttribute( 'value', value );
		elt.appendChild( param );
	},

	'setParam' : function ( elt, name, value ) {
		var params = elt.getElementsByTagName( 'param' );
		for ( var i = 0; i < params.length; i++ ) {
			if ( params[i].name.toLowerCase() == name.toLowerCase() ) {
				params[i].value = value;
				return;
			}
		}
		this.addParam( elt, name, value );
	}
};
// vim: ts=4 sw=4 noet cindent :
