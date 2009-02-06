/** base embedVideo object
    @param element <video> tag used for initialization.
    @constructor
*/

var embedVideo = function(element) {
	return this.init(element);
};

embedVideo.prototype = {
    /** The mediaElement object containing all mediaSource objects */
    media_element:null,
    preview_mode:false,
	slider:null,		
	ready_to_play:false, //should use html5 ready state
	load_error:false, //used to set error in case of error
	loading_external_data:false,
	thumbnail_updating:false,
	thumbnail_disp:true,
	init_with_sources_loadedDone:false,
	inDOM:false,
	//for onClip done stuff: 
	anno_data_cache:null,
	seek_time_sec:0,
	base_seeker_slider_offset:null,
	onClipDone_disp:false,
	supports:{},
	//for seek thumb updates:
	cur_thumb_seek_time:0,
	thumb_seek_interval:null,
	//set the buffered percent:	
	bufferedPercent:0,	
	//utility functions for property values:
	hx : function ( s ) {
		if ( typeof s != 'String' ) {
			s = s.toString();
		}
		return s.replace( /&/g, '&amp;' )
			. replace( /</g, '&lt;' )
			. replace( />/g, '&gt;' );
	},
	hq : function ( s ) {
		return '"' + this.hx( s ) + '"';
	},
	playerPixelWidth : function()
	{
		var player = $j('#mv_embedded_player_'+this.id).get(0);
		if(typeof player!='undefined' && player['offsetWidth'])
			return player.offsetWidth;
		else
			return parseInt(this.width);
	},
	playerPixelHeight : function()
	{
		var player = $j('#mv_embedded_player_'+this.id).get(0);
		if(typeof player!='undefined' && player['offsetHeight'])
			return player.offsetHeight;
		else
			return parseInt(this.height);
	},
	init: function(element){		
		//this.element_pointer = element;

		//inherit all the default video_attributes
	    for(var attr in default_video_attributes){ //for in loop oky on user object
	        if(element.getAttribute(attr)){
	            this[attr]=element.getAttribute(attr);
	            //js_log('attr:' + attr + ' val: ' + video_attributes[attr] +" "+'elm_val:' + element.getAttribute(attr) + "\n (set by elm)");
	        }else{
	            this[attr]=default_video_attributes[attr];
	            //js_log('attr:' + attr + ' val: ' + video_attributes[attr] +" "+ 'elm_val:' + element.getAttribute(attr) + "\n (set by attr)");
	        }
	    }		   
	    if( this.duration!=null && this.duration.split(':').length >= 2)
	    	this.duration = ntp2seconds( this.duration );	    
	        	
	    //if style is set override width and height
	    var dwh = mv_default_video_size.split('x');
	    this.width = element.style.width ? element.style.width : dwh[0];
	    this.height = element.style.height ? element.style.height : dwh[1];
	    //set the plugin id
	    this.pid = 'pid_' + this.id;

	    //grab any innerHTML and set it to missing_plugin_html
	    //@@todo we should strip source tags instead of checking and skipping
	    if(element.innerHTML!='' && element.getElementsByTagName('source').length==0){
            js_log('innerHTML: ' + element.innerHTML);
	        this.user_missing_plugin_html=element.innerHTML;
	    }	      	    
	    // load all of the specified sources
        this.media_element = new mediaElement(element);                         	
	},
	on_dom_swap: function(){
		js_log('f:on_dom_swap');				
		// Process the provided ROE file... if we don't yet have sources
        if(this.roe && this.media_element.sources.length==0 ){
			js_log('loading external data');
        	this.loading_external_data=true;
        	var _this = this;              	  
            do_request(this.roe, function(data)
            {            	            
            	//continue      	         	
            	_this.media_element.addROE(data);                                      
                js_log('added_roe::' + _this.media_element.sources.length);                               
                                                       
                js_log('set loading_external_data=false');     
                _this.loading_external_data=false;                               
                
                _this.init_with_sources_loaded();
            });
    	}
	},
	init_with_sources_loaded : function()
	{	
		js_log('f:init_with_sources_loaded');
		//set flag that we have run this function:
		this.init_with_sources_loadedDone=true;				
		//autoseletct the source
		this.media_element.autoSelectSource();		
		//auto select player based on prefrence or default order
		if( !this.media_element.selected_source )
		{
			//check for parent clip: 
			if( typeof this.pc != 'undefined' ){			
				js_log('no sources, type:' +this.type + ' check for html');				
				//do load player if just displaying innerHTML: 
				if(this.pc.type =='text/html'){
					this.selected_player = embedTypes.players.defaultPlayer( 'text/html' );
					js_log('set selected player:'+ this.selected_player.mime_type);	
				}
			}
		}else{		
        	this.selected_player = embedTypes.players.defaultPlayer( this.media_element.selected_source.mime_type );
		}			      
        if( this.selected_player ){
            js_log('selected ' + this.selected_player.getName());
            js_log("PLAYBACK TYPE: "+this.selected_player.library);
            this.thumbnail_disp = true;	    
			this.inheritEmbedObj();
        }else{        	        
        	//no source's playable
        	var missing_type ='';
        	var or ='';  
        	for( var i=0; i < this.media_element.sources.length; i++){
        		missing_type+=or + this.media_element.sources[i].mime_type;
        		or=' or ';
        	}        	
        	if( this.pc )
        		var missing_type = this.pc.type;        		        	
        	
           	js_log('no player found for given source type ' + missing_type);
           	this.load_error= gM('generic_missing_plugin', missing_type );             	          	            
        }        
	},
	inheritEmbedObj:function(){		
		//@@note: tricky cuz direct overwrite is not so ideal.. since the extended object is already tied to the dom
		//clear out any non-base embedObj stuff:
		if(this.instanceOf){
			eval('tmpObj = '+this.instanceOf);
			for(var i in tmpObj){ //for in loop oky for object  
				if(this['parent_'+i]){
					this[i]=this['parent_'+i];
				}else{
					this[i]=null;
				}
			}
		}    		  	
		//set up the new embedObj
        js_log('f: inheritEmbedObj: embedding with ' + this.selected_player.library);
		var _this = this;		
		this.selected_player.load( function()
		{
			//js_log('inheriting '+_this.selected_player.library +'Embed to ' + _this.id + ' ' + $j('#'+_this.id).length);
			//var _this = $j('#'+_this.id).get(0);
			//js_log( 'type of ' + _this.selected_player.library +'Embed + ' +
			//		eval('typeof '+_this.selected_player.library +'Embed')); 
			eval('embedObj = ' +_this.selected_player.library +'Embed;');
			for(var method in embedObj){ //for in loop oky for object  
				//parent method preservation for local overwritten methods
				if(_this[method])
					_this['parent_' + method] = _this[method];
				_this[method]=embedObj[method];
			}
			js_log('TYPEOF_ppause: ' + typeof _this['parent_pause']);
			
			if(_this.inheritEmbedOverride){
				_this.inheritEmbedOverride();
			}
			//update controls if possible
			if(!_this.loading_external_data)
				_this.refreshControlsHTML();												
			
			//js_log("READY TO PLAY:"+_this.id);			
			_this.ready_to_play=true;
			_this.getDuration();
			_this.getHTML();
		});
	},
    selectPlayer:function(player)
    {
		var _this = this;
		if(this.selected_player.id != player.id){
	        this.selected_player = player;
	        this.inheritEmbedObj();
		}
    },
	getTimeReq:function(){
		js_log('f:getTimeReq:'+ this.getDurationNTP());
		var default_time_req = '0:00:00/' + this.getDurationNTP() ;
		if(!this.media_element)
			return default_time_req;
		if(!this.media_element.selected_source)
			return default_time_req;		
		if(!this.media_element.selected_source.end_ntp)
			return default_time_req;		
		return this.media_element.selected_source.start_ntp+'/'+this.media_element.selected_source.end_ntp;
	},	
    getDuration:function(){   
    	//update some local pointers for the selected source:    	
    	if( this.media_element.selected_source.duration &&
    		this.media_element.selected_source.duration != 0 ){    		  
        	this.duration = this.media_element.selected_source.duration;        	        	
        	this.start_offset = this.media_element.selected_source.start_offset;
        	this.start_ntp = this.media_element.selected_source.start_ntp;
	        this.end_ntp = this.media_element.selected_source.end_ntp;         
        }else{        	
        	//update start end_ntp if duration !=0 (set from plugin) 
        	if(this.duration && this.duration !=0){
        		this.start_ntp = '0:0:0';
        		this.end_ntp = seconds2ntp( this.duration );
        	}        	
        }        
        //return the duration
        return this.duration;
    },
  	/* get the duration in ntp format */
	getDurationNTP:function(){
		return seconds2ntp(this.getDuration());
	},
	/*
	 * wrapEmebedContainer
     * wraps the embed code into a container to better support playlist function
     *  (where embed element is swapped for next clip
     *  (where plugin method does not support playlsits) 
	 */
	wrapEmebedContainer:function(embed_code){
		//check if parent clip is set( ie we are in a playlist so name the embed container by playlistID)
		var id = (this.pc!=null)?this.pc.pp.id:this.id;
		return '<div id="mv_ebct_'+id+'" style="width:'+this.width+'px;height:'+this.height+'px;">' + 
					embed_code + 
				'</div>';
	},	
	getEmbedHTML : function(){
		//return this.wrapEmebedContainer( this.getEmbedObj() );
		return 'function getEmbedHTML should be overitten by embedLib ';
	},
	//do seek function (should be overwritten by implementing embedLibs)
	// first check if seek can be done on locally downloaded content. 
	doSeek : function( perc ){
		js_log('f:mv_embed:doSeek:'+perc);
		if( this.supportsURLTimeEncoding() ){
			js_log('Seeking to ' + this.seek_time_sec + ' (local copy of clip not loaded at' + perc + '%)');
			this.stop();					
			this.didSeekJump=true;
			//update the slider
			this.setSliderValue( perc ); 
		}
		
		//do play in 100ms (give things time to clear) 
		setTimeout('$j(\'#' + this.id + '\').get(0).play()',100);
	},	
    doEmbedHTML:function()
    {
    	js_log('f:doEmbedHTML');
    	js_log('thum disp:'+this.thumbnail_disp);
		var _this = this;
		this.closeDisplayedHTML();

//		if(!this.selected_player){
//			return this.getPluginMissingHTML();		
		//Set "loading" here
		$j('#mv_embedded_player_'+_this.id).html(''+
			'<div style="color:black;width:'+this.width+'px;height:'+this.height+'px;">' + 
				gM('loading_plugin') + 
			'</div>'					
		);
		// schedule embedding
		this.selected_player.load(function()
		{
			js_log('performing embed for ' + _this.id);			
			var embed_code = _this.getEmbedHTML();
			//js_log(embed_code);
			$j('#mv_embedded_player_'+_this.id).html(embed_code);	
		});
    },
    /* todo abstract out onClipDone chain of functions and merge with textInterface */
    onClipDone:function(){
    	//stop the clip (load the thumbnail etc) 
    	this.stop();
    	var _this = this;
    	
    	//if the clip resolution is < 320 don't do fancy onClipDone stuff 
    	if(this.width<300){
    		return ;
    	}
    	this.onClipDone_disp=true;
    	this.thumbnail_disp=true;
    	//make sure we are not in preview mode( no end clip actions in preview mode) 
    	if( this.preview_mode )
    		return ;
    		
    	$j('#img_thumb_'+this.id).css('zindex',1);
    	$j('#big_play_link_'+this.id).hide();
    	//add the liks_info_div black back 
    	$j('#dc_'+this.id).append('<div id="liks_info_'+this.id+'" ' +
	    			'style="width:' +parseInt(parseInt(this.width)/2)+'px;'+	    
	    			'height:'+ parseInt(parseInt(this.height)) +'px;'+
	    			'position:absolute;top:10px;overflow:auto'+    			
	    			'width: '+parseInt( ((parseInt(this.width)/2)-15) ) + 'px;'+
	    			'left:'+ parseInt( ((parseInt(this.width)/2)+15) ) +'px;">'+	    			
    			'</div>' +
    			'<div id="black_back_'+this.id+'" ' +
	    			'style="z-index:-2;position:absolute;background:#000;' +
	    			'top:0px;left:0px;width:'+parseInt(this.width)+'px;' +
	    			'height:'+parseInt(this.height)+'px;">' +
	    		'</div>'
	   	);    	
    	
    	//start animation (make thumb small in upper left add in div for "loading"    	    
    	$j('#img_thumb_'+this.id).animate({    			
    			width:parseInt(parseInt(_this.width)/2),
    			height:parseInt(parseInt(_this.height)/2),
    			top:20,
    			left:10
    		},
    		1000, 
    		function(){
    			//animation done.. add "loading" to div if empty    	
    			if($j('#liks_info_'+_this.id).html()==''){
    				$j('#liks_info_'+_this.id).html(gM('loading_txt'));
    			}		
    		}
    	)       	 	   
    	//now load roe if run the showNextPrevLinks
    	if(this.roe && this.media_element.addedROEData==false){
    		do_request(this.roe, function(data)
            {            	                        	      	         
            	_this.media_element.addROE(data);
            	_this.getNextPrevLinks();
            });    
    	}else{
    		this.getNextPrevLinks();
    	}
    },
    //@@todo we should merge getNextPrevLinks with textInterface .. there is repeated code between them. 
    getNextPrevLinks:function(){
    	js_log('f:getNextPrevLinks');
    	var anno_track_url = null;
    	var _this = this; 
    	//check for annoative track
    	$j.each(this.media_element.sources, function(inx, n){    		
			if(n.mime_type=='text/cmml'){
				if( n.id == 'Anno_en'){
					anno_track_url = n.src;
				}
			}
    	});
    	if( anno_track_url ){
    		js_log('found annotative track:'+ anno_track_url);
    		//zero out seconds (should improve cache hit rate and generally expands metadata search)
    		//@@todo this could be repalced with a regExp
    		var annoURL = parseUri(anno_track_url);
    		var times = annoURL.queryKey['t'].split('/');      		
    		var stime_parts = times[0].split(':');   
    		var etime_parts = times[1].split(':');         				
    		//zero out the hour:
    		var new_start = stime_parts[0]+':'+'0:0';
    		//zero out the end sec
    		var new_end   = (etime_parts[0]== stime_parts[0])? (etime_parts[0]+1)+':0:0' :etime_parts[0]+':0:0';
    		 		
    		var etime_parts = times[1].split(':');
    		
    		var new_anno_track_url = annoURL.protocol +'://'+ annoURL.host + annoURL.path +'?';
    		$j.each(annoURL.queryKey, function(i, val){
    			new_anno_track_url +=(i=='t')?'t='+new_start+'/'+new_end +'&' :
    									 i+'='+ val+'&';
    		});
    		var request_key = new_start+'/'+new_end;
    		//check the anno_data cache: 
    		//@@todo search cache see if current is in range.  
    		if(this.anno_data_cache){
    			js_log('anno data found in cache: '+request_key);
    			this.showNextPrevLinks();
    		}else{    			    			
	    		do_request(new_anno_track_url, function(cmml_data){
	    			js_log('raw response: '+ cmml_data);
				    if(typeof cmml_data == 'string')
			        {
			            var parser=new DOMParser();
			            js_log('Parse CMML data:' + cmml_data);
			            cmml_data=parser.parseFromString(cmml_data,"text/xml");
			        }
	    			//init anno_data_cache
	    			if(!_this.anno_data_cache)
	    				_this.anno_data_cache={};	    			
	    			//grab all metadata and put it into the anno_data_cache: 	    			
	    			$j.each(cmml_data.getElementsByTagName('clip'), function(inx, clip){
	    				_this.anno_data_cache[ $j(clip).attr("id") ]={
	    						'start_time_sec':ntp2seconds($j(clip).attr("start").replace('npt:','')),
	    						'end_time_sec':ntp2seconds($j(clip).attr("end").replace('npt:','')),
	    						'time_req':$j(clip).attr("start").replace('npt:','')+'/'+$j(clip).attr("end").replace('npt:','')
	    					};
	    				//grab all its meta
	    				_this.anno_data_cache[ $j(clip).attr("id") ]['meta']={};
	    				$j.each(clip.getElementsByTagName('meta'),function(imx, meta){	    					
	    					//js_log('adding meta: '+ $j(meta).attr("name")+ ' = '+ $j(meta).attr("content"));
	    					_this.anno_data_cache[$j(clip).attr("id")]['meta'][$j(meta).attr("name")]=$j(meta).attr("content");
	    				});
	    			});
	    			_this.showNextPrevLinks();	    			
	    		});
    		}
    	}else{
    		js_log('no annotative track found');
    		$j('#liks_info_'+this.id).html('no metadata found for next, previous links');
    	}
    	//query current request time +|- 60s to get prev next speech links. 
    },
    showNextPrevLinks:function(){
    	js_log('f:showNextPrevLinks');
    	//int requested links: 
    	var link = {
    		'prev':'',
    		'current':'',
    		'next':''
    	}    	
    	var curTime = this.getTimeReq().split('/');
    	
    	var s_sec = ntp2seconds(curTime[0]);
    	var e_sec = ntp2seconds(curTime[1]); 
    	js_log('showNextPrevLinks: req time: '+ s_sec + ' to ' + e_sec);
    	//now we have all the data in anno_data_cache
    	var current_done=false;
    	for(var clip_id in this.anno_data_cache){  //for in loop oky for object
		 	var clip =  this.anno_data_cache[clip_id];
		 	//js_log('on clip:'+ clip_id);
		 	//set prev_link (if cur_link is still empty)
			if( s_sec > clip.end_time_sec){
				link.prev = clip_id;
				js_log('showNextPrevLinks: ' + s_sec + ' < ' + clip.end_time_sec + ' set prev');
			}
				
			if(e_sec==clip.end_time_sec && s_sec== clip.start_time_sec)
				current_done = true;
			//current clip is not done:
			if(  e_sec < clip.end_time_sec  && link.current=='' && !current_done){
				link.current = clip_id;
				js_log('showNextPrevLinks: ' + e_sec + ' < ' + clip.end_time_sec + ' set current'); 
			}
			
			//set end clip (first clip where start time is > end_time of req
			if( e_sec <  clip.start_time_sec && link.next==''){
				link.next = clip_id;
				js_log('showNextPrevLinks: '+  e_sec + ' < '+ clip.start_time_sec + ' && ' + link.next );
			}
    	}   
    	var html='';   
    	if(link.prev=='' && link.current=='' && link.next==''){
    		html='<p><a href="'+this.media_element.linkbackgetMsg+'">clip page</a>';
    	}else{    	
	    	for(var link_type in link){
	    		var link_id = link[link_type];    		
	    		if(link_id!=''){
	    			var clip = this.anno_data_cache[link_id];    			
	    			var title_msg='';
					for(var j in clip['meta']){
						title_msg+=j.replace(/_/g,' ') +': ' +clip['meta'][j].replace(/_/g,' ') +" <br>";
					}    	
					var time_req = 	clip.time_req;
					if(link_type=='current') //if current start from end of current clip play to end of current meta: 				
						time_req = curTime[1]+ '/' + seconds2ntp( clip.end_time_sec );
					
					//do special linkbacks for metavid content: 
					var regTimeCheck = new RegExp(/[0-9]+:[0-9]+:[0-9]+\/[0-9]+:[0-9]+:[0-9]+/);				
					html+='<p><a  ';
					if( regTimeCheck.test( this.media_element.linkback ) ){
						html+=' href="'+ this.media_element.linkback.replace(regTimeCheck,time_req) +'" '; 
					}else{
						html+=' href="#" onClick="$j(\'#'+this.id+'\').get(0).playByTimeReq(\''+ 
		    					time_req + '\'); return false; "';				
					}
					html+=' title="' + title_msg + '">' + 
		    	 		gM(link_type+'_clip_msg') + 	    	 	
		    		'</a><br><span style="font-size:small">'+ title_msg +'<span></p>';
	    		}    	    				
	    	}
    	}	
    	//js_og("should set html:"+ html);
    	$j('#liks_info_'+this.id).html(html);
    },
    playByTimeReq: function(time_req){
    	js_log('f:playByTimeReq: '+time_req );
    	this.stop();
    	this.updateVideoTimeReq(time_req);
    	this.play();    	
    },
    doThumbnailHTML:function()
    {  	
    	var _this = this;
    	js_log('f:doThumbnailHTML'+ this.thumbnail_disp);
        this.closeDisplayedHTML();
        this.thumbnail_disp = true;

        $j('#mv_embedded_player_'+this.id).html( this.getThumbnailHTML() );
		this.paused = true;		
    },
    refreshControlsHTML:function(){
    	js_log('refreshing controls HTML');
		if($j('#mv_embedded_controls_'+this.id).length==0)
		{
			js_log('#mv_embedded_controls_'+this.id + ' not present, returning');
			return;
		}else{
			$j('#mv_embedded_controls_'+this.id).html( this.getControlsHTML() );
			ctrlBuilder.addControlHooks(this);						
		}		
    },   
    getControlsHTML:function()
    {        	
    	return ctrlBuilder.getControls( this );
    },	
	getHTML : function (){		
		//@@todo check if we have sources avaliable	
		js_log('f:getHTML : ' + this.id );			
		var _this = this; 				
		var html_code = '';		
        html_code = '<div id="videoPlayer_'+this.id+'" style="width:'+this.width+'px;" class="videoPlayer">';        
			html_code += '<div style="width:'+parseInt(this.width)+'px;height:'+parseInt(this.height)+'px;"  id="mv_embedded_player_'+this.id+'">' +
							this.getThumbnailHTML() + 
						'</div>';											
			//js_log("mvEmbed:controls "+ typeof this.controls);									
	        if(this.controls)
	        {
	        	js_log("f:getHTML:AddControls");
	            html_code +='<div id="mv_embedded_controls_' + this.id + '" class="controls" style="width:' + this.width + 'px">';
	            html_code += this.getControlsHTML();       
	            html_code +='</div>';      
	            //block out some space by encapulating the top level div 
	            $j(this).wrap('<div style="width:'+parseInt(this.width)+'px;height:'
	            		+(parseInt(this.height)+ctrlBuilder.height)+'px"></div>');    	            
	        }
        html_code += '</div>'; //videoPlayer div close        
        //js_log('should set: '+this.id);
        $j(this).html( html_code );                    
		//add hooks once Controls are in DOM
		ctrlBuilder.addControlHooks(this);		
		                  
        //js_log('set this to: ' + $j(this).html() );	
        //alert('stop');
        //if auto play==true directly embed the plugin
        if(this.autoplay)
		{
			js_log('activating autoplay');
            this.play();
		}
	},
	/*
	* get missing plugin html (check for user included code)
	*/
	getPluginMissingHTML : function(){
		//keep the box width hight:
		var out = '<div style="width:'+this.width+'px;height:'+this.height+'px">';
	    if(this.user_missing_plugin_html){
	      out+= this.user_missing_plugin_html;
	    }else{
		  out+= gM('generic_missing_plugin') + ' or <a title="'+gM('download_clip')+'" href="'+this.src +'">'+gM('download_clip')+'</a>';
		}
		return out + '</div>';
	},
	updateVideoTimeReq:function(time_req){
		js_log('f:updateVideoTimeReq');
		var time_parts =time_req.split('/');
		this.updateVideoTime(time_parts[0], time_parts[1]);
	},
	//update video time
	updateVideoTime:function(start_ntp, end_ntp){					
		//update media
		this.media_element.updateSourceTimes( start_ntp, end_ntp );
		//update mv_time
		this.setStatus(start_ntp+'/'+end_ntp);
		//reset slider
		this.setSliderValue(0);
		//reset seek_offset:
		if(this.media_element.selected_source.supports_url_time_encoding)
			this.seek_time_sec=0;
		else
			this.seek_time_sec=ntp2seconds(start_ntp);
	},		
	//@@todo overwite by embed library if we can render frames natavily 
	renderTimelineThumbnail:function( options ){
		var my_thumb_src = this.media_element.getThumbnailURL();
		
		if( my_thumb_src.indexOf('t=') !== -1){
			var time_ntp =  seconds2ntp ( options.time + parseInt(this.start_offset) );
			my_thumb_src = getUpdateTimeURL( my_thumb_src, time_ntp, options.size );
		}
		var thumb_class = (typeof options['thumb_class'] !='undefined')? options['thumb_class'] : '';
		return '<div class="' + thumb_class + '" src="' + my_thumb_src +'" '+
				'style="height:' + options.height + 'px;' +
				'width:' + options.width + 'px" >' + 
				 	'<img src="' + my_thumb_src +'" '+
						'style="height:' + options.height + 'px;' +
						'width:' + options.width + 'px">' +
				'</div>';
	},
	updateThumbTimeNTP:function( time){
		this.updateThumbTime( ntp2seconds(time) - parseInt(this.start_offset) );
	},
	updateThumbTime:function( float_sec ){
		//js_log('updateThumbTime:'+float_sec);
		var _this = this;									   				
		if( typeof this.org_thum_src=='undefined' ){		
			this.org_thum_src = this.media_element.getThumbnailURL();
		}							
		if( this.org_thum_src.indexOf('t=') !== -1){
			this.last_thumb_url = getUpdateTimeURL(this.org_thum_src,seconds2ntp( float_sec + parseInt(this.start_offset)));									
			if(!this.thumbnail_updating){				
				this.updateThumbnail(this.last_thumb_url ,false);
				this.last_thumb_url =null;
			}
		}
	},
	//for now provide a src url .. but need to figure out how to copy frames from video for plug-in based thumbs
	updateThumbPerc:function( perc ){	
		return this.updateThumbTime( (this.getDuration() * perc) );
	},
	//updates the thumbnail if the thumbnail is being displayed
	updateThumbnail : function(src, quick_switch){				
		//make sure we don't go to the same url if we are not already updating: 
		if( !this.thumbnail_updating && $j('#img_thumb_'+this.id).attr('src')== src )
			return false;
		//if we are already updating don't issue a new update: 
		if( this.thumbnail_updating && $j('#new_img_thumb_'+this.id).attr('src')== src )
			return false;
		
		js_log('update thumb: ' + src);
		
		if(quick_switch){
			$j('#img_thumb_'+this.id).attr('src', src);
		}else{
			var _this = this;			
			//if still animating remove new_img_thumb_
			if(this.thumbnail_updating==true)
				$j('#new_img_thumb_'+this.id).stop().remove();		
					
			if(this.thumbnail_disp){
				js_log('set to thumb:'+ src);
				this.thumbnail_updating=true;
				$j('#dc_'+this.id).append('<img src="'+src+'" ' +
					'style="display:none;position:absolute;zindex:2;top:0px;left:0px;" ' +
					'width="'+this.width+'" height="'+this.height+'" '+
					'id = "new_img_thumb_'+this.id+'" />');						
				//js_log('appended: new_img_thumb_');		
				$j('#new_img_thumb_'+this.id).fadeIn("slow", function(){						
						//once faded in remove org and rename new:
						$j('#img_thumb_'+_this.id).remove();
						$j('#new_img_thumb_'+_this.id).attr('id', 'img_thumb_'+_this.id);
						$j('#img_thumb_'+_this.id).css('zindex','1');
						_this.thumbnail_updating=false;						
						//js_log("done fadding in "+ $j('#img_thumb_'+_this.id).attr("src"));
						
						//if we have a thumb queued update to that
						if(_this.last_thumb_url){
							var src_url =_this.last_thumb_url;
							_this.last_thumb_url=null;
							_this.updateThumbnail(src_url);
						}
				});
			}
		}
	},
    /** Returns the HTML code for the video when it is in thumbnail mode.
        This includes the specified thumbnail as well as buttons for
        playing, configuring the player, inline cmml display, HTML linkback,
        download, and embed code.
    */
	getThumbnailHTML : function ()
    {
	    var thumb_html = '';
	    var class_atr='';
	    var style_atr='';
	    //if(this.class)class_atr = ' class="'+this.class+'"';
	    //if(this.style)style_atr = ' style="'+this.style+'"';
	    //    else style_atr = 'overflow:hidden;height:'+this.height+'px;width:'+this.width+'px;';
        this.thumbnail = this.media_element.getThumbnailURL();

	    //put it all in the div container dc_id
	    thumb_html+= '<div id="dc_'+this.id+'" style="position:relative;'+
	    	' overflow:hidden; top:0px; left:0px; width:'+this.playerPixelWidth()+'px; height:'+this.playerPixelHeight()+'px; z-index:0;">'+
	        '<img width="'+this.playerPixelWidth()+'" height="'+this.playerPixelHeight()+'" style="position:relative;width:'+this.playerPixelWidth()+';height:'+this.playerPixelHeight()+'"' +
	        ' id="img_thumb_'+this.id+'" src="' + this.thumbnail + '">';
		
	    if(this.play_button==true)
		  	thumb_html+=this.getPlayButton();
		  	
   	    thumb_html+='</div>';
	    return thumb_html;
    },
	getEmbeddingHTML:function()
	{
		var thumbnail = this.media_element.getThumbnailURL();

		var embed_thumb_html;
		if(thumbnail.substring(0,1)=='/'){
			eURL = parseUri(mv_embed_path);
			embed_thumb_html = eURL.protocol + '://' + eURL.host + thumbnail;
			//js_log('set from mv_embed_path:'+embed_thumb_html);
		}else{
			embed_thumb_html = (thumbnail.indexOf('http://')!=-1)?thumbnail:mv_embed_path + thumbnail;
		}
		var embed_code_html = '&lt;script type=&quot;text/javascript&quot; ' +
					'src=&quot;'+mv_embed_path+'mv_embed.js&quot;&gt;&lt;/script&gt' +
					'&lt;video ';
		if(this.roe){
			embed_code_html+='roe=&quot;'+this.roe+'&quot; &gt;';
		}else{
			embed_code_html+='src=&quot;'+this.src+'&quot; ' +
				'thumbnail=&quot;'+embed_thumb_html+'&quot;&gt;';
		}
		//close the video tag
		embed_code_html+='&lt;/video&gt;';

		return embed_code_html;
	},
    doOptionsHTML:function()
    {
    	var sel_id = (this.pc!=null)?this.pc.pp.id:this.id;
    	var pos = $j('#options_button_'+sel_id).offset();
    	pos['top']=pos['top']+24;
		pos['left']=pos['left']-124;
		//js_log('pos of options button: t:'+pos['top']+' l:'+ pos['left']);
        $j('#mv_embedded_options_'+sel_id).css(pos).toggle();
        return;
	},
	getPlayButton:function(id){
		if(!id)id=this.id;
		return '<div id="big_play_link_'+id+'" class="large_play_button" '+
			'style="left:'+((this.playerPixelWidth()-130)/2)+'px;'+
			'top:'+((this.playerPixelHeight()-96)/2)+'px;"></div>';
	},
	//display the code to remotely embed this video:
	showEmbedCode : function(embed_code){
		if(!embed_code)
			embed_code = this.getEmbeddingHTML();
		var o='';
		if(this.linkback){
			o+='<a class="email" href="'+this.linkback+'">Share Clip via Link</a> '+
			'<p>or</p> ';
		}
		o+='<span style="color:#FFF;font-size:14px;">Embed Clip in Blog or Site</span>'+
			'<div class="embed_code"> '+
				'<textarea onClick="this.select();" id="embedding_user_html_'+this.id+'" name="embed">' +
					embed_code+
				'</textarea> '+
				'<button onClick="$j(\'#'+this.id+'\').get(0).copyText(); return false;" class="copy_to_clipboard">Copy to Clipboard</button> '+
			'</div> '+
		'</div>';
		this.displayHTML(o);
	},
	copyText:function(){
	  $j('#embedding_user_html_'+this.id).focus().select();	   	 
	  if(document.selection){  	
		  CopiedTxt = document.selection.createRange();	
		  CopiedTxt.execCommand("Copy");
	  }
	},
	showTextInterface:function(){	
		var _this = this;
		//display the text container with loading text: 
		//@@todo support position config
		var loc = $j(this).position();			
		if($j('#metaBox_'+this.id).length==0){
			$j(this).after('<div style="position:absolute;z-index:10;'+
						'top:' + (loc.top) + 'px;' +
						'left:' + (parseInt( loc.left ) + parseInt(this.width) + 10 )+'px;' +
						'height:'+ parseInt( this.height )+'px;width:400px;' +
						'background:white;border:solid black;' +
						'display:none;" ' +
						'id="metaBox_' + this.id + '">'+
							gM('loading_txt') +
						'</div>');					
		}
		//fade in the text display
		$j('#metaBox_'+this.id).fadeIn("fast");	
		//check if textObj present:
		if(typeof this.textInterface == 'undefined' ){
			//load the default text interface:
			mvJsLoader.doLoad({
					'textInterface':'libTimedText/mv_timed_text.js',
					'$j.fn.hoverIntent':'jquery/plugins/jquery.hoverIntent.js'
				}, function(){
					
					_this.textInterface = new textInterface( _this );							
					//show interface
					_this.textInterface.show();
					js_log("NEW TEXT INTERFACE");
					for(var i in _this.textInterface.availableTracks){
						js_log("tracks in new interface: "+_this.id+ ' tid:' + i);						
					}
				}
			);
		}else{
			//show interface
			this.textInterface.show();
		}
	},
	closeTextInterface:function(){
		js_log('closeTextInterface '+ typeof this.textInterface);
		if(typeof this.textInterface !== 'undefined' ){
			this.textInterface.close();
		}
	},
    /** Generic function to display custom HTML inside the mv_embed element.
        The code should call the closeDisplayedHTML function to close the
        display of the custom HTML and restore the regular mv_embed display.
        @param {String} HTML code for the selection list.
    */
    displayHTML:function(html_code)
    {
    	var sel_id = (this.pc!=null)?this.pc.pp.id:this.id;
    	
    	if(!this.supports['overlays'])
        	this.stop();
        
        //put select list on-top
        //make sure the parent is relatively positioned:
        $j('#'+sel_id).css('position', 'relative');
        //set height width (check for playlist container)
        var width = (this.pc)?this.pc.pp.width:this.playerPixelWidth();
        var height = (this.pc)?this.pc.pp.height:this.playerPixelHeight();
        
        if(this.pc)
        	height+=(this.pc.pp.pl_layout.title_bar_height + this.pc.pp.pl_layout.control_height);
      
        var fade_in = true;
        if($j('#blackbg_'+sel_id).length!=0)
        {
            fade_in = false;
            $j('#blackbg_'+sel_id).remove();
        }
        //fade in a black bg div ontop of everything
         var div_code = '<div id="blackbg_'+sel_id+'" class="videoComplete" ' +
			 'style="height:'+parseInt(height)+'px;width:'+parseInt(width)+'px;">'+
//       			 '<span class="displayHTML" id="con_vl_'+this.id+'" style="position:absolute;top:20px;left:20px;color:white;">' +
	  		'<div class="videoOptionsComplete">'+
			//@@TODO: this style should go to .css
			'<span style="float:right;margin-right:10px">' +			
		    		'<a href="#" style="color:white;" onClick="$j(\'#'+sel_id+'\').get(0).closeDisplayedHTML();return false;">close</a>' +
		    '</span>'+
            '<div id="mv_disp_inner_'+sel_id+'" style="padding-top:10px;">'+
            	 html_code 
           	+'</div>'+
//                close_link+'</span>'+
      		 '</div></div>';
        $j('#'+sel_id).prepend(div_code);
        if (fade_in)
            $j('#blackbg_'+sel_id).fadeIn("slow");
        else
            $j('#blackbg_'+sel_id).show();
        return false; //onclick action return false
    },
    /** Close the custom HTML displayed using displayHTML and restores the
        regular mv_embed display.
    */
    closeDisplayedHTML:function(){
	 	 var sel_id = (this.pc!=null)?this.pc.pp.id:this.id;
		 $j('#blackbg_'+sel_id).fadeOut("slow", function(){
			 $j('#blackbg_'+sel_id).remove();
		 });
 		return false;//onclick action return false
	},
    selectPlaybackMethod:function(){    	
    	//get id (in case where we have a parent container)
        var this_id = (this.pc!=null)?this.pc.pp.id:this.id;
        
        var _this=this;               
        var out='<span style="color:#FFF;background-color:black;"><blockquote style="background-color:black;">';
        var _this=this;
        //js_log('selected src'+ _this.media_element.selected_source.url);
		$j.each(this.media_element.getPlayableSources(), function(index, source)
        {     		
	        var default_player = embedTypes.players.defaultPlayer( source.getMIMEType() );
	        var source_select_code = '$j(\'#'+this_id+'\').get(0).closeDisplayedHTML(); $j(\'#'+_this.id+'\').get(0).media_element.selectSource(\''+index+'\');';
	        
	        //var player_code = _this.getPlayerSelectList( source.getMIMEType(), index, source_select_code);
	        
	        var is_selected = (source == _this.media_element.selected_source);
	        var image_src = mv_embed_path+'images/stream/';
	        if( source.mime_type == 'video/x-flv' ){
	        	image_src += 'flash_icon_';
	        }else if( source.mime_type == 'video/h264'){
	        	//for now all mp4 content is pulled from archive.org (so use archive.org icon) 
	        	image_src += 'archive_org_';
	        }else{
	        	image_src += 'fish_xiph_org_';
	        }
	        image_src += is_selected ? 'color':'bw';
	        image_src += '.png';
	        if (default_player)
	        {
	            out += '<img src="'+image_src+'"/>';
	            if( ! is_selected )
	                out+='<a href="#" onClick="' + source_select_code + 'embedTypes.players.userSelectPlayer(\''+default_player.id+'\',\''+source.getMIMEType()+'\'); return false;">';
	            out += source.getTitle()+ (is_selected?'</a>':'') + ' ';
	        	//output the player select code: 
	        	var supporting_players = embedTypes.players.getMIMETypePlayers( source.getMIMEType() );		
				out+='<div id="player_select_list_' + index + '" class="player_select_list"><ul>';
				for(var i=0; i < supporting_players.length ; i++){				
					if( _this.selected_player.id == supporting_players[i].id && is_selected ){
						out+='<li style="border-style:dashed;margin-left:20px;">'+
									'<img border="0" width="16" height="16" src="'+mv_embed_path+'images/plugin.png">'+
									supporting_players[i].getName() +
							'</li>';
					}else{
						//else gray plugin and the plugin with link to select
						out+='<li style="margin-left:20px;">'+
								'<a href="#" onClick="'+ source_select_code + 'embedTypes.players.userSelectPlayer(\''+supporting_players[i].id+'\',\''+ source.getMIMEType()+'\');return false;">'+
									'<img border="0" width="16" height="16" src="'+mv_embed_path+'images/plugin_disabled.png">'+
									supporting_players[i].getName() +
								'</a>'+
							'</li>';
					}
				 }
				 out+='</ul></div>';           
	        }else
	            out+= source.getTitle() + ' - no player available';
        });
        out+='</blockquote></span>';
        this.displayHTML(out);
    },
	/*download list is exessivly complicated ... rewrite for clarity: */
	showVideoDownload:function(){		
		//load the roe if avaliable (to populate out download options:
		js_log('f:showVideoDownload '+ this.roe + ' ' + this.media_element.addedROEData);
		if(this.roe && this.media_element.addedROEData==false){
			var _this = this;
			this.displayHTML(gM('loading_txt'));
			do_request(this.roe, function(data)
            {
               _this.media_element.addROE(data);                             
               $j('#mv_disp_inner_'+_this.id).html(_this.getShowVideoDownload());
            });	           
		}else{
			this.displayHTML(this.getShowVideoDownload());
		}       
	},
	getShowVideoDownload:function(){ 
		var out='<b style="color:white;">'+gM('download_segment')+'</b><br>';
		out+='<span style="color:white"><blockquote style="background:#000">';
		var dl_list='';
		var dl_txt_list='';
		
        $j.each(this.media_element.getSources(), function(index, source){
        	var dl_line = '<li>' + '<a style="color:white" href="' + source.getURI() +'"> '
                + source.getTitle()+'</a> '+ '</li>'+"\n";            
			if(	 source.getURI().indexOf('?t=')!==-1){
                out+=dl_line;
			}else if(this.getMIMEType()=="text/cmml"){
				dl_txt_list+=dl_line;
			}else{
				dl_list+=dl_line;
			}
        });
        if(dl_list!='')
        	out+='</blockquote>'+gM('download_full')+'<blockquote style="background:#000">' + dl_list + '</blockquote>';
        if(dl_txt_list!='')
			out+='</blockquote>'+gM('download_text')+'<blockquote style="background:#000">' + dl_txt_list +'</blockquote></span>';
       	return out;
	},
	/*
	*  base embed controls
	*	the play button calls
	*/
	play:function(){
		var this_id = (this.pc!=null)?this.pc.pp.id:this.id;		
		js_log( "mv_embed play:" + this.id);		
		js_log('thum disp:'+this.thumbnail_disp);
		//check if thumbnail is being displayed and embed html
		if( this.thumbnail_disp ){			
			if( !this.selected_player ){
				js_log('no selected_player');
				//this.innerHTML = this.getPluginMissingHTML();
				//$j(this).html(this.getPluginMissingHTML());
				$j('#'+this.id).html( this.getPluginMissingHTML() );
			}else{
                this.doEmbedHTML();
                this.onClipDone_disp=false;               
            	this.paused=false;       
            	this.thumbnail_disp=false;     	
			}
		}else{
			//the plugin is already being displayed			
			this.paused=false; //make sure we are not "paused"
		}				
       	$j("#mv_play_pause_button_" + this_id).attr({
       		'class':'pause_button'
       	}).unbind( "click" ).click(function(){
       		$j('#' + this_id ).get(0).pause();
       	});
	},
	/*
	 * base embed pause
	 * 	there is no general way to pause the video
	 *  must be overwritten by embed object to support this functionality.
	 */
	pause: function(){
		var this_id = (this.pc!=null)?this.pc.pp.id:this.id;		
		js_log('mv_embed:do pause');		
        //(playing) do pause        
        this.paused=true; 
        //update the ctrl "paused state"            	
        $j("#mv_play_pause_button_" + this_id).attr({
        		'class':'play_button'        		
        }).unbind( "click" ).click(function(){
        	$j('#'+this_id).get(0).play();
        });
	},	
	/*
	 * base embed stop (can be overwritten by the plugin)
	 */
	stop: function(){
		var _this = this;
		js_log('mvEmbed:stop:'+this.id);
		
		//no longer seeking:
		this.didSeekJump=false;
		
		//first issue pause to update interface	(only call the parent) 
		if(this['parent_pause']){
			this.parent_pause();
		}else{
			this.pause();
		}	
		//reset the currentTime: 
		this.currentTime=0;
		//check if thumbnail is being displayed in which case do nothing
		if(this.thumbnail_disp){
			//already in stooped state
			js_log('already in stopped state');
		}else{
			//rewrite the html to thumbnail disp
			this.doThumbnailHTML();
			this.bufferedPercent=0; //reset buffer state
			this.setSliderValue(0);
			this.setStatus( this.getTimeReq() );
		}
		//make sure the big playbutton is has click action: 
		$j('#big_play_link_' + _this.id).unbind('click').click(function(){
			$j('#' +_this.id).get(0).play();
		});
		
        if(this.update_interval)
        {
            clearInterval(this.update_interval);
            this.update_interval = null;
        }
	},
	toggleMute:function(){
		var this_id = (this.pc!=null)?this.pc.pp.id:this.id;
		js_log('f:toggleMute');		
		if(this.muted){
			this.muted=false;
			$j('#volume_icon_'+this_id).removeClass('volume_off').addClass('volume_on');
		}else{
			this.muted=true;
			$j('#volume_icon_'+this_id).removeClass('volume_on').addClass('volume_off');
		}
	},
	fullscreen:function(){
		js_log('fullscreen not supported for this plugin type');
	},
	/* returns bool true if playing or paused, false if stooped
	 */
	isPlaying : function(){
		if(this.thumbnail_disp){
			//in stoped state
			return false;
		}else if( this.paused ){
			//paused state
			return false;
		}else{
			return true;
		}
	},
	isPaused : function(){
		return this.isPlaying() && this.paused;
	},
	isStoped : function(){
		return this.thumbnail_disp;
	},
	playlistSupport:function(){
		//by default not supported (implemented in js)
		return false;
	},
	postEmbedJS:function(){
		return '';
	},
	getPluginEmbed : function(){
		if (window.document[this.pid]){
	        return window.document[this.pid];
		}
		if (embedTypes.msie){
			return document.getElementById(this.pid );
		}else{
	    	 if (document.embeds && document.embeds[this.pid])
	        	return  document.embeds[this.pid];
		}
		return null;
	},
	//HELPER Functions for selected source	
	/*
	* returns the selected source url for players to play
	*/
	getURI : function( seek_time_sec ){
		return this.media_element.selected_source.getURI( this.seek_time_sec );
	},
	supportsURLTimeEncoding: function(){
		return this.media_element.selected_source.supports_url_time_encoding;
	},
	setSliderValue: function(perc, hide_progress){
		
		//js_log('setSliderValue:'+perc+' ct:'+ this.currentTime);
		var this_id = (this.pc)?this.pc.pp.id:this.id;
		//alinment offset: 
		if(!this.mv_seeker_width)
			this.mv_seeker_width = $j('#mv_seeker_slider_'+this_id).width();				
		
		var val = Math.round( perc  * $j('#mv_seeker_'+this_id).width() - (this.mv_seeker_width*perc));
		if(val > ($j('#mv_seeker_'+this_id).width() -this.mv_seeker_width) )
			val = $j('#mv_seeker_'+this_id).width() -this.mv_seeker_width ;
		$j('#mv_seeker_slider_'+this_id).css('left', (val)+'px' );
		
		//update the playback progress bar
		if( ! hide_progress ){
			$j('#mv_seeker_' + this_id + ' .mv_playback').css("width",  Math.round( val + (this.mv_seeker_width*.5) ) + 'px' );
		}else{
			//hide the progress bar
			$j('#mv_seeker_' + this_id + ' .mv_playback').css("width", "0px");
		}
		
		//update the buffer progress bar (if available )
		if( this.bufferedPercent!=0 ){
			//js_log('bufferedPercent: ' + this.bufferedPercent);			
			if(this.bufferedPercent > 1)
				this.bufferedPercent=1;				
			$j('#mv_seeker_' + this_id + ' .mv_buffer').css("width", (this.bufferedPercent*100) +'%' );
		}else{
			$j('#mv_seeker_' + this_id + ' .mv_buffer').css("width", '0px' );
		}
		
		//js_log('set#mv_seeker_slider_'+this_id + ' perc in: ' + perc + ' * ' + $j('#mv_seeker_'+this_id).width() + ' = set to: '+ val + ' - '+ Math.round(this.mv_seeker_width*perc) );
		//js_log('op:' + offset_perc + ' *('+perc+' * ' + $j('#slider_'+id).width() + ')');
	},
	highlightPlaySection:function(options){
		js_log('highlightPlaySection');		
		var this_id = (this.pc)?this.pc.pp.id:this.id;
		var dur = this.getDuration();
		var hide_progress = true;
		//set the left percet and update the slider: 
		rel_start_sec = ( ntp2seconds( options['start']) - this.start_offset );
		
		var slider_perc=0;
		if( rel_start_sec <= 0 ){
			left_perc =0; 			
			options['start'] = seconds2ntp( this.start_offset );
			rel_start_sec=0;			
			this.setSliderValue( 0 , hide_progress);
		}else{
			left_perc = parseInt( (rel_start_sec / dur)*100 ) ;		
			slider_perc = (left_perc / 100);
		}			
		if( ! this.isPlaying() ){
			this.setSliderValue( slider_perc , hide_progress);		
		}
		
		width_perc = parseInt( (( ntp2seconds( options['end'] ) - ntp2seconds( options['start'] ) ) / dur)*100 ) ; 							
		if( (width_perc + left_perc) > 100 ){
			width_perc = 100 - left_perc; 
		}		
		//js_log('should hl: '+rel_start_sec+ '/' + dur + ' re:' + rel_end_sec+' lp:'  + left_perc + ' width: ' + width_perc);	
		$j('#mv_seeker_' + this_id + ' .mv_highlight').css({
			'left':left_perc+'%',
			'width':width_perc+'%'			
		}).show();				
		
		this.jump_time =  options['start'];
		this.seek_time_sec = ntp2seconds( options['start']);
		//trim output to 
		this.setStatus( gM('seek_to')+' '+ seconds2ntp( this.seek_time_sec ) );
		js_log('DO update: ' +  this.jump_time);
		this.updateThumbTime( rel_start_sec );	
	},
	hideHighlight:function(){
		var this_id = (this.pc)?this.pc.pp.id:this.id;
		$j('#mv_seeker_' + this_id + ' .mv_highlight').hide();
		this.setStatus( this.getTimeReq() );
		this.setSliderValue( 0 );
	},
	setStatus:function(value){
		var id = (this.pc)?this.pc.pp.id:this.id;
		//update status:
		$j('#mv_time_'+id).html(value);
	}	
}