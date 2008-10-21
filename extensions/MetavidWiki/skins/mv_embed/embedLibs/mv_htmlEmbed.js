/* 
 * used to embed HTML as a movie clip 
 * for use with mv_playlist SMIL additions 
 * (we make assumptions about this.pc (parent clip) being available)
 */
var pcHtmlEmbedDefaults={
	'dur':4 //default duration of 4seconds
}
var htmlEmbed ={
	 supports: {
    	'play_head':true, 
    	'play_or_pause':true,     	
    	'fullscreen':false, 
    	'time_display':true, 
    	'volume_control':true,
    	
    	'overlays':true,
    	'playlist_swap_loader':true //if the object supports playlist functions    	
   	},
   	pauseTime:0,
   	start_offset:0,
   	monitorTimerId:false,
	play:function(){
		//call the parent
    	this.parent_play();
    	
		js_log('f:play: htmlEmbedWrapper');
		var ct = new Date();	
		this.clockStartTime = ct.getTime();
		
		this.monitorTimerId=false;	
		//start up monitor: 
		this.monitor();				
	},
	pause:function(){
		js_log('f:pause: htmlEmbedWrapper');
		var ct = new Date();
		this.pauseTime = this.currentTime;
		js_log('pause time: '+ this.pauseTime);				
		
		window.clearInterval( this.monitorTimerId );
	},
	//monitor just needs to keep track of time (do it at frame rate time) . 
	monitor:function(){	
		//js_log('html:monitor: '+ this.currentTime);
		var ct = new Date();	
		this.currentTime =( ( ct.getTime() - this.clockStartTime )/1000 ) +this.pauseTime;
		var ct = new Date();	
		//js_log('mvPlayList:monitor trueTime: '+ this.currentTime);										
		
		if( ! this.monitorTimerId ){
	    	if(document.getElementById(this.id)){
	    		if( !MV_ANIMATION_CB_RATE )
	    			var MV_ANIMATION_CB_RATE= 33;
	        	this.monitorTimerId = window.setInterval('$j(\'#'+this.id+'\').get(0).monitor()', MV_ANIMATION_CB_RATE);
	    	}
	    }
	},
	//set up minimal media_element emulation: 	
	media_element:{
		autoSelectSource:function(){
			return true;
		},
		selectedPlayer:{
			library:"html"
		},
		selected_source:{
			supports_url_time_encoding:true
		}
	},
	inheritEmbedObj:function(){
		return true;
	},
	//nothing to update in static html display: 
	updateTimeThumb:function(){
		return ;
	},
	getEmbedHTML:function(){
		//set up the css for our parent div: 		
		$j(this).css({'width':this.pc.pp.width, 'height':this.pc.pp.height, 'overflow':"hidden"});
		//@@todo support more smil stuff: 
		if( this.pc.type =='image/jpeg'){
			js_log('should put src: '+this.pc.src);
			$j(this).html('<img src="'+this.pc.src+'">');
		}else{
			$j(this).html(this.pc.wholeText);
		}
	},
	/* since its just html display get the "embed" right away */
	getHTML:function(){
		js_log('getHTML: htmlEmbed');
		this.getEmbedHTML();
	},
	getDuration:function(){
		if(this.pc.dur)
			return this.pc.dur;
		//set duration (depreciated all .duration calls should get from getDuration)
		this.duration=pcHtmlEmbedDefaults.dur;
		//no dur use default: 
		return pcHtmlEmbedDefaults.dur;		
	},	
	//gives a chance to make any nesseary external requests
	//@@todo we can "start loading images" if we want
	on_dom_swap:function(){
		this.loading_external_data=false
		this.ready_to_play=true;
		return ;
	}	
}