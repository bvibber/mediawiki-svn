//add media wizard integration for mediaWiki

//to support remote media searching.
 
var mv_embed_url = 'http://localhost/wiki/extensions/MetavidWiki/skins/mv_embed/mv_embed.js';

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
function mv_do_load_wiz(){
	//show the loading screen:
	var body_elm = document.getElementsByTagName("body")[0];
	body_elm.innerHTML = body_elm.innerHTML + ''+		
		'<div id="modalbox" style="background:#DDD;border:3px solid #666666;'+
			'height:90%;left:10%;position:fixed;top:5%;width:80%;z-index:100;">'+
			
			'loading external media wizard<blink>...</blink>'+
			
		'</div>'+		
		'<div style="background:#000;cursor:wait;height:100%;left:0;position:fixed;'+
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
}
function check_for_mv_embed(){
	if( typeof MV_EMBED_VERSION == 'undefined'){
		setTimeout('check_for_mv_embed();', 25);
	}else{
		mv_do_remote_search({
			'target_id':'modalbox',
			'profile':'mediawiki_edit'
		});
	}
}
