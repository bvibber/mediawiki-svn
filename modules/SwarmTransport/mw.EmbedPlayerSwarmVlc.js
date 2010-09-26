/*
* Swarm VLC embed
* inherits  EmbedPlayerVlc
*/
mw.EmbedPlayerSwarmVlc = {

	//Instance Name: 
	instanceOf : 'SwarmVlc',
	
	doEmbedHTML: function() {
		var _this = this;		
		var oggHttpSource = this.mediaElement.getSources( 'video/ogg' )[0];
		$j( this ).html(
			'<object classid="clsid:98FF91C0-A3B8-11DF-8555-0002A5D5C51B" ' +				
				'id="' + this.pid + '" events="True" height="' + this.getHeight() + '" width="' + this.getWidth() + '"' +
				'>' +					
					'<param name="ShowDisplay" value="True" />' +
					'<param name="AutoLoop" value="False" />' +
					'<param name="AutoPlay" value="True" />' +
					'<param name="Volume" value="'+ this.volume * 100 + '" />' +
					'<param name="Src" value="' + this.getSrc() +'" />' +
					'<param name="AltSrc" value="' + mw.absoluteUrl( oggHttpSource.getSrc() ) +	'" />' +
			'</object>'
		);
		setTimeout( function() {
			_this.monitor();
		}, 100 );
	}
};

// Inherit the vlc object 
if( typeof mw.EmbedPlayerVlc == 'undefined' ){
	mw.log("Error:: EmbedPLayerVlc not defefined ");
} else {
	for( var i in mw.EmbedPlayerVlc ){
		if( !mw.EmbedPlayerSwarmVlc[ i ] ){
			mw.EmbedPlayerSwarmVlc[ i ] = mw.EmbedPlayerVlc[i];
		}
	};
}
