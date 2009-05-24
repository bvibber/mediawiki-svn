/*
* Adds advanced firefogg support (let you control and structure advanced controls over many aspects of video editing)  
*/

//@@todo put all msg text into loadGM json

var mvAdvFirefogg = function( initObj ){
	return this.init( initObj );
}
var default_mvAdvFirefogg_config = {
	//which config groups to include
	'config_groups' 	: ['preset', 'quality', 'meta', 'advVideo', 'advAudio'],
	
	//if you want to load any custom presets must follow the mvAdvFirefogg.presetConf json outline below
	'custom_presets'	: {}, 
	
	//any firefog config properties that may need to be excluded from options
	'exclude_settings' : [],
	
	//the control container (where we put all the controls) 
	'control_container'	 : ''		
}

mvAdvFirefogg.prototype = {	
	//the global groupings and titles for for configuration options : 
	config_groups :{
		'preset'     : "Preset: $1",
		'quality'    : "Basic Quality and Resolution Control",
		'meta'	     : "Meta Data for the Clip",
		'advVideo'   : "Advanced Video Encoding Controls",
		'advAudio'   : "Advanced Audio Encoding Controls"
	},	
	//list of pre-sets: 
	presetConf : {
		'presets' :{
			'd'	: 'webvideo',
			'type'	: 'select',
			'selectVal': ['webvideo'],
			'group' : "preset",
			'preset_conf':{
				'webvideo': {
					'desc': "Webvideo Theora, Vorbis 400kbs & 400px Width",
					'conf': {
							'maxSize': 400, 
							'videoBitrate': 400,
							'noUpscaling':true
						}
					}	
				}		
		}
	},	
	//local instance encoder config (empty by default) 
	local_encoder_config:{}, 
	//core firefogg default encoder configuration
	//see encoder options here: http://www.firefogg.org/dev/index.html
	default_encoder_config : {
		//base quality settings:
		'videoQuality': { 
			'd'		: 5,
			't'		: 'Video Quality',
			'range' 	: {'min':0,'max':10},
			'type' 		: 'slider',
			'group'		: 'quality',
			'help' 		: "Used to set the <i>Visual Quality</i> of the encoded video."
		},
		'audioQuality': {
			'd'		: 1,
			't'		: 'Audio Quality',
			'range' 	: {'min':-1,'max':10},
			'type'  	: 'slider',
			'group' 	: 'quality',
			'help'  	: "Used to set the <i>Acoustic Quality</i> of the encoded audio."
		},
		'videoCodec':{
			'd'		: "theora",
			't'		: 'Video Codec',
			'selectVal'	: ['theora'],
			'type'		: "select",
			'group'		: "quality",
			'help'  	: "Used to select the clip video codec. Presently only Theora is supported. More about the <a href=\"http://www.theora.org/\">theora codec</a> "
		},
		'audioCodec':{
			'd'    		: "vorbis",
			't'		: 'Audio Codec',
			'selectVal'	: ['vorbis'],
			'type'		: "select",
			'group'		: "quality",
			'help'  	: "Used to set the clip audio codec. Presently only Vorbis is supported. More about the <a href=\"http://www.vorbis.com//\">vorbis codec</a> "
		},
		'width': {
			't'		: 'Video Width',
			'type'		: "int",
			'group' 	: "quality",
			'help'		: "Resize to given width."
		},
		'height': {
			't'		: 'Video Height',
			'type'		: "int",
			'group'		: "quality",
			'help'		: "Resize to given height"
		},
		
		//advanced Video control configs: 
		'framerate':{
			't'		: 'Framerate',
			'selectVal'	: ['12', '16', '23:976', '24', '29:97', '30'],
			'type'	   	: "select",
			'group'    	: "advVideo",
			'help'	   	: "The video Framerate. More about <a target=\"_new\" href=\"http://en.wikipedia.org/wiki/Frame_rate\">Framerate</a>"
		},		
		'aspect':{
			't'		: 'Aspect Ratio',
			'type'		: "select",	
			'selectVal'	: ['4:3', '16:9'],
			'group'		: "advVideo",
			'help'		: "The video aspect ratio can be fraction 4:3 or 16:9. More about <a target=\"_new\" href=\"http://en.wikipedia.org/wiki/Aspect_ratio_%28image%29\">aspect ratios</a>"
		},
		'keyframeInterval':{
			'd'		: '64',
			't'		: 'Key Frame Interval',
			'range' 	: {'min':0,'max':65536},
			'numberType'	: 'force keyframe every $1 frames',
			'type' 		: 'slider',
			'group'		: 'advVideo',
			'help'		: "The keyframe interval in frames. Note: Most codecs force keyframes if the difference between frames is greater than keyframe encode size. More about <a href=\"http://en.wikipedia.org/wiki/I-frame\">keyframes</a>"
		},
		'denoise':{	
			'type'		: "boolean",
			't'		: "Denoise Filter",
			'group'		: 'advVideo',
			'help'		: "Denoise input video. More about <a target=\"_new\" href=\"http://en.wikipedia.org/wiki/Video_denoising\">denoise</a>"
		},
		'novideo':{
			't'		: "No Video",
			'type'		: "boolean",
			'group'		: 'advVideo',
			'help'		: "disable video in the output"
		},
	
		//advanced Audio control Config: 
		'audioBitrate':{
			't'		: "Audio Bitrate",
			'range'		: {'min':32,'max':500},
			'numberType'	: '$1 kbs',
			'type'		: 'slider',
			'group'		: 'advAudio'
		},
		'samplerate':{
			't'		: "Audio Sample Rate",
			'type'		: 'select',
			'selectVal'	: [{'22050':'22 kHz'}, {'44100':'44 khz'}, {'48000':'48 khz'}],
			'formatSelect'	: function(val){
						return (Math.round(val/100)*10) + ' Hz';
					},
			'help'		: "set output samplerate (in Hz)."
		},
		'noaudio':{
			't'		: "No Audio",		
			'type'		: 'boolean',
			'group'		: 'advAudio',
			'help'		: "disable audio in the output"
		},
	
		//meta tags:
		'title':{
			't'	: "Title",
			'type'	: 'string',
			'group' : 'meta',
			'help'	: "A title for your clip"
		},
		'artist':{
			't'	: "Artist Name",
			'type'	: 'string',
			'group' : 'meta',
			'help'	: "The artist that created this clip"
		},
		'date':{
			't'	: "Date",
			'group' : 'meta',
			'type'	: 'date',
			'help'	: "The date the footage was created or released"
		},
		'location':{
			't'	: "Location",
			'type'	: 'location',
			'group' : 'meta',
			'help'	: "The location of the footage"
		},
		'organization':{
			't'	: "Organization",
			'type'	: 'string',
			'group'	: 'meta',
			'help'  : "Name of organization (studio)"
		},
		'copyright':{
			't'	: "Copyright",
			'type'	: 'string',
			'group'	: 'meta',
			'help'	: "The Copyright of the clip"
		},
		'license':{
			't'	: "License",
			'type'	: 'url-license',
			'group'	: 'meta',
			'help'	: "The license of the clip (preferably a creative commons url)"
		},
		'contact':{
			't'	: "Contact",
			'type'	: 'string',
			'group'	: 'meta',
			'help'	: "Contact link"
		}
	},
	init:function( initObj ){				
		//setup a "supported" initObj:
		for(var i in initObj){
			if( typeof default_mvAdvFirefogg_config [i] != 'undefined' ){
				this[i] = initObj[i];				
			}			
		}
		//inherit the base mvFirefogg class: 
		var myFogg = new mvFirefogg( initObj );
		for(var i in myFogg){
			if( typeof this[i] != 'undefined'){
				this[ 'basefogg_' + i ] = myFogg[i];
			}else{
				this[ i ] = myFogg[i];
			}
		}
	},
	setupForm:function(){
		//call base firefogg form setup		
		basefogg_setupForm();
		//if we have a target control form gennerate the html and setup the bindings
		if( this.control_container != ''){
			//gennerate the control html
			this.doControlHTML();
				
			//setup bindings: 
			this.doControlBindings();
		}	
		//else maybe we could just have a single link that invokes the interface?
		
	},
	doControlHTML: function(){
		var out ='';
		var _this = this;
		$j.each(this.config_groups, function(group_key, group_desc){
			out+= '<div> '+
				'<h3><a href="#" id="gd_'+group_key+'" >' + group_desc + '</a></h3>'+
					'<div>';
			//output that group control options:
			out+='<table width="450" ><tr><td width="35%"></td><td width="65%"></td></tr>'; 
			//special preset case: 		
			
			for(var cK in _this.default_encoder_config){				
				var cConf = _this.default_encoder_config[cK];
				if(cConf.group == group_key){
					out+= _this.proccessCkControlHTML( cK );							
				}
			}
			out+='</table>';
			out+=		'</div>' + 
			     '</div>';
	
		});	
		//console.log("out: " + out);
		$j('#control_container').html( out ); 
	},
	proccessCkControlHTML:function( cK ){
		var cConf =  this.default_encoder_config[cK];
		var out ='';
		out+='<tr><td valign="top">'+
			'<label for="_' + cK + '">' +					
			 cConf.t + ':' + 
			 '<span id="help_'+ cK + '" class="ui-icon ui-icon-info" style="float:left"></span>'+
			 '</label></td><td>';
		//check if we have a value for this: 
		var dv = ( this.local_encoder_config[ cK ] ) ? this.local_encoder_config[ cK ] : '';				
		//switch on the config type
		switch(	cConf.type ){					
			case 'string':
				out+= '<input type="text" id="_' + cK + '" value="' + dv + '" >' ;
			break;
			case 'slider':
				maxdigits =  (Math.round( this.default_encoder_config[ cK ].range.max / 10) +1);
				out+= '<input type="text" maxlength="'+maxdigits+'" size="' +maxdigits + '" '+		
					'id="_' + cK + '" style="display:inline;border:0; color:#f6931f; font-weight:bold;" ' + 
					'value="' + dv + '" >' +								
					'<div id="slider_' + cK + '"></div>';
			break;
			case 'select':
				out+= '<select id="_' + cK + '">'+
						'<option value=""> </option>';						
				for(var i in cConf.selectVal){				
					var val = cConf.selectVal[i];
					if(typeof val == 'string'){	
						var sel = (	cConf.selectVal[i] == val)?' selected':'';									
						out+= '<option value="'+val+'"'+sel+'>'+val+'</option>';
					}else if(typeof val == 'object'){
						for(var key in val){
							hr_val = val[key];
						}
						var sel = ( cConf.selectVal[i] == key )?' selected':'';	
							
						out+= '<option value="'+key+'"'+sel+'>'+hr_val+'</option>';
					}
				}
				out+='</select>';
			break;
		}
		//output the help row:
		if(cConf.help){
			out+='<div id="helpRow_' + cK + '">'+
					'<span id="helpClose_' + cK +'" class="ui-icon ui-icon-circle-close" '+ 
					'title="Close Help"'+
					'style="float:left"/>'+
				 cConf.help +
				 '</div>';
		}
		out+='</td></tr><tr><td colspan="2" height="10"></td></tr>';
		return out;
	}, 
	doControlBindings:function(){
		//bind the select action: 
		$j( '#select_file' ).click( function(){
			doSelectFile();		
			//hide this show the select new button
			$j(this).hide();
			$j('#save_file,select_new_file,').show();						
		}).attr( 'disabled', false );		
	
		$j('#select_file_new').click(function(){
			$j("#dialog").dialog({
				bgiframe: true,
				resizable: false,
				height:140,
				modal: true,
				overlay: {
					backgroundColor: '#000',
					opacity: 0.5
				},
				buttons: {
					'Delete all items in recycle bin': function() {
						$j(this).dialog('close');
					},
					Cancel: function() {
						$j(this).dialog('close');
					}
				}
			});
		});
		function doSelectFile(){
			//select the video
			if( ogg.selectVideo() ) {
				//enable/show all the options
				$j('#fogg_control_td').fadeIn("slow");	
				doControlBindings();					
			}
		}
	
		for(var cK in this.default_encoder_config){
			var cConf =  this.default_encoder_config[cK];
			//set up the help for all types: 
			if(cConf.help){
				//initial state is hidden: 
				$j('#helpRow_' + cK).hide();		
				$j('#help_' + cK).click(function(){
					var cK = $j(this).attr("id").replace('help_','');				
					if(helpState[cK]){
						$j('#helpRow_' + cK).hide('slow');
						helpState[cK] = false;
					}else{							
						$j('#helpRow_' + cK).show('slow');
						helpState[cK] = true;
					}					
					return false;		
				}).hover(function(){
						var cK = $j(this).attr("id").replace('help_','');
						$j('#helpRow_' + cK).show('slow');
					},function(){
						var cK = $j(this).attr("id").replace('help_','');
						if(!helpState[cK])						
							$j('#helpRow_' + cK).hide('slow')
						
					}
				);
				$j('#helpClose_' + cK).click(function(){
					var cK = $j(this).attr("id").replace('helpClose_','');
					$j('#helpRow_' + cK).hide('slow');			
					helpState[cK] = false;	
					return false;
				});
	    	}else{
	    		$j('#help_' + cK).hide();
	    	}
			
			switch(	cConf.type ){	
				case 'string':
					//@@check if we have a validate function on the string
				break;
				case 'slider':
					$j('#slider_' + cK ).slider({
						range: "min",
						value: parseInt($j('#_' + cK ).val() ),
						min: this.default_encoder_config[ cK ].range.min,
						max: this.default_encoder_config[ cK ].range.max,
						slide: function(event, ui) {				
							var id  = $j(this).attr('id').replace('slider_', '');;
							$j('#_'+ id).val( ui.value );
						}
					});
				break
				case 'select':
					
				break;
			}
		}	
		$j('#control_container').accordion({ 
			header: "h3",
			collapsible: true, 
			active: false
		});
	},
	//sets up the local settings for the encode (restored from a cookie if you have them)
	setupSettings:function( force ){
		if(!force){
			if($.cookie('this.local_encoder_config')){
				this.local_encoder_config = JSON.parse( $.cookie('this.local_encoder_config') );
			}
		}
		for(var i in this.default_encoder_config){
			if( this.default_encoder_config[i]['d'] ){
				this.local_encoder_config[i] = this.default_encoder_config[i]['d'];
			}
		}
		setValuesInHtml();
	},
	setValuesInHtml:function(){
		//set the actual HTML: 
		$.each(this.local_encoder_config, function(inx, val){		
			if($j('#_'+inx).length !=0){
				$j('#_'+inx).val( val );
			}
		})
	},
	saveSettings:function(){
		$j.cookie('this.local_encoder_config', JSON.stringify( this.local_encoder_config ) );
	}
}