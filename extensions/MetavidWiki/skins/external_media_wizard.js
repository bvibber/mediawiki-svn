//add media wizard integration for mediaWiki

/* config */
var mv_embed_url = 'http://localhost/wiki/extensions/MetavidWiki/skins/mv_embed/mv_embed.js';
//Setup your content providers (see the remoteSearchDriver::content_providers for all options)
var wg_content_proivers_config = {
	'wiki_commons':{
		'local':false //note you will need to enable url file uploading to import assets	
	},
	'metavid':{
		'local':true //this will change the output from [[embed:StreamName]] to [[remoteEmbed:roe_url]]
	}	
}
var wg_local_wiki_api_url = 'http://localhost/wiki/api.php';


//*code should not have to modify anything below*/
//check if we are on a edit page:
if(wgAction=='edit'){
	//add onPage ready request:
	addOnloadHook( function(){			
		var toolbar = document.getElementById("toolbar");	
		toolbar.innerHTML =  toolbar.innerHTML + ''+
			'<img src="http://upload.wikimedia.org/wikipedia/commons/8/86/Button_add_media.png" '+
				'style="cursor:pointer;" ' +
				'onClick="mv_do_load_wiz()" />';
	});
}
var caret_pos={};
function mv_do_load_wiz(){
	caret_pos={};
	tb = document.getElementById('wpTextbox1');
	caret_pos.s = getTextCusorStartPos( tb );
	caret_pos.e = getTextCusorEndPos( tb );		
	
	//show the loading screen:
	var body_elm = document.getElementsByTagName("body")[0];
	body_elm.innerHTML = body_elm.innerHTML + ''+		
		'<div id="modalbox" style="background:#DDD;border:3px solid #666666;'+
			'top:30px;left:20px;right:20px;bottom:30px;position:fixed;z-index:100;">'+
			
			'loading external media wizard<blink>...</blink>'+
			
		'</div>'+		
		'<div id="mv_overlay" style="background:#000;cursor:wait;height:100%;left:0;position:fixed;'+
			'top:0;width:100%;z-index:5;filter:alpha(opacity=60);-moz-opacity: 0.6;'+
			'opacity: 0.6;"/>';
							
	//inject mv_embed
	if( typeof MV_EMBED_VERSION == 'undefined'){
		var e = document.createElement("script");
	    e.setAttribute('src', mv_embed_url);
	    e.setAttribute('type',"text/javascript");
	    document.getElementsByTagName("head")[0].appendChild(e);
	    setTimeout('check_for_mv_embed();', 25); 
	}else{
		check_for_mv_embed();
	}      	
	return false;
}
function check_for_mv_embed(){
	if( typeof MV_EMBED_VERSION == 'undefined'){
		setTimeout('check_for_mv_embed();', 25);
	}else{
		mv_do_remote_search({
			'target_id':'modalbox',
			'profile':'mediawiki_edit',
			'target_textbox': 'wpTextbox1', 
			'caret_pos':caret_pos,
			//note selections in the textbox will take over the default query
			'default_query': wgTitle,
			'cpconfig':wg_content_proivers_config,
			'local_wiki_api_url': wg_local_wiki_api_url
		});
	}
}
/*once we modify the dom we lose the text selection :( so here are some get pos functions  */
function getTextCusorStartPos(o){		
	if (o.createTextRange) {
			var r = document.selection.createRange().duplicate()
			r.moveEnd('character', o.value.length)
			if (r.text == '') return o.value.length
			return o.value.lastIndexOf(r.text)
		} else return o.selectionStart
}
function getTextCusorEndPos(o){
	if (o.createTextRange) {
		var r = document.selection.createRange().duplicate();
		r.moveStart('character', -o.value.length);
		return r.text.length;
	} else{ 
		return o.selectionEnd
	}
}

