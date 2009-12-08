/* 
* Simple embed object for unknown application/ogg plugin 
*/
var genericEmbed = {
	// List of supported features of the generic plugin
	 supports: {	 	
		'play_head':false,
		'pause':false,
		'stop':true,
		'fullscreen':false,
		'time_display':false,
		'volume_control':false
	},
	// Instance name: 
	instanceOf:'genericEmbed',
	/*
	* Generic embed html
	*
	* @return {String}
	* 	embed code for genneric ogg plugin 
	*/
	getEmbedHTML:function() {
		return '<object type="application/ogg" ' +
			'width="' + this.width + '" height="' + this.height + '" ' +
			'data="' + this.getSrc( this.seek_time_sec ) + '"></object>';
	}
};
