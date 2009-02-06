//add media wizard integration for mediaWiki

/* config */
//Setup your content providers (see the remoteSearchDriver::content_providers for all options)
var wg_content_proivers_config = {}; //you can overwrite by defining (after)

var wg_local_wiki_api_url = wgServer + wgScriptPath + '/api.php';

//if mv_embed is hosted somewhere other than near by the add_media_wizard you can define it here: 
var mv_add_media_wizard_path = 'http://mvbox2.cse.ucsc.edu/w/extensions/MetavidWiki/skins/';


//*code should not have to modify anything below*/
//check if we are on a edit page:
if( wgAction=='edit' ){
	//add onPage ready request:
	addOnloadHook( function(){						
		var imE = document.createElement('img');
		imE.style.cursor = 'pointer';	
		imE.id = 'mv-add_media';		
		imE.src = getAddMediaPath( 'mv_embed/images/Button_add_media.png' );
		
		var toolbar = document.getElementById("toolbar");
		if(toolbar)
			toolbar.appendChild(imE);	 
		
		addHandler( imE, 'click', function() {
			mv_do_load_wiz();
		});
	});
}
//add firefog support to Special Upload page:
if(wgPageName== "Special:Upload"){	
	addOnloadHook( function(){		
		//alert("!!upload hook");
		load_mv_embed( function(){			
			//load jQuery and what not (we need to refactor the loading system for mv_embed)
			mvEmbed.load_libs(function(){
				mvJsLoader.doLoad({'mvUploader' : 'libAddMedia/mv_upload.js'},function(){					
					mvUp = new mvUploader();		
				});
			});
		});
	});
}

var caret_pos={};
function mv_do_load_wiz(){
	caret_pos={};	
	var txtarea = document.editform.wpTextbox1;
	var getTextCusorStartPos = function (o){		
		if (o.createTextRange) {
				var r = document.selection.createRange().duplicate()
				r.moveEnd('character', o.value.length)
				if (r.text == '') return o.value.length
				return o.value.lastIndexOf(r.text)
			} else return o.selectionStart
	}
	var getTextCusorEndPos = function (o){
		if (o.createTextRange) {
			var r = document.selection.createRange().duplicate();
			r.moveStart('character', -o.value.length);
			return r.text.length;
		} else{ 
			return o.selectionEnd
		}
	}
	caret_pos.s = getTextCusorStartPos( txtarea );
	caret_pos.e = getTextCusorEndPos( txtarea );		
	caret_pos.text = txtarea.value;	
	//show the loading screen:
	var elm = document.getElementById('modalbox')
	if(elm){
		//use jquery to re-display the search
		if( typeof $j != 'undefined'){
			$j('#modalbox,#mv_overlay').show();
		}
	}else{
		var body_elm = document.getElementsByTagName("body")[0];
		body_elm.innerHTML = body_elm.innerHTML + ''+		
			'<div id="modalbox" style="background:#DDD;border:3px solid #666666;font-size:115%;'+
				'top:30px;left:20px;right:20px;bottom:30px;position:fixed;z-index:100;">'+			
				'loading external media wizard<blink>...</blink>'+			
			'</div>'+		
			'<div id="mv_overlay" style="background:#000;cursor:wait;height:100%;left:0;position:fixed;'+
				'top:0;width:100%;z-index:5;filter:alpha(opacity=60);-moz-opacity: 0.6;'+
				'opacity: 0.6;"/>';
	}
	//make sure the click action is still there
	imE = document.getElementById('mv-add_media');	
	if(imE){
		addHandler( imE, 'click', function() {
				mv_do_load_wiz();
		});
	}
	//load mv_embed and do text search interface: 
	load_mv_embed( function(){
		//restore text value: 
		var txtarea = document.editform.wpTextbox1;		
		txtarea.value = caret_pos.text;
		//do the remote search interface:		
		mv_do_remote_search({
			'target_id':'modalbox',
			'profile':'mediawiki_edit',
			'target_textbox': 'wpTextbox1', 
			'caret_pos': caret_pos,			
			//note selections in the textbox will take over the default query
			'default_query': wgTitle,
			'target_title':wgPageName,
			'cpconfig':wg_content_proivers_config,
			'local_wiki_api_url': wg_local_wiki_api_url
		});
	});	
	return false;
}
function load_mv_embed( callback ){					
	//inject mv_embed if needed:
	if( typeof mvEmbed == 'undefined'){		
		//get mv_embed path from _this_ file location: 	
		var mv_embed_url = getAddMediaPath( 'mv_embed/mv_embed.js' );
		var e = document.createElement("script");
	    e.setAttribute('src', mv_embed_url);	    
	    e.setAttribute('type',"text/javascript");
	    document.getElementsByTagName("head")[0].appendChild(e);
	    check_for_mv_embed( callback ); 
	}else{		
		check_for_mv_embed( callback );
	}      	
}

function check_for_mv_embed( callback ){
	if( typeof mvEmbed == 'undefined'){		 
		setTimeout('check_for_mv_embed( ' + callback +');', 25);
	}else{
		js_log('callback is now: ' + callback);
		callback();
	}
}
function getAddMediaPath( replace_str ){
	if(!replace_str)
		replace_str = '';
	for(var i=0; i < document.getElementsByTagName('script').length; i++){
		var s = document.getElementsByTagName('script')[i];
		if( s.src.indexOf('add_media_wizard.js') != -1 ){
			//use the external_media_wizard path: 
			return s.src.replace('add_media_wizard.js', replace_str);
		}
	}
	js_log('return default path: ' + mv_add_media_wizard_path + replace_str);
	return mv_add_media_wizard_path + replace_str;
}


