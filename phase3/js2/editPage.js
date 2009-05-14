//initial refactoring of the edit page into jquery based edit page

loadGM({
	"add_media_to_page" : "Add Media to this Page"
});

mwAddOnloadHook( function(){
	//add the add media button: 
	$j('#toolbar').append( '<img id="mv-add-media" title="' + gM('add_media_to_page') + '"' +
			'src="' + mv_skin_img_path + 'Button_add_media.png"' +
			'style="cursor:pointer" />' );	
	$j('#mv-add-media').click(mv_do_load_wiz);	
});

function mv_do_load_wiz(){
	//add the add media-wizard button: 
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
	//show/empty modalbox:
	$j('#modalbox,#mv_overlay').remove();
	$j('body').append(''+		
		'<div id="modalbox" style="background:#DDD;border:3px solid #666666;font-size:115%;'+
			'top:30px;left:20px;right:20px;bottom:30px;position:fixed;z-index:100;">'+			
			'loading external media wizard<blink>...</blink>'+			
		'</div>'+		
		'<div id="mv_overlay" style="background:#000;cursor:wait;height:100%;left:0;position:fixed;'+
			'top:0;width:100%;z-index:5;filter:alpha(opacity=60);-moz-opacity: 0.6;'+
			'opacity: 0.6;"/>');
						
	//load mv_embed and do text search interface: 	
	
	//setup the restore text value: 
	var txtarea = document.editform.wpTextbox1;		
	txtarea.value = caret_pos.text;
	//do the remote search interface:		
	mv_do_remote_search({
		'target_id'			:'modalbox',
		'profile'			:'mediawiki_edit',
		'target_textbox'	: 'wpTextbox1', 
		'caret_pos'			: caret_pos,			
		//note selections in the textbox will override the default query
		'default_query'		: wgTitle,
		'target_title'		: wgPageName,
		'cpconfig'			: {},
		'local_wiki_api_url': wgServer + wgScriptPath + '/api.php'
	});
}	