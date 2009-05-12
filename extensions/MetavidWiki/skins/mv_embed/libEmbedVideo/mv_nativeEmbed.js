//native embed library:
var nativeEmbed = {
	instanceOf:'nativeEmbed',
	canPlayThrough:false,
	grab_try_count:0,
	onlyLoadFlag:false,	
    supports: {
    	'play_head':true, 
    	'pause':true,     	
    	'fullscreen':false, 
    	'time_display':true, 
    	'volume_control':true,
    	
    	'overlays':true,
    	'playlist_swap_loader':true //if the object supports playlist functions    	
   },
    getEmbedHTML : function (){		    		
		var embed_code =  this.getEmbedObj();
		js_log("embed code: " + embed_code)				
		setTimeout('$j(\'#' + this.id + '\').get(0).postEmbedJS()', 150);
		return this.wrapEmebedContainer( embed_code);		
    },
    getEmbedObj:function(){
    	//we want to let mv_embed handle the controls so notice the absence of control attribute
    	// controls=false results in controls being displayed: 
    	//http://lists.whatwg.org/pipermail/whatwg-whatwg.org/2008-August/016159.html    	
    	js_log("native play url:" + this.getURI( this.seek_time_sec ));
		var eb = '<video ' +
					'id="' + this.pid + '" ' +
					'style="width:' + this.width+'px;height:' + this.height + 'px;" ' +
					'width="' + this.width + '" height="'+this.height+'" '+
				   	'src="' + this.media_element.selected_source.getURI( this.seek_time_sec ) + '" ';
				   	
		if(!this.onlyLoadFlag)
			eb+=	'autoplay="'+this.autoplay+'" ';
			
		//continue with the other attr: 				   	
		eb+=		'oncanplaythrough="$j(\'#'+this.id+'\').get(0).oncanplaythrough();return false;" ' +
				   	'onloadedmetadata="$j(\'#'+this.id+'\').get(0).onloadedmetadata();return false;" ' + 
				   	'loadedmetadata="$j(\'#'+this.id+'\').get(0).onloadedmetadata();return false;" ' +
				   	'onprogress="$j(\'#'+this.id+'\').get(0).onprogress( event );return false;" '+
				   	'onended="$j(\'#'+this.id+'\').get(0).onended();return false;" >' +
				'</video>';
		return eb;
	},
	//@@todo : loading progress	
	postEmbedJS:function(){
		js_log("f:native:postEmbedJS:");		
		this.getVID();
		if(typeof this.vid != 'undefined'){			
			//always load the media:
			if( this.onlyLoadFlag ){ 
				this.vid.load();
			}else{						 
				this.vid.play();
			}
							
			setTimeout('$j(\'#'+this.id+'\').get(0).monitor()',100);		
		}else{
			js_log('could not grab vid obj trying again:' + typeof this.vid);
			this.grab_try_count++;
			if(	this.grab_count == 10 ){
				js_log(' could not get vid object after 10 tries re-run: getEmbedObj()' ) ;						
			}else{
				setTimeout('$j(\'#'+this.id+'\').get(0).postEmbedJS()',100);
			}			
		}
	},	
	doSeek:function(perc){				
		js_log('native:seek:p: ' + perc+ ' : '  + this.supportsURLTimeEncoding() + ' dur: ' + this.getDuration() + ' sts:' + this.seek_time_sec );
		
		//@@todo check if the clip is loaded here (if so we can do a local seek)
		if( this.supportsURLTimeEncoding() ){			
			this.parent_doSeek(perc);
		}else if( this.vid.duration ){					
			this.vid.currentTime = perc * this.vid.duration;
			
		}
	},
	monitor : function(){
		this.getVID(); //make shure we have .vid obj
		if(!this.vid){
			js_log('could not find video embed: '+this.id + ' stop monitor');
			this.stopMonitor();			
			return false;
		}		
		//don't update status if we are not the current clip (playlist leekage?) .. should move to playlist overwite of monitor? 
		if(this.pc){
			if(this.pc.pp.cur_clip.id != this.pc.id)
				return true;
		}								
				
		//update currentTime				
		this.currentTime = this.vid.currentTime;		
		
		if( this.startOffset && !embedTypes.safari) //safari uses presentation time for currentTime rather than ogg Encoded time
			this.currentTime = this.currentTime - this.startOffset;
		
		//js_log('this.currentTime: ' + this.currentTime );
		//once currentTime is updated call parent_monitor
		this.parent_monitor();					
	},	
	/*
	 * native callbacks for the video tag: 
	 */
	oncanplaythrough : function(){		
		js_log('f:oncanplaythrough');
	},
	onloadedmetadata: function(){
		this.getVID();
		js_log('f:onloadedmetadata metadata ready (update duration)');		
		//update duration if not set (for now trust the getDuration more than this.vid.duration		
		this.duration = ( this.getDuration() ) ?this.getDuration() : this.vid.duration;
	},
	onprogress: function(e){		
		this.bufferedPercent =   e.loaded / e.total;
		//js_log("onprogress:" +e.loaded + ' / ' +  (e.total) + ' = ' + this.bufferedPercent);
	},
	onended:function(){		
		js_log('native:onended:');
		this.onClipDone();
	},
	pause : function(){		
		this.getVID();
		this.parent_pause(); //update interface
		if(this.vid){
			this.vid.pause();
		}
		//stop updates: 
		this.stopMonitor();
	},
	play:function(){
		this.getVID();
		this.parent_play(); //update interface
		if( this.vid ){
			this.vid.play();
			//re-start the monitor: 
			this.monitor();
		}
	},
	load:function(){
		this.getVID();
		if( !this.vid ){
			//no vid loaded
			js_log('native::load() ... doEmbed');
			this.onlyLoadFlag = true;
			this.doEmbedHTML();
		}else{
			//won't happen offten
			this.vid.load();
		}
	},
	// get the embed vlc object 
    getVID : function (){
    	this.vid = $j('#'+this.pid).get(0);  		
    },  
    /* 
     * playlist driver      
     * mannages native playlist calls          
     */    
}
