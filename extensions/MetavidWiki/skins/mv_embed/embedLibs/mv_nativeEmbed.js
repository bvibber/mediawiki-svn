
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
    	'playlist_swap_loader':true //if the object supports playlist functions    	
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
    	//we want to let mv_embed handle the controls so notice the absence of control attribute
    	// controls=false results in controls being displayed: 
    	//http://lists.whatwg.org/pipermail/whatwg-whatwg.org/2008-August/016159.html    	
		return '<video ' +
					'id="'+this.pid + '" ' +
					'style="width:'+this.width+'px;height:'+this.height+'px;" ' +
					'width="'+this.width+'" height="'+this.height+'" '+
				   	'src="'+this.media_element.selected_source.uri+'" ' +				   	
				   	'oncanplaythrough="$j(\'#'+this.id+'\').get(0).oncanplaythrough();return false;" ' +
				   	'onloadedmetadata="$j(\'#'+this.id+'\').get(0).onloadedmetadata();return false;" ' + 
				   	'loadedmetadata="$j(\'#'+this.id+'\').get(0).onloadedmetadata();return false;" ' +
				   	'onended="$j(\'#'+this.id+'\').get(0).onended();return false;" >' +
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
		//js_log('native:monitor');
		this.getVID(); //make shure we have .vid obj
		//js_log('time loaded: ' + this.vid.TimeRanges );
		//js_log('current time: '+ this.vid.currentTime  + ' dur: ' + this.duration);
		
		//update duration if not set (for now trust the getDuration more than this.vid.duration		
		this.duration =(this.getDuration())?this.getDuration():this.vid.duration;
				
		//update currentTime
		this.currentTime = this.vid.currentTime;
		
		//update the start offset:
		if(!this.start_offset)
			this.start_offset=this.media_element.selected_source.start_offset;	
		
		//don't update status if we are not the current clip
		if(this.pc.pp.cur_clip.id != this.pc.id)
			return true;
		
		//only update the interface if controls have been included:	
		if( this.currentTime > 0 ){
			if(!this.userSlide){
				if(this.currentTime > this.duration){//we are likely viewing a annodex stream add in offset
					this.setSliderValue((this.currentTime-this.start_offset)/this.duration);			
					this.setStatus( seconds2ntp(this.currentTime) + '/'+ seconds2ntp(this.start_offset+this.duration ));		
				}else{
					this.setSliderValue(this.currentTime/this.duration );
					this.setStatus( seconds2ntp(this.currentTime) + '/'+ seconds2ntp(this.duration ));
				}				
			}else{
				this.setStatus('seek to: ' + seconds2ntp(Math.round( (this.sliderVal*this.duration)) ));
			}
		}					
		//update load progress if nessisary f
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
		//this.play();
	},
	onloadedmetadata: function(){
		js_log('f:onloadedmetadata get duration: ' +this.vid.duration);
		//this.
	},
	onloadedmetadata: function(){
		js_log('f:onloadedmetadata metadata ready');
		//set the clip duration 
	},
	onended:function(){
		//clip "ended" 
		js_log('f:onended ');
		//stop monitor
		this.stopMonitor();
		this.stop();
	},
	stopMonitor:function(){
		if( this.monitorTimerId != 0 )
	    {
	        clearInterval(this.monitorTimerId);
	        this.monitorTimerId = 0;
	    }
	},
	pause : function(){		
		this.vid.pause();
		//stop updates: 
		this.stopMonitor();
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
	playMovieAt:function(order){
		js_log('f:playMovieAt '+order);
		this.play();
	},
	// get the embed vlc object 
    getVID : function (){
    	this.vid = $j('#'+this.pid).get(0);  		
    },  
    /* 
     * playlist driver      
     * mannages native playlist calls          
     */
    playlistNext:function(){
    	if(!this.pc){//make sure we are a clip
    		//
    		
    	}
    }
}