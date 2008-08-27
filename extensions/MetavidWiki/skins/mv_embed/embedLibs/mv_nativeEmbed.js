
var nativeEmbed = {
	instanceOf:'nativeEmbed',
	canPlayThrough:false,
    supports: {'play_head':true, 'play_or_pause':true, 'stop':true, 'fullscreen':true, 'time_display':true, 'volume_control':true},
    getEmbedHTML : function (){
		setTimeout('$j(\'#'+this.id+'\').get(0).postEmbedJS()', 150);
		//set a default duration of 30 seconds: cortao should detect duration.
		var embed_code =  this.getEmbedObj();
		js_log('EMBED CODE: ' + embed_code);
		return this.wrapEmebedContainer( embed_code);
    },
    getEmbedObj:function(){
		return '<video " ' +
					'id="'+this.pid + '" ' +
					'style="width:'+this.width+'px;height:'+this.height+'px;" ' +
				   	'src="'+this.media_element.selected_source.uri+'" ' +
				   	'controls="false" ' +
				   	'oncanplaythrough="$j(\'#'+this.id+'\').get(0).oncanplaythrough();return false;" ' +
				   	'onloadedmetadata="$j(\'#'+this.id+'\').get(0).onloadedmetadata();return false;" >' +
				'</video>';
	},
	//@@todo : loading progress
	postEmbedJS:function(){		
		this.getVID();
		if(this.vid){
			this.vid.load();	
			setTimeout('$j(\'#'+this.id+'\').get(0).monitor()',100);		
		}else{
			js_log('could not grab vid obj:' + typeof this.vid);
			setTimeout('$j(\'#'+this.id+'\').get(0).postEmbedJS()',100);	
		}		
	},	
	monitor : function(){
		this.getVID(); //make shure we have .vid obj
		js_log('time loaded: ' + this.vid.TimeRanges() );
		//update load progress and
		if( ! this.monitorTimerId ){
	    	if(document.getElementById(this.id)){
	        	this.monitorTimerId = setInterval('$j(\'#'+this.id+'\').get(0).monitor()', 1000);
	    	}
	    }
	},	
	/*
	 * native callbacks for the video tag: 
	 */
	oncanplaythrough : function(){
		js_log("f:oncanplaythrough start playback");
		this.play();
	},
	onloadedmetadata: function(){
		js_log('f:onloadedmetadata get duration');
		//this.
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
	},
	 // get the embed vlc object 
    getVID : function (){
    	this.vid = $j('#'+this.pid).get(0);  		
    }
}