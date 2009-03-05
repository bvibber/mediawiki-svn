var javaEmbed = {
	instanceOf:'javaEmbed',
	iframe_src:'',
    supports: {
    	'play_head':true, 
    	'pause':true, 
    	'stop':true, 
    	'fullscreen':true, 
    	'time_display':true, 
    	'volume_control':true
    },
    getEmbedHTML : function (){
		if(this.controls)
			setTimeout('document.getElementById(\''+this.id+'\').postEmbedJS()', 150);
		//set a default duration of 30 seconds: cortao should detect duration.
		return this.wrapEmebedContainer( this.getEmbedObj() );
    },
    getEmbedObj:function(){    
    	//get the duration
    	this.getDuration();
    	//if still unset set to an arbitrary time 60 seconds: 
    	if(!this.duration)this.duration=60;
		if( mv_java_iframe ){
			//make sure iframe and embed path match (java security model)
			var iframe_src='';
            var src = this.media_element.selected_source.getURI();
			//make url absolute: 
			if(src[0]=='/'){
				//js_log('java: media relative path from:'+ document.URL);
				var pURL=parseUri(document.URL);
				src=  pURL.protocol + '://' + pURL.authority + src;
			}else if(src.indexOf('://')===-1){
				//js_log('java: media relative file');
				var pURL=parseUri(document.URL);
				src=  pURL.protocol + '://' + pURL.authority + pURL.directory + src;
			}
			js_log('java media url: '+ src);
			var parent_domain='';
			if(parseUri(mv_embed_path).host != parseUri(src).host){
				iframe_src = parseUri(src).protocol + '://'+
							parseUri(src).authority +
							mv_media_iframe_path + 'cortado_iframe.php';
				parent_domain = '&parent_domain='+parseUri(mv_embed_path).host;
			}else{
				iframe_src = mv_embed_path + 'cortado_iframe.php';
			}
			//js_log('base iframe src:'+ iframe_src);
       		iframe_src+= "?media_url=" + src + '&id=' + this.pid;
			iframe_src+= "&width=" + this.width + "&height=" + this.height;
			iframe_src+= "&duration=" + this.duration;
			iframe_src+=parent_domain;
			
			//check for the mvMsgFrame
			if($j('#mvMsgFrame').length == 0){
				js_log('appened mvMsgFrame');
				//add it to the dom: (sh
				$j('body').prepend( '<iframe id="mvMsgFrame" width="0" height="0" scrolling=no marginwidth=0 marginheight=0 src="#none"></iframe>' );
			}
			this.iframe_src = iframe_src;
			return '<iframe id="iframe_' + this.pid + '" width="'+this.width+'" height="'+this.height+'" '+
	                   'frameborder="0" scrolling="no" marginwidth="0" marginheight="0" ' +
	                   'src = "'+ this.iframe_src + '"></iframe>';
		}else{
			//load directly in the page..
			// (media must be on the same server or applet must be signed)
			return ''+
			'<applet id="'+this.pid+'" code="com.fluendo.player.Cortado.class" archive="cortado-ovt-stripped_r34336.jar" width="'+this.width+'" height="'+this.height+'">	'+ "\n"+
				'<param name="url" value="'+this.media_element.selected_source.src+'" /> ' + "\n"+
				'<param name="local" value="false"/>'+ "\n"+
				'<param name="keepaspect" value="true" />'+ "\n"+
				'<param name="video" value="true" />'+"\n"+
				'<param name="audio" value="true" />'+"\n"+
				'<param name="seekable" value="true" />'+"\n"+
				'<param name="duration" value="'+this.duration+'" />'+"\n"+
				'<param name="bufferSize" value="200" />'+"\n"+
			'</applet>';
		}		
    },
    sendFrameMsg:function( msg ){
    	var iwin;
		if(navigator.userAgent.indexOf("Safari") != -1){
			iwin = frames["iframe_" + this.pid];
		}else{
			iwin = document.getElementById("iframe_" + this.pid).contentWindow;
		}
		//update the msg text: 
		iwin.location = this.iframe_src +'#'+ msg;
	},    
    postEmbedJS:function(){
    	//start monitor: 
		this.monitor();
		this.sendFrameMsg('test_frame_update');
    },
    monitor:function(){
   		this.updateJavaState();   	
    	
    	 if( ! this.monitorTimerId ){
	    	if(document.getElementById(this.id)){
	        	this.monitorTimerId = setInterval('document.getElementById(\''+this.id+'\').monitor()', 250);
	    	}
	    }
    },
    updateJavaState:function(){    	
    	if( ! mv_java_iframe ){
    		this.getJCE();
    		if(typeof this.jce != 'undefined' ){
				if(typeof this.jce.getPlayPosition != 'undefined' ){
					this.currentTime = this.jce.getPlayPosition();
				}
			}			
    	}else{
    		//read the packaged info from the iframe:     		
    		//js_log( 'iframe_src: ' + $j('#mvMsgFrame' + this.pid).attr("src") );
    	}
    },
    //get java cortado embed object
    getJCE:function(){    	
		this.jce = $j('#'+this.pid).get(0);    	
    },
    pause:function(){
    	this.parent_pause();
        this.stop();
    }
}
