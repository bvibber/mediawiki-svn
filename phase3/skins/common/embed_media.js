// JavaScript Document

/* 
	detects if the user has the vlc or annodex browser plugin.
	//if they don't have annodex & on a win/linux look for java/plugin
	//if on mac force java plugin. 
*/
var wiki_web_path = 'http://metavid.ucsc.edu/wiki_dev/phase3/';
//some client detection code: 
var agt=navigator.userAgent.toLowerCase();
var is_major = parseInt(navigator.appVersion);

var is_nav = ((agt.indexOf('mozilla')!=-1) && (agt.indexOf('spoofer')==-1)
&& (agt.indexOf('compatible') == -1) && (agt.indexOf('opera')==-1)
&& (agt.indexOf('webtv')==-1) && (agt.indexOf('hotjava')==-1));

var is_nav4up = (is_nav && (is_major >= 4));
var is_nav6up = (is_nav && (is_major >= 5));

var is_ie = ((agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1));
var is_ie5 = (is_ie && (is_major == 4) && (agt.indexOf("msie 5.0")!=-1) );
var is_ie5_5 = (is_ie && (is_major == 4) && (agt.indexOf("msie 5.5") !=-1));
var is_ie6 = (is_ie && (is_major == 4) && (agt.indexOf("msie 6.0") !=-1));
var is_ie5up = (is_ie && (is_major == 4)
&& ( (agt.indexOf("msie 5.0")!=-1)
|| (agt.indexOf("msie 5.5")!=-1)
|| (agt.indexOf("msie 6.0")!=-1) ) );

var is_mac = (agt.indexOf("mac os x")!= -1);

/*
	EMBED FUNCTIONS: 
	replace a target with a emebed type and provided url.
*/
function auto_embed(opt){
	//@todo don't be clickable untill document done loading:
	
	if(!opt){
		opt = new Array();
	}
	//get the width and height from the thumbnail image frame: 
	img = document.getElementById("img_"+opt['target']);
	//set default options if not provided:
	if(!opt['width'])opt['width']=img.getAttribute("width");
	if(!opt['height'])opt['height']=img.getAttribute("height");
	if(!opt['duration'])opt['duration']=30; //default durration of 30 seconds (required for seeking in cortado player)
	
	//detect available plug-in  
	var embed_type = detect_client_plugins();
	//if on a mac force java for now
	if(is_mac){
		embed_type = 'jre';
	}
	
	//draw given plugin type: 
	//document.getElementById(target).innerHTML='play with:'+embed_type +' url: ' + url;

	if(!document.getElementById("div_" + opt['target'])){
		alert('error can\'t find target: ' + opt['target']);
	}else{
		if(embed_type){	
			switch(embed_type){
				case 'jre':
					jre_embed(opt);
				break;
				case 'vlc':
					vlc_embed(opt);
				break;
				case 'anx':
					anx_embed(opt);
				break;
			}
			//eval(embed_type + "_embed(opt)");
		}else{
			//make it large enough for text
			//document.getElementById("div_" + opt['target']).style.height='320px';
			//document.getElementById("div_" + opt['target']).style.width='240px';
			document.getElementById("div_" + opt['target']).innerHTML='<span style="text-align:left">no ogg theora decoders found for your platform<br>'+
				'please visit <a href="#">embed video help page</a> for more details' +
				'<BR> you can download this file on the info page:</span>';
		}
	}
}


//annodex is virtualy the same as vlc with a slightly diffrent embed_type:
function anx_embed(opt){
	//alert('anx_embed');
	opt['embed_type']='application/x-annodex-vlc-viewer-plugin';
	return vlc_embed(opt);
}
//vlc embed: 
function vlc_embed(opt){	
	//first insert the embed element
	if(!opt['embed_type']){
		opt['embed_type']='application/x-vlc-plugin';
	}	
	var vid_id = opt['target'] + "_ebVid";

	//<embed type="<?=$embedType?>" id="video1" autoplay="no" loop="no" height="<?=$height?>" width="<?=$width?>"> 
	var eb = document.createElement("embed");
	eb.type=opt['embed_type'];
	//eb.id="video_" + target;
	eb.id="video_" + opt['target'];
	eb.height=opt['height'];
	eb.width=opt['width'];
	//alert('embed: height: ' + eb.height + ' widht: ' + opt['width']);
	//replace the image with the embed: 
	document.getElementById("div_"+opt['target']).innerHTML="";		
	document.getElementById("div_"+opt['target']).appendChild(eb);
	
	
	//hide the auto_embed play button: 
	document.getElementById("play_"+opt['target']).style.display='none';
	//div_parent.appendChild(div_cnt);
	
	//expand the magnified section to give space for the controls: 
	document.getElementById("magnify_"+opt['target']).style.width='172px';
	//show the controls:	
	document.getElementById("cnt_" + opt['target']).style.display='inline';
	
	//set the media url to the m3u file:
	opt['media_url']=wiki_web_path + 'embed/m3u.php?media_url='+opt['media_url'];
	//alert('url:'+opt['media_url']);
	setTimeout('run_vlc(\''+opt['target']+'\',\''+opt['media_url']+'\')', 200);
}
	
function run_vlc(target, media_url){
	//url = media_url;
	
	//alert('target: ' +  target +" media:"+ media_url);
	eval("document.video_" +target+".stop();");
	eval("document.video_" +target+".clear_playlist();");
	eval("document.video_" +target+".add_item(media_url);");
	eval("document.video_" +target+".play();");
	
	/*document.video_Launch_of_Skylab_ogg.stop();
	document.video_Launch_of_Skylab_ogg.clear_playlist();
	document.video_Launch_of_Skylab_ogg.add_item(media_url);
	document.video_Launch_of_Skylab_ogg.play();*/
}
function jre_embed(opt){
	//need to build an iframe include only really deal with java security issues
	//@todo make sure the embed code is coming from the same server as the media
	var iframe = document.createElement("iframe");
	iframe.width=opt['width'];
	//add 4 pixles for the iframe controls) 

	iframe.height= (parseInt(opt['height'])+4);
	iframe.frameborder=0;
	iframe.scrolling='no';
	iframe.MARGINWIDTH=0;
	iframe.MARGINHEIGHT=0;

	if(!opt['stream_type'])opt['stream_type']='video';

	//@todo load in the path and server url from mediaWiki or from the media URL:

	//for now use jcraft for audio: 
	if(opt['stream_type']=='audio'){
		var iframe_src = wiki_web_path + 'embed/jorbis_embed.php';
	}else{	
		var iframe_src = wiki_web_path + 'embed/cortado_embed.php';
	}	
	iframe_src+= "?media_url=" + opt['media_url'];
	iframe_src+= "&stream_type=" + opt['stream_type'];
	iframe_src+= "&width=" + opt['width'] + "&height=" + opt['height'];
	iframe_src+= "&duration=" + opt['duration'];
	
	//document.write(cortado_src);
	iframe.src=iframe_src;
	
	document.getElementById("div_" + opt['target']).innerHTML="";
	document.getElementById("div_" + opt['target']).appendChild(iframe);
	
	//hide the auto_embed play button: (java applet has video controls included)
	document.getElementById("play_"+opt['target']).style.display='none';
}

function detect_client_plugins(){
	//dynamicly load the client detection script: 	
	if(is_nav6up){
		//vlcPlugin = navigator.plugins["VLC multimedia plugin"];
		if( navigator.plugins["VLC multimedia plugin"] ){					
			//a("vlc found, setting preference<blink>....</blink>");
			//is_plug=true;
			//setTimeout("grabAsyncRefresh('setEmbedType=vlc')",2000);	\
			return 'vlc';
		}else{
			if( navigator.plugins["VLC Annodex viewer plugin"] ){
				//is_plug=true;
				//document.writeln("Annodex found, setting preference<blink>....</blink>");
				//setTimeout("grabAsyncRefresh('setEmbedType=anx')",2000);
				return 'anx';
			}else{																
				//try for a java-applet detection:
				return detect_applet();
			}
		}
	}else{
		//detect java-runtime: 
		return detect_applet();
	}	
}

function detect_applet(){	
	var pluginDetected = false;
	var activeXDisabled = false;
	
	//the general method to detect Enabled java: (works well if we don't need version info)
	if(navigator.javaEnabled() == 1){
		return 'jre';	
	}else{
		//navigator.javaEnabled should alwayse work for nav6up so return null
		if(is_nav6up){
			return null;	
		}
	}
	
	// we can check for plugin existence only when browser is 'is_ie5up' or 'is_nav4up'
	if(is_nav4up) {
		// Refresh 'navigator.plugins' to get newly installed plugins.
		// Use 'navigator.plugins.refresh(false)' to refresh plugins
		// without refreshing open documents (browser windows)
		if(navigator.plugins) {
			navigator.plugins.refresh(false);
		}
		
		// check for Java plugin in installed plugins
		if(navigator.mimeTypes) {
			for (i=0; i < navigator.mimeTypes.length; i++) {
				if( (navigator.mimeTypes[ i].type != null)
						&& (navigator.mimeTypes[ i].type.indexOf(
						"application/x-java-applet;jpi-version=1.3") != -1) ) {
					pluginDetected = true;
					break;
				}
			}
		}
	} else if (is_ie5up) {
		var javaVersion;
		var shell;
		try {
			// Create WSH(WindowsScriptHost) shell, available on Windows only
			shell = new ActiveXObject("WScript.Shell");			
			if (shell != null) {
				// Read JRE version from Window Registry
				try {
					javaVersion = shell.regRead("HKEY_LOCAL_MACHINE\\Software\\JavaSoft\\Java Runtime Environment\\CurrentVersion");
					//alert('java jre found version: ' + javaVersion);
					return 'jre';
				} catch(e) {
				
					// handle exceptions raised by 'shell.regRead(...)' here
					// so that the outer try-catch block would receive only
					// exceptions raised by 'shell = new ActiveXObject(...)'
				}
			}
		} catch(e) {
			//alert('IE sequirty too high for activeX detect: loading applet');
			try{
				//one more test:
				var js_test = document.createElement("span");
				js_test.innerHTML='<OBJECT id="myApplet" classid="clsid:8AD9C840-044E-11D1-B3E9-00805F499D93" WIDTH = 1 HEIGHT = 1 >'+
				'<PARAM NAME = CODE VALUE = "DetectPluginApplet.class" >'+
				'<PARAM NAME="scriptable" VALUE="true" >'+
				'<embed type="application/x-java-applet;version=1.3"'+
				'code = "/wiki_dev/phase3/embed/DetectPluginApplet" width = 2 height = 2 MAYSCRIPT = "true" >'+
				'</embed>'+
				'</EMBED>'+
				'</object>';
						
				document.getElementsByTagName("body")[0].appendChild(js_test);
				//check for applet:
				var applet = document.myApplet;
				if(applet == null){
					//alert('no java plugin installed');	
				}else{
					var javaVersion = applet.getJavaVersion();	
					//alert('found java jre version: ' + javaVersion);
					return 'jre';
				}
			} catch(e) {
				alert('error: ' + e);	
			}						
			activeXDisabled = true;
		}		
		// Check whether we got required (1.3+) Java Plugin
		if ( (javaVersion != null) && (javaVersion.indexOf("1.3") != -1) ) {
			pluginDetected = true;
		}
	}		
	if (pluginDetected) {
		return 'jre';
	} else {
		return null;
	}
}