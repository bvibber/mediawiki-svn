
var nativeEmbed = {
	instanceOf:'nativeEmbed',
    supports: {'play_head':true, 'play_or_pause':true, 'stop':true, 'fullscreen':true, 'time_display':true, 'volume_control':true},
    getEmbedHTML : function (){
		setTimeout('document.getElementById(\''+this.id+'\').postEmbedJS()', 150);
		//set a default duration of 30 seconds: cortao should detect duration.
		return this.wrapEmebedContainer( this.getEmbedObj() );
    },
    getEmbedObj:function(){
		return '<video " ' +
						'id="'+this.pid + '" ' +
						'style="width:'+this.width+';height:'+this.height+';" ' +
					   	'src="'+this.media_element.selected_source.uri+'" >' +
				'</video>';
	},
	postEmbedJS:function(){
		document.getElementById(this.pid).play();
	},
	pause : function(){
		document.getElementById(this.pid).pause();
	},
	play:function(){
		if(!document.getElementById(this.pid) || this.thumbnail_disp){
			this.parent_play();
		}else{
			document.getElementById(this.pid).play();
		}
	}
}