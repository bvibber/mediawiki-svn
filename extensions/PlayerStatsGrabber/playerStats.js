//wgServerOveride: leave empty to use the current server
// (else provide the absolute url to index.php of the wiki you are recording stats to)
var wgServerOveride = "";
var global_req_cb = new Array();//the global request callback array

/*parseUri class:*/
var parseUri=function(d){var o=parseUri.options,value=o.parser[o.strictMode?"strict":"loose"].exec(d);for(var i=0,uri={};i<14;i++){uri[o.key[i]]=value[i]||""}uri[o.q.name]={};uri[o.key[12]].replace(o.q.parser,function(a,b,c){if(b)uri[o.q.name][b]=c});return uri};parseUri.options={strictMode:false,key:["source","protocol","authority","userInfo","user","password","host","port","relative","path","directory","file","query","anchor"],q:{name:"queryKey",parser:/(?:^|&)([^&=]*)=?([^&]*)/g},parser:{strict:/^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,loose:/^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/}};

//extened version of OggHandler
wgExtendedOggPlayerStats = {
	init:function(player, params){
		this.parent_init( player, params );
		this.doStats();
	},
	doStats:function() {
		//make sure we ran detect:
		if (!this.detectionDone) {
			this.detect();
		}

		//build our request url:
		if( wgServerOveride!="" ){
			url= wgServerOveride;
		}else{
			url = wgServer +((wgScript == null) ? (wgScriptPath + "/index.php") : wgScript);
		}
		url += "?action=ajax&rs=mw_push_player_stats";

		//detect windows media player ( direct show filters could be installed)
		if ( navigator.mimeTypes && navigator.mimeTypes["video/x-ms-wm"]     &&
			navigator.mimeTypes["video/x-ms-wm"].enabledPlugin){
			this.clientSupports['ms_video'];
		}
		//@@todo research if we can detect if MS video support a given codec

		//detect flash support
		if( FlashDetect.installed )
			this.clientSupports['flash']=true;

		var j=0;
		for(var i in this.clientSupports){
			url+='&cs[]='+encodeURIComponent(i);
			j++;
		}

		//get the flash version:
		url+='&fv='+ encodeURIComponent( FlashDetect.raw );


		//detect java version if possible: (ie not IE with default security)
		if( javaDetect.version ){
			url+= '&jv='+ encodeURIComponent ( javaDetect.version );
		}

		//add some additional params seperated out to enum keys:
		url+= '&b_user_agent=' +encodeURIComponent( navigator.userAgent );
		url+= '&b_name=' + encodeURIComponent( BrowserDetect.browser ) ;
		url+= '&b_version=' + encodeURIComponent( BrowserDetect.version );
		url+= '&b_os=' + encodeURIComponent( BrowserDetect.OS ) ;

		//and finaly add the user hash:
		url+='&uh=' + encodeURIComponent ( wgOggPlayer.userHash );

		//now send out our stats update (run via javascript include to support remote servers:
		do_request ( url, function( responseObj ){
			wg_ran_stats( responseObj );
		});
	}
}
//extend the OggHandler object for stats collection
for(i in wgOggPlayer){
	if(typeof wgExtendedOggPlayerStats[i]!='undefined'){
		wgOggPlayer['parent_'+i]= wgOggPlayer[i];
		wgOggPlayer[i]=wgExtendedOggPlayerStats[i];
	}
}
function wg_ran_stats(responseObj){
	js_log('did stats with id:' + responseObj['id']);
}
/*
 * a few utily functions
 * to enable cross site requests via json:
 */
function loadExternalJs(url){
   	js_log('load js: '+ url);
    var e = document.createElement("script");
    e.setAttribute('src', url);
    e.setAttribute('type',"text/javascript");
    document.getElementsByTagName("head")[0].appendChild(e);
}
function do_request(req_url, callback, mv_json_response){
 	js_log('do request: ' + req_url);
 	global_req_cb.push(callback);
	loadExternalJs(req_url+'&cb=mv_jsdata_cb&cb_inx='+(global_req_cb.length-1));
}
function mv_jsdata_cb(response){
	js_log('f:mv_jsdata_cb:'+ response['cb_inx']);
	//run the callback from the global req cb object:
	if(!global_req_cb[response['cb_inx']]){
		js_log('missing req cb index');
		return false;
	}
	global_req_cb[response['cb_inx']](response);
}
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

//checks for java support records java version if possible
var javaDetect = {
	javaEnabled: false,
	version: false,
  	init:function(){
	  if (typeof navigator != 'undefined' && typeof navigator.javaEnabled != 'undefined'){
	    this.javaEnabled = navigator.javaEnabled();
	  }else{
	    this.javaEnabled = 'unknown';
	  }
	  if (navigator.javaEnabled() && typeof java != 'undefined')
	    this.version = java.lang.System.getProperty("java.version");
	  //try to get the IE version of java (not likely to work with default security setting)
	  if( wgOggPlayer.msie ){
	    var shell;
		try {
			// Create WSH(WindowsScriptHost) shell, available on Windows only
			shell = new ActiveXObject("WScript.Shell");

			if (shell != null) {
			// Read JRE version from Window Registry
			try {
				this.version = shell.regRead("HKEY_LOCAL_MACHINE\\Software\\JavaSoft\\Java Runtime Environment\\CurrentVersion");
			} catch(e) {
				// handle exceptions raised by 'shell.regRead(...)' here
				// so that the outer try-catch block would receive only
				// exceptions raised by 'shell = new ActiveXObject(...)'
				}
			}
		} catch(e) {
			//could not get it
		}
	  }
  }
}
javaDetect.init();

//http://www.featureblend.com/license.txt
var FlashDetect=new function(){var self=this;self.installed=false;self.raw="";self.major=-1;self.minor=-1;self.revision=-1;self.revisionStr="";var activeXDetectRules=[{"name":"ShockwaveFlash.ShockwaveFlash.7","version":function(obj){return getActiveXVersion(obj);}},{"name":"ShockwaveFlash.ShockwaveFlash.6","version":function(obj){var version="6,0,21";try{obj.AllowScriptAccess="always";version=getActiveXVersion(obj);}catch(err){}
return version;}},{"name":"ShockwaveFlash.ShockwaveFlash","version":function(obj){return getActiveXVersion(obj);}}];var getActiveXVersion=function(activeXObj){var version=-1;try{version=activeXObj.GetVariable("$version");}catch(err){}
return version;};var getActiveXObject=function(name){var obj=-1;try{obj=new ActiveXObject(name);}catch(err){}
return obj;};var parseActiveXVersion=function(str){var versionArray=str.split(",");return{"raw":str,"major":parseInt(versionArray[0].split(" ")[1],10),"minor":parseInt(versionArray[1],10),"revision":parseInt(versionArray[2],10),"revisionStr":versionArray[2]};};var parseStandardVersion=function(str){var descParts=str.split(/ +/);var majorMinor=descParts[2].split(/\./);var revisionStr=descParts[3];return{"raw":str,"major":parseInt(majorMinor[0],10),"minor":parseInt(majorMinor[1],10),"revisionStr":revisionStr,"revision":parseRevisionStrToInt(revisionStr)};};var parseRevisionStrToInt=function(str){return parseInt(str.replace(/[a-zA-Z]/g,""),10)||self.revision;};self.majorAtLeast=function(version){return self.major>=version;};self.FlashDetect=function(){if(navigator.plugins&&navigator.plugins.length>0){var type='application/x-shockwave-flash';var mimeTypes=navigator.mimeTypes;if(mimeTypes&&mimeTypes[type]&&mimeTypes[type].enabledPlugin&&mimeTypes[type].enabledPlugin.description){var version=mimeTypes[type].enabledPlugin.description;var versionObj=parseStandardVersion(version);self.raw=versionObj.raw;self.major=versionObj.major;self.minor=versionObj.minor;self.revisionStr=versionObj.revisionStr;self.revision=versionObj.revision;self.installed=true;}}else if(navigator.appVersion.indexOf("Mac")==-1&&window.execScript){var version=-1;for(var i=0;i<activeXDetectRules.length&&version==-1;i++){var obj=getActiveXObject(activeXDetectRules[i].name);if(typeof obj=="object"){self.installed=true;version=activeXDetectRules[i].version(obj);if(version!=-1){var versionObj=parseActiveXVersion(version);self.raw=versionObj.raw;self.major=versionObj.major;self.minor=versionObj.minor;self.revision=versionObj.revision;self.revisionStr=versionObj.revisionStr;}}}}}();};FlashDetect.release="1.0.3";

//http://www.quirksmode.org/js/detect.html
var BrowserDetect = {
	init: function () {
		this.browser = this.searchString(this.dataBrowser) || "An unknown browser";
		this.version = this.searchVersion(navigator.userAgent)
			|| this.searchVersion(navigator.appVersion)
			|| "an unknown version";
		this.OS = this.searchString(this.dataOS) || "an unknown OS";
	},
	searchString: function (data) {
		for (var i=0;i<data.length;i++)	{
			var dataString = data[i].string;
			var dataProp = data[i].prop;
			this.versionSearchString = data[i].versionSearch || data[i].identity;
			if (dataString) {
				if (dataString.indexOf(data[i].subString) != -1)
					return data[i].identity;
			}
			else if (dataProp)
				return data[i].identity;
		}
	},
	searchVersion: function (dataString) {
		var index = dataString.indexOf(this.versionSearchString);
		if (index == -1) return;
		return parseFloat(dataString.substring(index+this.versionSearchString.length+1));
	},
	dataBrowser: [
		{
			string: navigator.userAgent,
			subString: "Chrome",
			identity: "Chrome"
		},
		{ 	string: navigator.userAgent,
			subString: "OmniWeb",
			versionSearch: "OmniWeb/",
			identity: "OmniWeb"
		},
		{
			string: navigator.vendor,
			subString: "Apple",
			identity: "Safari"
		},
		{
			prop: window.opera,
			identity: "Opera"
		},
		{
			string: navigator.vendor,
			subString: "iCab",
			identity: "iCab"
		},
		{
			string: navigator.vendor,
			subString: "KDE",
			identity: "Konqueror"
		},
		{
			string: navigator.userAgent,
			subString: "Firefox",
			identity: "Firefox"
		},
		{
			string: navigator.vendor,
			subString: "Camino",
			identity: "Camino"
		},
		{		// for newer Netscapes (6+)
			string: navigator.userAgent,
			subString: "Netscape",
			identity: "Netscape"
		},
		{
			string: navigator.userAgent,
			subString: "MSIE",
			identity: "Explorer",
			versionSearch: "MSIE"
		},
		{
			string: navigator.userAgent,
			subString: "Gecko",
			identity: "Mozilla",
			versionSearch: "rv"
		},
		{ 		// for older Netscapes (4-)
			string: navigator.userAgent,
			subString: "Mozilla",
			identity: "Netscape",
			versionSearch: "Mozilla"
		}
	],
	dataOS : [
		{
			string: navigator.platform,
			subString: "Win",
			identity: "Windows"
		},
		{
			string: navigator.platform,
			subString: "Mac",
			identity: "Mac"
		},
		{
			string: navigator.platform,
			subString: "Linux",
			identity: "Linux"
		}
	]

};
BrowserDetect.init();
