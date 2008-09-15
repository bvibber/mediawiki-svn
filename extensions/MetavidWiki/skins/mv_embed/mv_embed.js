/*
 * ~mv_embed version 1.0~
 * for details see: http://metavid.ucsc.edu/wiki/index.php/Mv_embed
 *
 * All Metavid Wiki code is Released under the GPL2
 * for more info visit http:/metavid.ucsc.edu/code
 *
 * @url http://metavid.ucsc.edu
 *
 * parseUri:
 * http://stevenlevithan.com/demo/parseuri/js/
 *
 * config values you can manually set the location of the mv_embed folder here
 * (in cases where media will be hosted in a different place than the embbeding page)
 *
 */

var MV_EMBED_VERSION = '1.0';
var mv_embed_path = null;
//whether or not to load java from an iframe.
//note: this is necessary for remote embedding because of java security model)
var mv_java_iframe = true;
var ogg_chop_links = true;
//media_server mv_embed_path (the path on media servers to mv_embed for java iframe with leading and trailing slashes)
var mv_media_iframe_path = '/mv_embed/';

var global_ogg_list = new Array();
var global_req_cb = new Array();//the global request callback array
var _global = this;
var mv_init_done=false;

//this restricts playable sources to ROE xml media without start end time atttribute
var mv_restrict_roe_time_source = true;

//the default height/width of the vidoe (if no style or width parm provided)
var mv_default_video_size = '400x300'; 

var debug_global_vid_ref=null;
/*
 * its best if you just require all your external data sources to serve up json data.
 * or
 * have a limited set of domains that you accept data from
 * enabling mv_proxy is not such a good idea from security standpoint but if you know what your doing 
 * you can enable it here (also you have to uncomment mv_data_proxy die(); line)  
*/  
var MV_ENABLE_DATA_PROXY=false;

/*parseUri class:*/
var parseUri=function(d){var o=parseUri.options,value=o.parser[o.strictMode?"strict":"loose"].exec(d);for(var i=0,uri={};i<14;i++){uri[o.key[i]]=value[i]||""}uri[o.q.name]={};uri[o.key[12]].replace(o.q.parser,function(a,b,c){if(b)uri[o.q.name][b]=c});return uri};parseUri.options={strictMode:false,key:["source","protocol","authority","userInfo","user","password","host","port","relative","path","directory","file","query","anchor"],q:{name:"queryKey",parser:/(?:^|&)([^&=]*)=?([^&]*)/g},parser:{strict:/^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,loose:/^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/}};

//get mv_embed location if it has not been set
if(!mv_embed_path){
	getMvEmbedPath();
}
//here you can add in delay load refrence to test things with delayed load time: 
//mv_embed_path = mv_embed_path + 'delay_load.php/'; 

//the default thumbnail for missing images:
var mv_default_thumb_url = mv_embed_path + 'images/vid_default_thumb.jpg';

if(!gMsg){var gMsg={};}
//all default msg in [english] should be overwritten by the CMS language msg system.
gMsg['loading_txt'] ='loading <blink>...</blink>';
gMsg['loading_plugin'] ='loading plugin<blink>...</blink>';
gMsg['select_playback']='Set Playback Preference';
gMsg['link_back']='Link Back';
gMsg['error_load_lib']='mv_embed: Unable to load required javascript libraries\n'+
			 	'insert script via DOM has failed, try reloading?  ';
			 	
gMsg['error_swap_vid']='Error:mv_embed was unable to swap the video tag for the mv_embed interface';

gMsg['download_from']='Download Selection:';
gMsg['download_full']='Download Full Video File:'
gMsg['download_clip']='Download the Clip';
gMsg['download_text']='Download Text (<a style="color:white" title="cmml" href="http://wiki.xiph.org/index.php/CMML">cmml</a> xml):';

gMsg['clip_linkback']='Clip Source Page';
//plugin names:
gMsg['ogg-player-vlc-mozilla']='VLC Plugin';
gMsg['ogg-player-videoElement']='Native Ogg Video Support';
gMsg['ogg-player-vlc-activex']='VLC ActiveX';
gMsg['ogg-player-oggPlay']='Annodex OggPlay Plugin';
gMsg['ogg-player-oggPlugin']='Generic Ogg Plugin';
gMsg['ogg-player-quicktime-mozilla']='Quicktime Plugin';
gMsg['ogg-player-quicktime-activex']='Quicktime ActiveX';
gMsg['ogg-player-cortado']='Java Cortado';
gMsg['ogg-player-flowplayer']='Flowplayer';
gMsg['ogg-player-selected']=' (selected)';
gMsg['generic_missing_plugin']='You don\'t appear to have a supported in browser playback method<br>' +
		'visit the <a href="http://metavid.ucsc.edu/wiki/index.php/Client_Download">Playback Methods</a> page to download a player<br>';
gMsg['add_to_end_of_sequence']='Add to End of Sequence';

gMsg['missing_video_stream']='The video file for this stream is missing';

gMsg['select_transcript_set']='Select Transcripts';
gMsg['auto_scroll']='auto scroll';
gMsg['close']='close';
gMsg['improve_transcript']='Improve Transcript';

gMsg['next_clip_msg']='Play Next Clip';
gMsg['prev_clip_msg']='Play Previus Clip';
gMsg['current_clip_msg']='Continue Playing this Clip';

gMsg['seek_to']='Seek to';

//grabs from the globalMsg obj
//@@todo integrate msg serving into CMS
function getMsg( key ) {
	 if ( key in gMsg ) {
	 	return gMsg[key];getCont
	 } else{
	 	return '[' + key + ']';
	 }
}

/**  the base video control JSON object with default attributes
*    for supported attribute details see README
*/
var default_video_attributes = {
    "id":null,
    "class":null,
    "style":null,
    "name":null,
    "innerHTML":null,
    "width":"320",
    "height":"240",

    //video attributes:
    "src":null,
    "autoplay":false,
    "start":0,
    "end":null,
    "controls":true,
	"muted":false,
	
    //roe url (for xml based metadata)
    "roe":null,
    //if roe includes metadata tracks we can expose a link to metadata
	"show_meta_link":true,

	//default state attributes per html5 spec:
	//http://www.whatwg.org/specs/web-apps/current-work/#video)
	"paused":true,
	"readyState":0,  //http://www.whatwg.org/specs/web-apps/current-work/#readystate
	"currentTime":0, //current playback position (should be updated by plugin)
	"duration":NaN,   //media duration (read from file or anx temporal url)

    //custom attributes for mv_embed:
    "play_button":true,
    "thumbnail":null,
    "linkback":null,
    "embed_link":true,
    "download_link":true,
    "type":null 	//the content type of the media 
};

/*********** INITIALIZATION CODE *************
 * the mvEmbed object drives basic loading of libs
 * its load_libs function will be called during initialization
 *********************************************/
var mvEmbed = {
  Version: MV_EMBED_VERSION,
  loaded:false,
  load_time:0,
  flist:Array(),
  load_callback:false,
  loading:false,
  libs_loaded:false,
  //plugin libs var names and paths:
  lib_jquery:{'window.jQuery':'jquery/jquery-1.2.6.min.js'},
  lib_plugins:{
	'$j.fn.offsetParent':'jquery/plugins/jquery.dimensions.js',
	'$j.ui.mouseInteraction':'jquery/plugins/ui.mouse.js',
	'$j.ui.slider':'jquery/plugins/ui.slider.js',
	'$j.timer.global':'jquery/plugins/jquery.timers.js'
  },
  pc:null, //used to store pointer to parent clip (when in playlist mode)
  load_libs:function(callback){
  	if(callback)this.load_callback = callback;
 	//two loading stages, first get jQuery
	var _this = this;
  	mvJsLoader.doLoad(this.lib_jquery,function(){
  		js_log("type of " + typeof window.jQuery);
  		//once jQuery is loaded set up no conflict & load plugins:
 		_global['$j'] = jQuery.noConflict();
 		//set up ajax to not send dynamic urls for loading scripts
 		$j.ajaxSetup({		  
		  cache: true
		});
 		js_log('jquery loaded'); 		
 		
		mvJsLoader.doLoad(_this.lib_plugins, function(){
			js_log('plugins loaded');
			mvEmbed.libs_loaded=true;
			mvEmbed.init();
		});
  	});
  },  
  addLoadEvent:function(fn){
  	this.flist.push(fn);
  },
  init: function(){
    //load css:
  	if(!styleSheetPresent(mv_embed_path+'mv_embed.css'))
		loadExternalCss(mv_embed_path+'mv_embed.css');
  	if(!styleSheetPresent(mv_embed_path+'skin/styles.css'))
		loadExternalCss(mv_embed_path+'skin/styles.css');

  	//call the callback:
  	if(this.load_callback)this.load_callback();
	mv_embed();
	
	//affter init run any queued functions:
	//js_log('run queue functions:' + mvEmbed.flist);
	while (mvEmbed.flist.length){
		mvEmbed.flist.shift()();
	}
  }
}

/**
  * mediaPlayer represents a media player plugin.
  * @param {String} id id used for the plugin.
  * @param {Array<String>} supported_types n array of supported MIME types.
  * @param {String} library external script containing the plugin interface code. (mv_<library>Embed.js)
  * @constructor
  */
function mediaPlayer(id, supported_types, library)
{
    this.id=id;
    this.supported_types = supported_types;
    this.library = library;
	this.loaded = false;
	this.loading_callbacks = new Array();
    return this;
}
mediaPlayer.prototype =
{
    id:null,
    supported_types:null,
    library:null,
	loaded:false,
	loading_callbacks:null,	
    supportsMIMEType : function(type)
    {
        for (var i in this.supported_types)
            if(this.supported_types[i] == type)
                return true;
        return false;
    },
    getName : function()
    {
        return getMsg('ogg-player-' + this.id);
    },
	load : function(callback)
	{
		if(this.loaded)
		{
			js_log('plugin loaded, scheduling immediate processing');
			$j(document).oneTime(1, callback);
		}
		else
		{
			js_log('plugin not loaded, queing callback');
			this.loading_callbacks.push(callback);
			if(this.loading_callbacks.length==1)
			{
				var plugin_path = mv_embed_path + 'embedLibs/mv_'+this.library+'Embed.js';
				js_log('requesting plugin: ' + plugin_path);
				var _this = this;
				//swaped for doLoad so that we use cache 
				// getScript has cache disabled for some reason (probably could be set up at init to cache)
				
				//I am getting vlEmebed is not defined like 1/5 or 1/20th the time
				//the load order should be more defined and ordered via callbacks  
				$j.getScript(plugin_path, function(){				
					js_log(_this.id + ' plugin loaded');
					_this.execute_callbacks();
				});
/*				eval('var lib = {"'+this.library+'Embed":\'embedLibs/mv_'+this.library+'Embed.js\'}'); 
				mvJsLoader.doLoad(lib,function(){
					js_log(_this.id + ' plugin loaded');
					_this.loaded = true;
					//callback();	
					for(var i in _this.loading_callbacks)
						_this.loading_callbacks[i]();
					_this.loading_callbacks = null;
				});*/
			}
		}
	},
	execute_callbacks : function()
	{
		// make sure the object exists
		if(eval('typeof '+this.library + 'Embed')!='undefined')
		{
			js_log(this.id + ' executing callbacks');
			this.loaded = true;
			for(var i in this.loading_callbacks)
				this.loading_callbacks[i]();
			this.loading_callbacks = null;
		}
		else
		{
			js_log(this.id + ' object not present, delay callbacks');
			// if not, wait a little
			var _this = this;
			$j(document).oneTime(25, function()
			{
				_this.execute_callbacks();
			});
		}
	}
}

var flowPlayer = new mediaPlayer('flowplayer',['video/x-flv'],'flash');
var cortadoPlayer = new mediaPlayer('cortado',['video/ogg'],'java');
var videoElementPlayer = new mediaPlayer('videoElement',['video/ogg'],'native');
var vlcMozillaPlayer = new mediaPlayer('vlc-mozilla',['video/ogg', 'video/x-flv', 'video/mp4'],'vlc');
var vlcActiveXPlayer = new mediaPlayer('vlc-activex',['video/ogg', 'video/x-flv', 'video/mp4'],'vlc');
var oggPlayPlayer = new mediaPlayer('oggPlay',['video/ogg'],'oggplay');
var oggPluginPlayer = new mediaPlayer('oggPlugin',['video/ogg'],'generic');
var quicktimeMozillaPlayer = new mediaPlayer('quicktime-mozilla',['video/ogg'],'quicktime');
var quicktimeActiveXPlayer = new mediaPlayer('quicktime-activex',['video/ogg'],'quicktime');

/**
  * mediaPlayers is a collection of mediaPlayer objects supported by the client.
  * It could be merged with embedTypes, since there is one embedTypes per script
  * and one mediaPlayers per embedTypes.
  */
function mediaPlayers()
{
    this.init();
}

mediaPlayers.prototype =
{
    players : null,
    preference : null,
    default_players : null,
    init : function()
    {
        this.players = new Array();
        this.loadPreferences();
        this.default_players = new Object();
        this.default_players['video/x-flv']= ['flash','vlc'];
        this.default_players['video/ogg']=['native','vlc','java'];
		this.default_players['video/mp4']=['vlc'];
    },
    addPlayer : function(player, mime_type)
    {
        //js_log('Adding ' + player.id + ' with mime_type ' + mime_type);
        for (var i in this.players)
            if (this.players[i].id==player.id)
            {
                if(mime_type!=null)
                {
                    //js_log('adding ' + mime_type + ' support to ' + player.id);
                    this.players[i].supported_types.push(mime_type);
                }
                return;
            }
        if(mime_type!=null)
            player.supported_types.push(mime_type);
        this.players.push(player);
    },
    getMIMETypePlayers : function(mime_type)
    {
        var mime_players = new Array();	
		if(this.default_players[mime_type])
			for (var d in this.default_players[mime_type])
			{
				var library = this.default_players[mime_type][d];
				for (var i in this.players)
					if (this.players[i].library==library && this.players[i].supportsMIMEType(mime_type))
						mime_players.push(this.players[i]);
			}
        return mime_players;
    },
    defaultPlayer : function(mime_type)
    {
        var mime_players = this.getMIMETypePlayers(mime_type);
        if(mime_players.length)
        {
            // check for prior preference for this mime type
            for(var i in mime_players)
                if(mime_players[i].id==this.preference[mime_type])
                    return mime_players[i];
            // otherwise just return the first compatible player
			// (it will be chosen according to the default_players list
            return mime_players[0];
        }
        js_log('No default player found for ' + mime_type);
        return null;
    },
    userSelectFormat : function (mime_format){
    	 this.preference['format_prefrence'] = mime_format;
    	 this.savePreferences();
    },
    userSelectPlayer : function(player_id, mime_type)
    {
        var selected_player=null;
        for(var i in this.players)
            if(this.players[i].id == player_id)
            {
                selected_player = this.players[i];
                js_log('choosing ' + player_id + ' for ' + mime_type);
                this.preference[mime_type]=player_id;
                this.savePreferences();
                break;
            }
        if(selected_player)
        {
            for(var i in global_ogg_list)
            {
                var embed = document.getElementById(global_ogg_list[i]);
                if(embed.media_element.selected_source && (embed.media_element.selected_source.mime_type == mime_type))
                {
                    embed.selectPlayer(selected_player);
                    js_log('using ' + embed.selected_player.getName() + ' for ' + embed.media_element.selected_source.getTitle());
                }
            }
        }
    },
    loadPreferences : function()
    {
        this.preference = new Object();
    	// see if we have a cookie set to a clientSupported type:
		var cookieVal = getCookie( 'ogg_player_exp' );
		if (cookieVal)
        {
            var pairs = cookieVal.split('&');
            for(var i in pairs)
            {
                var name_value = pairs[i].split('=');
                this.preference[name_value[0]]=name_value[1];
                js_log('load preference for ' + name_value[0] + ' is ' + name_value[1]);
            }
        }
    },
    savePreferences : function()
    {
        var cookieVal = '';
        for(var i in this.preference)
            cookieVal = cookieVal + i + '='+ this.preference[i] + '&';
        cookieVal=cookieVal.substr(0, cookieVal.length-1);
        js_log('setting preference cookie to ' + cookieVal);
		var week = 7*86400*1000;
		setCookie( 'ogg_player_exp', cookieVal, week, false, false, false, false );
    }
};

var getCookie = function ( cookieName ) {
		 var m = document.cookie.match( cookieName + '=(.*?)(;|$)' );
		 return m ? unescape( m[1] ) : false;
	 }

var setCookie = function ( name, value, expiry, path, domain, secure ) {
     var expiryDate = false;
     if ( expiry ) {
         expiryDate = new Date();
         expiryDate.setTime( expiryDate.getTime() + expiry );
     }
     document.cookie = name + "=" + escape(value) +
     (expiryDate ? ("; expires=" + expiryDate.toGMTString()) : "") +
     (path ? ("; path=" + path) : "") +
     (domain ? ("; domain=" + domain) : "") +
     (secure ? "; secure" : "");
}

/* 
 * controlsBuilder:
 * 
 */
var ctrlBuilder = {
	height:29,
	getControls:function(embedObj){	
		js_log('f:controlsBuilder');		
    	ctrlBuilder.id = (embedObj.pc)?embedObj.pc.pp.id:embedObj.id;
    	ctrlBuilder.avaliable_width=embedObj.playerPixelWidth();
    	//make pointer to the embedObj
    	ctrlBuilder.embedObj =embedObj;
    	//build local support var
    	ctrlBuilder.suports ={
    			'options':true,     			
    			'borders':true   			
    		};
    	for(i in embedObj.supports)
    		ctrlBuilder.suports[i] = embedObj.supports[i];
    	//special case vars: 
    	if(embedObj.roe && embedObj.show_meta_link)
    		ctrlBuilder.suports['closed_captions']=true;   
    		
    	//append options to body (if not already there) 		
		if($j('#mv_embedded_options_'+ctrlBuilder.id).length==0)
			$j('body').append(ctrlBuilder.components['mv_embedded_options'].o());		
		    		
    	var o='';    
    	for(i in ctrlBuilder.components){
    		if(ctrlBuilder.suports[i]){
    			if(ctrlBuilder.avaliable_width > ctrlBuilder.components[i].w){
    				o+=ctrlBuilder.components[i].o();
    				ctrlBuilder.avaliable_width -= ctrlBuilder.components[i].w;
    			}else{
    				js_log('not enough space for control component:'+i);
    			}
    		}
    	}
    	return o;
	},
	components:{
		'borders':{
			'w':8,
			'o':function(){
				return	'<span class="border_left">&nbsp;</span>'+
						'<span class="border_right">&nbsp;</span>';
			}
		},
		'fullscreen':{
			'w':20,
			'o':function(){
				return '<div class="fullscreen"><a href="javascript:$j(\'#'+ctrlBuilder.id+'\').get(0).fullscreen();"></a></div>'
			}
		},
		'options':{
			'w':26,
			'o':function(){
				return '<div id="options_button_'+ctrlBuilder.id+'" class="options"><a href="javascript:$j(\'#'+ctrlBuilder.id+'\').get(0).doOptionsHTML();"></a></div>';			 			
			}
		},
		'mv_embedded_options':{
			'w':0,
			'o':function(){
				return '<div id="mv_embedded_options_'+ctrlBuilder.id+'" class="videoOptions">'
+'				<div class="videoOptionsTop"></div>'
+'				<div class="videoOptionsBox">'
+'					<div class="block">'
+'						<h6>Video Options</h6>'
+'					</div>'
+'					<div class="block">'
+'						<p class="short_match"><a href="javascript:$j(\'#'+ctrlBuilder.id+'\').get(0).selectPlaybackMethod();" onClick="$j(\'#mv_embedded_options_'+ctrlBuilder.id+'\').hide();"><span><strong>Stream Selection</strong></span></a></p>'
+'						<p class="short_match"><a href="javascript:$j(\'#'+ctrlBuilder.id+'\').get(0).showVideoDownload();" onClick="$j(\'#mv_embedded_options_'+ctrlBuilder.id+'\').hide();" ><span><strong>Download</strong></span></a></p>'
+'						<p class="short_match"><a href="javascript:$j(\'#'+ctrlBuilder.id+'\').get(0).showEmbedCode();" onClick="$j(\'#mv_embedded_options_'+ctrlBuilder.id+'\').hide();" ><span><strong>Share or Embed</strong></span></a></p>'
+'					</div>'
+'				</div><!--videoOptionsInner-->'
+'				<div class="videoOptionsBot"></div>'
+'			</div><!--videoOptions-->';
			}
		},
		'play_or_pause':{
			'w':24,
			'o':function(){
				return '<div id="mv_play_pause_button_'+ctrlBuilder.id+'" class="play_button"><a href="javascript:document.getElementById(\''+ctrlBuilder.id+'\').play_or_pause();"></a></div>'
			}
		},
		'closed_captions':{
			'w':40,
			'o':function(){
				return '<div class="closed_captions"><a href="javascript:$j(\'#'+ctrlBuilder.id+'\').get(0).showTextInterface();"></a></div>'
			}			
		},
		'volume_control':{
			'w':22,
			'o':function(){
				return '<div id="volume_icon_'+ctrlBuilder.id+'" class="volume_icon"><a href="javascript:$j(\'#'+ctrlBuilder.id+'\').get(0).toggleMute();"></a></div>'
			}
		},
		'time_display':{
			'w':80,
			'o':function(){
				return '<div id="mv_time_'+ctrlBuilder.id+'" class="time">'+ctrlBuilder.embedObj.getTimeReq()+'</div>'
			}
		},
		'play_head':{
			'w':0, //special case (takes up remaning space) 
			'o':function(){
				return '<div class="seeker" id="mv_seeker_'+ctrlBuilder.id+'" style="width: ' + (ctrlBuilder.avaliable_width - 18) + 'px;">'+           
                    '		<div class="seeker_bar">'+
                    '			<div class="seeker_bar_outer"></div>'+
                    '			<div id="mv_seeker_slider_'+ctrlBuilder.id+'" class="seeker_slider"></div>'+
                    '			<div class="seeker_bar_close"></div>'+
                    '		</div>'+            
                    '	</div><!--seeker-->'
			}
		}	    	                            
	}    
}



js_log("mv embed path:"+ mv_embed_path);
/*
 * embedTypes object handles setting and getting of supported embed types:
 * closely mirrors OggHandler so that its easier to share efforts in this area:
 * http://svn.wikimedia.org/viewvc/mediawiki/trunk/extensions/OggHandler/OggPlayer.js
 */
var embedTypes = {
	 // List of players
	 players: null,
	 init: function(){
		//detect supported types
		this.detect();
	},
	clientSupports: { 'thumbnail' : true },
 	detect: function() {
 		js_log("running detect");
        this.players = new mediaPlayers();

		 // First some browser detection
		 this.msie = ( navigator.appName == "Microsoft Internet Explorer" );
		 this.msie6 = ( navigator.userAgent.indexOf("MSIE 6")===false);
		 this.opera = ( navigator.appName == 'Opera' );
		 this.safari = ( navigator.vendor && navigator.vendor.substr( 0, 5 ) == 'Apple' );

		 // In Mozilla, navigator.javaEnabled() only tells us about preferences, we need to
		 // search navigator.mimeTypes to see if it's installed
		 var javaEnabled = navigator.javaEnabled();
		 // In Opera, navigator.javaEnabled() is all there is
		 var invisibleJava = this.opera;
		 // Some browsers filter out duplicate mime types, hiding some plugins
		 var uniqueMimesOnly = this.opera || this.safari;
		 // Opera will switch off javaEnabled in preferences if java can't be found.
		 // And it doesn't register an application/x-java-applet mime type like Mozilla does.
		 if ( invisibleJava && javaEnabled )
		 	this.players.addPlayer(cortadoPlayer);

		 // ActiveX plugins
		 if(this.msie){
		 	 // check for flash		 
		 	  if ( this.testActiveX( 'ShockwaveFlash.ShockwaveFlash'))
		 	  	this.players.addPlayer(flowPlayer);
			 // VLC
			 if ( this.testActiveX( 'VideoLAN.VLCPlugin.2' ) )
			 	this.players.addPlayer(vlcActiveXPlayer);
			 // Java
			 if ( javaEnabled && this.testActiveX( 'JavaWebStart.isInstalled' ) )
			 	this.players.addPlayer(cortadoPlayer);
			 // quicktime
			 if ( this.testActiveX( 'QuickTimeCheckObject.QuickTimeCheck.1' ) )
			 	this.players.addPlayer(quicktimeActiveXPlayer);			 
		 }

		 // <video> element (should not need to be attached to the dom to test)(
		 var v = document.createElement("video");
		 if(v.play)
		 	this.players.addPlayer(videoElementPlayer);

		 // Mozilla plugins
		if(navigator.mimeTypes && navigator.mimeTypes.length > 0) {
			for ( var i = 0; i < navigator.mimeTypes.length; i++) {
				var type = navigator.mimeTypes[i].type;
				var semicolonPos = type.indexOf( ';' );
				if ( semicolonPos > -1 ) {
					type = type.substr( 0, semicolonPos );
				}
				//js_log('on type: '+type);
				var pluginName = navigator.mimeTypes[i].enabledPlugin ? navigator.mimeTypes[i].enabledPlugin.name : '';
				if ( !pluginName ) {
					// In case it is null or undefined
					pluginName = '';
				}
                if ( pluginName.toLowerCase() == 'vlc multimedia plugin' || pluginName.toLowerCase() == 'vlc multimedia plug-in' ) {
                    this.players.addPlayer(vlcMozillaPlayer, type);
                    continue;
                }

				if ( javaEnabled && type == 'application/x-java-applet' ) {
					this.players.addPlayer(cortadoPlayer);
					continue;
				}
				if(type=='application/liboggplay'){
					this.players.addPlayer(oggPlayPlayer);
					continue;
				}

				if ( type == 'application/ogg' ) {
					if ( pluginName.toLowerCase() == 'vlc multimedia plugin' )
						this.players.addPlayer(vlcMozillaPlayer, type);
					else if ( pluginName.indexOf( 'QuickTime' ) > -1 )
						this.players.addPlayer(quicktimeMozillaPlayer);
					else
						this.players.addPlayer(oggPluginPlayer);
					continue;
				} else if ( uniqueMimesOnly ) {
					if ( type == 'application/x-vlc-player' ) {
						this.players.addPlayer(vlcMozillaPlayer, type);
						continue;
					} else if ( type == 'video/quicktime' ) {
						this.players.addPlayer(quicktimeMozillaPlayer);
						continue;
					}
				}

/*				if ( type == 'video/quicktime' ) {
					this.players.addPlayer(vlcMozillaPlayer, type);
					continue;
				}*/
   				if(type=='application/x-shockwave-flash'){
					this.players.addPlayer(flowPlayer);
					continue;
				}
			}
		}
		//@@The xiph quicktime component does not work well with annodex streams (temporarly disable)
		//this.clientSupports['quicktime-mozilla'] = false;
		//this.clientSupports['quicktime-activex'] = false;
		//js_log(this.clientSupports);
	 },
	testActiveX : function ( name ) {
		 var hasObj = true;
		 try {
			 // No IE, not a class called "name", it's a variable
			 var obj = new ActiveXObject( '' + name );
		 } catch ( e ) {
			 hasObj = false;
		 }
		 return hasObj;
	 }	 
}

//load an external JS (similar to jquery .require plugin)
//but checks for object availability rather than load state
var mvJsLoader = {
	 libreq:{},
	 load_time:0,
	 doLoad:function(libs,callback){		 	 	 
		 this.callback=	(callback) ? callback:this.callback;		 
		 this.libs = (libs) ? libs: this.libs;
		 var loading=0;
		 var i=null;
		 //js_log("doLoad_ load set to 0 on libs:"+ libs);
		 for(var i in this.libs){
		 	 //if(i=='vlcEmbed')alert('got called with '+i+' ' + typeof(vlcEmbed));
			 //itor the objPath (to avoid 'has no properties' errors)
			 var objPath = i.split('.');
			 var cur_path ='';
			 var cur_load=0;
			 for(var p in objPath){
				 cur_path = (cur_path=='')?cur_path+objPath[p]:cur_path+'.'+objPath[p];
				 //if(i=='vlcEmbed')alert("looking at path: "+ cur_path);
				 //js_log("eval:  " + eval('typeof ('+cur_path+');'));
				 if(eval('typeof '+cur_path)=='undefined'){
					 cur_load = loading=1;
					 break;
				 }
				 //if we have made the full comparison break out:
				 if(cur_path==i){
				 	break;
				 }
		 	 }
			 if(cur_load==1){
				 //js_log('missing lib:'+i + ' do load:'+mv_embed_path+libs[i]);
				 if(!this.libreq[i])loadExternalJs(mv_embed_path + this.libs[i]);
				 this.libreq[i]=1;
			 }
		 }
		 if(loading){
			 if( this.load_time++ > 2000){ //time out after ~50seconds
			 	js_error( getMsg('error_load_lib') );
			 }else{
				setTimeout('mvJsLoader.doLoad()',25);
			 }
		 }else{
		 	this.callback();
		 }
	 }
}

/*********** INITIALIZATION CODE *************
 * this will get called when DOM is ready
 *********************************************/
/* jQuery .ready does not work when jQuery is loaded dynamically
 * for an example of the problem see:1.1.3 working:http://pastie.caboo.se/92588
 * and >= 1.1.4 not working: http://pastie.caboo.se/92595
 * $j(document).ready( function(){ */
function init_mv_embed(force){
	js_log('mv_init');
	if(!force){
		if(mv_init_done){
			js_log("caught second call not doing anything");
			return ;
		}
		mv_init_done=true;
	}
	//check if this page does have video or playlist
	if(document.getElementsByTagName("video").length!=0 ||
	   document.getElementsByTagName("playlist").length!=0){
		js_log('we have vids to process');
		//set up embedTypes		
		embedTypes.init();
		//load libaries		    		
		mvEmbed.load_libs();		
	}else{
		js_log('no video or playlist on the page... (done)');
		//run any queued functions:
		while (mvEmbed.flist.length){
			mvEmbed.flist.shift()();
		}
	}
}

/*
 * this function allows for targeted rewriting (the host element does not have to be <video> tag)
 */
function rewrite_by_id(vid_id){
	//confirm the nessesary libs are loaded.
	if(!mvEmbed.libs_loaded){
		mvEmbed.load_libs(function(){
			return rewrite_by_id(vid_id);
		});
	}else{
		var vidElm = document.getElementById(vid_id);
		if(vidElm){
			var videoInterface = new embedVideo(vidElm);
			if(swapEmbedVideoElement(vidElm, videoInterface)){
				return videoInterface;
			}
		}else{
			js_log('video element not found: '+vid_id);
		}
	}
}


/*********** INITIALIZATION CODE *************
 * set DOM ready callback to init_mv_embed
 * for Safari, also force load all the libraries.
 *********************************************/
// for Mozilla browsers
if (document.addEventListener && !embedTypes.safari) {
    document.addEventListener("DOMContentLoaded", function(){init_mv_embed()}, false);
}else{
	//backup "onload" method in case on DOMContentLoaded does not exist
	window.onload = init_mv_embed;
}
//for IE (temporarily disabled causing empty document rewrites:
/*if (document.all && !window.opera){ //Crude test for IE
	js_log('doing IE on ready');
//Define a "blank" external JavaScript tag
  document.write('<script type="text/javascript" id="contentloadtag" defer="defer" src="javascript:void(0)"><\/script>')
  var contentloadtag=document.getElementById("contentloadtag")
  contentloadtag.onreadystatechange=function(){
    if (this.readyState=="complete" || this.readyState=='loaded')
      init_mv_embed();
  }
}*/
//safari now supports dom injection
/*if(embedTypes.safari){
	//load the base lib_jquery library:
	for(var i in mvEmbed.lib_jquery){
		var cur_lib_url = mv_embed_path + mvEmbed.lib_jquery[i];
		js_log('load lib:' + cur_lib_url);
		document.write('<script type="text/javascript" src="'+cur_lib_url+'"><\/script>');
	}

	//load the rest (@@todo we could merge these)
  	for(var i in mvEmbed.lib_plugins){
		var cur_lib_url = mv_embed_path + mvEmbed.lib_plugins[i];
		js_log('load lib:' + cur_lib_url);
		document.write('<script type="text/javascript" src="'+cur_lib_url+'"><\/script>');
	}
	window.onload=function(){
		//once jQuery is loaded set up no conflict:
        js_log('setting up jQuery no conflict');
		_global['$j'] = jQuery.noConflict();
        mvEmbed.libs_loaded=true;
		init_mv_embed();
	}
}
*/

/*
* Coverts all occurrences of <video> tag into video object
*/
function mv_embed(){
	//get mv_embed location if it has not been set
	js_log('mv_embed ' + mvEmbed.Version);
    //send a request to load the given type
    //js_log('detected: '+ embedTypes.getPlayerType() );
    video_elements = document.getElementsByTagName("video");
    //js_log('found video '+ video_elements.length);
    if( video_elements.length > 0) {
        for(var i = 0; i < video_elements.length; i++) {
        	debug_global_vid_ref =video_elements[i];
            //grab id:
            vid_id = $j(video_elements[i]).attr("id");           
            //set id if empty:
            if(!vid_id || vid_id==''){
  				video_elements[i].id= 'v'+ global_ogg_list.length;
            }            
            
            //create and swap in the video interface:             
	   		var videoInterface = new embedVideo(video_elements[i]);	   		
   			//swap in:
	   		if(swapEmbedVideoElement(video_elements[i], videoInterface)){
	   			//remove pre_loading_div_	   			
	   			i--;	   				   			
	   		}else{
	   			//replace loading with failed 
	   			$j('#pre_loading_div_'+vid_id).html(getMsg('error_swap_vid'));	   		
	   		}
        }
    }else{
    	js_log('f:mv_embed no more <video> elements found');
    }
    //if there are no playlist elements do not load the playlist-js
    playlist_elements = document.getElementsByTagName("playlist");
    if( playlist_elements.length > 0) {
		do_playlist_functions();
    }
}

var sq_load_time=0;
function mv_do_sequence(initObj){
	js_log('mv_do_sequence');
	//issue a request to get the css file (if not already included):
	if(!styleSheetPresent(mv_embed_path+'mv_sequence.css'))
		loadExternalCss(mv_embed_path+'mv_sequence.css');
	//make sure we have the required mv_ebed libs (they are not loaded when no video element is on the page)
	mvEmbed.load_libs(function(){
		//load playlist object and drag,drop,resize,hoverintent,libs
		mvJsLoader.doLoad({
				'mvPlayList':'mv_playlist.js',
				'$j.ui.resizable':'jquery/plugins/ui.resizable.js',
				'$j.ui.draggable':'jquery/plugins/ui.draggable.js',
				'$j.ui.droppable':'jquery/plugins/ui.droppable.js'
				//'$j.ui.sortable':'jquery/plugins/ui.sortable.js'
			},function(){
				//load the sequencer and draggable ext
				mvJsLoader.doLoad({
						'mvSequencer':'mv_sequencer.js',
						'$j.ui.draggable.prototype.plugins.drag':'jquery/plugins/ui.draggable.ext.js',
						'$j.ui.droppable.prototype.plugins.over':'jquery/plugins/ui.droppable.ext.js'
					},function(){
						//init the sequence object (it will take over from there)
						mvSeq = new mvSequencer(initObj);
					});
		});
	});
}

var pl_load_time=0;
function do_playlist_functions(){
	mvJsLoader.doLoad({'mvPlayList':'mv_playlist.js'},function(){
		playlist_elements = document.getElementsByTagName("playlist");
		js_log('loded pl js ' +playlist_elements.length );
		for(var i = 0; i < playlist_elements.length; i++) {
			var pl_id = $j(playlist_elements[i]).attr('id');
			if(!pl_id || pl_id==''){
  				playlist_elements[i].id = 'v'+ global_ogg_list.length;
            }        
			//add loading: 
			parent_elm = playlist_elements[i].parentNode;     
	        load_div = document.createElement('div');
	        load_div.setAttribute("id", 'pre_loading_div_'+pl_id);
	        load_div.innerHTML=getMsg('loading_txt');        
		    parent_elm.appendChild(load_div);
		    js_log('load div: '+load_div.innerHTML);
		
			//create new playlist interface:
			var playlistInterface = new mvPlayList( playlist_elements[i] );
			if(swapEmbedVideoElement(playlist_elements[i], playlistInterface) ){				
				i--;
			}
		}
	});
}

/*
* swapEmbedVideoElement
* takes a video element as input and swaps it out with
* an embed video interface based on the video_elements attributes
*/
function swapEmbedVideoElement(video_element, videoInterface){
	js_log('do swap ' + videoInterface.id + ' for ' + video_element);
	embed_video = document.createElement('div');
	//make sure our div has a hight/width set:
		
	$j(embed_video).css({'width':videoInterface.width,'height':videoInterface.height});
	//inherit the video interface
	for(method in videoInterface){	
		if(method!='readyState'){ //readyState crashes IE
			if(method=='style'){
					embed_video.setAttribute('style', videoInterface[method]);
			}else if(method=='class'){
				if(embedTypes.msie)
					embed_video.setAttribute("className", videoInterface['class']);
				else
					embed_video.setAttribute("class", videoInterface['class']);
			}else{
				//normal inherit:
				embed_video[method]=videoInterface[method];
			}
		}
		//string -> boolean:
		if(embed_video[method]=="false")embed_video[method]=false;
		if(embed_video[method]=="true")embed_video[method]=true;
	}	
	///js_log('did vI style');  
	//now swap out the video element for the embed_video obj:  	
  	$j(video_element).after(embed_video).remove();	
  	//js_log('did swap');    	  
  	$j('#'+embed_video.id).get(0).on_dom_swap();
  	//remove loading: 
  	$j('#pre_loading_div_'+embed_video.id).remove();
	// now that "embed_video" is stable, do more initialization (if we are ready)
	if($j('#'+embed_video.id).get(0).loading_external_data==false && 
	   	$j('#'+embed_video.id).get(0).init_with_sources_loadedDone==false){
		//load and set ready state since source are avaliable: 
		$j('#'+embed_video.id).get(0).init_with_sources_loaded();
	}
	//js_log(" isd: "+this.init_with_sources_loadedDone + ' ed:' + )	
	//js_log('vid elm:'+ $j(video_element).html() );
    /* var parent_elm = video_element.parentNode;
    js_log('remove video elm');
    parent_elm.removeChild(video_element);

    //append the object into the dom:
    js_log('append mvEmbed vid elm');
    parent_elm.appendChild(embed_video);

	embed_video.more_init();

    //now run the getHTML on the new embedVideo Obj:
    embed_video.getHTML();
    */    
    //js_log('html set:' + document.getElementById(embed_video.id).innerHTML);
    //store a reference to the id
    //(for single instance plugins that need to keep track of other instances on the page)
    global_ogg_list.push(embed_video.id);

    js_log('done with child: ' + embed_video.id + ' len:'+global_ogg_list.length);
 	return true;
}
/*
*  The base embedVideo object constructor
*/
/*var textTrack = function(track){
	return this.init(track);
}
textTrack.prototype ={
	init:function(track){
		for(i in track){
			this[i]=track[i];
		}
	},
	loadTrack:function(){

	}
}*/
var textInterface = function(parentEmbed){
	return this.init(parentEmbed);
}
textInterface.prototype = {
	text_lookahead_time:0,
	body_ready:false,
	request_length:5*60, //5 min
	transcript_set:null,
	autoscroll:true,
	scrollTimerId:0,
	availableTracks:{},
	init:function(parentEmbed){
		//set the parent embed object:
		this.pe=parentEmbed;
		//parse roe if not already done:
        this.getParseCMML();		
	},
	//@@todo separate out data loader & data display
	getParseCMML:function(){
		js_log("load cmml from roe: "+ this.pe.roe);
		//read the current play head time (if embed object is playing)
		
		//if roe not yet loaded do load it: 
		if(this.pe.roe){
			if(!this.pe.media_element.addedROEData){
				js_log("load roe data!");
				var _this = this;
				do_request(this.pe.roe, function(data)
	            {            	            
	            	//continue         	
	            	_this.pe.media_element.addROE(data);                                      	                                              
	                _this.getParseCMML_rowReady();                               
	            });
			}else{
				js_log('row data ready (no roe request)');
				this.getParseCMML_rowReady();
			}						
		}else{
			js_log('no roe data to get text transcript from');
		}		
	},
	getParseCMML_rowReady: function (){
		_this = this;
		$j.each(this.pe.media_element.sources, function(inx, n){
			if(n.mime_type=='text/cmml'){
				_this.availableTracks[n.id] = {
					src:n.src,
					title:n.title,
					loaded:false,
					display:false
				}
				//load or skip the track based on "default" attribute
				if(n.marked_default){
					return;
				}else{
					//load the track if its default track
					_this.load_track(n.id);
				}
			}
		});
	},
	load_track:function(track_id){
		var track = this.availableTracks[track_id];
		js_log('cmml available');
		//add transcript to bodyHTML
		var pcurl =  parseUri(track.src);
		var req_time =pcurl.queryKey['t'].split('/');
		req_time[0]=ntp2seconds(req_time[0]);
		req_time[1]=ntp2seconds(req_time[1]);
		if(req_time[1]-req_time[0]> _this.request_length){
			//longer than 5 min will only issue a (request 5 min)
			req_time[1] = req_time[0]+_this.request_length;
		}
		//set up request url:
		url = pcurl.protocol+'://'+pcurl.authority+pcurl.path+'?';
		for( i in pcurl.queryKey){
			if(i!='t'){
				url+=i+'='+pcurl.queryKey[i]+'&';
			}else{
				url+='t='+seconds2ntp(req_time[0])+'/'+seconds2ntp(req_time[1])+'&';
			}
		}
		//js_log('do request on url:' + url);
		//$j('#mv_loading_icon').css('display','inline');
		do_request(url, function(data){
			//js_log('wtf' + data.xml);
			//js_log("load track data: "+ data.toString() );
			//hide loading icon:
			$j('#mv_loading_icon').css('display','none');
			$j.each(data.getElementsByTagName('clip'), function(inx, n){
				var text_clip = {
					start:n.getAttribute('start').replace('npt:', ''),
					end:n.getAttribute('end').replace('npt:', ''),
					type_id:track_id,
					id:n.getAttribute('id')
				}
				$j.each(n.getElementsByTagName('body'), function(binx, bn){
					if(bn.textContent){
						text_clip.body = bn.textContent;
					}else if(bn.text){
						text_clip.body = bn.text;
					}
				});
				_this.add_merge_text_clip(text_clip);
			});
			//done loading update availableTracks
			_this.availableTracks[track_id].loaded=true;
			_this.availableTracks[track_id].display=true;
			//start the autoscroll timer:
			_this.setAutoScroll(true);
		});
	},
	add_merge_text_clip:function(text_clip){
		//make sure the clip does not alreay exist:
		if($j('#tc_'+text_clip.id).length==0){
			var inserted = false;
			var text_clip_start_time = ntp2seconds(text_clip.start);
			var insertHTML = '<div style="border:solid thin black;" id="tc_'+text_clip.id+'" ' +
				'start="'+text_clip.start+'" end="'+text_clip.end+'" class="mvtt '+text_clip.type_id+'">' +
					'<div style="top:0px;left:0px;right:0px;height:20px;font-size:small">'+
						'<img style="display:inline;" src="'+mv_embed_path+'/images/control_play_blue.png">'+
						text_clip.start + ' to ' +text_clip.end+
					'</div>'+
					text_clip.body +
			'</div>';
			if($j('#mmbody_'+this.pe.id).length==0)this.show();
			$j('#mmbody_'+this.pe.id +' .mvtt').each(function(){
				if(!inserted){
					js_log( ntp2seconds($j(this).attr('start')) + ' > ' + text_clip_start_time);
					if( ntp2seconds($j(this).attr('start')) > text_clip_start_time){
						inserted=true;
						$j(this).before(insertHTML);
					}
				}
			});
			//js_log('should just append: '+insertHTML);
			if(!inserted){
				$j('#mmbody_'+this.pe.id ).append(insertHTML);
			}
		}
	},
	show:function(){
		//js_log("show text interface");
		/*fade out cc button*/
		$j('#metaButton_'+this.pe.id).fadeOut('fast');
		/*slide in intefrace container*/
		//dont' know how 'px' creeps in here: 
		this.pe.height = this.pe.height.replace('px', '');
		
		if($j('#metaBox_'+this.pe.id).length==0){			
			//append it to body relative to offset of this.pe
			var loc = $j(this.pe).position();
			//js_log('top ' +loc.top + ' left:'+loc.left );
			$j(this.pe).after('<div style="position:absolute;z-index:'+($j(this.pe).css("zindex")+1) + ';'+
						'top:'+(loc.top)+'px;' +
						'left:'+(parseInt(loc.left)+parseInt(this.pe.width)+10)+'px;' +
						'height:'+this.pe.height+'px;width:400px;' +
						'background:white;border:solid black;" ' +
						'id="metaBox_'+this.pe.id+'">' +
					this.getMenu() +
					this.getBody() +
						'</div>');
			//$j('body').append();
		}else{
			//if($j('#metaBox_'+this.pe.id).css('display')!='none'){
			$j('#metaBox_'+this.pe.id).fadeIn("fast");
			//}
		}
	},
	close:function(){
		//the meta box:
		$j('#metaBox_'+this.pe.id).fadeOut('fast');
		//the icon link:
		$j('#metaButton_'+this.pe.id).fadeIn('fast');
	},
	getBody:function(){
		return '<div id="mmbody_'+this.pe.id+'" style="position:absolute;top:20px;left:0px;right:0px;bottom:0px;height:'+(this.pe.height-20)+'px;overflow:auto;"/>';
	},
	getTsSelect:function(){
		js_log('getTsSelect');
		//check if menu already present
		if($j('mvtsel_'+this.pe.id).length!=0){
			$j('mvtsel_'+this.pe.id).fadeIn('fast');
		}else{
			var selHTML = '<div id="mvtsel_'+this.pe.id+'" style="position:absolute;background:#FFF;top:20px;left:0px;right:0px;bottom:0px;overflow:auto;">';
			selHTML+='<b>'+getMsg('select_transcript_set')+'</b><ul>';
			for(i in this.availableTracks){
				var checked = (this.availableTracks[i].display)?'checked':'';
				selHTML+='<li><input name="'+i+'" class="mvTsSelect" type="checkbox" '+checked+'>'+
					this.availableTracks[i].title + '</li>';
			}
			selHTML+='</ul>' +
						'<a href="#" onClick="document.getElementById(\''+this.pe.id+'\').textInterface.applyTsSelect();return false;">'+getMsg('close')+'</a>'+
					'</div>';
			$j('#metaBox_'+this.pe.id).append(selHTML);
			//js_log('appended: '+ selHTML);
		}
	},
	applyTsSelect:function(){
		//update availableTracks
		var _this = this;
		$j('#mvtsel_'+this.pe.id+' .mvTsSelect').each(function(){
			if(this.checked){
				//if not yet loaded now would be a good time
				if(!_this.availableTracks[this.name].loaded){
					_this.load_track( this.name);	//will load and dispaly
				}else{
					_this.availableTracks[this.name].display=true;
					$j('#mmbody_'+_this.pe.id +' .'+this.name ).fadeIn("fast");
				}
			}else{
				if(_this.availableTracks[this.name].display){
					_this.availableTracks[this.name].display=false;
					$j('#mmbody_'+_this.pe.id +' .'+this.name ).fadeOut("fast");
				}
			}
		});
		$j('#mvtsel_'+this.pe.id).fadeOut('fast');
	},
	monitor:function(){
		//grab the time from the video object
		var cur_time = parseInt( this.pe.currentTime );
		if(cur_time!=0 && this.prevTimeScroll!=cur_time){
			//search for current time:  flash red border trascript
			_this = this;
			$j('#mmbody_'+this.pe.id +' .mvtt').each(function(){
				if(ntp2seconds($j(this).attr('start')) == cur_time){
					_this.prevTimeScroll=cur_time;
					$j('#mmbody_'+_this.pe.id).animate({scrollTop: $j(this).position().top}, 'slow');
				}
			});
		}
	},
	setAutoScroll:function(timer){
		this.autoscroll = timer;
		if(this.autoscroll){
			//start the timmer if its not alreay running
			if(!this.scrollTimerId){
				this.scrollTimerId = setInterval('document.getElementById(\''+this.pe.id+'\').textInterface.monitor()', 500);
			}
			//jump to the current position:
			var cur_time = parseInt (this.pe.currentTime );
			js_log('cur time: '+ cur_time);

			_this = this;
			$j('#mmbody_'+this.pe.id +' .mvtt').each(function(){
				if(cur_time > ntp2seconds($j(this).attr('start'))  ){
					_this.prevTimeScroll=cur_time;
					$j('#mmbody_'+_this.pe.id).animate({scrollTop: $j(this).position().top}, 'slow');
				}
			});
		}else{
			//stop the timmer
			clearInterval(this.scrollTimerId);
			this.scrollTimerId=0;
		}
	},
	getMenu:function(){
		var out='';
		//add in loading icon:
		var as_checked = (this.autoscroll)?'checked':'';
		out+= '<div id="mmenu_'+this.pe.id+'" style="background:#AAF;font-size:small;position:absolute;top:0;height:20px;left:0px;right:0px;">' +
				'<a style="font-color:#000;" title="'+getMsg('close')+'" href="#" onClick="document.getElementById(\''+this.pe.id+'\').closeTextInterface();return false;">'+
					'<img border="0" width="16" height="16" src="'+mv_embed_path + 'images/cancel.png"></a> ' +
				'<a style="font-color:#000;" title="'+getMsg('select_transcript_set')+'" href="#"  onClick="document.getElementById(\''+this.pe.id+'\').textInterface.getTsSelect();return false;">'+
					getMsg('select_transcript_set')+'</a> | ' +
				'<input onClick="document.getElementById(\''+this.pe.id+'\').textInterface.setAutoScroll(this.checked);return false;" ' +
				'type="checkbox" '+as_checked +'>'+getMsg('auto_scroll');
		if(this.pe.linkback){
			out+=' | <a style="font-color:#000;" title="'+getMsg('improve_transcript')+'" href="'+this.pe.linkback+'">'+
				getMsg('improve_transcript')+'</a> ';
		}
		out+='</div>';
		return out;
	}
}


/**
  * mediaSource class represents a source for a media element.
  * @param {String} type MIME type of the source.
  * @param {String} uri URI of the source.
  * @constructor
  */
function mediaSource(element)
{
    this.init(element);
}

mediaSource.prototype =
{
    /** MIME type of the source. */
    mime_type:null,
    /** URI of the source. */
    uri:null,
    /** Title of the source. */
    title:null,
    /** True if the source has been marked as the default. */
    marked_default:null,
    /** Start offset of the requested segment */
    start_offset:null,
    /** Duration of the requested segment (NaN if not known) */
    duration:NaN,
    is_playable:null,
    upddate_interval:null,

    id:null,
    start_ntp:null,
    end_ntp:null,

    init : function(element)
    {
    	js_log(element);    	
    	
        this.src = $j(element).attr('src');
        if(ogg_chop_links)
            this.src = this.src.replace(".anx", '');
        this.marked_default = false;

        var tag = element.tagName.toLowerCase();

        if (tag == 'video')
            this.marked_default = true;

        if ($j(element).attr('type'))
            this.mime_type = $j(element).attr('type');
        else if ($j(element).attr('content-type'))
            this.mime_type = $j(element).attr('content-type');
        else
            this.mime_type = this.detectType(this.src);

		js_log("MIME TYPE: "+  this.mime_type );

        if ($j(element).attr("title"))
            this.title = $j(element).attr("title");
        else
        {
            var parts = this.mime_type.split("/",2);
            parts[0]=parts[0].replace('video', 'stream');
            parts[1]=parts[1].replace('x-flv', 'flash');
            this.title = parts[1] + ' ' + parts[0];
        }
        
        if ($j(element).attr("id"))
            this.id = $j(element).attr("id");
                        
		//@@todo parse start time format and put into start_ntp
		if($j(element).attr("start"))
			this.start = $j(element).attr("start");
		if($j(element).attr("end"))
			this.start = $j(element).attr("end");
        //js_log('Adding mediaSource of type ' + this.mime_type + ' and uri ' + this.src + ' and title ' + this.title);
        this.parseURLDuration();
    },
    /** updates the src time and start & end
     *  @param {String} start_time in NTP format
     *  @param {String} end_time in NTP format
     */
    updateSrcTime:function (start_ntp, end_ntp){
    	js_log("f:updateSrcTime: "+ start_ntp+'/'+ end_ntp);
    	//js_log("pre uri:" + this.src);
    	//if we have time we can use:
    	if(this.start_ntp!=null){
    		var index_time_val = false;
    		var time_req_delimitator = '';
	        if(this.src.indexOf('?t=')!=-1)index_time_val='?t=';
	        if(this.src.indexOf('&t=')!=-1)index_time_val='&t=';
	        if(index_time_val){
	        	var end_req_string = (this.src.indexOf('&', this.src.indexOf(index_time_val)+3)==-1)?
	     					'':
			    			this.src.indexOf('&', this.src.indexOf(index_time_val));
	        	this.src = this.src.substring(0, this.src.indexOf(index_time_val) ) + index_time_val + start_ntp + '/'+end_ntp + end_req_string;
	        }
    	}
    	//update the duration
    	this.parseURLDuration();
	  	//js_log("post uri:" + this.src);
    },
    /** MIME type accessor function.
        @return the MIME type of the source.
        @type String
    */
    getMIMEType : function()
    {
        return this.mime_type;
    },
    /** URI accessor function.
     * 	@param int seek_time_sec (used to adjust the URI for players that can't do local seeks well on given media types) 
        @return the URI of the source.
        @type String
    */
    getURI : function(seek_time_sec)
    {
    	if(!seek_time_sec)
       		return this.src;
		       		
       	pSrc = parseUri(this.src);
       	var new_url = pSrc.protocol +'://'+ pSrc.host + pSrc.path +'?';       	
       	for(i in pSrc.queryKey){
       		new_url +=(i=='t')?'t=' + seconds2ntp(seek_time_sec) +'/'+ this.end_ntp +'&' :
    									 i+'='+ pSrc.queryKey[i]+'&';    	
       	}
       	return new_url;
    },
    /** Title accessor function.
        @return the title of the source.
        @type String
    */
    getTitle : function()
    {
        return this.title;
    },
    /** Index accessor function.
        @return the source's index within the enclosing mediaElement container.
        @type Integer
    */
    getIndex : function()
    {
        return this.index;
    },
    /*
	 * function getDuration in milliseconds
     * special case derive duration from request url
	 * supports media_url?t=ntp_start/ntp_end url request format
     */
    parseURLDuration : function(){
        js_log('f:parseURLDuration() for:' + this.src);
        var index_time_val = false;
        if(this.src.indexOf('?t=')!=-1)index_time_val='?t=';
        if(this.src.indexOf('&t=')!=-1)index_time_val='&t=';
        if(index_time_val){
            var end_index = (this.src.indexOf('&', this.src.indexOf(index_time_val)+3)==-1)?
	     					this.src.length:
			    			this.src.indexOf('&', this.src.indexOf(index_time_val));
			this.start_ntp = this.src.substring(
	   				this.src.indexOf(index_time_val)+index_time_val.length,
		    		this.src.indexOf('/', this.src.indexOf(index_time_val) ));
		    this.end_ntp = this.src.substring(
		    		this.src.indexOf('/', this.src.indexOf(index_time_val))+1, end_index);
		    this.start_offset = ntp2seconds(this.start_ntp);
	   		this.duration = ntp2seconds( this.end_ntp ) - this.start_offset;
	   		
		    this.duration = this.duration;
        }else{
	     	//else normal media request (can't predict the duration without the plugin reading it)
	     	this.duration=null;
	   	 	this.start_offset=0;
        }
	},
    /** Attempts to detect the type of a media file based on the URI.
        @param {String} uri URI of the media file.
        @returns The guessed MIME type of the file.
        @type String
    */
    detectType:function(uri)
    {
    	//@@todo if media is on the same server as the javascript or we have mv_proxy configured
    	//we can issue a HEAD request and read the mime type of the media...
    	// (this will detect media mime type independently of the url name)
    	//http://www.jibbering.com/2002/4/httprequest.html (this should be done by extending jquery's ajax objects)
        switch(uri.substr(uri.lastIndexOf('.'),4)){
        	case '.flv':return 'video/x-flv';break;
        	case '.ogg':return 'video/ogg';break;
        	case '.anx':return 'video/annodex';break;
	    }
    }
};

/** A media element corresponding to a <video> element.
    It is implemented as a collection of mediaSource objects.  The media sources
    will be initialized from the <video> element, its child <source> elements,
    and/or the ROE file referenced by the <video> element.
    @param {element} video_element <video> element used for initialization.
    @constructor
*/
function mediaElement(video_element)
{
    this.init(video_element);
};

mediaElement.prototype =
{
    /** The array of mediaSource elements. */
    sources:null,    
    addedROEData:false,    
    /** Selected mediaSource element. */
    selected_source:null,
    thumbnail:null,
    linkback:null,    

    /** @private */
    init:function(video_element)
    {
        var _this = this;
        js_log('Initializing mediaElement...' + video_element);
        this.sources = new Array();
        this.thumbnail = mv_default_thumb_url;
        // Process the <video> element
        if($j(video_element).attr("src"))
        	this.tryAddSource(video_element);        	  
        
        if($j(video_element).attr('thumbnail'))
            this.thumbnail=$j(video_element).attr('thumbnail');
            
        if($j(video_element).attr('poster'))
            this.thumbnail=$j(video_element).attr('poster');
            
        // Process all inner <source> elements    
        //js_log("inner sorce count: " + video_element.getElementsByTagName('source').length );
        $j.each(video_element.getElementsByTagName('source'), function(inx, inner_source)
        {
        	//js_log(' on inner source: '+i + ' obj: '+ inner_source);
            _this.tryAddSource(inner_source);
        });              
    },  
    /** Updates the time request for all sources that have a standard time request argument (ie &t=start_time/end_time)
     */
    updateSourceTimes:function(start_time, end_time){
    	var _this = this;
    	$j.each(this.sources, function(inx, mediaSource){
    		mediaSource.updateSrcTime(start_time, end_time);
    	});
    },
    /** Returns the array of mediaSources of this element.
        \returns {Array} Array of mediaSource elements.
    */
    getSources:function()
    {
        return this.sources;
    },
    /** Selects a particular source for playback.
    */
    selectSource:function(index)
    {
    	js_log('f:selectSource:'+index);
    	var playable_sources = this.getPlayableSources();
    	for(var i in playable_sources){
    		if(i==index){
    			this.selected_source = playable_sources[i];
    			//update the user prefrence: 
    			embedTypes.players.userSelectFormat(playable_sources[i].mime_type);
    			break;
    		}
    	}
    	js_log("selected source " + this.sources[index].getTitle());
    	
    },
    /** selects the default source via cookie preference, default marked, or by id order
     * */
    autoSelectSource:function(){ 
    	js_log('f:autoSelectSource');
    	//@@todo read user preference for source    	
    	// Select the default source
    	var playable_sources = this.getPlayableSources();  
    	var flash_flag=ogg_flag=false;  	    
        for (var source in playable_sources){
        	var mime_type =playable_sources[source].mime_type;
            if(playable_sources[source].marked_default){
                this.selected_source = playable_sources[source];                
                return true;
            }
            //set via user-preference
            if(embedTypes.players.preference['format_prefrence']==mime_type){
            	 js_log('set via prefrence: '+playable_sources[source].mime_type);
            	 this.selected_source = playable_sources[source];        
            }                                	                        
        }    
        //set Ogg via player support:
        for(var source in playable_sources){
        	var mime_type =playable_sources[source].mime_type;        	
       		//set source via player                 
            if(mime_type=='video/ogg' || mime_type=='ogg/video' || mime_type=='video/annodex'){
            	for(var i in embedTypes.players){
	        		var player = embedTypes.players[i];
	        		//debugger;
	        		if(player.library=='vlc' || player.library=='native'){
	        			js_log('setting ogg via order')
	        			this.selected_source = playable_sources[source];    
	        		}
	        	}
            }
        }
        //set Flash via player support
        if (!this.selected_source){
	        for(var source in playable_sources){    
	        	var mime_type =playable_sources[source].mime_type;        
	            if(mime_type=='video/x-flv'){
	            	js_log('seeting flash by player prefrence')
	            	this.selected_source = playable_sources[source];
	            }            	        
	        }
    	}
        //select first source        
        if (!this.selected_source)
        {
            js_log('autoselecting first source:' + playable_sources[0]);
            this.selected_source = playable_sources[0];
        }
    },
    /** Returns the thumbnail URL for the media element.
        \returns {String} thumbnail URL
    */
    getThumbnailURL:function()
    {
        return this.thumbnail;
    },
    /** Checks whether there is a stream of a specified MIME type.
        @param {String} mime_type MIME type to check.
        @type {BooleanPrimitive}.
    */
    hasStreamOfMIMEType:function(mime_type)
    {
        for(source in this.sources)
        {
            if(this.sources[source].getMIMEType() == mime_type)
                return true;
        }
        return false;
    },
    isPlayableType:function(mime_type)
    {
        return mime_type=='video/ogg' || mime_type=='ogg/video' || mime_type=='video/annodex' || mime_type=='video/x-flv';
    },
    /** Adds a single mediaSource using the provided element if
        the element has a 'src' attribute.        
        @param element {element} <video>, <source> or <mediaSource> element.
    */
    tryAddSource:function(element)
    {
    	js_log('f:tryAddSource:'+ $j(element).attr("src"));    	
        if (! $j(element).attr("src")){
        	//js_log("element has no src");
            return false;
        }
        var new_src = $j(element).attr('src');
        //make sure an existing element with the same src does not already exist:         
        for(i in this.sources){    
        	if(this.sources[i].getURI()==new_src){        		
        		return false;
        	}
        }
        var source = new mediaSource(element);        
        this.sources.push(source);        
        //js_log('pushed source to stack'+ source + 'sl:'+this.sources.length);
    },
    getPlayableSources: function(){
    	 var playable_sources= new Array();
    	 for(var i in this.sources){    	 	    	 	
    	 	if(this.isPlayableType(this.sources[i].mime_type)){
    	 		if(mv_restrict_roe_time_source){
    	 			if(this.sources[i]['start'])
    	 				continue;	
    	 		}
    	 		playable_sources.push(this.sources[i]);
    	 	}else{
    	 		js_log("type "+ this.sources[i].mime_type + 'is not playable');
    	 	}
    	 }    	 
    	 return playable_sources;
    },
    /** Imports media sources from ROE data.
        @param roe_data ROE data.
    */
    addROE:function(roe_data)
    {    	
    	this.addedROEData=true;
        var _this = this;        
        if(typeof roe_data == 'string')
        {
            var parser=new DOMParser();
            js_log('ROE data:' + roe_data);
            roe_data=parser.parseFromString(roe_data,"text/xml");
        }
        if(roe_data){
	        $j.each(roe_data.getElementsByTagName('mediaSource'), function(inx, source)
	        {
				_this.tryAddSource(source);
	        });
	        //set the thumbnail:
			$j.each(roe_data.getElementsByTagName('img'), function(inx, n){
	            if($j(n).attr("id")=="stream_thumb"){
	                js_log('roe:set thumb to '+$j(n).attr("src"));
	                _this['thumbnail'] =$j(n).attr("src");
	            }
	        })
	        //set the linkback:
			$j.each(roe_data.getElementsByTagName('link'), function(inx, n){
	            if($j(n).attr('id')=='html_linkback'){
	                js_log('roe:set linkback to '+$j(n).attr("href"));
	                _this['linkback'] = $j(n).attr('href');
	            }
	        })
        }
		else
			js_log('ROE data empty.');		
				
    }
};

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
	slider:null,		
	ready_to_play:false, //should use html5 ready state
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
	    for(var attr in default_video_attributes){
	        if(element.getAttribute(attr)){
	            this[attr]=element.getAttribute(attr);
	            //js_log('attr:' + attr + ' val: ' + video_attributes[attr] +" "+'elm_val:' + element.getAttribute(attr) + "\n (set by elm)");
	        }else{
	            this[attr]=default_video_attributes[attr];
	            //js_log('attr:' + attr + ' val: ' + video_attributes[attr] +" "+ 'elm_val:' + element.getAttribute(attr) + "\n (set by attr)");
	        }
	    }
	    //js_log("ROE SET: "+ this.roe);
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
                js_log('added_roe::' + _this.media_element.sources);                               
                js_log('done loading ROE  '+_this.thumbnail_disp )                        
                _this.init_with_sources_loaded();
                js_log('set loading_external_data=false');     
                _this.loading_external_data=false;                               
            });
    	}
	},
	init_with_sources_loaded : function()
	{	
		js_log('f:init_with_sources_loaded');
		//autoseletct the source
		this.media_element.autoSelectSource();		
		//auto select player based on prefrence or default order
		if(!this.media_element.selected_source)
		{
			js_log('no sources');
			return this;
		}

        this.selected_player = embedTypes.players.defaultPlayer(this.media_element.selected_source.mime_type);
        if(this.selected_player){
            js_log('selected ' + this.selected_player.getName());
            js_log("PLAYBACK TYPE: "+this.selected_player.library);
        }else
            js_log('no player found for mime type ' + this.media_element.selected_source.mime_type);

        this.thumbnail_disp = true;
	    /*
	    * @@TODO lazy load plugin types
	    * override all relevant exported functions with the {embed_type} Object
	    * place the base functions in parent.{function name}
	    */	    
		this.inheritEmbedObj();

  		//update HTML
  		//$j('#'+embed_video.id).get(0).getHTML();

		//js_log('HTML FROM IN OBJECT' + this.getHTML());
		//return this object:
		//return this;		
		this.init_with_sources_loadedDone=true;
	},
	inheritEmbedObj:function(ready_callback){
		js_log("f: inheritEmbedObj");
		//@@note: tricky cuz direct overwrite is not so ideal.. since the extended object is already tied to the dom
		//clear out any non-base embedObj stuff:
		if(this.instanceOf){
			eval('tmpObj = '+this.instanceOf);
			for(i in tmpObj){
				if(this['parent_'+i]){
					this[i]=this['parent_'+i];
				}else{
					this[i]=null;
				}
			}
		}      		
		//set up the new embedObj
        js_log('embedding with ' + this.selected_player.library);
		var _this = this;		
		this.selected_player.load(function()
		{
			js_log('inheriting '+_this.selected_player.library +'Embed to ' + _this.id + ' ' + $j('#'+_this.id).length);
			//var _this = $j('#'+_this.id).get(0);
			js_log( 'type of ' + _this.selected_player.library +'Embed + ' +
					eval('typeof '+_this.selected_player.library +'Embed')); 
			eval('embedObj = ' +_this.selected_player.library +'Embed;');
			for(method in embedObj){
				//parent method preservation for local overwritten methods
				if(_this[method])
					_this['parent_' + method] = _this[method];
				_this[method]=embedObj[method];
			}
			if(_this.inheritEmbedOverride){
				_this.inheritEmbedOverride();
			}
			//update controls if possible
			if(!_this.loading_external_data)
				_this.refreshControlsHTML();
				
			if(ready_callback)
				ready_callback();
			js_log('plugin load callback complete');					
			
			js_log("READY TO PLAY:"+_this.id);			
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
		js_log('f:getTimeReq');
		var default_time_req = '0:00:00/0:00:00';
		if(!this.media_element)
			return default_time_req;
		if(!this.media_element.selected_source)
			return default_time_req;		
		if(!this.media_element.selected_source.start_ntp)
			return default_time_req;		
		return this.media_element.selected_source.start_ntp+'/'+this.media_element.selected_source.end_ntp;
	},	
    getDuration:function(){
        this.duration = this.media_element.selected_source.duration;
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
		return 'function getEmbedHTML should be overiten by embedLib ';
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
				getMsg('loading_plugin') + 
			'</div>'					
		);
		// schedule embedding
		this.selected_player.load(function()
		{
			js_log('performing embed for ' + _this.id);			
			var embed_code = _this.getEmbedHTML();
			//js_log(embed_code);
			$j('#mv_embedded_player_'+_this.id).html(embed_code);
			js_log('changed embed code');
			_this.paused = false;
			_this.thumbnail_disp=false;
			$j("#mv_play_pause_button_"+_this.id).attr('class', 'pause_button');
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
    	$j('#img_thumb_'+this.id).css('zindex',1);
    	$j('#big_play_link_'+this.id).hide();
    	//add the liks_info_div black back 
    	$j('#dc_'+this.id).append('<div id="liks_info_'+this.id+'" ' +
	    			'style="width:' +parseInt(parseInt(this.width)/2)+'px;'+	    
	    			'height:'+ parseInt(parseInt(this.height)) +'px;'+
	    			'position:absolute;top:10px;'+    			
	    			'width: '+parseInt( ((parseInt(this.width)/2)-15) ) + 'px;'+
	    			'left:'+ parseInt( ((parseInt(this.width)/2)+15) ) +'px;">'+	    			
    			'</div>' +
    			'<div id="black_back_'+this.id+'" ' +
	    			'style="z-index:-2;position:absolute;background:#000;' +
	    			'top:0px;left:0px;width:'+parseInt(this.width)+'px;' +
	    			'height:'+parseInt(this.height)+'px;">' +
	    		'</div>');    	
    	
    	//start animation (make thumb small in uper left add in div for "loading"    	    
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
    				$j('#liks_info_'+_this.id).html(getMsg('loading_txt'));
    			}		
    		}
    	)       	 	   
    	//now load roe if nessesaryand showNextPrevLinks
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
    	if(anno_track_url){
    		js_log('found annotative track'+ anno_track_url);
    		//zero out seconds (should improve cache hit rate and generally expands metadata search)
    		//@@todo this could be repalced with a regExp
    		var annoURL = parseUri(anno_track_url);
    		var times = annoURL.queryKey['t'].split('/');      		
    		var stime_parts = times[0].split(':');   
    		var etime_parts = times[0].split(':');         				
    		//zero out the hour:
    		var new_start = stime_parts[0]+':'+'0:0';
    		//zero out the end sec
    		var new_end   = (etime_parts[0]== stime_parts[0])? (etime_parts[0]+1)+':0:0' :etime_parts[0]+':0:0';
    		 		
    		var etime_parts = times[1].split(':');
    		
    		var new_anno_track_url = annoURL.protocol +'://'+ annoURL.host + annoURL.path +'?';
    		for(i in annoURL.queryKey){
    			new_anno_track_url +=(i=='t')?'t='+new_start+'/'+new_end +'&' :
    									 i+'='+ annoURL.queryKey[i]+'&';    					    		
    		}    		
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
    	}
    	//query current request time +|- 60s to get prev next speech links. 
    },
    showNextPrevLinks:function(){
    	//int requested links: 
    	var link = {
    		'prev':'',
    		'current':'',
    		'next':''
    	}    	
    	var curTime = this.getTimeReq().split('/');
    	
    	var s_sec = ntp2seconds(curTime[0]);
    	var e_sec = ntp2seconds(curTime[1]); 
    	
    	//now we have all the data in anno_data_cache
    	for(var clip_id in this.anno_data_cache){  
		 	var clip =  this.anno_data_cache[clip_id];
		 	//js_log('on clip:'+ clip_id);
		 	//set prev_link (if cur_link is still empty)
			if(s_sec < clip.start_time_sec && link.current=='') 
				link.prev = clip_id;
				
			//clip is encapsulated by the current clip add current link:
			if(s_sec > clip.start_time_sec && e_sec < clip.end_time_sec )
				link.current = clip_id;
						
			if(e_sec <  clip.start_time_sec && link.next=='')
				link.next = clip_id;
    	}   
    	var html='';   
    	for(var link_type in link){
    		var link_id = link[link_type];    		
    		if(link_id!=''){
    			var clip = this.anno_data_cache[link_id];
    			
    			var title_msg='';
				for(var j in clip['meta']){
					title_msg+=j.replace(/_/g,' ') +': ' +clip['meta'][j].replace(/_/g,' ') +" \n";
				}    	
				var time_req = 	clip.time_req;	
				if(link_type=='current') //if current start from end of current clip play to end of current meta: 				
					time_req = curTime[1]+ '/' + seconds2ntp(clip.end_time_sec);
				
	    		html+='<p><a href="#" title="' +title_msg + '" '+	    				 
	    				'onClick="$j(\'#'+this.id+'\').get(0).playByTimeReq(\''+ 
	    					time_req + '\'); return false; ">' +
	    	 		getMsg(link_type+'_clip_msg') + 	    	 	
	    		'</a></p>';
    		}    	    				
    	}
    	//js_log("should set html:"+ html);
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
    	js_log('f:doThumbnailHTML');
    	js_log('thum disp:'+this.thumbnail_disp);
        this.closeDisplayedHTML();
        this.thumbnail_disp = true;
        var embed_code = this.getThumbnailHTML();              
        
        //js_log("embed code: " + embed_code);
        if($j('#mv_embedded_player_'+this.id).length==0)
        	js_log("can't find mv_embedded_player_"+this.id);
        	
        $j('#mv_embedded_player_'+this.id).html(embed_code);
		this.paused = true;
        $j("#mv_play_pause_button_"+this.id).attr('class', 'play_button');
    },
    refreshControlsHTML:function(){
    	js_log('refreshing controls HTML');
		if($j('#mv_embedded_controls_'+this.id).length==0)
		{
			js_log('#mv_embedded_controls_'+this.id + ' not present, returning');
			return;
		}else{
			$j('#mv_embedded_controls_'+this.id).html( this.getControlsHTML() );
		}		
    },
    getControlsHTML:function()
    {        	
    	return ctrlBuilder.getControls(this);
    },	
	getHTML : function (){		
		//@@todo check if we have sources avaliable	
		js_log('f : getHTML');			
		var _this = this; 				
		var html_code = '';		
        html_code = '<div id="videoPlayer_'+this.id+'" style="width:'+this.width+'px;" class="videoPlayer">';        
			html_code += '<div style="width:'+parseInt(this.width)+'px;height:'+parseInt(this.height)+'px;"  id="mv_embedded_player_'+this.id+'">' +
							this.getThumbnailHTML() + 
						'</div>';
					
			js_log("mvEmbed:controls "+ typeof this.controls);
						
	        if(this.controls)
	        {
	        	js_log("f:getHTML:AddControls");
	            html_code +='<div id="mv_embedded_controls_'+this.id+'" class="controls" style="width:'+this.width+'px">';
	            html_code += this.getControlsHTML();       
	            html_code +='</div>';      
	            //block out some space by encapulating the top level div 
	            $j(this).wrap('<div style="width:'+parseInt(this.width)+'px;height:'
	            		+(parseInt(this.height)+ctrlBuilder.height)+'px"></div>');    	            
	        }
        html_code += '</div>'; //videoPlayer div close        
        js_log('should set: '+this.id);
        $j(this).html(html_code);      
        
        if(!_this.base_seeker_slider_offset)
        	_this.base_seeker_slider_offset = $j('#mv_seeker_slider_'+_this.id).get(0).offsetLeft;
        	
        _this.start_time_sec = ntp2seconds(_this.getTimeReq().split('/')[0]);
        
        js_log('start sec: '+_this.start_time_sec + ' base offset: '+_this.base_seeker_slider_offset);
        
        //buid dragable hook here:
        $j('#mv_seeker_slider_'+this.id).draggable({
        	containment:'parent',
        	axis:'x',
        	opacity:.6,
        	start:function(e, ui){
        		_this.userSlide=true;
        		js_log("started draging set userSlide"+_this.userSlide)
        	},
        	drag:function(e, ui){
        		//@@todo get the -14 number from the skin somehow
        		var perc = (($j('#mv_seeker_slider_'+_this.id).get(0).offsetLeft-_this.base_seeker_slider_offset)
						/
					($j('#mv_seeker_'+_this.id).width()-14));   
					 													
				this.jump_time = seconds2ntp(parseInt(_this.duration*perc)+ _this.start_time_sec);	
				js_log('perc:' + perc + ' * ' + _this.duration + ' jt:'+  this.jump_time);
				_this.setStatus( getMsg('seek_to')+' '+this.jump_time );    						
        	},
        	stop:function(e, ui){
        		_this.userSlide=false;
        		js_log('do jump to: '+this.jump_time)
        		//reset slider				
        		_this.seek_time_sec=ntp2seconds(this.jump_time);
        		_this.stop();
        		_this.play();
        	}
        });
                  
        //js_log('set this to: ' + $j(this).html() );	
        //alert('stop');
        //if auto play==true directly embed the plugin
        if(this.autoplay)
		{
			js_log('activating autoplay');
            this.doEmbedHTML();
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
		  out+= getMsg('generic_missing_plugin') + ' or <a title="'+getMsg('download_clip')+'" href="'+this.src +'">'+getMsg('download_clip')+'</a>';
		}
		return out + '</div>';
	},
	updateVideoTimeReq:function(time_req){
		var time_parts =time_req.split('/');
		this.updateVideoTime(time_parts[0], time_parts[1]);
	},
	//update video time
	updateVideoTime:function(start_time, end_time){
		//update media
		this.media_element.updateSourceTimes(start_time, end_time);
		//update mv_time
		this.setStatus(start_time+'/'+end_time);
		//reset slider
		this.setSliderValue(0);
		//reset seek_offset:
		this.seek_time_sec=0;
	},
	//updates the video src
	updateVideoSrc : function(src){
		js_log("UPDATE SRC:"+src);
		this.src = src;
	},
	//updates the thumbnail if the thumbnail is being displayed
	updateThumbnail : function(src, quick_switch){
		js_log('set to thumb:'+ src);
		if(quick_switch){
			$j('#img_thumb_'+this.id).attr('src', src);
		}else{
			var _this = this;
			
			//if still animating remove new_img_thumb_
			if(this.thumbnail_updating==true)
				$j('#new_img_thumb_'+this.id).stop().remove();
			
			if(this.thumbnail_disp){
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
				});
			}else{
				//do a quick switch
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
        var thumbnail = this.media_element.getThumbnailURL();

	    //put it all in the div container dc_id
	    thumb_html+= '<div id="dc_'+this.id+'" style="position:relative;'+
	    	' overflow:hidden; top:0px; left:0px; width:'+this.playerPixelWidth()+'px; height:'+this.playerPixelHeight()+'px; z-index:0;">'+
	        '<img width="'+this.playerPixelWidth()+'" height="'+this.playerPixelHeight()+'" style="position:relative;width:'+this.playerPixelWidth()+';height:'+this.playerPixelHeight()+'"' +
	        ' id="img_thumb_'+this.id+'" src="' + thumbnail + '">';
		
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
		return '<div onclick="$j(\'#'+id+'\').get(0).play()" id="big_play_link_'+id+'" class="large_play_button" '+
			'style="left:'+((this.playerPixelWidth()-130)/2)+'px;'+
			'top:'+((this.playerPixelHeight()-96)/2)+'px;"></div>';
		/*;
		//setup button size
		var play_btn_height =
		var play_btn_width = 109;
		if(this.width<320){
			var play_btn_width= play_btn_height = Math.round(this.width/3);
		}

	    var top = Math.round(this.height/2)- (play_btn_height/2);
	    var left = Math.round(this.width/2)- (play_btn_width/2);

	    out='';
	    out+='<div style="border:none;position:absolute;top:'+top+'px;left:'+left+'px;z-index:1">'+
				     '<a id="big_play_link_'+id+'" title="Play Media" href="javascript:document.getElementById(\''+id+'\').play();">';

	        //fix for IE<7 and its lack of PNG support:
		out+=getTransparentPng(new Object ({id:'play_'+id, width:play_btn_width, height:play_btn_height, border:"0",
						src:mv_embed_path + '/skin/images/player_big_play_button.png' }));
		out+='</a></div>';
		return out;*/
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
		//check if textObj present:
		if(typeof this.textInterface == 'undefined' ){
			this.textInterface = new textInterface(this);
		}
		//show interface
		this.textInterface.show();
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
        	height+=(pl_layout.title_bar_height + pl_layout.control_height);

      
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
            '<div id="mv_disp_inner_'+sel_id+'">'+
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
    getPlayerSelectList:function(mime_type, index, file_select_code){
        var supporting_players = embedTypes.players.getMIMETypePlayers(mime_type);

		var select_html='<div id="player_select_list_' + index + '" class="player_select_list"><ul>';
		for(i in supporting_players){
			//put colored plugin icon and no link for supported player:
			if(embedTypes.players.defaultPlayer(mime_type).id==supporting_players[i].id ){
				select_html+='<li>'+
									'<img border="0" width="16" height="16" src="'+mv_embed_path+'images/plugin.png">'+
									supporting_players[i].getName() +
							'</li>';
			}else{
				//else gray plugin and the plugin with link to select
				select_html+='<li>'+
								'<a href="#" onClick="'+ file_select_code + 'embedTypes.players.userSelectPlayer(\''+supporting_players[i].id+'\',\''+mime_type+'\');return false;">'+
									'<img border="0" width="16" height="16" src="'+mv_embed_path+'images/plugin_disabled.png">'+
									supporting_players[i].getName() +
								'</a>'+
							'</li>';
			}
		 }
		 select_html+='</ul></div>';
         js_log(select_html);
		 return select_html;
	},
    selectPlaybackMethod:function(){    	
    	//get id (in case where we have a parent container)
        var this_id = (this.pc!=null)?this.pc.pp.id:this.id;
        
        var _this=this;               
        var out='<span style="color:white"><blockquote>';
        var _this=this;
        //js_log('selected src'+ _this.media_element.selected_source.url);
		$j.each(this.media_element.getPlayableSources(), function(index, source)
        {     		
	        var default_player = embedTypes.players.defaultPlayer(source.getMIMEType());
	        var source_select_code = '$j(\'#'+this_id+'\').get(0).closeDisplayedHTML(); $j(\'#'+_this.id+'\').get(0).media_element.selectSource(\''+index+'\');';
	        var player_code = _this.getPlayerSelectList(source.getMIMEType(), index, source_select_code);
	        var is_not_selected = (source != _this.media_element.selected_source);
	        var image_src = mv_embed_path+'/images/stream/';
	        image_src += (source.mime_type == 'video/x-flv')?'flash_icon_':'fish_xiph_org_';
	        image_src += is_not_selected ? 'bw' : 'color';
	        image_src += '.png';
	        if (default_player)
	        {
	            out += '<img src="'+image_src+'"/>';
	            if(is_not_selected)
	                out+='<a href="#" onClick="' + source_select_code + 'embedTypes.players.userSelectPlayer(\''+default_player.id+'\',\''+source.getMIMEType()+'\'); return false;">';
	            out += source.getTitle()+/*' - ' + default_player.getName() +*/ (is_not_selected?'</a>':'') + ' ';
	            out += /*'(<a href="#" onClick=\'$j("#player_select_list_'+index+'").fadeIn("slow");return false;\'>choose player</a>)' +*/ player_code;           
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
			this.displayHTML(getMsg('loading_txt'));
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
		var out='<b style="color:white;">'+getMsg('download_from')+'</b><br>';
		out+='<span style="color:white"><blockquote>';
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
        	out+='</blockquote>'+getMsg('download_full')+"<blockquote>"+dl_list+'</blockquote>';
        if(dl_txt_list!='')
			out+='</blockquote>'+getMsg('download_text')+"<blockquote>"+dl_txt_list+'</blockquote></span>';
       	return out;
	},
	/*getDLlist:function(transform_function){
		
		var dl_list=dl_txt_list='';
		$j.each(this.media_element.getSources(), function(index, source)
        {
			
		});
		
		return out;
	},*/
	/*
	*  base embed controls
	*	the play button calls
	*/
	play : function(){
		js_log("mv_embed play:"+this.id);		
		js_log('thum disp:'+this.thumbnail_disp);
		//check if thumbnail is being displayed and embed html
		if(this.thumbnail_disp){			
			if(!this.selected_player){
				js_log('no selected_player');
				//this.innerHTML = this.getPluginMissingHTML();
				//$j(this).html(this.getPluginMissingHTML());
				$j('#'+this.id).html(this.getPluginMissingHTML());
			}else{
                this.doEmbedHTML();
                this.onClipDone_disp=false;
			}
		}else{
			//the plugin is already being displayed
			js_log("we are already playing" );
		}
	},
	toggleMute:function(){		
		if(this.muted){
			this.muted=false;
			$j('#volume_icon_'+this.id).removeClass('volume_off').addClass('volume_on');
		}else{
			this.muted=true;
			$j('#volume_icon_'+this.id).removeClass('volume_on').addClass('volume_off');
		}
	},
	play_or_pause : function(){
		js_log('base play or pause');
		var id = (this.pc!=null)?this.pc.pp.id:this.id;

        //check state and set play or pause
        if(this.paused){
            js_log('do play');
            //(paused) do play
            this.play();
            this.paused=false;
            $j("#mv_play_pause_button_"+this.id).attr('class', 'pause_button');
        }else{
            js_log('do pause');
            //(playing) do pause
            this.pause();
            this.paused=true;
            $j("#mv_play_pause_button_"+this.id).attr('class', 'play_button');
        }
	},
	//called when we play to the end of a stream (load the thumbnail)
	streamEnd : function(){
		//if we are not in playlist mode stop:
		if(!this.pc){
			this.stop();
		}		
	},
	/*
	 * base embed pause
	 * 	there is no general way to pause the video
	 *  must be overwritten by embed object to support this functionality.
	 */
	pause : function(){
		return null
	},
	/*
	 * base embed stop (should be overwritten by the plugin)
	 */
	stop: function(){
		js_log('base stop:'+this.id);
		//check if thumbnail is being displayed in which case do nothing
		if(this.thumbnail_disp){
			//already in stooped state
			js_log('already in stopped state');
		}else{
			//rewrite the html to thumbnail disp
			this.doThumbnailHTML();
			this.setSliderValue(0);
		}
        if(this.update_interval)
        {
            clearInterval(this.update_interval);
            this.update_interval = null;
        }
	},
	fullscreen:function(){
		js_log('fullscreen not supported for this plugin type');
	},
	/* returns bool true if playing false if paused or stooped
	 */
	isPlaying : function(){
		if(this.thumbnail_disp){
			//in stoped state
			return false;
		}else{
			return true;
		}
	},
	//loads in the css and js for the extended interface (controls = true)
	//depricated
	/*get_interface_lib : function(doLoad){
		//var doLoad = (doLoad==null)? true:doLoad;
		//js_log('get interface:' + doLoad);
		var loading_interface =false;

		//grab the css file:
		if(!styleSheetPresent(mv_embed_path+'mv_embed.css')){
			if(doLoad) loadExternalCss(mv_embed_path+'mv_embed.css');
			js_log('css und');
			loading_interface=true;
		}
		if(loading_interface){
			//call get_interface_lib (without requests) until interface is done loading:
			setTimeout('document.getElementById(\''+this.id+'\').get_interface_lib(false)', 50);
			//if loading interface is not yet available
			return false;
		}else{
			//js_log('loading_interface = false');
			//if it was a load request and it was already loaded return true
			if(doLoad){
				return true;
			}else{
				//non loading request means time has passed so we need to update the innerHTML
				this.doEmbedHTML();
			}
		}
	},*/
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
	activateSlider : function(slider_id){
		var id = (this.pc)?this.pc.pp.id:this.id;
		var thisVid = this;
		this.sliderVal=0;
		//js_log('parent id: '+ parent_id + ' id: ' + this.id);
		$j('#slider_'+id).slider({
				handle:'#playhead_'+id,
				slide:function(e, ui) {
					thisVid.userSlide=true;
					thisVid.sliderVal=( ui.pixel/ ( $j('#slider_'+id).width()-
					 $j('#playhead_'+id).width() ));
					//js_log('user slide: ' +thisVid.sliderVal );
				},
				change: function(slider){
					//js_log("change: " + thisVid.sliderVal);
					thisVid.doSeek(thisVid.sliderVal);
					thisVid.userSlide=false;
				}
		});
		//if(!slider_id)slider_id=this.id;
		//get a pointer to this id (as this in onSlide context is not "this")
		/*var parent_id = this.id;	*/	
	},
	setSliderValue: function(perc){
		var id = (this.pc)?this.pc.pp.id:this.id;
		//alinment offset: 
		if(!this.mv_seeker_width)
			this.mv_seeker_width = $j('#mv_seeker_slider_'+id).width();
		
		js_log('currentTime:'+ this.currentTime);
		
		var val = Math.round( perc  * $j('#mv_seeker_'+id).width() - (this.mv_seeker_width*perc));
		$j('#mv_seeker_slider_'+id).css('left', (val+41)+'px' );
		//js_log('perc in: ' + perc + ' * ' + $j('#mv_seeker_'+id).width() + ' = set to: '+ val + ' - '+ Math.round(this.mv_seeker_width*perc) );
		//js_log('op:' + offset_perc + ' *('+perc+' * ' + $j('#slider_'+id).width() + ')');
	},
	setStatus:function(value){
		var id = (this.pc)?this.pc.pp.id:this.id;
		//update status:
		$j('#mv_time_'+id).html(value);
	}	
}

/* returns html for a transparent png (for ie<7)*/
function getTransparentPng(image){
	if(!image.style)image.style='';
	if( embedTypes.msie ){
		return '<span id="'+image.id+'" style="display:inline-block;width:'+image.width+'px;height:'+image.height+'px;' +
    		'filter:progid:DXImageTransform.Microsoft.AlphaImageLoader' +
    		'(src=\''+image.src+'\', sizingMethod=\'scale\');"></span>';
	}else{
		return '<img id="'+image.id+'" style="'+image.style+'"  width="'+image.width+'" height="'+image.height+'" border="0" src="'+
			image.src + '">';
	}
}

/*
* EMBED OBJECTS:
* (dynamically included)
*/

/*
* utility functions:
*/
function seconds2ntp(sec){	
	sec = parseInt(sec);
	hours = Math.floor(sec/ 3600);
	minutes = Math.floor((sec/60) % 60);
	seconds = sec % 60;
	if ( minutes < 10 ) minutes = "0" + minutes;
	if ( seconds < 10 ) seconds = "0" + seconds;
	return hours+":"+minutes+":"+seconds;
}
/* takes hh:mm:ss input returns number of seconds */
function ntp2seconds(ntp){
	if(!ntp){
		js_log('ntp2seconds:not valid ntp:'+ntp);
		return null;
	}
	times = ntp.split(':');
	if(times.length!=3){
		js_log('ntp2seconds:not valid ntp:'+ntp);
		return null;
	}
	//return seconds float (ie take seconds float value if present):
	return parseInt(times[0]*3600)+parseInt(times[1]*60)+parseFloat(times[2]);
}
//addLoadEvent for adding functions to be run when the page DOM is done loading
function mv_addLoadEvent(func) {
	mvEmbed.addLoadEvent(func);
}
function do_request(req_url, callback, mv_json_response){
 	js_log('do request: ' + req_url);
		if( parseUri(document.URL).host == parseUri(req_url).host){
			//no proxy at all do a direct request:
			$j.ajax({
				type: "GET",
				url:req_url,
                async: false,
				success:function(data){
					callback(data);
				}
			});
		}else{			
			//check if MV_embed path matches document.URL then we can use the local proxy:
			if(parseUri(document.URL).host == parseUri(mv_embed_path).host && MV_ENABLE_DATA_PROXY){
				js_log('use mv_embed_proxy : ' + parseUri(document.URL).host + ' == '+ parseUri(mv_embed_path).host);				
				$j.ajax({
					type: "POST",
					url:mv_embed_path + 'mv_data_proxy.php',
					data:{url:req_url},
                    async: false,
					success:function(data){
						js_log("did ajax req:"+ typeof data);
						callback(data);
					}
				});
			}else{
				//get data via DOM injection of proxy request with callback
				global_req_cb.push(callback);
				if(!mv_json_response && MV_ENABLE_DATA_PROXY){
					//@@todo should remove this functionality from mv_data_proxy
					//and require sites serve up data as javascript with a callback
					req_url  =req_url.replace(/&/g,'__amp__');
					loadExternalJs(mv_embed_path+'mv_data_proxy.php?url='+req_url+
						'&cb=mv_jsdata_cb&cb_inx='+(global_req_cb.length-1) );
				}else{
					//add json_ to req url
					if(req_url.indexOf("feed_format=")!=-1)
						req_url = req_url.replace(/feed_format=/, 'feed_format=json_');
					js_log('json url: '+ req_url);
					//response type is mv_json_response or proxy dissabled			
					loadExternalJs(req_url+'&cb=mv_jsdata_cb&cb_inx='+(global_req_cb.length-1));
				}
			}
		}
}
function mv_jsdata_cb(response){
	js_log('f:mv_jsdata_cb');
	//run the callback from the global req cb object:
	if(!global_req_cb[response['cb_inx']]){
		js_log('missing req cb index');
		return false;
	}
	if(!response['pay_load']){
		js_log("missing pay load");
		return false;
	}
	//switch on content type:
	switch(response['content-type']){
		case 'text/plain':
		break;
		case 'text/xml':
			if(typeof response['pay_load'] == 'string'){
				//js_log('load string:'+ response['pay_load']);
				//attempt to parse as xml for IE
				if(embedTypes.msie){
					var xmldata=new ActiveXObject("Microsoft.XMLDOM");
					xmldata.async="false";
					xmldata.loadXML(response['pay_load']);
				}else{ //for others (firefox, safari etc)
					var xmldata = (new DOMParser()).parseFromString(response['pay_load'], "text/xml");
				}
				//@@todo hanndle xml parser errors
				if(xmldata)response['pay_load']=xmldata;
			}
		break
		default:
			js_log('bad response type' + response['content-type']);
			return false;
		break;
	}
	global_req_cb[response['cb_inx']](response['pay_load']);
}
//load external js via dom injection
//@@todo swich over to jQuery injection
function loadExternalJs(url, callback){
   	js_log('load js: '+ url);
    //if(window['$j'])
   //	$j.getScript(url, callback);
    	//have to use direct ajax call insted of $j.getScript()
    	//since you can't send "cache" option to $j.getScript()
       /*$j.ajax({
			type: "GET",
			url: url,
			dataType: 'script',
			cache: true
		});*/
  //  else{
    	var e = document.createElement("script");
        e.setAttribute('src', url);
        e.setAttribute('type',"text/javascript");
        //e.setAttribute('defer', true);
        document.getElementsByTagName("head")[0].appendChild(e);
   // }
}

function styleSheetPresent(url){
    style_elements = document.getElementsByTagName('link');
    if( style_elements.length > 0) {
        for(i = 0; i < style_elements.length; i++) {
			if(style_elements[i].href==url)
				return true;
		}
    }
    return false;
}
function loadExternalCss(url){
   js_log('load css: ' + url);
   var e = document.createElement("link");
   e.href = url;
   e.type = "text/css";
   e.rel = 'stylesheet';
   document.getElementsByTagName("head")[0].appendChild(e);
}
/*
 * sets the global mv_embed path based on the scripts location
 */
function getMvEmbedPath(){	
	js_elements = document.getElementsByTagName("script");
	for(var i=0;i<js_elements.length; i++){
		var mstr = js_elements[i].src.indexOf('mv_embed.js');
		if( mstr !=-1){
			mv_embed_path = js_elements[i].src.substr(0,mstr);
		}
	}		
	//absolute the url (if relative) (if we don't have mv_embed path)
	if(mv_embed_path.indexOf('://')==-1){
		var doc_url =  document.URL;			
		var pURL = parseUri(doc_url);		
		if(mv_embed_path.charAt(0)=='/'){
			mv_embed_path = pURL.protocol + '://' + pURL.authority + mv_embed_path;
		}else{
			//relative:
			if(mv_embed_path==''){
				mv_embed_path = pURL.protocol + '://' + pURL.authority + pURL.directory + mv_embed_path;
			}
		}		
	}else{
		js_log('already absolute');
	}	 
}
if (typeof DOMParser == "undefined") {
   DOMParser = function () {}
   DOMParser.prototype.parseFromString = function (str, contentType) {
      if (typeof ActiveXObject != "undefined") {
         var d = new ActiveXObject("MSXML.DomDocument");
         d.loadXML(str);
         return d;
      } else if (typeof XMLHttpRequest != "undefined") {
         var req = new XMLHttpRequest;
         req.open("GET", "data:" + (contentType || "application/xml") +
                         ";charset=utf-8," + encodeURIComponent(str), false);
         if (req.overrideMimeType) {
            req.overrideMimeType(contentType);
         }
         req.send(null);
         return req.responseXML;
      }
   }
}
/*
* utility functions:
*/
function js_log(string){
  if( window.console ){
        console.log(string);        
  }else{
     /*
      * IE and non-firebug debug:
      */
    /*var log_elm = document.getElementById('mv_js_log');
     if(!log_elm){
     	document.write('<div style="position:absolute;z-index:500;top:0px;left:0px;right:0px;height:150px;"><textarea id="mv_js_log" cols="80" rows="6"></textarea></div>');
     	var log_elm = document.getElementById('mv_js_log');
     }
     if(log_elm){
     	log_elm.value+=string+"\n";
     }*/
   }
   //in case of "throw error" type usage
   return false;
}
function getNextHighestZindex(obj){
	var highestIndex = 0;
	var currentIndex = 0;
	var elArray = Array();
	if(obj){ elArray = obj.getElementsByTagName('*'); }else{ elArray = document.getElementsByTagName('*'); }
	for(var i=0; i < elArray.length; i++){
		if (elArray[i].currentStyle){
			currentIndex = parseFloat(elArray[i].currentStyle['zIndex']);
		}else if(window.getComputedStyle){
			currentIndex = parseFloat(document.defaultView.getComputedStyle(elArray[i],null).getPropertyValue('z-index'));
		}
       	if(!isNaN(currentIndex) && currentIndex > highestIndex){ highestIndex = currentIndex; }
    }
    return(highestIndex+1);
}
function var_dump(obj) {
   if(typeof obj == "object") {
      return "Type: "+typeof(obj)+((obj.constructor) ? "\nConstructor: "+obj.constructor : "")+"\nValue: " + obj;
   } else {
      return "Type: "+typeof(obj)+"\nValue: "+obj;
   }
}

function js_error(string){
	alert(string);
}