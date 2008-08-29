
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
				
		//update currentTime
		this.currentTime = this.vid.currentTime;
		
		//check for overlays to run or stop
		this.doSmilTransitionOverlays();	
		
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
	playMovieAt:function(order){
		js_log('f:playMovieAt '+order);
		this.play();
	},
	// get the embed vlc object 
    getVID : function (){
    	this.vid = $j('#'+this.pid).get(0);  		
    },
    //handles the rendering of overlays loaind of future clips (if nessesary)
    //@@todo could be lazy loaded if nessesary 
    doSmilTransitionOverlays: function(){    	
   		if(this.pc){
			_pClip = this.pc;					
			if(_pClip.transIn){
				js_log('transIn exist see if we are in time range');
				if(_pClip.transIn.dur){
					//only run if the animation_done is not done yet: 
					if(!_pClip.transIn.animation_done){ //make sure the animation is not done
						js_log('transIn duration is: '+ _pClip.transIn.dur);
						if(this.currentTime < _pClip.transIn.dur){
							var overlay_selector= 'transIn_'+_pClip.id;
							if( ! $j(overlay_selector).get(0) )
								$j('mv_ebct_'+this.id).append(''+
									'<div id="'+overlay_selector+'" ' +
										'style="position:relative;top:0px;left:0px;' +
										'height:'+this.height+'px;'+
										'width:'+this.width+'px;">' +
									'</div>');							
							//start transition for remaining time
							var offSetTime = 0; //future think about what time info we need to send
							var tran_function = getTransitionFunction(_pClip.transIn,overlay_selector, offSetTime)
							//special case of cross fading clips:
							js_log('tran function: '+tran_function);
							debugger;
						}
					}							
				}						
			}					
		}
    }
}
/*
 * get the transition function 
 * @param tObj transition function
 * @param overlay_selector the div or media element to apply the css sytle to
 * @param (optional) offSetTime how much time has already passed (ie can we run the full transition) 
 */
 /*
 * Smil Transition Effects see:  
 * http://www.w3.org/TR/SMIL3/smil-transitions.html#TransitionEffects-TransitionAttribute
 */			
function getTransitionFunction(tObj, overlay_selector, offSetTime){
		if(!tObj.type)
			return js_log('transition is missing type or type attribute');
		var _this = this;		
		//function lookup: 
		switch(tObj.type){
			case 'fade':
				if(!tObj.subtype)
					return js_log("fade transition is missing sub-type");
				if(!tObj.dur)
					return js_log('missing transition duration');							
					
				switch(tObj.subtype){
					case 'fadeFromColor':
						if(!tObj.fadeColor)
							return js_log('missing fadeColor');							
											
						return function(){
							//set the initial state
							$j(overlay_selector).css({
								'background-color':_tObj.fadeColor
							});
							//annimate the transition
							$j(overlay_selector).animate(
								{
		      						"opacity" : "0"
			    				}, 
			    				{
		    						"duration" : ( tObj.dur - offSetTime ) 
		    					}, 
		    					'linear',		    				
		    					function(){ //callback
		    						tObj.animation_done=true;
		    					}
		    				);
						}
					break;
					case 'fadeToColor':
						
					break;	
				}
			break; // fade type	
		}
		//get transition type
		
	}