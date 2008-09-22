/* 
 * the playlist object code 
 * only included if playlist object found
 * 
 * part of mv_embed: 
 * http://metavid.ucsc.edu/wiki/index.php/Mv_embed 
 */
 js_log('load mv_playlist');
var mv_default_playlist_attributes = {
	//playlist attributes :
	"id":null,
	"title":null,
	"width":400,
	"height":300,
	"desc":'',
	"controls":true,
	//playlist user controlled features
    "linkback":null, 
	"src":null,
	"embed_link":true,
	
	//enable sequencer? (only display top frame no navigation or accompanying text
	"sequencer":false
}

//the layout for the playlist object
var pl_layout = {
	seq_title:.1,
	clip_desc:.63, //displays the clip description
	clip_aspect:1.33,  // 4/3 video aspect ratio
	seq:.25,		   	  //display clip thumbnails 
	seq_thumb:.25,	 //size for thumbnails (same as seq by default) 
	seq_nav:0,	//for a nav bar at the base (currently disabled)
	//some pl_layout info:
	title_bar_height:20,
	control_height:29
}
//globals:
var mv_lock_vid_updates=false;
//10 possible colors for clips: (can be in hexadecimal)
var mv_clip_colors = new Array('aqua', 'blue', 'fuchsia', 'green', 'lime', 'maroon', 'navy', 'olive', 'purple', 'red');
//the base url for requesting stream metadata 
if(typeof wgServer=='undefined'){
	var defaultMetaDataProvider = 'http://metavid.ucsc.edu/overlay/archive_browser/export_cmml?stream_name=';
}else{
	var defaultMetaDataProvider = wgServer+wgScript+'?title=Special:MvExportStream&feed_format=roe&stream_name=';
}

var mvPlayList = function(element) {		
	return this.init(element);
};
//set up the mvPlaylist object
mvPlayList.prototype = {
	instanceOf:'mvPlayList',
	pl_duration:null,
	update_tl_hook:null,
	clip_ready_count:0,
	cur_clip:null,	
	start_clip:null, 
	start_clip_src:null,
	disp_play_head:null,
	userSlide:false,
	loading:true,
	loading_external_data:true, //load external data by default
	tracks:{},
	default_track:null, // the default track to add clips to.
	init : function(element){
		js_log('init');				
		//add default track & default track pointer: 
		this.tracks[0]= new trackObj;
		this.default_track = this.tracks[0];
		
		//get all the attributes:
	  	for(var attr in mv_default_playlist_attributes){       
	        if(element.getAttribute(attr)){
	            this[attr]=element.getAttribute(attr);
	            //js_log('attr:' + attr + ' val: ' + video_attributes[attr] +" "+'elm_val:' + element.getAttribute(attr) + "\n (set by elm)");  
	        }else{        
	            this[attr]=mv_default_playlist_attributes[attr];
	            //js_log('attr:' + attr + ' val: ' + video_attributes[attr] +" "+ 'elm_val:' + element.getAttribute(attr) + "\n (set by attr)");  
	        }
	    }
		//make sure height and width are int:
		this.width=	parseInt(this.width);
		this.height=parseInt(this.height);
		
	    //if style is set override width and height
	    if(element.style.width)this.width = parseInt(element.style.width.replace('px',''));
	    if(element.style.height)this.height = parseInt(element.style.height.replace('px',''));	    	   	    
	    
	    //@@todo more attribute value checking: 
	    
    	//if no src is specified try and use the innerHTML as a sorce type:
    	if(!this.src){	    	
    		//no src set check for innerHTML: 
    		if(element.innerHTML==''){
    			//check if we are in IE .. (ie does not expose innerHTML for video or playlist tags) 
    			if(embedTypes.msie){
    				var bodyHTML = document.body.innerHTML;
    				var vpos = bodyHTML.indexOf(element.outerHTML);
    				if(vpos!=-1){
    					//js_log('vpose:'+vpos +' '+ element.outerHTML.length );
    					vpos= vpos+ element.outerHTML.length;
    					vclose = bodyHTML.indexOf('</'+element.nodeName+'>', vpos);
    					//js_log("found vopen at:"+vpos + ' close:'+ vclose);
    					//js_log('innerHTML:'+bodyHTML.substring(vpos, vclose));
    					this['data'] = bodyHTML.substring(vpos, vclose);
						this.loading_external_data=false;
    				}    				
    			}
    		}else{
	    		this.data = element.innerHTML;
    			this.loading_external_data=false;
    		}
    	}else{
    		js_log('src exists');
	        this.inner_playlist_html=element.innerHTML;
    	}
	    //get and parse the src playlist *and update the page*
	    return this;	    
	},
	//the element has now been swaped into the dom: 
	on_dom_swap:function(){
		js_log('pl: dom swap');
		//get and load the html:
		this.getHTML();
	},
	//run inheritEmbedObj on every clip (we have changed the playback method) 
	inheritEmbedObj:function(){		
		$j.each(this.tracks, function(i,track){	
			track.inheritEmbedObj();			
		});
	},	
	doOptionsHTML:function(){
		//grab "options" use current clip:
		this.cur_clip.embed.doOptionsHTML();
	},
	//pulls up the video editor inline
	doEditor:function(){
		//black out the page: 
		$j('body').append('<div id="overlay"/> '+
						'<div id="modalbox" class="editor">');
		$j('#modalbox').html('loading editor<blink>...</blink>');
						
	},
	selectPlaybackMethod:function(){
		this.cur_clip.embed.selectPlaybackMethod();
	},
	closeDisplayedHTML:function(){
		this.cur_clip.embed.closeDisplayedHTML();
	},
	showVideoDownload:function(){
		this.cur_clip.embed.showVideoDownload();
	},
	showEmbedCode:function(){
		var embed_code = '&lt;script type=&quot;text/javascript&quot; '+
						'src=&quot;'+mv_embed_path+'mv_embed.js&quot;&gt;&lt;/script&gt '+"\n" + 
						'&lt;playlist id=&quot;'+this.id+'&quot; ';
						if(this.src){
							embed_code+='src=&quot;'+this.src+'&quot; /&gt;';
						}else{
							embed_code+='&gt;'+"\n";
							embed_code+= this.data.htmlEntities();
							embed_code+='&lt;playlist/&gt;';
						}
		this.cur_clip.embed.showEmbedCode(embed_code);
	},
	getPlaylist:function(){		
		js_log("f:getPlaylist: " + this.srcType );
		//@@todo lazy load plLib
		eval('var plObj = '+this.srcType+'Playlist;');	
   	  	//import methods from the plObj to this
   	  	for(var method in plObj){
        	//js parent preservation for local overwritten methods
        	if(this[method])this['parent_' + method] = this[method];
            this[method]=plObj[method];
            js_log('inherit:'+ method);
        }                
        if(typeof this.doParse == 'function'){
	   	  	if( this.doParse() ){
	   	  		this.doWhenParseDone();	
	   	  	}else{
	   	  		js_log("error: failed to parse playlist");
	   	  		//error or parse needs to do ajax requests	
	   	  	}
        }else{
        	js_log('error: method doParse not found in plObj'+ this.srcType);		        	
        }        		
	},
	doWhenParseDone:function(){				
		js_log('f:doWhenParseDone');
		//do additional int for clips: 
		var _this = this;
		_this.clip_ready_count=0;		
		for(var i in this.default_track.clips){
			var clip = 	this.default_track.clips[i];
			if(clip.embed.ready_to_play){
				_this.clip_ready_count++;
				continue;
			}
			js_log('clip sources count: '+ clip.embed.media_element.sources.length);		
			clip.embed.on_dom_swap();
			if(clip.embed.loading_external_data==false && 
	   			clip.embed.init_with_sources_loadedDone==false){
					clip.embed.init_with_sources_loaded();
			}					
		}
		//@@todo for some plugins we have to conform types of clips
		// ie vlc can play flash _followed_by_ ogg _followed_by_ whatever 
		// 		but
		// native ff 3.1a2 can only play ogg 
		if(_this.clip_ready_count == this.getClipCount() ){
			js_log("done initing all clips");
			this.loading=false;
			this.getHTML();
		}else{
			js_log("only "+ _this.clip_ready_count +" clips done, scheduling callback:");
			setTimeout('document.getElementById(\''+this.id+'\').doWhenParseDone()', 250);
		}				    	    	
	},
	doWhenClipLoadDone:function(){
		this.loading=false;
		this.getHTML();
	},	
	getDuration:function(regen){			
		//js_log("GET PL DURRATION for : "+ this.tracks[this.default_track_id].clips.length + 'clips');
		if(!regen && this.pl_duration)
			return this.pl_duration;
						
		var durSum=0;		
		$j.each(this.default_track.clips, function(i,clip){	
			if(clip.embed){			
				js_log('plDUR:add : '+ clip.getDuration());
				clip.dur_offset=durSum;
				durSum+=clip.getDuration();
			}else{
				js_log("ERROR: clip " +clip.id + " not ready");
			}
		});
		this.pl_duration=durSum;		
		//js_log("return dur: " + this.pl_duration);
		return this.pl_duration;
	},
	getDataSource:function(){	
		js_log("f:getDataSource "+ this.src);
		//determine the type / first is it m3u or xml? 	
		var pl_parent = this;
		this.makeURLAbsolute();
		if(this.src!=null){
			do_request(this.src, function(data){
				pl_parent.data=data;
				pl_parent.getSourceType();
			});	
		}
	},
	getSourceType:function(){
		js_log('data type of: '+ this.src + ' = ' + typeof (this.data) + "\n"+ this.data);
		this.srcType =null;
		//if not external use different detection matrix
		if(this.loading_external_data){				
			if(typeof this.data == 'object' ){
				js_log('object');		
				//object assume xml (either xspf or rss) 
				plElm = this.data.getElementsByTagName('playlist')[0];
				if(plElm){
					if(plElm.getAttribute('xmlns')=='http://xspf.org/ns/0/'){
						this.srcType ='xspf';
					}
				}
				//check itunes style rss "items" 
				rssElm = this.data.getElementsByTagName('rss')[0];
				if(rssElm){
					if(rssElm.getAttribute('xmlns:itunes')=='http://www.itunes.com/dtds/podcast-1.0.dtd'){
						this.srcType='itunes';						
					}					
				}				
				//check for smil tag: 
				smilElm = this.data.getElementsByTagName('smil')[0];
				if(smilElm){
					//don't check for dtd yet.. (have not defined the smil subset) 
					this.srcType='smil';
				}
			}else if(typeof this.data == 'string'){		
				js_log('String');
				//look at the first line: 
				var first_line = this.data.substring(0, this.data.indexOf("\n"));
				js_log('first line: '+ first_line);	
				//string
				if(first_line.indexOf('#EXTM3U')!=-1){
					this.srcType = 'm3u';
				}else if(first_line.indexOf('<smil')!=-1){
					//@@todo parse string
					this.srcType = 'smil';
				}
			}
		}else{
			js_log("data is inline");
			//inline xml not supported:
			//if(this.data.getAttribute('xmlns')=='http://xspf.org/ns/0/'){
			//	this.srcType='xspf';
			//}else{
				//@@todo do inline version processing: 
				this.srcType='inline';
			//}		
		}
		if(this.srcType){
			js_log('is of type:'+ this.srcType);
			this.getPlaylist();
		}else{
			//unkown playlist type
			js_log('unknown playlist type?');
			if(this.src){
				this.innerHTML= 'error: unknown playlist type at url:<br> ' + this.src;
			}else{
				this.innerHTML='error: unset src or unknown inline playlist data<br>';
			}
		}			
	},	
	//simple function to make a path into an absolute url if its not already
	makeURLAbsolute:function(){		
		if(this.src){
			if(this.src.indexOf('://')==-1){
				var purl = parseUri(document.URL);			
				if(this.src.charAt(0)=='/'){						
					this.src = purl.protocol +'://'+ purl.host + this.src;
				}else{
					this.src= purl.protocol +'://'+ purl.host + purl.directory + this.src;				
				}
			}
		}
	},	
	//@@todo needs to update for multi-track clip counts
	getClipCount:function(){
		return this.default_track.clips.length; 
	},	
	//},
	//takes in the playlist 
	// inherits all the properties 
	// swaps in the playlist object html/interface div	
	getHTML:function(){						
		if(this.loading){
			js_log('called getHTML (loading)');
			$j('#'+this.id).html('loading playlist<blink>...</blink>'); 
			if(this.loading_external_data){
				//load the data source chain of functions (to update the innerHTML)   			
				this.getDataSource();  
			}else{
				//detect datatype and parse directly: 
				this.getSourceType();
			}
		}else{			
			js_log('track length: ' +this.default_track.getClipCount() );''
			if(this.default_track.getClipCount()==0){
				$j(this).html('empty playlist');
				return ;
			}					
			var plObj=this;			
			//setup layout for title and dc_ clip container  
			$j(this).html('<div id="dc_'+this.id+'" style="width:'+this.width+'px;' +
					'height:'+(this.height+pl_layout.title_bar_height + pl_layout.control_height)+'px;position:relative;">' +
					'	<div style="font-size:13px" id="ptitle_'+this.id+'"></div>' +
					'</div>');												
			
			//add the playlist controls:			
			$j('#dc_'+plObj.id).append(
				'<div class="videoPlayer" style="position:absolute;top:'+(plObj.height+pl_layout.title_bar_height)+'px">' +
					'<div id="mv_embedded_controls_'+plObj.id+'" ' +
						'style="postion:relative;top:'+(plObj.height+pl_layout.title_bar_height)+'px;' +
							'width:'+plObj.width+'px" ' +
						'class="controls">' + 
						 plObj.getControlsHTML() +
					'</div>'+
				'</div>'
			);
			//once the contorls are in the DOM add hooks: 
			ctrlBuilder.addControlHooks(this);
			//add the play button:						
		  	$j('#dc_'+plObj.id).append(
		  		this.cur_clip.embed.getPlayButton()
		  	);
			
				
			$j.each(this.default_track.clips, function(i, clip){
				$j('#dc_'+plObj.id).append('<div class="clip_container" id="clipDesc_'+clip.id+'" '+
					'style="display:none;position:absolute;text-align: center;width:'+plObj.width + 'px;'+
					'height:'+(plObj.height )+'px;'+
					'top:20px;left:0px"></div>');	
				//update the embed html: 					
				clip.embed.height=plObj.height;
				clip.embed.width=plObj.width;				
				clip.embed.play_button=false;
				
				clip.embed.getHTML();//get the thubnails for everything			
				$j(clip.embed).css({ 'position':"absolute",'top':"0px", 'left':"0px"});					
				if($j('#clipDesc_'+clip.id).get(0)){
					$j('#clipDesc_'+clip.id).get(0).appendChild(clip.embed);
				}else{
					js_log('cound not find: clipDesc_'+clip.id);					
				}																
			}); 	
			if(this.cur_clip)
				$j('#clipDesc_'+this.cur_clip.id).css({display:'inline'});						 	
							
			//update the title and status bar
			this.updateBaseStatus();									
		}
	},
	updateTimeThumb:function(perc){
		//get float seconds:
		var float_sec =  (this.getDuration()*perc)
		//js_log('float sec:' +  float_sec);
	
		//update display & cur_clip:
		var pl_sum_time =0; 
		var clip_float_sec=0;
		//js_log('seeking clip: ');
		for(var i in this.default_track.clips){
			var clip = this.default_track.clips[i];
			if( (clip.getDuration() + pl_sum_time) >= float_sec ){
				if(this.cur_clip.id != clip.id){					
					$j('#clipDesc_'+this.cur_clip.id).hide();
					this.cur_clip = clip;
					$j('#clipDesc_'+this.cur_clip.id).show();
				}								
				break;
			}
			pl_sum_time+=clip.getDuration();
		}	
		//js_log('found clip: '+ this.cur_clip.id + 'transIn:');
		
		//updte start offset (@@todo should probably happen somewhere else like in getDuration() ) 
		if(!this.cur_clip.embed.start_offset)
			this.cur_clip.embed.start_offset=this.cur_clip.embed.media_element.selected_source.start_offset;	
		
		//render effects ontop:
		//issue thumbnail update request: (if plugin supports it will render out frame (if media )  
		this.cur_clip.embed.updateTimeThumb(perc);
		
		this.cur_clip.embed.currentTime = (float_sec -pl_sum_time)+this.cur_clip.embed.start_offset ;
		this.cur_clip.embed.seek_time_sec = (float_sec -pl_sum_time );
				
		this.doSmilActions();
		
	},
	updateBaseStatus:function(){
		js_log('f:updateBaseStatus');
		$j('#ptitle_'+this.id).html(''+
			'<b>' + this.title + '</b> '+				
			this.getClipCount()+' clips, <i>'+
			seconds2ntp( this.getDuration() ) + '</i>' + 
			'<a href="#" onclick="$j(\'#'+this.id+'\').get(0).doEditor();" style="float:right">edit</a>');		
		//update status:
		this.setStatus('0:0:00/'+seconds2ntp( this.getDuration() ));				
	},
	/*setStatus overide (could call the jquery directly) */
	setStatus:function(value){
		$j('#mv_time_'+this.id).html(value);
	},
	setSliderValue:function(value){
		//js_log('calling original embed slider with val: '+value);
		this.cur_clip.embed.pe_setSliderValue(value);
	},
	/*gets adds hidden desc to the #dc container*/
	getAllClipDesc : function(){		
		//js_log("build all clip details pages");		
		//debugger;
		var ay=Math.round(this.height* pl_layout.clip_desc);		
		var plObj =this;
		$j.each(plObj.default_track.clips, function(i, clip){
			//js_log('clip parent pl:'+ clip.pp.id);
			//border:solid thin
			$j('#dc_'+plObj.id).append('<div class="clip_container" id="clipDesc_'+clip.id+'" '+
				'style="display:none;position:absolute;width:'+plObj.width + 'px;'+
				'height:'+ay+'px;'+
				'top:27px;left:0px"></div>');	
			clip.getDetail();
		});
	},
	getSeqThumb: function(){
		//for each clip 
		if(this.getClipCount()>3){
			pl_layout.seq_thumb=.17;
		}else{
			pl_layout.seq_thumb=.25;
		}
		$j.each(this.default_track.clips, function(i,n){
			//js_log('add thumb for:' + n.src);
			n.getThumb();
		});
	},
	getPlayHeadPos: function(prec_done){
		var	plObj = this;
		if($j('#mv_seeker_'+this.id).length==0){
			//js_log('no playhead so we can\'t get playhead pos' );
			return 0;
		}
		var track_len = $j('#mv_seeker_'+this.id).css('width').replace(/px/, '');
		//assume the duration is static and present at .duration during playback
		var clip_perc = this.cur_clip.embed.duration / this.getDuration();
		var perc_offset =time_offset = 0;
		for(var i in this.default_track.clips){
			var clip = this.default_track.clips[i];
			if(this.cur_clip.id ==clip.id)break;
			perc_offset+=(clip.embed.duration /  plObj.getDuration());
			time_offset+=clip.embed.duration;
		} 		
		//run any update time line hooks:		
		if(this.update_tl_hook){	
			var cur_time_ms = time_offset + Math.round(this.cur_clip.embed.duration*prec_done);
			if(typeof update_tl_hook =='function'){
				this.update_tl_hook(cur_time_ms);
			}else{
				//string type passed use eval: 
				eval(this.update_tl_hook+'('+cur_time_ms+');');
			}
		}
		
		//handle offset hack @@todo fix so this is not needed:
		if(perc_offset > .66)
			perc_offset+=(8/track_len);
		//js_log('perc:'+ perc_offset +' c:'+ clip_perc + '*' + prec_done + ' v:'+(clip_perc*prec_done));
		return perc_offset + (clip_perc*prec_done);
	},
	//attempts to load the embed object with the playlist
	loadEmbedPlaylist: function(){
		//js_log('load playlist');
	},
	//called when the plugin advances to the next clip in the playlist
	playlistNext:function(){
		js_log('pl advance');
		this.cur_clip=this.getClip(1);
	},
	next: function(){		
		//advance the playhead to the next clip			
		var next_clip = this.getClip(1);
		if(this.cur_clip.embed.supports['playlist_driver']){ //where the plugin is just feed a playlist
			//do next clip action on start_clip embed cuz its the one being displayed: 
			this.start_clip.embed.playlistNext();
			this.cur_clip=next_clip;					
		}else if(this.cur_clip.embed.supports['playlist_swap_loader']){
			//where the plugin supports pre_loading future clips and manage that in javascript
			//stop current clip
			this.cur_clip.embed.stop();
			this.updateCurrentClip(next_clip);				
			this.cur_clip.embed.play();			
		}else{
			js_log('do next');								
			this.switchPlayingClip(next_clip);
		}		
	},
	updateCurrentClip:function(new_clip){
		//do swap:		
		$j('#clipDesc_'+this.cur_clip.id).hide();			
		this.cur_clip=new_clip;			
		$j('#clipDesc_'+this.cur_clip.id).show();
	},
	prev: function(){
		//advance the playhead to the previous clip			
		var prev_clip = this.getClip(-1);
		if(this.cur_clip.embed.supports['playlist_driver']){
			this.start_clip.embed.playlistPrev();
			this.cur_clip=prev_clip;	
		}else if(this.cur_clip.embed.supports['playlist_swap_loader']){
			//where the plugin supports pre_loading future clips and manage that in javascript
			//pause current clip
			this.cur_clip.embed.pause;
			//do swap:
			this.updateCurrentClip(prev_clip);			
			this.cur_clip.embed.play();			
		}else{			
			js_log('do prev hard embed swap');										
			this.switchPlayingClip(prev_clip);
		}		
	},
	switchPlayingClip:function(new_clip){
		//swap out the existing embed code for next clip embed code
		$j('#mv_ebct_'+this.id).empty();
		new_clip.embed.width=this.width;
		new_clip.embed.height=this.height;
		//js_log('set embed to: '+ new_clip.embed.getEmbedObj());
		$j('#mv_ebct_'+this.id).html( new_clip.embed.getEmbedObj() );
		this.cur_clip=new_clip;
		//run js code: 
		this.cur_clip.embed.pe_postEmbedJS();
	},
	//playlist play
	play: function(){
		var plObj=this;
		js_log('pl play');
		//hide the playlist play button: 
		$j('#big_play_link_'+this.id).hide();				
		
		this.start_clip = this.cur_clip;		
		this.start_clip_src= this.cur_clip.src;
		 
		if(this.cur_clip.embed.supports['playlist_swap_loader'] ){
			//navtive support:
			// * pre-loads clips
			// * mv_playlist smil extension, mannages transitions animations overlays etc. 			
			js_log('clip obj supports playlist swap_loader (ie playlist controlled playback)');
			//update cur clip based if sequence playhead set: 
			var d = new Date();
			this.clockStartTime = d.getTime();
			this.monitor();
		
			//@@todo pre-load each clip: 
			this.cur_clip.embed.play();			
		}else if(this.cur_clip.embed.supports['playlist_driver']){				
			js_log('playlist_driver');
			//embedObject is feed the playlist info directly and mannages next/prev
			this.cur_clip.embed.playMovieAt(this.cur_clip.order);
		}else{
			//not much playlist support just play the first clip:
			js_log('basic play');
			//play cur_clip			
			this.cur_clip.embed.play();		
		}
	},	
	toggleMute:function(){
		this.cur_clip.embed.toggleMute();
	},
	//wrappers for call to pl object to current embed obj
	play_or_pause:function(){
		js_log('pl:play_or_pause');			
		this.cur_clip.embed.play_or_pause();		
	},
	fullscreen:function(){
		this.cur_clip.embed.fullscreen();
	},
	//playlist stops playback for the current clip (and resets state for start clips)
	stop:function(){
		/*js_log("pl stop:"+ this.start_clip.id + ' c:'+this.cur_clip.id);
		//if start clip 
		if(this.start_clip.id!=this.cur_clip.id){
			//restore clipDesc visibility & hide desc for start clip: 
			$j('#clipDesc_'+this.start_clip.id).html('');
			this.start_clip.getDetail();
			$j('#clipDesc_'+this.start_clip.id).css({display:'none'});
			this.start_clip.setBaseEmbedDim(this.start_clip.embed);
			//equivalent of base stop
			$j('#'+this.start_clip.embed.id).html(this.start_clip.embed.getThumbnailHTML());
			this.start_clip.embed.thumbnail_disp=true;
		}
		//empty the play-back container
		$j('#mv_ebct_'+this.id).empty();*/
		
		//make sure the current clip is visable:
		$j('#clipDesc_'+this.cur_clip.id).css({display:'inline'});
		
		//do stop current clip
		this.cur_clip.embed.stop();
	},
	//gets playlist controls large control height for sporting 
	//next prev button and more status display
	getControlsHTML:function(){
		//get controls from current clip  (add some playlist specific controls:  		
		this.cur_clip.embed.supports['prev_next']=true;		
		return ctrlBuilder.getControls(this.cur_clip.embed);
	},	
	//ads colors/dividers between tracks
	colorPlayHead: function(){
		//total duration:		
		var pl_duration = this.getDuration();
		var track_len = $j('#slider_'+this.id).css('width').replace(/px/, '');
		var cur_pixle=0;
		
		//set up plObj
		var plObj = this;
		//js_log("do play head total dur: "+pl_duration );
		$j.each(this.default_track.clips, function(i, clip){
			var perc = (clip.embed.getDuration() / pl_duration );
			pwidth = Math.round(perc*track_len);
			//do div border-1 from pixle to current pixle
			$j('#slider_'+plObj.id).append('<div style="' +
					'position:absolute;' + 
					'left:'+cur_pixle +'px;'+
					'width:'+pwidth + 'px;'+
					'height:4px;'+
					'top:0px;'+
					'z-index:1;'+
					'border:solid thin;' +
					'filter:alpha(opacity=40);'+
					'-moz-opacity:.40;'+
					'background:'+clip.getColor()+'"></div>');			
			//put colors on top of playhead/track						
			//js_log('offset:' + cur_pixle +' width:'+pwidth+' add clip'+ clipID + 'is '+clip.embed.getDuration()+' = ' + perc +' of ' + track_len);
			cur_pixle+=pwidth;						
		});
				
		//$j('#dc_'+this.id).append('');
	},
	setUpHover:function(){
		js_log('Setup Hover');
		//set up hover for prev,next 
		var th = 50;
		var tw = th*pl_layout.clip_aspect;
		var plObj = this;
		$j('#mv_prev_link_'+plObj.id+',#mv_next_link_'+plObj.id).hover(function() {
		  	var clip = (this.id=='mv_prev_link_'+plObj.id)?
		  		plObj.getClip(-1):plObj.getClip(1);
		  	//get the position of #mv_perv|next_link:
  			var loc = getAbsolutePos(this.id);
		  	//js_log('Hover: x:'+loc.x + ' y:' + loc.y + ' :'+clip.img);
		   	$j("body").append('<div id="mv_Athub" style="position:absolute;' +
	   			'top:'+loc.y+'px;left:'+loc.x+'px;width:'+tw+'px;height:'+th+'px;">'+
				'<img style="border:solid 2px '+clip.getColor()+';position:absolute;top:0px;left:0px;" width="'+tw+'" height="'+th+'" src="'+clip.img+'"/>'+
			'</div>');
      }, function() {
      		$j('#mv_Athub').remove();
      });     
	},
	//returns a clip. If offset is out of bound returns first or last clip
	getClip: function(clip_offset){		
		if(!clip_offset)clip_offset=0;	
					
		var cov = this.cur_clip.order + clip_offset;
		var cmax = this.getClipCount()-1;
		//js_log('cov:'+cov +' cmax:'+ cmax);
		
		//force first or last clip if offset is outOfBounds 
		if( cov >= 0 && cov <= cmax ){
			return this.default_track.clips[cov]
		}else{
			if(cov<0)return this.default_track.clips[0];
			if(cov>cmax)return this.default_track.clips[cmax];
		}
	},
	/* 
	 *genneric add Clip to ~default~ track
	 */
	addCliptoTrack: function(clipObj, pos){
		js_log('add clip' + clipObj.id +' to track');		
		//set up default track if missing: 
		if(typeof(this.default_track)=='undefined'){
			this.tracks[0]=new trackObj();
			this.default_track = this.tracks[0]
		}
		//set the first clip to current (maybe depricate) 
		if(clipObj.order==0){
			if(!this.cur_clip)this.cur_clip=clipObj;
		}		
		this.default_track.addClip(clipObj, pos);
		
	},
	swapClipDesc: function(req_clipID, callback){
		//hide all but the requested
		var plObj=this;
		js_log('r:'+req_clipID+' cur:'+plObj.id);
		if(req_clipID==plObj.cur_clip.id){
			js_log('no swap to same clip');
		}else{
			//fade out clips
			req_clip=null;
			$j.each(this.default_track.clips, function(i, clip){
				if(clip.id!=req_clipID){
					//fade out if display!=none already
					if($j('#clipDesc_'+clip.id).css('display')!='none'){
						$j('#clipDesc_'+clip.id).fadeOut("slow");
					}
				}else{
					req_clip =clip;
				}
			});
			//fade in requested clip *and set req_clip to current
			$j('#clipDesc_'+req_clipID).fadeIn("slow", function(){
					plObj.cur_clip = req_clip;
					if(callback)
						callback();
			});		
		}
	},	
	getPLControls: function(){
		js_log('getPL cont');
		return 	'<a id="mv_prev_link_'+this.id+'" title="Previus Clip" onclick="document.getElementById(\''+this.id+'\').prev();return false;" href="#">'+
					getTransparentPng({id:'mv_prev_btn_'+this.id,style:'float:left',width:'27', height:'27', border:"0", 
						src:mv_embed_path+'images/vid_prev_sm.png' }) + 
				'</a>'+
				'<a id="mv_next_link_'+this.id+'"  title="Next Clip"  onclick="document.getElementById(\''+this.id+'\').next();return false;" href="#">'+
					getTransparentPng({id:'mv_next_btn_'+this.id,style:'float:left',width:'27', height:'27', border:"0", 
						src:mv_embed_path+'images/vid_next_sm.png' }) + 
				'</a>';		
	}
}	
var gclipFocus=null;
//delay the swap by .2 seconds
function mvSeqOver(clipID,playlistID){
	setTimeout('doMvSeqOver(\''+clipID+'\',\''+playlistID+'\')', 200);
	gclipFocus=clipID;
}
function mvSeqOut(){
	gclipFocus=null;
}
function doMvSeqOver(clipID, playlistID){
	if(!mv_lock_vid_updates){
		if(gclipFocus==clipID){
			plElm = document.getElementById(playlistID);
			//js_log("got playlist by id: "+ plElm.id);
			if(plElm)plElm.swapClipDesc(clipID);
		}
	}
}

/* Object Stubs: 
 * 
 * @videoTrack ... stores clips and layer info
 * 
 * @clip... each clip segment is a clip object. 
 * */
var mvClip = function(o) {	
	if(o)
		this.init(o);
	return this;
};
//set up the mvPlaylist object
mvClip.prototype = {
	id:null, //clip id
	pp:null, // parent playlist
	order:null, //the order/array key for the current clip
	src:null,
	info:null,
	title:null,
	mvclip:null,
	type:null,
	img:null,
	duration:null,
	loading:false,
	isAnimating:false,			
	init:function(o){		
		//init object including pointer to parent
		for(var i in o){
			js_log('clip init vars: '+ i + ' ' + o[i]);
			this[i]=o[i];
		};		
		js_log('id is: '+ this.id);
	},
	//setup the embed object:
	setUpEmbedObj:function(){
		//init:
		this.embed=null;		
		//js_log('setup embed for clip '+ this.id + ':id is a function?'); 
		//set up the pl_mv_embed object:
		var init_pl_embed={id:'e_'+this.id,
			pc:this, //parent clip
			src:this.src};
		
		this.setBaseEmbedDim(init_pl_embed);
		//always display controls for playlists: 
		
		//if in sequence mode hide controls / embed links 		
		//			init_pl_embed.play_button=false;
		init_pl_embed.controls=false;	
		//if(this.pp.sequencer=='true'){
		init_pl_embed.embed_link=null;	
		init_pl_embed.linkback=null;	
		//}else{						
			//set optional values if present
		//	if(this.linkback)init_pl_embed.linkback=this.linkback;
		//	if(this.pp.embed_link)init_pl_embed.embed_link=true;
		//}
		if(this.img)init_pl_embed['thumbnail']=this.img;
		
		if(this.type)init_pl_embed['type'] = this.type;
		
		this.embed = new PlMvEmbed(init_pl_embed);
				
		js_log('ve src len:'+ this.embed.media_element.sources.length);
		//js_log('media element:'+ this.embed.media_element.length);
		
		//js_log('type of embed:' + typeof(this.embed) + ' seq:' + this.pp.sequencer+' pb:'+ this.embed.play_button);
	},
	//returns the mvClip representation of the clip ie stream_name?start_time/end_time
	getMvClip:function(){
		if(this.mvclip)return this.mvclip;
		return false;
	},
	//@@todo group all remote data requests
	//set src and image & title & desc from metavid source data 
	getRemoteData:function(callback){
		var thisClip =this;	
		//check for js_log("gDuration:setupEmbed" + this.embed.media_element.sources.length);mvclip type:	
		if(thisClip.mvclip){
			thisClip.loading=true;
			if(!thisClip.mvMetaDataProvider){
				thisClip.mvMetaDataProvider = defaultMetaDataProvider;
			}
			//get xml data to resolve location of the media, desc + caption data
			var url = thisClip.mvMetaDataProvider +	this.mvclip.replace(/\?/, "&");			
			
			do_request(url, function(data){
				//ajax return (done loading) 
				thisClip.loading=false;
				//search for and set video src:
				js_log('data:'+data);
				$j.each(data.getElementsByTagName('video'), function(inx,n){	
					if(n.getAttribute('default')=='true'){
						thisClip.src=n.getAttribute('src');						
					};
				});				
				js_log('set src: '+ thisClip.src);
				
				//idorate through top head nodes: 			
				$j.each(data.getElementsByTagName('head')[0].childNodes, function(inx,n){
					//js_log('node:'+ n.nodeName+ ' inx:'+inx);
					if(!thisClip.title){
						if(n.nodeName.toLowerCase()=='title'){
							thisClip.title = n.textContent;
						}
					}
					//search for and set linkback: 
					if(!thisClip.linkback){							
						if(n.nodeName.toLowerCase()=='link' && n.getAttribute('type')=='text/html'){
							thisClip.linkback=n.getAttribute('href');
						};
					}
					//search for and set img:
					if(!thisClip.img){
						if(n.nodeName.toLowerCase()=='img'){
							thisClip.img=n.getAttribute('src');
						};
					}
				});
				js_log('set title to: ' + thisClip.title + "\n"+
					   'set linkback to: '+ thisClip.linkback + "\n"+
					   'set img to: ' + thisClip.img);
																				
				//now build the desc (if not already set) 
				if(!thisClip.desc){
					thisClip.desc='';
					$j.each(data.getElementsByTagName('clip'), function(inx,n){						
						if(n.getElementsByTagName('desc').length!=0){
							for(i=0;i< n.getElementsByTagName('desc').length; i++){
								thisClip.desc += n.getElementsByTagName('desc')[i].textContent + '<br>';
							}
						}
						if(n.getElementsByTagName('meta').length!=0){
							for(i=0;i<n.getElementsByTagName('meta').length;i++){
								if( n.getElementsByTagName('meta')[i].getAttribute('name')=='Person'){
									thisClip.desc+='<i>'+n.getElementsByTagName('meta')[i].getAttribute('content') + '</i><br>';
								}
							}
						}
					});
				}								
				//set up the embed object for this clip: 
				thisClip.setUpEmbedObj();					
				js_log('thisClip len:'+ thisClip.embed.media_element.sources.length);
				//check if we are in callbackmode or clip batch load
				js_log('callback: '+ callback);
				if(typeof callback!='undefined'){
					callback();
				}else{				
					//check if the rest of the clips are done loading
					//  if so call doWhenParseDone
					var parseDone=true;
					$j.each(thisClip.pp.default_track.clips, function(i,clip){
						if(clip.loading==true)
							parseDone=false;
					});		
					if(parseDone){
						//@@todo need to fix to use abstraction
						js_log('parse done for:' + thisClip.pp.default_track.clips.length);
						//re-order clips based on clip.order: 
						function sortClip(cA,cB){return cA.order-cB.order;}
						thisClip.pp.default_track.clips.sort(sortClip);
						//set the current clip to the first clip: 
						thisClip.pp.cur_clip = thisClip.pp.default_track.clips[0];
						thisClip.pp.doWhenParseDone();
					}
				}
			});		
		}
		
	},
	doAdjust:function(side, delta){
		if(this.embed){
			if(this.src.indexOf('?')!=-1){
				var base_src = this.src.substr(0,this.src.indexOf('?'));
				js_log("delta:"+ delta);
				if(side=='start'){
					//since we adjust start invert the delta: 
					var start_offset =parseInt(this.embed.start_offset/1000)+parseInt(delta*-1);
					this.src = base_src +'?t='+ seconds2ntp(start_offset) +'/'+ this.embed.end_ntp;							
				}else if(side=='end'){
					//put back into seconds for adjustment: 
					var end_offset = parseInt(this.embed.start_offset/1000) + parseInt(this.embed.duration/1000) + parseInt(delta);
					this.src = base_src +'?t='+ this.embed.start_ntp +'/'+ seconds2ntp(end_offset);
				}
				js_log('new src:'+this.src);
				this.embed.updateVideoSrc(this.src);
				//update values
				this.duration = this.embed.getDuration();
				this.pp.pl_duration=null;
				//update playlist stuff:
				this.pp.updateTitle();
			}
		}
	},	
	getDuration:function(){		
		if(!this.embed)this.setUpEmbedObj();		
		return this.embed.getDuration();
	},
	setBaseEmbedDim:function(o){
		if(!o)o=this;
		//o.height=Math.round(pl_layout.clip_desc*this.pp.height)-2;//give it some padding:
		//o.width=Math.round(o.height*pl_layout.clip_aspect)-2;
		o.height=	this.pp.height;
		o.width =	this.pp.width;	
	},	
	/*doRestoreEmbed:function(){
		//set the th and tw for the 
		this.setBaseEmbedDim(this.embed);		
		//call the appropriate stop to restore the thumbnail
		if(this.embed['parent_stop']){
			this.embed.parent_stop();
		}else{
			this.embed.pe_stop();
		}
	},*/
	//output the detail view:
	//@@todo
	getDetail:function(){
		//js_log('get detail:' + this.pp.title);
		var th=Math.round(pl_layout.clip_desc*this.pp.height);	
		var tw=Math.round(th*pl_layout.clip_aspect);		
		
		var twDesc = (this.pp.width-tw)-2;
		
		if(this.title==null)
			this.title='clip ' + this.order + ' ' +this.pp.title;
		if(this.desc==null)
			this.desc=this.pp.desc;
		//update the embed html: 
		this.embed.getHTML();
					
		$j(this.embed).css({ 'position':"absolute",'top':"0px", 'left':"0px"});
		
		//js_log('append child to:#clipDesc_'+this.id);
		if($j('#clipDesc_'+this.id).get(0)){
			$j('#clipDesc_'+this.id).get(0).appendChild(this.embed);
			
			$j('#clipDesc_'+this.id).append(''+
			'<div id="pl_desc_txt_'+this.id+'" class="pl_desc" style="position:absolute;left:'+(tw+2)+'px;width:'+twDesc+'px;height:'+th+'px;overflow:auto;">'+
					'<b>'+this.title+'</b><br>'+			
					this.desc + '<br>' + 
					'<b>clip length:</b> '+ this.embed.getDurationNTP()+ 
			'</div>');		
		}
	},
	getThumb:function(){
		var out='';
		//if we have the parent playlist grab it to get the image scale 
		if(this.pp){
			//js_log('pl height:' + this.pp.height + ' * ' +  pl_layout.seq);
			var th = Math.round(this.pp.height * pl_layout.seq_thumb);
			//assume standard 4 by 3 video thumb res:
			var tw = Math.round(th*pl_layout.clip_aspect);
			//js_log('set by relative position:'+ th + ' '+tw);
		}					
		var img = this.getClipImg();
		
		out+='<span ';
		if(this.title)out+='title="'+this.title+'" ';
		out+='style="position:relative;display:inline;padding:2px;" ';
		out+='onclick="document.getElementById(\''+this.pp.id+'\').play()" ';
		out+='onmouseover="mvSeqOver(\''+this.id+'\',\''+this.pp.id+'\')" ';
		out+='onmouseout="mvSeqOut()" ';
		out+='>';
		out+='<img style="border:solid 2px '+this.getColor()+'" height="'+th+'" width="'+tw+'" src="'+img+'"></span>';
	
		$j('#seqThumb_'+this.pp.id).append(out);
	},
	getClipImg:function(start_offset, size){	
		//if its a metavid image (grab the requested size)
		if(!this.img){
			return mv_default_thumb_url; 
		}else{
			if(!size && !start_offset){			
				return this.img;
			}else{
				//if a metavid image (has request parameters) use size and time args
				if(this.img.indexOf('?')!=-1){
					js_log('get with offset: '+ start_offset);
					var time = seconds2ntp( start_offset+ (this.embed.start_offset/1000) );
					js_log("time is: " + time);
					this.img = this.img.replace(/t\=[^&]*/gi, "t="+time);
					if(this.img.indexOf('&size=')!=-1){
						this.img = this.img.replace(/size=[^&]*/gi, "size="+size);
					}else{
						this.img+='&size='+size;
					}
				}
				return this.img;
				
			}
		}
	},
	getColor: function(){
		//js_log('get color:'+ num +' : '+  num.toString().substr(num.length-1, 1) + ' : '+colors[ num.toString().substr(num.length-1, 1)] );
		var num = this.id.substr( this.id.length-1, 1);
		if(!isNaN(num)){
			num=num.charCodeAt(0);
		}
		if(num >= 10)num=num % 10;
		return mv_clip_colors[num];
	}
}
/* mv_embed extensions for playlists */
var PlMvEmbed=function(vid_init){
	//js_log('PlMvEmbed: '+ vid_init.id);	
	//create the div container
	ve = document.createElement('div');
	//extend ve with all this 
	this.init(vid_init);	
	for(method in this){
		if(method!='readyState'){					
			ve[method]= this[method];
		}
	}
	js_log('ve src len:'+ ve.media_element.sources.length);
	return ve;
}
//all the overwritten and new methods for playlist extension of mv_embed
PlMvEmbed.prototype = {	
	init:function(vid_init){				
		//send embed_video a created video element: 
		ve = document.createElement('div');
		for(i in vid_init){		
			//set the parent clip pointer: 	
			if(i=='pc'){
				this['pc']=vid_init['pc'];
			}else{
				ve.setAttribute(i,vid_init[i]);
			}
		}
		var videoInterface = new embedVideo(ve);	
		js_log('created Embed Video src len:'+ videoInterface.media_element.sources.length);
		//inherit the videoInterface
		for(method in videoInterface){			
			if(method!='style'){
				if(this[method]){
					//perent embed method preservation:
					this['pe_'+method]=videoInterface[method];	
				}else{
					this[method]=videoInterface[method];
				}
			}
			//string -> boolean:
			if(this[method]=="false")this[method]=false;
			if(this[method]=="true")this[method]=true;
		}
		//continue init (load sorces) 
		js_log('this Video src len:'+ this.media_element.sources.length);
		
	},
	stop:function(){
		//set up connivance pointer to parent playlist
		var plObj = this.pc.pp;
		var plEmbed = this;					
			
		js_log('do stop');
		var th=Math.round(pl_layout.clip_desc*plObj.height);	
		var tw=Math.round(th*pl_layout.clip_aspect);
		//run the parent stop:
		this.pe_stop();
		var pl_height = (plObj.sequencer=='true')?plObj.height+27:plObj.height;
		//restore control offsets: 		
		/*(if(this.pc.pp.controls){
			$j('#dc_'+plObj.id).animate({
				height:pl_height
			},"slow");
		}*/	
		//if(plObj.sequencer=='true'){			
			plEmbed.getHTML();
		/*}else{
			//fade in elements
			$j('#big_play_link_'+this.id+',#lb_'+this.id+',#le_'+this.id+',#seqThumb_'+plObj.id+',#pl_desc_txt_'+this.pc.id).fadeIn("slow");	
			//animate restor of resize 
			var res ={};
			this.pc.setBaseEmbedDim(res);
			//debugger;
			$j('#img_thumb_'+this.id).animate(res,"slow",null,function(){
				plEmbed.pc.setBaseEmbedDim(plEmbed);
				plEmbed.getHTML();
				//restore the detail
				$j('#clipDesc_'+plEmbed.pc.id).empty();
				plEmbed.pc.getDetail();
				//$j('#seqThumb_'+plObj.id).css({position:'absolute',bottom:Math.round(this.height* pl_layout.seq_nav)});
				//$j('#'+plEmbed.id+',#dc_'+plEmbed.id).css({position:'absolute', zindex:0,width:tw,height:th});		
			});
		}*/
	},
	play:function(){
		js_log('pl eb play');
		var plEmbed = this;
		var plObj = this.pc.pp;	
		//check if we are already playing
		if(!this.thumbnail_disp){
			plEmbed.pe_play();	
			return '';
		}
		mv_lock_vid_updates=true; 
		//if we have controls expand dc so we can display them 
		/*if(plObj.controls){
			$j('#dc_'+plObj.id).animate({
				height:(parseInt(plObj.height)+80)
			},"slow");
		}*/
		js_log('controls: '+plObj.controls);
		//fade out interface elements
		/*$j('#big_play_link_'+this.id+',#seqThumb_'+plObj.id+',#pl_desc_txt_'+this.pc.id).fadeOut("slow");*/		
		js_log('got here in play');
		plEmbed.pe_play();			
		/*$j('#'+this.id+',#dc_'+this.id).css({
			position:'absolute', zindex:5,
			width:plObj.width,
			height:plObj.height
		});		
		$j('#img_thumb_'+this.id).animate({
			height:plObj.height,
			width:plObj.width
		},"slow",null,function(){
			//set the parent properties: 
			plEmbed.height=plObj.height;
			plEmbed.width=plObj.width;
			plEmbed.pe_play();						
		});	*/
	},
	//do post interface operations
	postEmbedJS:function(){		
		//add playlist clips (if plugin supports it) 
		if(this.pc.pp.cur_clip.embed.playlistSupport())
			this.pc.pp.loadEmbedPlaylist();
		//color playlist points (if play_head present)
		if(this.pc.pp.disp_play_head)
			this.pc.pp.colorPlayHead();
		//setup hover images (for playhead and next/prev buttons)
		this.pc.pp.setUpHover();
		//call the parent postEmbedJS
	 	this.pe_postEmbedJS();
	 	mv_lock_vid_updates=false;
	},
	getPlayButton:function(){
		return this.pe_getPlayButton(this.pc.pp.id);
	},	
	setStatus:function(value){		
		//status updates hanndled by playlist obj
	},
	setSliderValue:function(value){
		//setSlider value hanndled by playlist obj
	},
	/*activateSlider:function(){
		//map the slider to the parent playlist slider id:
		this.pe_activateSlider(this.pc.pp.id);
	},*/
	doSeek:function(v){
		var plObj = this.pc.pp;
		var prevClip=null;
		//jump to the clip in the current percent. 
		var perc_offset=0;
		for(var i in plObj.default_track.clips){
			var clip = plObj.default_track.clips[i];		
			perc_offset+=(clip.embed.duration /  plObj.getDuration());
			if(perc_offset > v ){	
				if(this.playMovieAt){							
					this.playMovieAt(i);				
					plObj.cur_clip = clip;
					return '';
				}
			}
		} 	
	}
}

/* 
 *  m3u parse
 */
var m3uPlaylist = {
	doParse:function(){
		//for each line not # add as clip 
		var inx =0;
		var this_pl = this;
		//js_log('data:'+ this.data.toString());
		$j.each(this.data.split("\n"), function(i,n){			
			//js_log('on line '+i+' val:'+n+' len:'+n.length);
			if(n.charAt(0)!='#'){
				if(n.length>3){ 
					//@@todo make sure its a valid url
					//js_log('add url: '+i + ' '+ n);
					var cur_clip = new mvClip({type:'srcClip',id:'p_'+this_pl.id+'_c_'+inx,pp:this_pl,src:n,order:inx});
					//setup the embed object 
					cur_clip.setUpEmbedObj();
					js_log('m3uPlaylist len:'+ thisClip.embed.media_element.sources.length);	
					this_pl.addCliptoTrack(cur_clip);					
					inx++;
				}
			}
		});
		return true;
	}
}
/*
 * parse inline playlist format
 */
var inlinePlaylist = {
	parseClipWait:{},
	doParse:function(){
		js_log("doParse inline");
		var properties = { title:'title', linkback:'linkback', 
						   desc:'desc', image:'img'};
		var lines = this.data.split("\n");
		var cur_attr=null;
		var cur_clip=null;
		var clip_inx=0;
		var ajax_flag = false;
		var plObj = this;
		
		function close_clip(){		
			if(cur_clip!=null){	
				plObj.addCliptoTrack(cur_clip);
				if(cur_clip.src){
					cur_clip.setUpEmbedObj();	
				}else{
					if(cur_clip.mvclip){
						ajax_flag=true;
						cur_clip.getRemoteData();
					}else{					
						js_log('clip '+ clip_inx +' not added (no src or mvclip');
						return '';
					}
				}
				cur_clip=null;
				cur_attr=null;
				clip_inx++;
			}
		}
		js_log("line length: "+ lines.length);
		for(var i=0;i<lines.length;i++){		
			var n = lines[i];
			if(n.charAt(0)!='#' && n.substring(0,3)!='-->' && n.substring(0,4)!='<!--'){
				var ix = n.indexOf('=');
				if(ix!=-1){
					cur_attr=n.substring(1,ix);
				}
				js_log("on line: "+ i + ' n:'+ cur_attr);
				if(cur_attr=='mvClip'){
					close_clip();
					cur_clip = new mvClip({type:'mvClip',id:'p_'+this.id+'_c_'+clip_inx,pp:this,order:clip_inx});
					cur_clip.mvclip = n.substring(ix+1);
					//js_log('NEW mvclip '+ clip_inx + ' '+ cur_clip.mvclip);
					cur_attr=null;
				}
				if(cur_attr=='srcClip'){
					close_clip();
					cur_clip = new mvClip({type:'srcClip',id:'p_'+this.id+'_c_'+clip_inx,pp:this,order:clip_inx});				
					cur_clip.src = n.substring(ix+1);
					//js_log('NEW clip '+ clip_inx + ' '+ cur_clip.src);
					cur_attr=null;
				}		
				if(properties[cur_attr]){
					var k = properties[cur_attr]; 		
					var v = n.substring(ix+1);	
					if(cur_clip==null){ //not bound to any clip apply property to playlist
						this[k]=v;			
					}else{ //clip				
						if(cur_clip[k]){
							cur_clip[k]+=v;
						}else{
							cur_clip[k]=v;
						}			
					}
				}
				//close the current attr if not desc
				if(cur_attr!='desc')cur_attr=null;
			}
		}
		js_log("LINKBACK:"+ this.linkback);
		//close the last clip:
		close_clip();	
		//paser done		
		if(ajax_flag){
			//we have to wait for an ajax request don't continue processing
			return false;
		}else{
			return true;
		}
	}
}

var itunesPlaylist = {
	doParse:function(){ 
		var properties = { title:'title', linkback:'link', 
						   author:'itunes:author',desc:'description',
						   date:'pubDate' };
		var tmpElm = null;
		for(i in properties){
			tmpElm = this.data.getElementsByTagName(properties[i])[0];
			if(tmpElm){
				this[i] = tmpElm.childNodes[0].nodeValue;
				//js_log('set '+i+' to '+this[i]);
			}
		}
		//image src is nested in itunes rss:
		tmpElm = this.data.getElementsByTagName('image')[0];
		if(tmpElm){
			imgElm = tmpElm.getElementsByTagName('url')[0];
				if(imgElm){
					this.img = imgElm.childNodes[0].nodeValue;
				}
		}
		//get the clips: 
		var clips = this.data.getElementsByTagName("item");
		properties.src = 'guid';
		for (var i=0;i<clips.length;i++){
			var cur_clip = new mvClip({type:'srcClip',id:'p_'+this.id+'_c_'+i,pp:this,order:i});			
			for(var j in properties){
				tmpElm = clips[i].getElementsByTagName( properties[j] )[0];
				if(tmpElm!=null){
					cur_clip[j] = tmpElm.childNodes[0].nodeValue;
					//js_log('set clip property: ' + j+' to '+cur_clip[j]);
				}
			}
			//image is nested
			tmpElm = clips[i].getElementsByTagName('image')[0];
			if(tmpElm){
				imgElm = tmpElm.getElementsByTagName('url')[0];
					if(imgElm){
						cur_clip.img = imgElm.childNodes[0].nodeValue;
					}
			}
			//set up the embed object now that all the values have been set
			cur_clip.setUpEmbedObj();
			
			//add the current clip to the clip list
			this.addCliptoTrack(cur_clip);
		}
		return true;
	}
}

/* 
 * parse xsfp: 
 * http://www.xspf.org/xspf-v1.html
 */
var xspfPlaylist ={
	doParse:function(){
		//js_log('do xsfp parse: '+ this.data.innerHTML);
		var properties = { title:'title', linkback:'info', 
						   author:'creator',desc:'annotation',
						   img:'image', date:'date' };
		var tmpElm = null;
		//get the first instance of any of the meta tags (ok that may be the meta on the first clip)
		//js_log('do loop on properties:' + properties);
		for(i in properties){
			js_log('on property: '+i);			
			tmpElm = this.data.getElementsByTagName(properties[i])[0];
			if(tmpElm){
				if(tmpElm.childNodes[0]){
					this[i] = tmpElm.childNodes[0].nodeValue;
					js_log('set pl property: ' + i+' to '+this[i]);
				}
			}
		}
		var clips = this.data.getElementsByTagName("track");
		js_log('found clips:'+clips.length);
		//add any clip specific properties 
		properties.src = 'location';
		for (var i=0;i<clips.length;i++){
			var cur_clip = new mvClip({type:'srcClip',id:'p_'+this.id+'_c_'+i,pp:this,order:i});			
			//js_log('cur clip:'+ cur_clip.id);
			for(var j in properties){
				tmpElm = clips[i].getElementsByTagName( properties[j] )[0];
				if(tmpElm!=null){				
					if( tmpElm.childNodes.length!=0){
						cur_clip[j] = tmpElm.childNodes[0].nodeValue;
						js_log('set clip property: ' + j+' to '+cur_clip[j]);
					}
				}
			}			
			//add mvClip ref from info link: 
			if(cur_clip.linkback){
				//if mv linkback
				mvInx = 'Stream:';
				mvclippos = cur_clip.linkback.indexOf(mvInx);
				if(mvclippos!==false){
					cur_clip.mvclip=cur_clip.linkback.substr( mvclippos+mvInx.length );
				}
			}			
			//set up the embed object now that all the values have been set
			cur_clip.setUpEmbedObj();
			//add the current clip to the clip list
			this.addCliptoTrack(cur_clip);
		}
		//js_log('done with parse');
		return true;
	}
}
/*****************************
 * SMIL CODE (could be put into another js file / lazy_loaded for improved basic playlist performace / modularity)
 *****************************/
/*playlist driver extensions to the playlist object*/
mvPlayList.prototype.monitor = function(){	
	//js_log('pl:monitor');
	var ct = new Date();		
	//js_log('mvPlayList:monitor trueTime: '+ ( (ct.getTime() - this.clockStartTime )/1000));	

	//update the playlist current time: 
	this.currentTime = this.cur_clip.dur_offset + this.cur_clip.embed.currentTime;

	//update status
	this.setStatus(seconds2ntp(this.currentTime) + '/' + seconds2ntp(this.getDuration()) );
	
	//update slider: 
	if(!this.userSlide){				
		this.setSliderValue(this.currentTime / this.getDuration());
	}

	//status updates are hanndled by children clips ... playlist just mannages smil actions
	this.doSmilActions();
	
	if( ! this.smil_monitorTimerId ){
    	if(document.getElementById(this.id)){
        	this.smil_monitorTimerId = setInterval('$j(\'#'+this.id+'\').get(0).monitor()', 250);
    	}
    }
}
//handles the rendering of overlays loaind of future clips (if nessesary)
//@@todo could be lazy loaded if nessesary 
mvPlayList.prototype.doSmilActions = function(){ 		
	//js_log('f:doSmilActions: ' + this.cur_clip.id + ' tid: ' + this.cur_clip.transIn.pClip.id );		
	var offSetTime = 0; //offset time should let us start a transition later on if we have to. 
	var _clip = this.cur_clip;	//setup a local pointer to cur_clip
	
	
	//do any smil time actions that may change the current clip
	if(this.userSlide){
		//current clip set via updateTimeThumb function 			
	}else{
		//assume playing and go to next: 
		if( _clip.dur <= _clip.embed.currentTime 
			 && _clip.order != _clip.pp.getClipCount()-1 ){
			//force next clip
			js_log('order:' +_clip.order + ' != count:' + (_clip.pp.getClipCount()-1) +
				' smil dur: '+_clip.dur + ' <= curTime: ' + _clip.embed.currentTime + ' go to next clip..');		
				//do a _play next:
				_clip.pp.next();
		}
	}						
	//@@todo could maybe generalize transIn with trasOut into one "flow" with a few scattered if statements	
	//update/setup all transitions (will render current transition state)	
	var in_range=false;
	//pretty similar actions per transition types so group into a loop:
	var tran_types = {'transIn':true,'transOut':true};
	for(var tid in tran_types ){				
		eval('var tObj =  _clip.'+tid);		
		if(!tObj)
			continue;			
		//js_log('f:doSmilActions: ' + _clip.id + ' tid:'+tObj.id + ' tclip_id:'+ tObj.pClip.id);					
		//make sue we are in range: 
		if(tid=='transIn')
			in_range = (_clip.embed.currentTime <= tObj.dur)?true:false;			
		
		if(tid=='transOut')
			in_range = (_clip.embed.currentTime >= (_clip.dur - tObj.dur))?true:false;
		
		if(in_range){
			if(this.userSlide){				
				if(tid=='transIn')
					mvTransLib.doUpdate(tObj, (_clip.embed.currentTime / tObj.dur) );
					
				if(tid=='transOut')
					mvTransLib.doUpdate(tObj, (_clip.embed.currentTime-(_clip.dur - tObj.dur)) /tObj.dur);
					
			}else{
				if(tObj.animation_state==0){
					js_log('init/run_transition ');
					tObj.run_transition();	
				}
			}
		}else{
			//close up transition if done & still onDispaly
			if( tObj.overlay_selector_id ){
				js_log('close up transition :'+tObj.overlay_selector_id);
				mvTransLib.doCloseTransition( tObj );
			}
		}
	}																					
}

/*
 * mvTransLib libary of transitions
 * a single object called to initiate transition effects can easily be extended in separate js file
 * /mvTransLib is a all static object no instances of mvTransLib/
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
	doInitTransition:function(tObj){
		js_log('mvTransLib:f:doInitTransition');		
		if(!tObj.type)
			return js_log('transition is missing type attribute');
		
		if(!tObj.subtype)
			return js_log('transition is missing subtype attribute');
		
		if(!this['type'][tObj.type])
			return js_log('mvTransLib does not support type: '+tObj.type);
		
		if(!this['type'][tObj.type][tObj.subtype])
			return js_log('mvTransLib does not support subType: '+tObj.subtype);				
				
		//setup overlay_selector_id			
		if(tObj.subtype=='crossfade'){
			if(tObj.transAttrType=='transIn')				
				var other_pClip = tObj.pClip.pp.getClip(-1);
			if(tObj.transAttrType=='transOut')
				var other_pClip = tObj.pClip.pp.getClip(1);
				
			if(typeof(other_pClip)=='undefined' ||  other_pClip.id == tObj.pClip.pp.cur_clip.id)
				js_log('Error: crossfade without media asset');
			//if not sliding start playback: 
			if(!tObj.pClip.pp.userSlide)
				other_pClip.embed.play();						
			tObj.overlay_selector_id = 'clipDesc_'+other_pClip.id;			
		}else{
			tObj.overlay_selector_id =this.getOverlaySelector(tObj);																
		}				
					
		//all good call function with  tObj param
		js_log('should call: '+tObj.type + ' ' + tObj.subtype );
		this['type'][tObj.type][tObj.subtype].init(tObj);					
	},
	doCloseTransition:function(tObj){
		if(tObj.subtype=='crossfade'){
			//close up crossfade
			js_log("close up crossfade");	
		}else{
			$j('#'+tObj.overlay_selector_id).remove();
		}
		//null selector: 
		tObj.overlay_selector_id=null;
	},
	getOverlaySelector:function(tObj){	
			var overlay_selector_id= tObj.transAttrType + tObj.pClip.id; 	
			js_log('f:getOverlaySelector: '+overlay_selector_id);
			if( ! $j('#'+overlay_selector_id).get(0) ){																											
				$j('#videoPlayer_'+tObj.pClip.embed.id).prepend(''+
					'<div id="'+overlay_selector_id+'" ' +
						'style="position:absolute;top:0px;left:0px;' +
						'height:'+parseInt(tObj.pClip.pp.height)+'px;'+
						'width:'+parseInt(tObj.pClip.pp.width)+'px;' +					
						'z-index:2">' +
					'</div>');
			}				
		return overlay_selector_id;	
	},
	doUpdate:function(tObj, percent){
		//init the transition if nessesary:
		if(!tObj.overlay_selector_id)
			this.doInitTransition(tObj);
		
		//@@todo we should ensure visability outside of doUpate loop			
		if(!$j('#'+tObj.overlay_selector_id).is(':visible'))
			$j('#'+tObj.overlay_selector_id).show();
			
		//do update:
		/*js_log('doing update for: '+ tObj.pClip.id + 
			' type:' + tObj.transAttrType +
			' t_type:'+ tObj.type +
			' subypte:'+ tObj.subtype  + 
			' percent:' + percent);*/					
			
		this['type'][tObj.type][tObj.subtype].u(tObj,percent);
	},
	/*
	 * mvTransLib: functional library mapping:
	 */ 
	type:{
		//types:
		fade:{
			fadeFromColor:{
				'init':function(tObj){										
					//js_log('f:fadeFromColor: '+tObj.overlay_selector_id +' to color: '+ tObj.fadeColor);
					if(!tObj.fadeColor)
						return js_log('missing fadeColor');		
					if($j('#'+tObj.overlay_selector_id).get(0).length==0){
						js_log("ERROR cant find: "+ tObj.overlay_selector_id);
					}	
					//set the initial state
					$j('#'+tObj.overlay_selector_id).css({
						'background-color':tObj.fadeColor,
						'opacity':"1",
					});
				},			
				'u':function(tObj, percent){
					//js_log(':fadeFromColor:update: '+ percent);
					//fade from color (invert the percent)
					var percent = 1- percent;
					$j('#'+tObj.overlay_selector_id).css({
						"opacity" : percent
					});
				}
			},
			//corssFade
			crossfade:{
				"init":function(tObj){
					js_log('f:crossfade: '+tObj.overlay_selector_id);
					if($j('#'+tObj.overlay_selector_id).length==0)
						js_log("ERROR overlay selector not found: "+tObj.overlay_selector_id);
					
					//set the initial state show the zero opacity animiation
					$j('#'+tObj.overlay_selector_id).css({'opacity':0}).show();									
				},
				'u':function(tObj, percent){
					$j('#'+tObj.overlay_selector_id).css({
						"opacity" : percent
					});
				}
			}			
		}							
	}
}
//very limited smile feature set more details soon:  
//region="video_region" transIn="fromGreen" begin="2s"
//http://www.w3.org/TR/2007/WD-SMIL3-20070713/smil-extended-media-object.html#edef-ref
var smilPlaylist ={
	transitions:{},
	doParse:function(){
		var _this = this;
		js_log('do parse smil'+ typeof this.transitions);
		//@@todo get/parse meta: 
		var meta_tags = this.data.getElementsByTagName('meta');
		$j.each(meta_tags, function(i,meta_elm){
			js_log("ON META TAG: "+meta_elm.getAttribute('name'));
			if(meta_elm.hasAttribute('name') && meta_elm.hasAttribute('content')){
				if(meta_elm.getAttribute('name')=='title' ){
					_this.title = meta_elm.getAttribute('content');					
				}
			}
		});				
		//add transition objects: 
		var transition_tags = this.data.getElementsByTagName('transition');			
		$j.each(transition_tags, function(i,trans_elm){		
			if(trans_elm.hasAttribute("id")){
				_this.transitions[trans_elm.getAttribute("id")]= new transitionObj(trans_elm);
			}else{
				js_log('skipping transition: (missing id) ' + trans_elm );
			}
		});
		js_log('loaded transitions');	
		//add seq (latter we will have support than one) 
		var seq_tags = this.data.getElementsByTagName('seq');
		$j.each(seq_tags, function(i,seq_elm){
			var inx = 0;
			//get all the clips for the given seq:
			$j.each(seq_elm.childNodes, function(i, mediaElemnt){ 
				//~complex~ have to hannlde a lot like "switch" "region" etc
				//js_log('proccess: ' + mediaElemnt.tagName); 
				if(typeof mediaElemnt.tagName!='undefined'){
					//set up basic mvSMILClip send it the mediaElemnt & mvClip init: 
					var cur_clip = new mvSMILClip(mediaElemnt, 
								{
									id:'p_' + _this.id + '_c_'+inx,
									pp:_this,
									order:inx
								}								
							);
					if(cur_clip){
						//set up embed:						
						cur_clip.setUpEmbedObj();
						js_log('smil cur_clip len:'+ cur_clip.embed.media_element.sources.length);
						//add clip to track: 
						_this.addCliptoTrack(cur_clip);						
						//valid clip up the order inx: 
						inx++;
					}				
				}
			});
			//var cur_clip = new mvClip({type:'srcClip',id:'p_'+this.id+'_c_'+i,pp:this,order:i});	
		});
		js_log("done proc seq tags");		
		return true;
	}
}
/* extention to mvClip to support smil properties */
var mvSMILClip=function(smil_clip_element, mvClipInit){
	return this.init(smil_clip_element, mvClipInit);
}
//http://www.w3.org/TR/2007/WD-SMIL3-20070713/smil-extended-media-object.html#smilMediaNS-BasicMedia
var mv_supported_media_attr = new Array(
	'src',
	'type',
	'region',
	'transIn',
	'transOut',
	'fill',
	'dur'
);	
//all the overwritten and new methods for playlist extension of mv_embed
mvSMILClip.prototype = {	
	init:function(smil_clip_element, mvClipInit){
		_this = this;				
		
		//make new mvCLip with ClipInit vals  
		var myMvClip = new mvClip(mvClipInit);
		//inherit mvClip		
		for(method in myMvClip){			
			if(typeof this[method] != 'undefined' ){				
				this['parent_'+method]=myMvClip[method];				
			}else{		
				this[method] = myMvClip[method];
			}		
		}				 
		//get supported media attr 			
		$j.each(mv_supported_media_attr, function(i, attr){			
			if( $j(smil_clip_element).attr(attr))
				_this[attr]=$j(smil_clip_element).attr(attr);
		})				
		this['tagName'] =smil_clip_element.tagName;
		
		//mv_embed specific property: 
		if(smil_clip_element.hasAttribute('poster'))
			this['img'] = smil_clip_element.getAttribute('poster');
		
		//lookup and assing copies of transitions 
		// (since transition needs to hold some per-instance state info)		
		if(this.transIn && this.pp.transitions[this.transIn]){			
			this.transIn = this.pp.transitions[this.transIn].clone();
			this.transIn.pClip = _this;
			this.transIn.transAttrType='transIn'; 			
		}		
		
		if(this.transOut && this.pp.transitions[this.transOut]){		
			this.transOut = this.pp.transitions[ this.transOut ].clone();
			this.transOut.pClip = _this;
			this.transOut.transAttrType = 'transOut';			
		}		
		//parse duration / begin times: 
		if(this.dur)
			this.dur = smilParseTime(this.dur);							
		
		//@@todo check if valid transition id		
		return this;		
	},
	/*
	 * getDuration
	 * @returns duration in int
	 */
	getDuration:function(){
		//check for smil dur: 
		if(!this.dur)
			this.dur = this.embed.getDuration();
		return this.dur;					
	},
	setUpEmbedObj:function(){
		js_log('set up embed for smil based clip');
		if(this.tagName=='video')
			this.parent_setUpEmbedObj();
	}
}

//
// ImgWrapperEmbed.
//
var imgWrapperEmbed=function(img_init){
	return this.init;
}
//all the overwritten and new methods for playlist extension of mv_embed
imgWrapperEmbed.prototype = {	
	init:function(){
		js_log("imgWrapperEmbed init");
	}
}
var mv_supported_transition_attr = new Array(
	'id',
	'type',
	'subtype',
	'fadeColor',
	'dur'
);
//around 30 frames a second: 
var MV_ANIMATION_CB_RATE = 33;
var transitionObj = function(element) {		
	this.init(element);
};
transitionObj.prototype = {	
	transAttrType:null, //transIn or transOut
	overlay_selector_id:null,
	pClip:null,
	timerId:null,
	animation_state:0, //can be 0=unset, 1=running, 2=done 
	interValCount:0, //inter-intervalCount for animating between time updates
	dur:2, //default duration of 2	
	init:function(element){
		//load supported attributes: 	
		var _this = this;
		$j.each(mv_supported_transition_attr, function(i, attr){
			if(element.getAttribute(attr))
				_this[attr]= element.getAttribute(attr);
		});				
		//@@todo proccess duration (for now just srip s) per: 
		//http://www.w3.org/TR/SMIL3/smil-timing.html#Timing-ClockValueSyntax
		if(_this.dur)
			_this.dur = smilParseTime(_this.dur);
	},
	/*
	 * the main animation loop called every MV_ANIMATION_CB_RATE or 34ms ~around 30frames per second~
	 */
	run_transition:function(){
		//js_log('f:run_transition:' + this.interValCount);	 			
		//read directly from plugin if avaliable (for native video)  
		if(typeof this.pClip.embed.vid !='undefined'){
			this.interValCount=0;
			this.pClip.embed.currentTime = this.pClip.embed.vid.currentTime;
		}else{
			//relay on currentTime update grabs (every 250ms or so) (ie for images)
			if(this.prev_curtime!=this.pClip.embed.currentTime){	
				this.prev_curtime =	this.pClip.embed.currentTime;
				this.interValCount=0;
			}
		}		
		//start_time =assinged by doSmilActions
		//base_cur_time = pClip.embed.currentTime;
		//dur = assinged by attribute		
		if(this.animation_state==0){
			mvTransLib.doInitTransition(this);
			this.animation_state=1;
		}
		//set percetage include difrence of currentTime to prev_curTime 
		// ie updated inbetween currentTime updates) 
		
		if(this.transAttrType=='transIn')
			var percentage = ( this.pClip.embed.currentTime + 
									( (this.interValCount*MV_ANIMATION_CB_RATE)/1000 )
							) / this.dur ;
				
		if(this.transAttrType=='transOut')
			var percentage = (this.pClip.embed.currentTime  + 
									( (this.interValCount*MV_ANIMATION_CB_RATE)/1000 )
									- (this.pClip.dur - this.dur)
							) /this.dur ;			
		
		/*js_log('percentage = ct:'+this.pClip.embed.currentTime + ' + ic:'+this.interValCount +' * cb:'+MV_ANIMATION_CB_RATE +
			  ' / ' + this.dur + ' = ' + percentage );
		*/
		
		//js_log('cur percentage of transition: '+percentage);
		//update state based on curent time + cur_time_offset (for now just use pClip.embed.currentTime)
		mvTransLib.doUpdate(this, percentage);
		
		if(percentage >= 1){
			js_log("transition done update with percentage "+percentage);
			this.animation_state=2;					
			clearInterval(this.timerId);	
			mvTransLib.doCloseTransition(this)
			return true;
		}
						
		this.interValCount++;
		//setInterval in we are still in running state and user is not using the playhead 
		if(this.animation_state==1){
			if(!this.timerId){
				this.timerId = setInterval('document.getElementById(\''+this.pClip.pp.id+'\').cur_clip.'+this.transAttrType+'.run_transition()',
						 MV_ANIMATION_CB_RATE);
			}
		}else{
			clearInterval(this.timerId);
		}
		return true;
	},
	clone:function(){
		var cObj = new this.constructor();
		for(var i in this)
			cObj[i]=this[i];				
		return cObj;
	}	
}
/*
 * takes an input 
 * @time_str input time string 
 * returns time in seconds 
 * 
 * @@todo proccess duration (for now just srip s) per: 
 * http://www.w3.org/TR/SMIL3/smil-timing.html#Timing-ClockValueSyntax
 * (probably have to use a Time object to fully support the smil spec
 */
function smilParseTime(time_str){
	return parseInt(time_str.replace('s', ''));
}
/***************************
 * end SMIL specific code
 ***************************/
 var trackObj = function(initObj){
 	return this.init(initObj);
 }
 var supported_track_attr = {
 	title:'untitled track',
	desc:'empty description',		
 }
trackObj.prototype = {			
	clips:new Array(),	
	init : function(initObj){
		if(!initObj)
			initObj={};
		var _this = this;
		$j.each(supported_track_attr, function(i, attr){
			if(initObj[attr])
				_this[attr] = initObj[attr];
		});
	},
	addClip:function(clipObj, pos){
		js_log('ignored pos: '+ pos);
		//for now just add to the end: 
		this.clips.push(clipObj);
		js_log('addClip cur_clip len:'+ clipObj.embed.media_element.sources.length);		
	},
	getClipCount:function(){		
		return this.clips.length;
	},
	inheritEmbedObj: function(){
		$j.each(this.clips, function(i, clip){
			clip.embed.inheritEmbedObj();
		});
	}
};			
	
/* utility functions 
 * (could be combined with other stuff) 
 */

function getAbsolutePos(objectId) {
	// Get an object left position from the upper left viewport corner
	o = document.getElementById(objectId);
	oLeft = o.offsetLeft;            // Get left position from the parent object	
	while(o.offsetParent!=null) {   // Parse the parent hierarchy up to the document element
		oParent = o.offsetParent    // Get parent object reference
		oLeft += oParent.offsetLeft // Add parent left position
		o = oParent
	}	
	o = document.getElementById(objectId);
	oTop = o.offsetTop;
	while(o.offsetParent!=null) { // Parse the parent hierarchy up to the document element
		oParent = o.offsetParent  // Get parent object reference
		oTop += oParent.offsetTop // Add parent top position
		o = oParent
	}
	return {x:oLeft,y:oTop};
}
String.prototype.htmlEntities = function(){
  var chars = new Array ('&','à','á','â','ã','ä','å','æ','ç','è','é',
                         'ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô',
                         'õ','ö','ø','ù','ú','û','ü','ý','þ','ÿ','À',
                         'Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë',
                         'Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö',
                         'Ø','Ù','Ú','Û','Ü','Ý','Þ','€','\"','ß','<',
                         '>','¢','£','¤','¥','¦','§','¨','©','ª','«',
                         '¬','­','®','¯','°','±','²','³','´','µ','¶',
                         '·','¸','¹','º','»','¼','½','¾');

  var entities = new Array ('amp','agrave','aacute','acirc','atilde','auml','aring',
                            'aelig','ccedil','egrave','eacute','ecirc','euml','igrave',
                            'iacute','icirc','iuml','eth','ntilde','ograve','oacute',
                            'ocirc','otilde','ouml','oslash','ugrave','uacute','ucirc',
                            'uuml','yacute','thorn','yuml','Agrave','Aacute','Acirc',
                            'Atilde','Auml','Aring','AElig','Ccedil','Egrave','Eacute',
                            'Ecirc','Euml','Igrave','Iacute','Icirc','Iuml','ETH','Ntilde',
                            'Ograve','Oacute','Ocirc','Otilde','Ouml','Oslash','Ugrave',
                            'Uacute','Ucirc','Uuml','Yacute','THORN','euro','quot','szlig',
                            'lt','gt','cent','pound','curren','yen','brvbar','sect','uml',
                            'copy','ordf','laquo','not','shy','reg','macr','deg','plusmn',
                            'sup2','sup3','acute','micro','para','middot','cedil','sup1',
                            'ordm','raquo','frac14','frac12','frac34');

  newString = this;
  for (var i = 0; i < chars.length; i++)
  {
    myRegExp = new RegExp();
    myRegExp.compile(chars[i],'g')
    newString = newString.replace (myRegExp, '&' + entities[i] + ';');
  }
  return newString;
}
