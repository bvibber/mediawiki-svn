// JavaScript Document

/*
	detects if the user has the vlc or annodex browser plugin.
	//if they dont' have annodex & on a win/linux look for java/plugin
		//look for windows media ogg filter?
	//if they don't have annodex & on OSX look quicktime, and ogg extention
		//if on mac and have
*/

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

/*
	EMBED FUNCTIONS: 
	replace a target with a emebed type and provided url.
*/
function auto_embed(target, url, opt){
	//@todo don't be clickable untill document done loading:
	
	if(!opt){
		//set default options if not provided:
		opt = new Array();
		opt['width']='320';
		opt['height']='240';
		opt['autoplay']=false;
	}
	//detect plugin avalibilty
	var embed_type = detect_client_plugins();
	//draw given plugin type: 
	//document.getElementById(target).innerHTML='play with:'+embed_type +' url: ' + url;
	if(!document.getElementById(target)){
		alert('error can\'t find target: ' + target);
	}else{
		if(embed_type){
			eval(embed_type + "_embed(target, url, opt)");
		}else{
			document.getElementById(target).innerHTML='no valid ogg theora decoders found for your platform<br>'+
				'please visit <a href="#">embed video help page</a> for more details';
		}
	}
}


//annodex is treated virtualy the same as vlc: 
function anx_embed(target, media_url, opt){
	alert('anx_embed');
	opt['embed_type']='application/x-annodex-vlc-viewer-plugin';
	return vlc_embed(target, media_url, opt);
}
//vlc embed: 
function vlc_embed(target, media_url, opt){	
	//first insert the embed element
	if(!opt['embed_type']){
		opt['embed_type']='application/x-vlc-plugin';
	}	
	var vid_id = target+"_ebVid";
	
	//<embed type="<?=$embedType?>" id="video1" autoplay="no" loop="no" height="<?=$height?>" width="<?=$width?>"> 
	var eb = document.createElement("embed");
	eb.type=opt['embed_type'];
	eb.id="video1";
	if(opt['height']==false){
		eb.autoplay="no";
	}else{
		eb.autoplay="yes";
	}
	eb.loop="no";
	eb.height=opt['height'];
	eb.width=opt['width'];
	
	document.getElementById(target).innerHTML="";
	document.getElementById(target).appendChild(eb);
	
	//need to give the attached element time to load
	//theoreticaly 
	//alert("media_url: " + media_url);
	setTimeout('run_vlc(\''+media_url+'\')', 300);
}
function run_vlc(media_url){
	url = 'http://metavid.ucsc.edu' +media_url;
	//alert('run vlc');
	document.video1.stop();
	document.video1.clear_playlist();
	document.video1.add_item( url );
	document.video1.play();
}
function jre_embed(target, media_url){
	alert('jre_embed');
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
					alert('java jre found version: ' + javaVersion);
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
					alert('no java plugin installed');	
				}else{
					var javaVersion = applet.getJavaVersion();	
					alert('java jre found version: ' + javaVersion);
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
		return 'java';
	} else {
		return null;
	}
}
/* function to dymanicly load javascript files */
function dhtmlLoadScript(url)
{
   var e = document.createElement("script");
   e.src = url;
   e.type="text/javascript";
   document.getElementsByTagName("head")[0].appendChild(e);
}

onload = function()
{
   dhtmlLoadScript("dhtml_way.js");
}