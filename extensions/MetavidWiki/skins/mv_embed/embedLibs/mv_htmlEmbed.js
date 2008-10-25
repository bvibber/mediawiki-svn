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
   	ready_to_play:true,
   	pauseTime:0,
   	currentTime:0,
   	start_offset:0,
   	monitorTimerId:false,
	play:function(){
		//call the parent
    	this.parent_play();
    	
		js_log('f:play: htmlEmbedWrapper');
		var ct = new Date();	
		this.clockStartTime = ct.getTime();
			
		//start up monitor: 
		this.monitor();				
	},
	stop:function(){
		this.pause();
		window.clearInterval( this.monitorTimerId );
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
	        	this.monitorTimerId = window.setInterval('$j(\'#'+this.id+'\').get(0).monitor()', 250);
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
	renderTimelineThumbnail:function( options ){
		//generate a scaled down version _that_ we can clone if nessisary 
		//add a not visiable container to the body:		
		var do_refresh = (typeof options['refresh'] != 'undefined')?true:false;
		
		var thumb_render_id =   this.id +'_thumb_render_'+ options.height;
		if( $j('#' + thumb_render_id ).length == 0 ||  do_refresh ){
			//set the font scale down percentage: (kind of arbitrary) 
			var scale_perc = options.width / $j(this).width();			
			//min scale font percent of 70 (overflow is hidden) 
			var font_perc  = ( Math.round( scale_perc*100 ) < 80 )?80:Math.round( scale_perc*100 ); 		
						
			$j('body').append( '<div id="' + thumb_render_id + '" style="display:none">'+
									'<div style="display:block;border:solid thin black;'+
									'width:'+options.width+'px;height:'+options.height+'px;overflow:hidden;" >'+								    	
											this.getThumbnailHTML() + 
									'</div>'+
						  	  '</div>' 
						  	);
			//scale down the font:		
			$j('#' + thumb_render_id + ' *').filter('span,div,p,h,h1,h2,h3,h4,h5,h6').css('font-size',font_perc+'%')
			
			//replace out links:
			$j('#' + thumb_render_id +' a').each(function(){
				$j(this).replaceWith("<span>" + $j(this).html() + "</span>");
			});	
			
			//scale images that have width or height:
			$j('#' + thumb_render_id + ' img').filter('[width]').each(function(){
				$j(this).attr({ 
						'width':$j(this).attr('width') * scale_perc,
					 	'height':$j(this).attr('height') * scale_perc
					 } 
				);
			});
		} 			
		return $j('#' + thumb_render_id ).html();  			 
	},
	//nothing to update in static html display: (return a static representation) 
	//@@todo render out a mini text "preview"
	updateThumbTime:function( float_time ){
		return ;
	},
	getEmbedHTML:function(){
		js_log('f:html:getEmbedHTML');
		//set up the css for our parent div: 		
		$j(this).css({'width':this.pc.pp.width, 'height':this.pc.pp.height, 'overflow':"hidden"});
		//@@todo support more smil image layout stuff: 
		
		//wrap output in videoPlayer_ div:
		$j(this).html('<div id="videoPlayer_'+ this.id+'">'+this.getThumbnailHTML()+'</div>');
	},
	getThumbnailHTML:function(){
		var out='';
		if( this.pc.type =='image/jpeg'){
			js_log('should put src: '+this.pc.src);
			out = '<img src="'+this.pc.src+'">';
		}else{
			out = this.pc.wholeText;
		}
		return out;
	},
	doThumbnailHTML:function(){
		js_log('f:htmlEmbed:doThumbnailHTML');
		this.getEmbedHTML();
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
	//gives a chance to make any neseary external requests
	//@@todo we can "start loading images" if we want
	on_dom_swap:function(){
		this.loading_external_data=false
		this.ready_to_play=true;		
		debugger;
		return ;		
	}	
}