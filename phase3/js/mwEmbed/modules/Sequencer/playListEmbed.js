/**
 * The playlistEmbed object code
 * Acts as a playback system for embedPlayer where the src  
 * is xml with multiple files
 *
 * Only works with "video" tag browsers 
 * 
 * @author: Michael Dale  mdale@wikimedia.org
 * @license GPL2
 * 
 * supports frame by frame rendering of "smil" and other playlist formats
 * supports basic drop frame live playback of "smil" and other playlist formats
 * 
 * Extends the "embedPlayer" and represents the playlist as a single video stream
 * 
 */
var playListEmbed = {

	// Instance Name
	instanceOf: 'playListEmbed',
	
	// Native player supported feature set
	supports: {
		'play_head': true,
		'pause': true,
		'fullscreen': false,
		'time_display': true,
		'volume_control': true,		
		'overlays': true,	
	},
	 
	/**
	* Return the embed code for the first clip in the playlist
	*  ( monitor and seek handle clip switching )
	*/
	getEmbedHTML : function () {
		var _this = this;
		var embed_code =  this.getEmbedObj();
		mw.log( "embed code: " + embed_code )
		setTimeout( function(){
			_this.postEmbedJS();
		}, 150 );
		return embed_code;
	},
	 
	/**
	* Get the native embed  code for clipset at given time and seek 
	*/
	getEmbedObj:function() {
		// Output Video tag for every clip in next 20s 
		var eb = '<video ' +
					'id="' + this.pid + '" ' +
					'style="width:' + this.width + 'px;height:' + this.height + 'px;" ' +
					'width="' + this.width + '" height="' + this.height + '" ' +
					'src="' + this.getSrc() + '" ' +				
				 '</video>';
		return eb;
	},
	
}