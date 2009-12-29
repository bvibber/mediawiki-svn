/**
 * The mw.PlayList object code 
 * 
 * @author: Michael Dale  mdale@wikimedia.org
 * @license GPL2
 * 
 * supports frame by frame rendering of "smil" and other playlist formats
 * supports basic drop frame live playback of "smil" and other playlist formats
 * 
 * Extends the "embedPlayer" and represents the playlist as a single video stream
 */

// Bind to mw (for uncluttered global namespace) 
( function( $ ) {				
	
mw.PlayList = function( element ) {
	return this.init( element );
};
// set up the mvPlaylist object
mw.PlayList.prototype = {
	init: function( element ) {		
		var myPlayer = new mw.EmbedPlayer( element );
		// Inherit "embedPlayer" 
		for( var i in myPlayer ){
			if( this[i] ){
				this['parent_' + i] = myPlayer[i]; 		
			}else{
				this[i] = myPlayer[i];
			}
		}		
	},
	// Check, parse and load player sources
	checkPlayerSources: function(){
		var _this = this;
		// Get the first xml source (as potential playlist source)
		var source = this.mediaElement.getSources( 'text/xml' )[0];		
		if( !source ){		
			mw.log("Error: could not load playlist source");
			return ;
		}
		// Setup localpointer
		this.source = new mw.PlayListSource( source );
		
		// Load and parse
		this.source.load( function(){			
			mw.log( 'Loaded source:' + this.source )					
			// Show player interface:
			_this.showPlayer();
		});			
	},
	/**
	 * Get the duration for the playlist
	 */
	getDuration: function(){
		
	},
	/**
	 * Show the player interface
	 */
	 showPlayer: function(){
	 	// Update duration:
		_this.getDuration();
		// Add playlist title
		
		// Add 
	 }
			
}

/**
 * Extends the base "source" type with playlist source 
 */
mw.PlayListSource = function( source ){
	return this.init( source )
}
mw.PlayListSource.prototype = {
	/**
	 * @constructor 
	 * @param {Object} source Source object to extend
	 */
	init: function( source ){
		for(var i in source){
			this[i] = source[i];
		}
	},
	/**
	 * Function to load and parse the source xml
	 * @param {Function} callback Function called once text source is loaded
	 */
	load: function( callback ){
		var _this = this;
					
		//check if its already loaded:
		if( _this.loaded ){
			if( callback ){ 
				callback();
			}
		}							
		// Try to load src via src attr:			
		if( this.getSrc() ){
			// Issue the direct load request ( if we can ) 
			if ( mw.parseUri( document.URL ).host != mw.parseUri( this.getSrc() ).host ){
				mw.log("Error: host mis-match: " + mw.parseUri( document.URL ).host != mw.parseUri( this.getSrc() ).host )
				return ;
			}
			$j.get( this.getSrc(), function( data ){		
				// set the local data
				_this.data = data;
				// Set the parser handle
				_this.setParserHandle();				
				// Parse and load dependencies
				
				// Update the loaded state:
				_this.loaded = true;
				if( callback ){ 
					callback();
				}
			}, 'text' );
			return ;
		}				
	},
	
	/**
	 * Update src parser handler for given loaded data in this.data 
	 */
	setParserHandle: function( ) {
		mw.log( 'data type of: ' + this.src + ' = ' + typeof ( this.data ) + "\n" + this.data );
		this.parseHandle = null;
		// if not external use different detection matrix
		if ( typeof this.data == 'object' ) {
			mw.log( 'object' );
			// object assume xml (either xspf or rss) 
			plElm = this.data.getElementsByTagName( 'playlist' )[0];
			if ( plElm ) {
				if ( plElm.getAttribute( 'xmlns' ) == 'http://xspf.org/ns/0/' ) {
					this.parseHandle = 'xspf';
				}
			}
			// check itunes style rss "items" 
			rssElm = this.data.getElementsByTagName( 'rss' )[0];
			if ( rssElm ) {
				if ( rssElm.getAttribute( 'xmlns:itunes' ) == 'http://www.itunes.com/dtds/podcast-1.0.dtd' ) {
					this.parseHandle = 'itunes';
				}
			}
			// check for smil tag: 
			smilElm = this.data.getElementsByTagName( 'smil' )[0];
			if ( smilElm ) {
				this.parseHandle = 'smil';
			}
		} else if ( typeof this.data == 'string' ) {
			mw.log( 'String' );
			// look at the first line: 
			var first_line = this.data.substring( 0, this.data.indexOf( "\n" ) );
			mw.log( 'first line: ' + first_line );
			// string
			if ( first_line.indexOf( '#EXTM3U' ) != -1 ) {
				this.parseHandle = 'm3u';
			} else if ( first_line.indexOf( '<smil' ) != -1 ) {
				// @@todo parse string
				this.parseHandle = 'smil';
			}
		}
		if ( this.parseHandle ) {
			mw.log( 'is of type:' + this.srcType );	
		} else {
			// unknown playlist type
			mw.log( 'unknown playlist type?' );
		}
	}
}


/* 
 *  m3u parse
 */
var m3uPlaylist = {
	doParse:function() {
		// for each line not # add as clip 
		var inx = 0;
		var _this = this;
		// mw.log('data:'+ this.data.toString());
		$j.each( this.data.split( "\n" ), function( i, n ) {
			// mw.log('on line '+i+' val:'+n+' len:'+n.length);
			if ( n.charAt( 0 ) != '#' ) {
				if ( n.length > 3 ) {
					// @@todo make sure its a valid url
					// mw.log('add url: '+i + ' '+ n);
					var cur_clip = new mvClip( { type:'srcClip', id:'p_' + _this.id + '_c_' + inx, pp:this_pl, src:n, order:inx } );
					// setup the embed object 
					cur_clip.setUpEmbedObj();
					mw.log( 'm3uPlaylist len:' + thisClip.embed.mediaElement.sources.length );
					_this.addCliptoTrack( cur_clip );
					inx++;
				}
			}
		} );
		return true;
	}
}
/**
 * itunes Playlist parser
 */
var itunesPlaylist = {
	doParse:function() {
		var properties = { 
			title: 'title', 
			linkback: 'link',
			author: 'itunes:author', 
			desc: 'description',
			date:'pubDate' 
		};
		var tmpElm = null;
		for ( i in properties ) {
			tmpElm = this.data.getElementsByTagName( properties[i] )[0];
			if ( tmpElm ) {
				this[i] = tmpElm.childNodes[0].nodeValue;
				// mw.log('set '+i+' to '+this[i]);
			}
		}
		// image src is nested in itunes rss:
		tmpElm = this.data.getElementsByTagName( 'image' )[0];
		if ( tmpElm ) {
			imgElm = tmpElm.getElementsByTagName( 'url' )[0];
				if ( imgElm ) {
					this.img = imgElm.childNodes[0].nodeValue;
				}
		}
		// get the clips: 
		var clips = this.data.getElementsByTagName( "item" );
		properties.src = 'guid';
		for ( var i = 0; i < clips.length; i++ ) {
			var cur_clip = new mvClip( { type:'srcClip', id:'p_' + this.id + '_c_' + i, pp:this, order:i } );
			for ( var j in properties ) {
				tmpElm = clips[i].getElementsByTagName( properties[j] )[0];
				if ( tmpElm != null ) {
					cur_clip[j] = tmpElm.childNodes[0].nodeValue;
					// mw.log('set clip property: ' + j+' to '+cur_clip[j]);
				}
			}
			// image is nested
			tmpElm = clips[i].getElementsByTagName( 'image' )[0];
			if ( tmpElm ) {
				imgElm = tmpElm.getElementsByTagName( 'url' )[0];
					if ( imgElm ) {
						cur_clip.img = imgElm.childNodes[0].nodeValue;
					}
			}
			// set up the embed object now that all the values have been set
			cur_clip.setUpEmbedObj();
			
			// add the current clip to the clip list
			this.addCliptoTrack( cur_clip );
		}
		return true;
	}
}

/* 
 * xsfp playlist parser: 
 * http://www.xspf.org/xspf-v1.html
 */
var xspfPlaylist = {
	doParse:function() {
		// mw.log('do xsfp parse: '+ this.data.innerHTML);
		var properties = { title:'title', linkback:'info',
						   author:'creator', desc:'annotation',
						   poster:'image', date:'date' };
		var tmpElm = null;
		// get the first instance of any of the meta tags (ok that may be the meta on the first clip)
		// mw.log('do loop on properties:' + properties);
		for ( i in properties ) {
			mw.log( 'on property: ' + i );
			tmpElm = this.data.getElementsByTagName( properties[i] )[0];
			if ( tmpElm ) {
				if ( tmpElm.childNodes[0] ) {
					this[i] = tmpElm.childNodes[0].nodeValue;
					mw.log( 'set pl property: ' + i + ' to ' + this[i] );
				}
			}
		}
		var clips = this.data.getElementsByTagName( "track" );
		mw.log( 'found clips:' + clips.length );
		// add any clip specific properties 
		properties.src = 'location';
		for ( var i = 0; i < clips.length; i++ ) {
			var cur_clip = new mvClip( { id:'p_' + this.id + '_c_' + i, pp:this, order:i } );
			// mw.log('cur clip:'+ cur_clip.id);
			for ( var j in properties ) {
				tmpElm = clips[i].getElementsByTagName( properties[j] )[0];
				if ( tmpElm != null ) {
					if ( tmpElm.childNodes.length != 0 ) {
						cur_clip[j] = tmpElm.childNodes[0].nodeValue;
						mw.log( 'set clip property: ' + j + ' to ' + cur_clip[j] );
					}
				}
			}
			// add mvClip ref from info link: 
			if ( cur_clip.linkback ) {
				// if mv linkback
				mvInx = 'Stream:';
				mvclippos = cur_clip.linkback.indexOf( mvInx );
				if ( mvclippos !== false ) {
					cur_clip.mvclip = cur_clip.linkback.substr( mvclippos + mvInx.length );
				}
			}
			// set up the embed object now that all the values have been set
			cur_clip.setUpEmbedObj();
			// add the current clip to the clip list
			this.addCliptoTrack( cur_clip );
		}
		// mw.log('done with parse');
		return true;
	}
}


} )( window.mw );