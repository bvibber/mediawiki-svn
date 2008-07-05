/** @fileoverview
 * ~mv_embed~
 * for details see: http://metavid.ucsc.edu/wiki/index.php/Mv_embed
 *
 * All Metavid Wiki code is Released under the GPL2
 * for more info visit http:/metavid.ucsc.edu/code
 *
 * @url http://metavid.ucsc.edu
 * 
 * portions sampled from the vlc browser interface:
 * http://people.videolan.org/~damienf/plugin-0.8.6.html *
 *
 * parseUri:
 * http://stevenlevithan.com/demo/parseuri/js/
 *
 * config values you can manually set the location of the mv_embed folder here
 * (in cases where media will be hosted in a different place than the embbeding page)
 *
 */
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

//get mv_embed location if it has not been set
if(!mv_embed_path){
	getMvEmbedPath();
}
//the default thumbnail for missing images:
var mv_default_thumb_url = mv_embed_path + 'images/vid_default_thumb.jpg';

if(!gMsg){var gMsg={};}
//all default msg in [english] should be overwritten by the CMS language msg system.
gMsg['loading_txt'] ='loading<blink>...</blink>';
gMsg['select_playback']='Set Playback Preference';
gMsg['link_back']='Link Back';
gMsg['error_load_lib']='mv_embed: unable to load required javascript libraries\n'+
			 	'insert script via DOM has failed, try reloading?  ';
			 	
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

//grabs from the globalMsg obj 
//@@todo integrate msg serving into CMS
function getMsg( key ) {
	 if ( key in gMsg ) {
	 	return gMsg[key];
	 } else{
	 	return '[' + key + ']';
	 }
}

/**  the base video control JSON object with default attributes
*    for supported attribute details see README
*/
var video_attributes = {
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
    
    //roe url (for xml based metadata)
    "roe":null,
    //if roe includes metadata tracks we can expose a link to metadata
//    "show_meta_link":true,

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
    "download_link":true
};

//the mvEmbed object drives basic loading of libs:
var mvEmbed = {
  Version: '0.7',  
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
	'$j.ui.slider':'jquery/plugins/ui.slider.js'		
  },
  pc:null, //used to store pointer to parent clip (when in playlist mode) 
  load_libs:function(callback){
  	//load css:
  	if(!styleSheetPresent(mv_embed_path+'mv_embed.css'))
		loadExternalCss(mv_embed_path+'mv_embed.css');
		
  	if(callback)this.load_callback = callback;
 	//two loading stages, first get jQuery
	var _this = this;
  	mvJsLoader.doLoad(this.lib_jquery,function(){
  		//once jQuery is loaded set up no conflict & load plugins: 
 		_global['$j'] = jQuery.noConflict();
 		js_log('jquery loaded');
		mvJsLoader.doLoad(_this.lib_plugins, function(){			
			js_log('plugins loaded');
			mvEmbed.libs_loaded=true;
			mvEmbed.init();
		});	
  	});
  },
  userSetPlayerType:function(player){
  	//callback to the embedType obj to set the cookie/pref: 
  	embedTypes.userSetPlayerType(player);
  	//fade out all pref windows and remove: 
  	$j('.set_ogg_player_pref').fadeOut('slow',function(){
  			$j(this).remove();
  	});  	
  	//@@todo temporarily disable playback or set all to loading...
	//request the new player library: 
/*	var plugins={};
	plugins[embedTypes.getPlayerLib()+'Embed']='mv_'+embedTypes.getPlayerLib()+'Embed.js';
  	mvJsLoader.doLoad(plugins, function(){
  		js_log("done loading: " + embedTypes.getPlayerLib());
  		//re-rewrite all the video objects on the page: 
	  	for(i in global_ogg_list){
	  		js_log('selector: '+'#'+global_ogg_list[i]);
	  		$j('#'+global_ogg_list[i]).get(0).inheritEmbedObj();
	  	}  	
  	})*/
  },
  addLoadEvent:function(fn){  	
  	this.flist.push(fn);  	
  },  
  init: function(){
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

function mediaPlayer(id, supported_types, library)
{
    this.id=id;
    this.supported_types = supported_types;
    this.library = library;
    return this;
}

mediaPlayer.prototype =
{
    id:null,
    supported_types:null,
    library:null,
    
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
    getLibraryFile : function()
    {
        return 'mv_'+this.library+'Embed.js';
    },
    getLibraryObject : function()
    {
        return this.library+'Embed';
    }
}

var flowPlayer = new mediaPlayer(
    'flowplayer',
    ['video/x-flv'],
    'flash'
);

var cortadoPlayer = new mediaPlayer(
    'cortado',
    ['video/ogg'],
    'java'
);

var videoElementPlayer = new mediaPlayer(
    'videoElement',
    ['video/ogg'],
    'native'
);

var vlcMozillaPlayer = new mediaPlayer(
    'vlc-mozilla',
    ['video/ogg', 'video/x-flv', 'video/mp4'],
    'vlc'
);

var vlcActiveXPlayer = new mediaPlayer(
    'vlc-activex',
    ['video/ogg', 'video/x-flv', 'video/mp4'],
    'vlc'
);

var oggPlayPlayer = new mediaPlayer(
    'oggPlay',
    ['video/ogg'],
    'oggplay'
);

var oggPluginPlayer = new mediaPlayer(
    'oggPlugin',
    ['video/ogg'],
    'generic'
);

var quicktimeMozillaPlayer = new mediaPlayer(
    'quicktime-mozilla',
    ['video/ogg'],
    'quicktime'
);

var quicktimeActiveXPlayer = new mediaPlayer(
    'quicktime-activex',
    ['video/ogg'],
    'quicktime'
);

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
        this.default_players['video/x-flv']= ['flowplayer','vlc'];
        this.default_players['video/annodex']=['cortado','vlc'];
    },
    addPlayer : function(player)
    {
        for (var i in this.players)
            if (this.players[i].id==player.id)
                return;
        js_log('Adding ' + player.id);
        this.players.push(player);
    },
    getMIMETypePlayers : function(mime_type)
    {
        var mime_players = new Array();
        for (var i in this.players)
            if (this.players[i].supportsMIMEType(mime_type))
                mime_players.push(this.players[i]);
                
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
            // otherwise use the hard-coded preference list
            for(var d in this.default_players)
                for(var i in mime_players)
                    if(mime_players[i].id==this.default_players[d])
                        return mime_players[i];
            // otherwise just return the first compatible player
            return mime_players[0];
        }
        return null;
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
                if(embed.media_element.selected_source.mime_type == mime_type)
                {
                    embed.selected_player = selected_player;
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
                js_log('setting preference for ' + name_value[0] + ' to ' + name_value[1]);
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

/*parseUri class:*/
var parseUri=function(d){var o=parseUri.options,value=o.parser[o.strictMode?"strict":"loose"].exec(d);for(var i=0,uri={};i<14;i++){uri[o.key[i]]=value[i]||""}uri[o.q.name]={};uri[o.key[12]].replace(o.q.parser,function(a,b,c){if(b)uri[o.q.name][b]=c});return uri};parseUri.options={strictMode:false,key:["source","protocol","authority","userInfo","user","password","host","port","relative","path","directory","file","query","anchor"],q:{name:"queryKey",parser:/(?:^|&)([^&=]*)=?([^&]*)/g},parser:{strict:/^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,loose:/^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/}};

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
/*		if(!this.playerType){
			for ( var i = 0; i < this.players.length; i++ ) {
				if ( this.clientSupports[this.players[i]] ) {
					this.playerType = this.players[i];
					break;
				}
			}
		}*/
/*		js_log('selected:' + this.playerType);
		js_log( this.clientSupports);
		
		//add the detected plugin playback type to the plugins :
		if(this.playerType){
			js_log('player type ok: '+this.getPlayerLib());
			if( this.getPlayerLib() ){				
				mvEmbed.lib_plugins[this.getPlayerLib()+'Embed']='mv_'+this.getPlayerLib()+'Embed.js';
			}
			js_log('set lib: '  +mvEmbed.lib_plugins[this.getPlayerLib()+'Embed']);
		}*/
        // set to load all the supported libraries
        //@@todo... should only load the player type for the current selected plugin...
        for (var i in this.players.players)
            mvEmbed.lib_plugins[this.players.players[i].getLibraryObject()]=this.players.players[i].getLibraryFile();
	},
	clientSupports: { 'thumbnail' : true },
 	detect: function() {
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
			 // VLC
			 if ( this.testActiveX( 'VideoLAN.VLCPlugin.2' ) )
			 	this.players.addPlayer(vlcActiveXPlayer);
			 // Java
			 if ( javaEnabled && this.testActiveX( 'JavaWebStart.isInstalled' ) )
			 	this.players.addPlayer(cortadoPlayer);
			 //temporary disable QuickTime check
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
						this.players.addPlayer(vlcMozillaPlayer);
					else if ( pluginName.indexOf( 'QuickTime' ) > -1 )
						this.players.addPlayer(quicktimeMozillaPlayer);
					else
						this.players.addPlayer(oggPluginPlayer);
					continue;
				} else if ( uniqueMimesOnly ) {
					if ( type == 'application/x-vlc-player' ) {
						this.players.addPlayer(vlcMozillaPlayer);
						continue;
					} else if ( type == 'video/quicktime' ) {
						this.players.addPlayer(quicktimeMozillaPlayer);
						continue;
					}
				}
			
				if ( type == 'video/quicktime' ) {
					this.players.addPlayer(vlcMozillaPlayer);
					continue;
				}
   				if(type=='application/x-shockwave-flash'){
					this.players.addPlayer(flowPlayer);
					continue;
				}
   				if(type=='application/x-shockwave-flash'){
   					//@@temporary disabled flash player (not fully functional yet)    		
					//this.clientSupports['flowplayer']= true;
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
	 },
}
//setup the embed type (cookie preference or javascript detected embed type)
embedTypes.init();
//load an external JS (similar to jquery .require plugin)
//but checks for object availability rather than load state
var mvJsLoader = {
	 libreq:{},
	 load_time:0,
	 doLoad:function(libs,callback){
		 if(libs){this.libs=libs;}else{libs=this.libs;}		 
		 this.callback=(callback)?callback:this.callback;
		 var loading=0;
		 var i=null;
		 //js_log("doLoad_ load set to 0 on libs:"+ libs);
		 for(i in libs){
			 //itor the objPath (to avoid 'has no properties' errors)
			 var objPath = i.split('.');
			 var cur_path ='';
			 var cur_load=0;
			 for(p in objPath){
				 cur_path = (cur_path=='')?cur_path+objPath[p]:cur_path+'.'+objPath[p];
				 //js_log("looking at path: "+ cur_path);
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
				 if(!this.libreq[i])loadExternalJs(mv_embed_path + libs[i]);
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
//ENTRY POINT when dom ready:
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
		//if safari we have already foce loadded the libs
		if(embedTypes.safari){
			js_log('run init for safari');
			mvEmbed.init();
		}else{		
			mvEmbed.load_libs();
		}
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
			swapEmbedVideoElement(vidElm, videoInterface);
			return videoInterface;
		}else{
			js_log('video element not found: '+vid_id);
		}
	}
}
//SET DOM Ready state:
// for Mozilla browsers
if (document.addEventListener) {
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
//for safari just force load all the libraries since it has no dom injection
if(embedTypes.safari){
	//load the base lib_jquery library: 
	for(i in mvEmbed.lib_jquery){
		var cur_lib_url = mv_embed_path + mvEmbed.lib_jquery[i];
		js_log('load lib:' + cur_lib_url);
		document.write('<script type="text/javascript" src="'+cur_lib_url+'"><\/script>');
	}
	
	//load the rest (@@todo we could merge these)
  	for(i in mvEmbed.lib_plugins){
		var cur_lib_url = mv_embed_path + mvEmbed.lib_plugins[i];
		js_log('load lib:' + cur_lib_url);
		document.write('<script type="text/javascript" src="'+cur_lib_url+'"><\/script>');
	}
	window.onload=function(){
		//once jQuery is loaded set up no conflict: 
		_global['$j'] = jQuery.noConflict();
		init_mv_embed();
	}
}
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
        for(i = 0; i < video_elements.length; i++) {           	         	        
            //grab id:
            vid_id = video_elements[i].getAttribute("id");       
            //set id if empty:
            if(!vid_id || vid_id==''){
  				video_elements[i].id= 'v'+ global_ogg_list.length;    	
            }
            //create and swap in the video interface:
	   		var videoInterface = new embedVideo(video_elements[i]);
   			//swap:
	   		if(swapEmbedVideoElement(video_elements[i], videoInterface)){
	   			i--;
	   		}
        }
    }else{
    	js_log('no <video> elements found');
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
			var pl_id = playlist_elements[i].getAttribute('id');
			if(!pl_id || pl_id==''){
  				playlist_elements[i].id = 'v'+ global_ogg_list.length;    	
            }
			//create new playlist interface:
			var playlistInterface = new mvPlayList( playlist_elements[i] );
			if(swapEmbedVideoElement(playlist_elements[i], playlistInterface) ){	                 	   
				i--;			
			}
		}
	});
}

/*
* createEmbedVideoElement 
* takes a video element as input and swaps it out with 
* an embed video interface based on the video_elements attributes
*/
function swapEmbedVideoElement(video_element, videoInterface){
	js_log('do swap');
	embed_video = document.createElement('div');	
	//inherit the video interface  
	for(method in videoInterface){
		//string -> boolean:
		if(videoInterface[method]=='false')videoInterface[method]=false;
		if(videoInterface[method]=='true')videoInterface[method]=true;
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
	}
	///js_log('did vI style');
  	//now swap out the video element for the embed_video obj:  	  	
  	$j(video_element).after(embed_video).remove();
  	//update HTML 
  	$j('#'+embed_video.id).get(0).getHTML();
  	
    /*var parent_elm = video_element.parentNode;
    parent_elm.removeChild(video_element);
    
    //append the object into the dom: 
    parent_elm.appendChild(embed_video);  
       
    //now run the getHTML on the new embedVideo Obj:
    embed_video.getHTML();    	
    */
    js_log('html set:' + document.getElementById(embed_video.id).innerHTML);
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
		this.pe=parentEmbed;
		//parse roe if not already done: 
		
		//if not "live" or under 5 min load all transcript in one request	
		if(!this.pe.roe_xml){
			js_log("MISSING ROE DATA for text interface");
			var _this = this;
			this.pe.getParseROE( function(){				
				_this.textInterface.getParseCMML();
			});
		}else{
			this.getParseCMML();
		}
		//start the autoscroll timer: 
		this.setAutoScroll(true);		
	},
	//@@todo seperate out data loader & data display 
	getParseCMML:function(){		
		js_log("load cmml");
		//read the current play head time (if embed object is playing)
		
		//look at avaliable layers and default layer set 
		
		//@@todo use user-language as key to select transcript layer.
		_this = this;
		$j.each(this.pe.roe_xml.getElementsByTagName('mediaSource'), function(inx, n){
			if(n.getAttribute('content-type')=='text/cmml'){
				_this.availableTracks[n.getAttribute('id')] = {
					src:n.getAttribute('src'),
					title:n.getAttribute('title'),
					loaded:false,
					display:false
				}
				//load or skip the track based on "default" attribute
				if(n.getAttribute('default')!='true'){
					return;
				}else{
					//load the track if its default track
					_this.load_track(n.getAttribute('id'));
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
					start:n.getAttribute('start').replace('ntp:', ''),
					end:n.getAttribute('end').replace('ntp:', ''),
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
			js_log('appended: '+ selHTML);
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
		/*
		out+= '<div class="mv_loading_icon" style="background:url(\''+mv_embed_path+'images/indicator.gif\');display:';
		out+= (this.roe_xml)? 'none':'inline';
		out+='"/>';		
		*/
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
    
    start_ntp:null,
    end_ntp:null,
    
    init : function(element)
    {
        this.uri = element.getAttribute('src');
        if(ogg_chop_links)
            this.uri = this.uri.replace(".anx", '');
        this.marked_default = false;

        var tag = element.tagName.toLowerCase();
        
        if (tag == 'video')
            this.marked_default = true;

        if (element.hasAttribute("title"))
            this.title = element.getAttribute("title");
        else
            this.title = this.uri;

        if (element.hasAttribute('type'))
            this.mime_type = element.getAttribute('type');
        else if (element.hasAttribute('content-type'))
            this.mime_type = element.getAttribute('content-type');
        else
            this.mime_type = this.detectType(this.uri);
        js_log('Adding mediaSource of type ' + this.mime_type + ' and uri ' + this.uri + ' and title ' + this.title);
        this.parseURLDuration();
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
        @return the URI of the source.
        @type String
    */
    getURI : function()
    {
        return this.uri;
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
     * special case derive duration from request url (in float seconds) @@todo should be float seconds
	 * (for media_url?t=ntp_start/ntp_end url request format
     */  
    parseURLDuration : function(){	 	
        //js_log('get duration for:' + this.uri);	 		 	 
        var index_time_val = false;		 		 
        if(this.uri.indexOf('?t=')!=-1)index_time_val='?t=';
        if(this.uri.indexOf('&t=')!=-1)index_time_val='&t=';
        if(index_time_val){
            var end_index = (this.uri.indexOf('&', this.uri.indexOf(index_time_val))==-1)?
	     					this.uri.length:
			    			this.uri.indexOf('&', this.uri.indexOf(index_time_val));
			this.start_ntp = this.uri.substring( 
	   				this.uri.indexOf(index_time_val)+index_time_val.length,
		    		this.uri.indexOf('/', this.uri.indexOf(index_time_val) ));
		    this.end_ntp = this.uri.substring(
		    		this.uri.indexOf('/', this.uri.indexOf(index_time_val))+1, end_index);
		    this.start_offset = ntp2seconds(this.start_ntp);
	   		this.duration = ntp2seconds( this.end_ntp ) - this.start_offset;
		    //keep values in float seconds 
		    this.start_offset =  this.start_offset
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
    	//@@todo if media is on the same server as the javascript we can issue a HEAD request and read the mime type of the media... 
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
    /** Selected mediaSource element. */
    selected_source:null,
    thumbnail:null,
    linkback:null,
    
    /** @private */
    init:function(video_element)
    {
        var _this = this;
        
        js_log('Initializing mediaElement...');
        this.sources = new Array();
        this.thumbnail = mv_default_thumb_url;
        // Process the <video> element
        this.tryAddSource(video_element);
        if(video_element.hasAttribute('thumbnail'))
            this.thumbnail=video_element.getAttribute('thumbnail');
        if(video_element.hasAttribute('poster'))
            this.thumbnail=video_element.getAttribute('poster');
        // Process all inner <source> elements
        inner_source_elements = video_element.getElementsByTagName('source');
        $j.each(inner_source_elements, function(inx, inner_source)
        {
            _this.tryAddSource(inner_source);
        });
        // Process the provided ROE file if any
        if(video_element.hasAttribute('roe'))
            do_request(video_element.getAttribute('roe'), function(data)
            {
                _this.addROE(data);
            });
        // Select the default source
        for (var source in this.sources)
            if(this.sources[source].marked_default)
                this.selected_source = this.sources[source];
        // or the first source
        if (!this.selected_source)
        {
            js_log('autoselecting first source');
            this.selected_source = this.sources[0];
        }
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
        js_log("selected source " + this.sources[index].getTitle());
        this.selected_source = this.sources[index];
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
    /** Adds a single mediaSource using the provided element if
        the element has a 'src' attribute.
        @param element {element} <video>, <source> or <mediaSource> element.
    */
    tryAddSource:function(element)
    {
        if (!element.hasAttribute('src'))
            return;

        this.sources.push(new mediaSource(element));
    },
    /** Imports media sources from ROE data.
        @param roe_data ROE data.
    */
    addROE:function(roe_data)
    {
        var _this = this;
        if(typeof roe_data == 'string')
        {
            var parser=new DOMParser();
            js_log(roe_data)
            roe_data=parser.parseFromString(roe_data,"text/xml");
        }
        if(roe_data){
	        $j.each(roe_data.getElementsByTagName('mediaSource'), function(inx, source)
	        {
				_this.tryAddSource(source);
	        });
	        //set the thumbnail:
			$j.each(roe_data.getElementsByTagName('img'), function(inx, n){
	            if(n.getAttribute("id")=="stream_thumb"){
	                js_log('set thumb to '+n.getAttribute("src"));
	                _this['thumbnail'] = n.getAttribute("src");	
	            }
	        })
	        //set the linkback:
			$j.each(roe_data.getElementsByTagName('link'), function(inx, n){
	            if(n.getAttribute('id')=='html_linkback'){
	                js_log('set linkback to '+n.getAttribute("href"));
	                _this['linkback'] = n.getAttribute('href');
	            }
	        })
        }
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
	roe_xml:null,
	load_external_data:false,	
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
	init: function(element){
		this.element_pointer = element;
		//inherit all the default video_attributes
	    for(var attr in video_attributes){       
	        if(element.getAttribute(attr)){
	            this[attr]=element.getAttribute(attr);
	            //js_log('attr:' + attr + ' val: ' + video_attributes[attr] +" "+'elm_val:' + element.getAttribute(attr) + "\n (set by elm)");  
	        }else{        
	            this[attr]=video_attributes[attr];
	            //js_log('attr:' + attr + ' val: ' + video_attributes[attr] +" "+ 'elm_val:' + element.getAttribute(attr) + "\n (set by attr)");  
	        }
	    }
        // load all of the specified sources
        this.media_element = new mediaElement(element);
	    js_log('continue_thumb:'+ this['thumbnail']);
   	    js_log('continue_src:'+ this['src']);

	    //if style is set override width and height
	    if(element.style.width)this.width = element.style.width.replace('px','');
	    if(element.style.height)this.height = element.style.height.replace('px','');
	    //set the plugin id
	    this.pid = 'pid_' + this.id;
	         
	    //grab any innerHTML and set it to missing_plugin_html
	    if(element.innerHTML!=''){
            js_log('innerHTML: ' + element.innerHTML);
	        this.user_missing_plugin_html=element.innerHTML;
	    } 

		//auto select player based on prefrence or default order
        this.selected_player = embedTypes.players.defaultPlayer(this.media_element.selected_source.mime_type);
        if(this.selected_player)
        {
            js_log('selected ' + this.selected_player.getName());
            js_log("PLAYBACK TYPE: "+this.selected_player.library);
        }
        else
            js_log('no player found for mime type ' + this.media_element.selected_source.mime_type);
	    
	    /*
	    * @@TODO lazy load plugin types
	    * override all relevant exported functions with the {embed_type} Object 
	    * place the base functions in parent.{function name}
	    */
		this.inheritEmbedObj();
		
	   //js_log('HTML FROM IN OBJECT' + this.getHTML());
	   //return this object:	   
	   return this;
	},
    getDuration:function(){
        return this.media_element.selected_source.duration;
    },
  	/* get the duration in ntp format */
	getDurationNTP:function(){
		return seconds2ntp(this.getDuration()/1000);
	},

    doEmbedHTML:function()
    {     
    	this.inheritEmbedObj();   
        var embed_code = this.getEmbedHTML();
        js_log(embed_code);
        this.innerHTML = embed_code;
		this.paused = false;
		this.thumbnail_disp=false;
    },
	inheritEmbedObj:function(){
		js_log("inheritEmbedObj");
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
		eval('embedObj = ' +this.selected_player.library +'Embed;');
        for(method in embedObj){
        	//parent method preservation for local overwritten methods
        	if(this[method])this['parent_' + method] = this[method];
            this[method]=embedObj[method];
           
        }
        if(this.inheritEmbedOverride){
        	this.inheritEmbedOverride();
        }
	},
	getHTML : function (){
        //returns the innerHTML based on auto play option and global_embed_type
        //if auto play==true directly embed the plugin
        if(this.autoplay){ 	   
            this.thumbnail_disp = false;			
            this.innerHTML = this.getEmbedHTML();
        }else{
            //if autoplay=false or render out a thumbnail with link to embed html      
           this.thumbnail_disp = true;
           this.innerHTML = this.getThumbnailHTML();	    
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
	//updates the video src
	updateVideoSrc : function(src){
		this.src = src;
	},
	//updates the thumbnail if the thumbnail is being displayed
	updateThumbnail : function(src, quick_switch){
		js_log('set to thumb:'+ src);
		if(quick_switch){
			$j('#img_thumb_'+this.id).attr('src', src);
		}else{
			if(this.thumbnail_disp){
				$j('#dc_'+this.id).append('<img src="'+src+'" ' +
					'style="display:none;position:absolute;zindex:2;top:0px;left:0px;" ' +
					'width="'+this.width+'" height="'+this.height+'" '+
					'id = "new_img_thumb_'+this.id+'" />');
				$j('#new_img_thumb_'+this.id).fadeIn("slow", function(){
						//once faded in remove org and rename new:
						$j('#img_thumb_'+this.id).remove();
						$j('#new_img_thumb_'+this.id).attr('id', 'img_thumb_'+this.id);
						$j('#img_thumb_'+this.id).css('zIndex','1');
				});			
			}
		}
	},
    /** Returns the HTML code for the video when it is in thumbnail mode.
        This includes the specified thumbnail as well as buttons for
        playing, configuring the player, inline cmml display, HTML linkback,
        download, and embed code.
    */
	getThumbnailHTML : function (){
	    var thumb_html = '';
	    var class_atr='';
	    var style_atr='';
	    //if(this.class)class_atr = ' class="'+this.class+'"';
	    //if(this.style)style_atr = ' style="'+this.style+'"';
	    //    else style_atr = 'overflow:hidden;height:'+this.height+'px;width:'+this.width+'px;';
        var thumbnail = this.media_element.getThumbnailURL();

	    //put it all in the div container dc_id
	    thumb_html+= '<div id="dc_'+this.id+'" style="position:relative;'+
	    	' overflow:hidden; top:0px; left:0px; width:'+this.width+'px; height:'+this.height+'px; z-index:0;">'+
	        '<img width="'+this.width+'" height="'+this.height+'" style="position:relative;width:'+this.width+';height:'+this.height+'"' +
	        ' id="img_thumb_'+this.id+'" src="' + thumbnail + '">';

		js_log("PLAY BUTTON: " + this.play_button);
	    if(this.play_button==true)
		  	thumb_html+=this.getPlayButton();
	  	
	  	 //add plugin config button (don't add for playlists) 
	  	 if(!this.pc){
			 thumb_html+='<div style="border:none;position:absolute;top:2px;left:2px;z-index:99;width:28px;height:28px;">' +
				 '<a title="'+getMsg('select_playback')+'" href="#" onClick="document.getElementById(\''+this.id+'\').selectPlaybackMethod();return false;">'+
				 	getTransparentPng({id:'plug_'+this.id,width:'27',height:'27',src:mv_embed_path + 'images/vid_plugin_edit_sm.png'})+
				 '</a>'+
			 '</div>';
	  	 }

	  	//add in cmml inline dispaly if roe descption avaliable
	  	//not to be displayed in stream interface.
	  	if(this.media_element.hasStreamOfMIMEType('text/cmml')){
	  		thumb_html+='<div style="border:none;position:absolute;top:2px;right:2px;z-index:1">'+
		     '<a title="'+getMsg('select_transcript_set')+'" href="javascript:document.getElementById(\''+this.id+'\').showTextInterface();">';
		    thumb_html+=getTransparentPng({id:'metaButton_'+this.id, width:"40", height:"27", border:"0", 
						src:mv_embed_path + 'images/cc_meta_sm.png' });
			thumb_html+='</div>';    	
	  	}
	  	
	    //add link back if requested
	    if(this.linkback){
	    	thumb_html+='<div style="border:none;position:absolute;bottom:2px;right:2px;z-index:1">'+
		     '<a title="'+getMsg('clip_linkback')+'" target="_new" href="'+this.linkback+'">';
		    thumb_html+=getTransparentPng({id:'lb_'+this.id, width:"27", height:"27", border:"0", 
						src:mv_embed_path + 'images/vid_info_sm.png' });
			thumb_html+='</div>';    	
	    }
	    //add direct download link if option:
	    if(this.download_link){
	    	//check for roe attribute (extened download options)
	    	var dlLink = (this.roe)?'javascript:document.getElementById(\''+this.id+'\').showVideoDownload();':this.src;	    	
	    	thumb_html+='<div style="border:none;position:absolute;bottom:2px;left:2px;z-index:1">'+
		     '<a title="'+getMsg('download_clip')+'" href="'+dlLink+'">';		     
		    thumb_html+=getTransparentPng({id:'lb_'+this.id, width:"27", height:"27", border:"0", 
						src:mv_embed_path + 'images/vid_download_sm.png' });
			thumb_html+='</div>';   
	    }
	    //add in embed link (if requested) 
	    if(this.embed_link){	
	    	var right_offset = (this.linkback)?32:0;
			thumb_html+='<div style="border:none;position:absolute;bottom:2px;right:'+right_offset+'px;z-index:1">'+
		     '<a title="Embed Clip Code" href="javascript:document.getElementById(\''+this.id+'\').hideShowEmbedCode();">';
	
			thumb_html+=getTransparentPng(new Object ({id:'le_'+this.id, width:"27", height:"27", border:"0", 
						src:mv_embed_path + 'images/vid_embed_sm.png' }));
			thumb_html+='</a></div>';
			//make link absolute (if it was not already)
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
			//add the hidden embed code:
			thumb_html+='<div id="embed_code_'+this.id+'" style="border:solid;border-color:black;overflow:hidden;display:none;position:absolute;bottom:2px;right:'+(right_offset+30)+'px;width:'+(this.width-100)+'px;z-index:1">'+
				'<input onClick="this.select();" type="text" size="40" length="1024" value="'+embed_code_html+'">'
				 '</div>';
	    }	
	    thumb_html+='</div>';		  	
	    return thumb_html;
	},
	getPlayButton:function(id){
		if(!id)id=this.id;
		//setup button size
		var play_btn_height = play_btn_width = 109;
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
						src:mv_embed_path + 'images/mv_embed_play.png' }));				
		out+='</a></div>';
		return out;
	},
	//display the code to remotely embed this video:
	hideShowEmbedCode : function(){
		if($j('#embed_code_'+this.id).css('display')=='none'){
			$j('#embed_code_'+this.id).fadeIn("slow");
		}else{
			$j('#embed_code_'+this.id).fadeOut("slow", function(){
				$j('#embed_code_'+this.id).css('display', 'none');
			});
		}		
		/* this should work but does not! :( 
		$j('#embed_code_'+this.id).toggle(function(){
			$j(this).fadeIn("slow");	
		},function(){
			$j(this).fadeOut("slow");
		} );
		* 
		*/
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
        //put select list on-top
        //make sure the parent is relatively positioned:
        $j('#'+this.id).css('position', 'relative');
        //set height width (check for playlist container) 
        var width = (this.pc)?this.pc.pp.width:this.width;
        var height = (this.pc)?this.pc.pp.height:this.height;
        if(width<320)width=320;
        if(height<240)height=240;

        var sel_id = (this.pc!=null)?this.pc.pp.id:this.id;
        var close_link ='<a href="#" style="color:white" onClick="document.getElementById(\''+this.id+'\').closeDisplayedHTML();return false;">close</a>';

        //fade in a black bg div ontop of everything
         var div_code = '<div id="blackbg_'+sel_id+'" ' +
			 'style="overflow:auto;position:absolute;display:none;z-index:2;background:black;top:0px;left:0px;' +
				 'height:'+parseInt(height)+'px;width:'+parseInt(width)+'px;">'+
                 '<span id="close_vl'+this.id+'" style="position:absolute:top:2px;left:2px;color:white;">'+close_link+'</span>'+
       			 '<span class="displayHTML" id="con_vl_'+this.id+'" style="position:absolute;top:20px;left:20px;color:white;">' +
             html_code +
                close_link+'</span>'
      		 '</div>';
        $j('#'+sel_id).append(div_code);
        $j('#blackbg_'+sel_id).fadeIn("slow");
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
			if(this.selected_player.library==supporting_players[i].library ){
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
        var _this=this;
        var select_code=this.getDLlist(function(index, source)
        {
            var default_player = embedTypes.players.defaultPlayer(source.getMIMEType());
            var source_select_code = 'document.getElementById(\''+_this.id+'\').closeDisplayedHTML(); document.getElementById(\''+_this.id+'\').media_element.selectSource(\''+index+'\');';
            var player_code = _this.getPlayerSelectList(source.getMIMEType(), index, source_select_code);
            return '<a href="#" onClick="' + source_select_code + 'embedTypes.players.userSelectPlayer(\''+default_player.id+'\',\''+source.getMIMEType()+'\'); return false;">'
                + source.getTitle()+' - ' + default_player.getName() + '</a> '
                + '(<a href="#" onClick=\'$j("#player_select_list_'+index+'").fadeIn("slow");return false;\'>choose player</a>)' + player_code;
        });
        this.displayHTML(select_code);
    },
	showVideoDownload:function(){
        var select_code=this.getDLlist(function(index, source)
        {
            return '<a style="color:white" href="' + source.getURI() +'"> '
                + source.getTitle()+'</a>';
        });
        this.displayHTML(select_code);
	},
	/*
	 * Its pretty confusing to merge getDLlist with selectPlaybackMethod
	 */
	getDLlist:function(transform_function){
		var out='<b style="color:white;">'+getMsg('download_from')+' '+parseUri(this.src).queryKey['t']+'</b><br>';
		out+='<span style="color:white"><blockquote>';
		var dl_list=dl_txt_list='';
		$j.each(this.media_element.getSources(), function(index, source)
        {	
			var dl_line = '<li>' + transform_function(index, source) + '</li>'+"\n";						
			if(this.getMIMEType()=="video/ogg"){
                out+=dl_line;
			}else if(this.getMIMEType()=="text/cmml"){
				dl_txt_list+=dl_line;
			}else{
				dl_list+=dl_line;
			}
		});
		out+='</blockquote>'+getMsg('download_full')+"<blockquote>"+dl_list+'</blockquote>';
		out+='</blockquote>'+getMsg('download_text')+"<blockquote>"+dl_txt_list+'</blockquote></span>';		
		return out;
	},
	/*
	*  base embed controls 
	*	the play button calls 
	*/
	play : function(){	
		js_log("mv_embed play");
		//check if thumbnail is being displayed and embed html
		if(this.thumbnail_disp){
			//js_log('rewrite embed');
			if(!this.selected_player){
				//this.innerHTML = this.getPluginMissingHTML(); 
				//$j('#'+this.id).html(this.getPluginMissingHTML());
				this.innerHTML = this.getPluginMissingHTML();
			}else
                this.doEmbedHTML();
		}else{
			//the plugin is already being displayed
		}
	},
	play_or_pause : function(){	
		js_log('base play or pause');
		var id = (this.pc!=null)?this.pc.pp.id:this.id;
		
		var play_or_pause = document.getElementById('mv_play_or_pause_'+id);	
		//check if we are in a playlist: 
		
	    if(play_or_pause){
	    	//check state and set play or pause
	    	if(this.paused){
	    		js_log('do play');
				//(paused) do play
				this.play();
				this.paused=false;
				play_or_pause.innerHTML = getTransparentPng(new Object ({id:'mv_pop_btn_'+id,style:'float:left',width:'27', height:'27', border:"0", 
						src:mv_embed_path+'images/vid_pause_sm.png' }));
			}else{
				js_log('do pause');
				//(playing) do pause
				this.pause();
				this.paused=true;
				play_or_pause.innerHTML = getTransparentPng(new Object ({id:'mv_pop_btn_'+id,style:'float:left',width:'27', height:'27', border:"0", 
						src:mv_embed_path+'images/vid_play_sm.png' }));
			}
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
		js_log('base stop');
		//check if thumbnail is being displayed in which case do nothing
		if(this.thumbnail_disp){
			//already in stooped state
			js_log('already in stopped state');
		}else{
			//rewrite the html to thumbnail disp 
			//$j(this).html(this.getThumbnailHTML());						
			this.innerHTML = this.getThumbnailHTML();
			this.thumbnail_disp=true;
		}
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
	get_interface_lib : function(doLoad){				
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
				//$j('#'+this.id).html(this.getEmbedHTML());
				this.innerHTML = this.getEmbedHTML();
			}
		}
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
		/*this.slider = new Control.Slider('playhead_'+this.id, 'track_'+this.id,{
			sliderValue:0,
			onSlide:function(v){
				if(! thisVid.userSlide){
					//user slide clip:
					thisVid.userSlide=true;
					js_log('user slide: ' + thisVid.userSlide);
				}										
			},
			onChange:function(v){ 
				if(thisVid.userSlide==true){						
					//seek to a given position:
					js_log('this.userSlide seek to: ' + v);	
					thisVid.doSeek(v);	
					thisVid.userSlide=false;
				}
			}
		});*/			
	},
	setSliderValue: function(perc){
		var id = (this.pc)?this.pc.pp.id:this.id;
		//this.slider.setValue(perc);
		var cur_slider = $j('#playhead_'+id).width();
		var offset_perc = 1-(cur_slider / $j('#slider_'+id).width());
		var val = Math.round( offset_perc* (perc *  $j('#slider_'+id).width() ) );
		$j('#playhead_'+id).css('left',val);		
		//js_log('op:' + offset_perc + ' *('+perc+' * ' + $j('#slider_'+id).width() + ')');
	},
	setStatus:function(value){
		var id = (this.pc)?this.pc.pp.id:this.id;
		//update status: 
		$j('#info_'+id).html(value);		
	},
	wrapEmebedContainer:function(embed_code){
		//check if parent clip is set( ie we are in a playlist so name the embed container by playlistID)
		var id = (this.pc!=null)?this.pc.pp.id:this.id;
		return '<div id="mv_ebct_'+id+'" style="width:'+this.width+'px;height:'+this.height+'px;">' + 
					embed_code + 
				'</div>';
	},
	getEmbedHTML:function(){
		//double check we have a player and associated getEmbedObj html
		if(!this.selected_player){
			return this.getPluginMissingHTML();		
		}else{
	    	var controls_html ='';
			if(this.controls){
				//all that is supported when we don't know the js hooks is "stop"
				controls_html = this.getControlsHtml('stop');
			}
	        return this.wrapEmebedContainer( this.getEmbedObj() ) + controls_html;
		}
    },    
	getControlsHtml : function(type){
		var id = (this.pc)?this.pc.pp.id:this.id;
		switch(type){
			case 'all':
				return 	this.getControlsHtml('play_head') +					
						this.getControlsHtml('play_or_pause') + 
						this.getControlsHtml('stop') + 
						this.getControlsHtml('fullscreen') + 
						this.getControlsHtml('info_span');
			break;
			case 'play_or_pause':
				return '<a id="mv_play_or_pause_'+id+'" title="play_or_pause" href="javascript:document.getElementById(\''+id+'\').play_or_pause();">'+
					getTransparentPng(new Object ({id:'mv_pop_btn_'+id,style:'float:left',width:'27', height:'27', border:"0", 
						src:mv_embed_path+'images/vid_pause_sm.png' })) + 
					'</a>';
			break;
			case 'play':
				return '<a title="play" href="javascript:document.getElementById(\''+id+'\').play();">'+
					getTransparentPng(new Object ({id:'mv_play_btn',style:'float:left',width:'27', height:'27', border:"0", 
						src:mv_embed_path+'images/vid_play_sm.png' })) + 
					'</a>';
			break;
			case 'stop':
				return	'<a title="stop" href="javascript:document.getElementById(\''+id+'\').stop();">'+
					getTransparentPng(new Object ({id:'mv_stop_btn',style:'float:left',width:'27', height:'27', border:"0", 
						src:mv_embed_path+'images/vid_stop_sm.png' })) + 							
					'</a>';
			break;
			case 'fullscreen':
				return '<a title="fullscreen" href="javascript:document.getElementById(\''+id+'\').fullscreen();">'+
					getTransparentPng(new Object ({id:'mv_fs_btn',style:'float:left',width:'27', height:'27', border:"0", 
						src:mv_embed_path+'images/vid_full_screen_sm.png' })) + 
				'</a>';
			break;
			case 'play_head':
				js_log('set pl: '+id);
				return '<div class="mv_track" id="slider_'+id+'" style="width:'+this.width+'px;'+ 
							'z-index:5;height:4px; background: url('+mv_embed_path+'images/bd-gray.gif) repeat scroll 5px 0px;">'+
								' <div id="playhead_'+id+'" class="mv_playhead" ' +
									'style="z-index:5;background-image: url('+mv_embed_path+'images/slider_handle.gif);"></div>' + 						
						'</div>';				
			break;
			case 'info_span':
				return '<span id="info_cnt_'+id+'" style="float:left">' +
						' <span id="info_'+id+'" class="mv_status" style="position:relative;top:'+((this.pc)?-10:0)+'px">--</span>'+
						'</span>';
			break;		
		}		
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
	//if ( hours < 10 ) hours = "0" + hours;
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
	if(times.length!=3)
		return null;
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
			if(parseUri(document.URL).host == parseUri(mv_embed_path).host ){
				js_log('use mv_embed_proxy : ' + parseUri(document.URL).host + ' == '+ parseUri(mv_embed_path).host);	
				//alert("do ajax req:" +req_url);
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
				if(!mv_json_response){
					//@@todo should remove this functionality from mv_data_proxy 
					//and require sites serve up data as javascript with a callback 									
					req_url  =req_url.replace(/&/g,'__amp__');
					loadExternalJs(mv_embed_path+'mv_data_proxy.php?url='+req_url+
						'&cb=mv_jsdata_cb&cb_inx='+(global_req_cb.length-1) );
				}else{
					//response type is mv_json_response don't hit mv_data_proxy
					loadExternalJs(req_url+'&cb=mv_jsdata_cb&cb_inx='+(global_req_cb.length-1));
				}
			}
		}
}
function mv_jsdata_cb(response){	
	js_log('mv_jsdata_cb');
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
				js_log('load string:'+ response['pay_load']);				
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
	}			
	global_req_cb[response['cb_inx']](response['pay_load']);
}
//load external js via dom injection
function loadExternalJs(url){  
   	js_log('load js: '+ url);
	var e = document.createElement("script");
	e.setAttribute('src', url);
	e.setAttribute('type',"text/javascript");
	//e.setAttribute('defer', true);
	document.getElementsByTagName("head")[0].appendChild(e);     
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
   //js_log('load css: ' + url);
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
	for(i=0;i<js_elements.length; i++){
		var mstr = js_elements[i].src.indexOf('mv_embed.js');
		if( mstr !=-1){
			mv_embed_path = js_elements[i].src.substr(0,mstr);
		}	
	}	
	//js_log('found mv_embed:'+ mv_embed_path +' '+ mv_embed_path.indexOf('://'));
	//absolute the url (if relative) (if we don't have mv_embed path)
	if(mv_embed_path.indexOf('://')==-1){
		//js_log('url: ' + document.URL);
		var pURL = parseUri(document.URL);		
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