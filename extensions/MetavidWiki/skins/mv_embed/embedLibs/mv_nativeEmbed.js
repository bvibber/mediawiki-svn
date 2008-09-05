
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
		return '<video " ' +
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
		
		//does smili based actions
		this.doSmilActions();
		//if in playlist mode make sure we are the "current_clip"
		if(this.pc){
			if(this.pc.pp.cur_clip.embed.id!=this.id){
				js_log("no status updates for "+this.id + ' not current clip');				
				return ;
			}
		}
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
    //handles the rendering of overlays loaind of future clips (if nessesary)
    //@@todo could be lazy loaded if nessesary 
    doSmilActions: function(){    	
   		if(this.pc){ //make sure we haev a parent clip:
   			var offSetTime = 0; //offset time should let us start a transition later on if we have to. 
			_pClip = this.pc;	
			
			//@@todo move some of this out of this loop
			if(!_pClip.dur)
				_pClip.dur = this.duration; 
			
			//check for duration actions / clip freze mode
			if(_pClip.dur <= this.currentTime  && _pClip.order != _pClip.pp.getClipCount()-1 ){
				//force next clip
				js_log('order:' +_pClip.order + ' != count:' + (_pClip.pp.getClipCount()-1) +
					' smil dur: '+_pClip.dur + ' <= curTime: ' + this.currentTime + ' go to next clip..');
				_pClip.pp.next();
			}
			
			if(_pClip.dur >= this.currentTime ){
				//@@todo freeze on onClipStop
			}
			
			if(_pClip.begin){
				//@@todo do freeze until begin time
			}
			
			//check future clip for transitions (assume we are the current clip) 
			/*var next_pClip = _pClip.pp.getClip(1);			
			if(next_pClip.id!=_pClip.id){ //make sure we are not transitioning to the current clip
				if(next_pClip.transIn){
					if(next_pClip.transIn.subtype=='crossfade'){					
						//apply the effect to the end of "this"
						if(this.currentTime > (this.duration - next_pClip.transIn.dur) ){
							js_log("do clip: cross fade now: "+(this.duration - next_pClip.transIn.dur) );
							if(!_pClip['trans_corssfadeOut']){
								//make new trans_corssfadeOut (special for cross fade)
								_pClip['trans_corssfadeOut']=_pClip.pp.transitions[next_pClip.transIn.id].clone();
								_pClip.trans_corssfadeOut.cfout=true;
							}						
							var overlay_selector_id = 'clipDesc_'+_pClip.id;			
							mvTransLib.doTransition(_pClip.trans_corssfadeOut, overlay_selector_id, offSetTime )																		
						}
						
					}
				}
			}*/
			//js_log("check for transOut: ct:"+this.currentTime + ' not >  dur:'+_pClip.dur+'-'+'cdur:'+  _pClip.transOut.dur +' = '+ (_pClip.dur - _pClip.transOut.dur));
			if(_pClip.transOut){				
				if(this.currentTime > (_pClip.dur - _pClip.transOut.dur) ){					
					if(_pClip.transOut.animation_state==0){
						js_log("RUN transOut: ct:"+this.currentTime + ' > ' + (_pClip.dur - _pClip.transOut.dur));
						_pClip.transOut.animation_state=1;//running transition
						if(_pClip.transOut.subtype=='crossfade'){
							//make sure the "next" clip is visiable
							var next_pClip = _pClip.pp.getClip(1);
							//start playing the clip	
							next_pClip.embed.play();							
							//alert('opacity: '+$j('#clipDesc_'+next_pClip.id).css('opacity'));
							var overlay_selector_id = 'clipDesc_'+next_pClip.id;
							js_log("do transOUT for: "+overlay_selector_id);
							mvTransLib.doTransition(_pClip.transOut, overlay_selector_id, offSetTime );
						}
					}else if(_pClip.transOut.animation_state==2){
						this.stop();
					}
				}
			}
			if(_pClip.transIn){
				if(this.currentTime < _pClip.transIn.dur){
					if(_pClip.transIn.animation_state==0){
						_pClip.transIn.animation_state=1;//running transition						
						js_log("RUN transIn ");												
						//@@todo process special case when overlay is subsequent clip							
						if(_pClip.transIn.subtype=='crossfade'){
							//check if prev clip pid exist:
							var prev_clip = _pClip.pp.getClip(-1);															
							var overlay_selector_id = 'clipDesc_'+prev_clip.id;
						}else{
							var overlay_selector_id =this.getOverlaySelector(_pClip, 'transIn_');																
						}						
						js_log('selector element: '+$j('#'+overlay_selector_id).length);
						//start transition for remaining time
						
						//var tran_function = getTransitionFunction(_pClip.transIn,overlay_selector_id, offSetTime)
						mvTransLib.doTransition(_pClip.transIn, overlay_selector_id, offSetTime )
					}
					//special case of cross fading clips:
					//js_log('tran function: '+tran_function);		
					//debugger;					
				}											
			}					
		}
    },    
    getOverlaySelector:function(_pClip, pre_var){
		var overlay_selector_id= pre_var+_pClip.id; 	
		if( ! $j('#'+overlay_selector_id).get(0) ){																											
			$j('#mv_ebct_'+_pClip.pp.id).prepend(''+
				'<div id="'+overlay_selector_id+'" ' +
					'style="position:absolute;top:0px;left:0px;' +
					'height:'+this.height+'px;'+
					'width:'+this.width+'px;' +					
					'z-index:2">' +
				'</div>');
		}	
		return overlay_selector_id;	
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
/*
 * mvTransLib libary of transitions
 * a single object called to initiate transition effects can easily be extended in separate js file
 * (that way a limited feature set "sequence" need not include a _lot_ of js unless necessary )
 * 
 * Smil Transition Effects see:  
 * http://www.w3.org/TR/SMIL3/smil-transitions.html#TransitionEffects-TransitionAttribute
 */    		
var mvTransLib = {
	/*
	 * function doTransition lookups up the transition in the  mvTransLib obj
	 * 		and intiates the transition if its avaliable 
	 * @param tObj transition attribute object
	 * @param offSetTime default value 0 if we need to start rendering from a given time 
	 */
	doTransition:function(tObj, overlay_selector_id, offSetTime){		
		if(!tObj.type)
			return js_log('transition is missing type attribute');
		
		if(!tObj.subtype)
			return js_log('transition is missing subtype attribute');
		
		if(!this['type'][tObj.type])
			return js_log('mvTransLib does not support type: '+tObj.type);
		
		if(!this['type'][tObj.type][tObj.subtype])
			return js_log('mvTransLib does not support subType: '+tObj.subtype);				
							
		//has type and subype call function with params:  
		this['type'][tObj.type][tObj.subtype](tObj,overlay_selector_id, offSetTime);					
	},
	type:{
		//types:
		fade:{
			fadeFromColor:function(tObj, overlay_selector_id, offSetTime){
				js_log('f:fadeFromColor: '+overlay_selector_id +' to color: '+ tObj.fadeColor);
				if(!tObj.fadeColor)
					return js_log('missing fadeColor');		
				if($j('#'+overlay_selector_id).get(0).length==0){
					js_log("ERROR cant find: "+ overlay_selector_id);
				}	
				//set the initial state
				$j('#'+overlay_selector_id).css({
					'background-color':tObj.fadeColor,
					'opacity':"1",
				});
				/*js_log('do transition annimation for: '+ (tObj.dur - offSetTime)
						 + ' seconds for: '+$j('#'+overlay_selector_id).get(0) + 
						 ' current opacity: '+$j('#'+overlay_selector_id).css('opacity') );*/
			
				//annimate the trasiion
				$j('#'+overlay_selector_id).animate(
					{
  						"opacity" : "0"
    				}, 
    				(( tObj.dur - offSetTime )*1000), //duration value should be stored in ms earlier on. 
					'linear',		    				
					function(){ //callback
						js_log('fade done');
						tObj.animation_state=2;
					}
				);
			},
			//corssFade
			crossfade:function(tObj, overlay_selector_id, offSetTime){
				js_log('f:crossfade: '+overlay_selector_id);
				if($j('#'+overlay_selector_id).length==0)
					js_log("ERROR overlay selector not found: "+overlay_selector_id);
				
				//set the initial state show the zero opacity animiation
				$j('#'+overlay_selector_id).css({'opacity':0}).show();
				
				/*js_log("should have set "+overlay_selector_id +"to zero is: "+
					$j('#'+overlay_selector_id).css('opacity') + ' should annimate for'+
					(( tObj.dur - offSetTime )*1000) );*/
				
				$j('#'+overlay_selector_id).animate(
					{
  						"opacity" : "1"
    				}, 
    				(( tObj.dur - offSetTime )*1000),					
					'linear',		    				
					function(){ //callback
						js_log("animated opacity done");
						tObj.animation_state=2;						
					}
				);
			}			
		}							
	}
}