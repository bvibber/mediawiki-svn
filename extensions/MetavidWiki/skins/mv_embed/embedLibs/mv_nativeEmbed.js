
var nativeEmbed = {
	instanceOf:'nativeEmbed',
	canPlayThrough:false,
    supports: {
    	'play_head':true, 
    	'play_or_pause':true,     	
    	'fullscreen':false, 
    	'time_display':true, 
    	'volume_control':true,
    	
    	'overlays':true,
    	'playlist_driver':true //if the object supports playlist functions
    },
    getEmbedHTML : function (){
		setTimeout('$j(\'#'+this.id+'\').get(0).postEmbedJS()', 150);
		//set a default duration of 30 seconds: cortao should detect duration.
		var embed_code =  this.getEmbedObj();
		js_log('embed code: ' + embed_code);
		js_log("DURATION: "+ this.getDuration() );
		return this.wrapEmebedContainer( embed_code);
    },
    getEmbedObj:function(){
		return '<video " ' +
					'id="'+this.pid + '" ' +
					'style="width:'+this.width+'px;height:'+this.height+'px;" ' +
					'width="'+this.width+'" height="'+this.height+'" '+
				   	'src="'+this.media_element.selected_source.uri+'" ' +
				   	'controls="false" ' +
				   	'oncanplaythrough="$j(\'#'+this.id+'\').get(0).oncanplaythrough();return false;" ' +
				   	'onloadedmetadata="$j(\'#'+this.id+'\').get(0).onloadedmetadata();return false;" ' + 
				   	'loadedmetadata="$j(\'#'+this.id+'\').get(0).onloadedmetadata();return false;" >' +
				'</video>';
	},
	//@@todo : loading progress
	postEmbedJS:function(){		
		this.getVID();
		if(this.vid){
			this.vid.play();
			//this.vid.load(); //does not seem to work so well	
			setTimeout('$j(\'#'+this.id+'\').get(0).monitor()',100);		
		}else{
			js_log('could not grab vid obj:' + typeof this.vid);
			setTimeout('$j(\'#'+this.id+'\').get(0).postEmbedJS()',100);	
		}		
	},	
	monitor : function(){
		this.getVID(); //make shure we have .vid obj
		js_log('time loaded: ' + this.vid.TimeRanges );
		js_log('current time: '+ this.vid.currentTime  + ' dur: ' + this.duration);
			
		//update duration if not set
		this.duration =(this.vid.duration==0)?this.getDuration():this.vid.duration;
				
		//update pointers (should just have a loop): 
		this.currentTime = this.vid.currentTime;
		
		if( this.currentTime > 0 ){
			if(!this.userSlide){
				this.setSliderValue(this.currentTime/this.duration );
				this.setStatus( seconds2ntp(this.currentTime) + '/'+ seconds2ntp(this.duration));
			}else{
				this.setStatus('seek to: ' + seconds2ntp(Math.round( (this.sliderVal*this.duration)) ));
			}
		}					
		//update load progress if nessisary 
		if( ! this.monitorTimerId ){
	    	if(document.getElementById(this.id)){
	        	this.monitorTimerId = setInterval('$j(\'#'+this.id+'\').get(0).monitor()', 250);
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
		js_log('f:onloadedmetadata get duration: ' +this.vid.duration);
		//this.
	},
	onloadedmetadata: function(){
		js_log('f:onloadedmetadata metadata ready');
		//set the clip duration 
	},
	pause : function(){		
		this.vid.pause();
		//stop updates: 
		if( this.monitorTimerId != 0 )
	    {
	        clearInterval(this.monitorTimerId);
	        this.monitorTimerId = 0;
	    }
	},
	play:function(){
		this.getVID();
		if(!this.vid || this.thumbnail_disp){
			this.parent_play();
		}else{			
			this.vid.play();
			//re-start the monitor: 
			this.monitor();
		}
	},
	// get the embed vlc object 
    getVID : function (){
    	this.vid = $j('#'+this.pid).get(0);  		
    }
}