//javascript for all pages (adds auto_complete for search, and our linkback logo, and re-writes mvd links)

mv_addLoadEvent(mv_setup_allpage); 	
var mv_setup_allpage_flag=false;
function mv_setup_allpage(){	
	js_log("mv embed done loading now setup all page");
	//make sure we have jQuery and any base requried libs: 
	mvJsLoader.doLoad(mvEmbed.lib_jquery, function(){			
 		_global['$j'] = jQuery.noConflict();	
 		js_log('allpage_ did jquery check');
 		var reqLibs = {'$j.fn.autocomplete':'jquery/plugins/jquery.autocomplete.js',
 					   '$j.fn.hoverIntent':'jquery/plugins/jquery.hoverIntent.js'};
 		mvJsLoader.doLoad(
 			reqLibs, function(){
	 				//js_log('allpage_ auto and hover check'+mv_setup_allpage_flag);
					if(!mv_setup_allpage_flag){//have no idea why this gets called twice					   		
						mv_setup_search_ac();
						mv_do_mvd_link_rewrite();						
						mv_setup_allpage_flag=true; 
					}
				});
	});	
}
function mv_do_mvd_link_rewrite(){
	js_log('mv_do_mvd_link_rewrite');
	var patt_mvd = new RegExp("MVD:([^:]*):([^\/]*)\/([0-9]+:[0-9]+:[^\/]+)\/?([0-9]+:[0-9]+:[^\/]+)?");
	var i =0;
	$j('a').each(function(){
		if(this.href.indexOf('Special:')==-1 && this.href.indexOf('action=')==-1){
			titleTest = this.title.match(patt_mvd);
			if(titleTest){
				res = this.href.match(patt_mvd);		
				if(res){			
					i++;
					//js_log(this.href);			
					//js_log(res);
					//replace with: 
					//TEMP:
					var img_url = wgScript+'?action=ajax&rs=mv_frame_server&stream_name='+res[2]+'&t='+res[3]+'&size=icon';
					var stream_link = wgScript+'?title=Stream:'+res[2]+'/'+res[3]+'/'+res[4];
					var stream_desc = res[2].substr(0,1).toUpperCase() + res[2].substr(1).replace('_', ' ')+' '+ res[3] + ' to '+ res[4];
					var expand_link = '<span id="mv_mvd_ex_'+i+'" style="cursor:pointer;width:16px;height:16px;float:left;background:url(\''+wgScriptPath+'/extensions/MetavidWiki/skins/images/closed.png\');"/>';
					$j(this).replaceWith('<div id="mvd_link_'+i+'" ' +
							'style="vertical-align: bottom;margin:.5em;border:solid thin black;width:300px;height:60px;">' +
								'	<img id="mvd_link_im_'+i+'" onclick="mv_ext('+i+',\''+res[2]+'/'+res[3]+'/'+res[4]+'\')" ' +
										'style="cursor:pointer;float:left;height:60px;width:80px;" src="'+img_url+'">' +expand_link+
										'<a title="'+stream_desc+'" href="'+stream_link+'">'+stream_desc+'</a>'+
								'<br>'+																			
							'</div>');							
					$j('#mv_mvd_ex_'+i).click(function(){
						inx = this.id.substr(10);
						mv_ext(inx);
					});
				}	
			}	
		}		
	});
	js_log('got to I: '+i);
	$j('#mvd_link_'+i).after('<div style="clear:both"></div>')
}
function mv_ext(inx){
	//grow the window to 300+240 540	
	js_log('i: is '+ inx);
	$j('#mvd_link_'+inx).animate({width:'540px','height':'240px'},1000);
	$j('#mvd_link_im_'+inx).animate({width:'320px','height':'240px'},1000,function(){
		//do mv_embed swap
	});
	$j('#mv_mvd_ex_'+inx).css('background', 'url(\''+wgScriptPath+'/extensions/MetavidWiki/skins/images/opened.png\')');
	$j('#mv_mvd_ex_'+inx).unbind();
	$j('#mv_mvd_ex_'+inx).click(function(){
		inx = this.id.substr(10);
		mv_cxt(inx);
	});
	js_log('did mv ex');
}
function mv_cxt(inx){
	$j('#mvd_link_'+inx).animate({width:'300px','height':'60px'},1000);
	$j('#mvd_link_im_'+inx).animate({width:'80px','height':'60px'},1000);
	$j('#mv_mvd_ex_'+inx).css('background', 'url(\''+wgScriptPath+'/extensions/MetavidWiki/skins/images/closed.png\')');
	$j('#mv_mvd_ex_'+inx).unbind();
	$j('#mv_mvd_ex_'+inx).click(function(){
		inx = this.id.substr(10);
		mv_ext(inx);
	});
}
function mv_setup_search_ac(){
	var uri = wgScript;
	//add the person choices div to searchInput
	var obj = $j('#searchInput').get(0);
	//base offset: 
	var curleft=55;
	var curtop=20;
	//get pos of searchInput:
	if (obj.offsetParent) {
		do {
			curleft += obj.offsetLeft;
			curtop += obj.offsetTop;
		}while (obj = obj.offsetParent);		
	}
	//get the search pos: 
	$j('body').append('<div class="ac_results" id="mv_ac_choices" ' +
			'style="border:solid black;background:#FFF;position:absolute;left:'+curleft+'px;top:'+curtop+'px;z-index:99;width:300px;display: none;"/>');
	//turn off browser baseed autocomplete: 
	$j('#searchInput').attr('autocomplete',"off");
	//add hook:
	$j('#searchInput').autocomplete(
		uri,
		{
			autoFill:false,
			onItemSelect:function(v){		
				//alert('selected:' + v.innerHTML + ' page:'+$j('#searchInput').val());	
				//jump to page: 			
				if($j('#searchInput').val()=='do_search'){
					qs = v.innerHTML.toLowerCase().indexOf('<b>')+3;
					qe = v.innerHTML.toLowerCase().indexOf('</b>');
					//update the search input (incase redirect fails)
					$j('#searchInput').val(v.innerHTML.substring(qs,qe));
					window.location=uri+'/'+'Special:Search?search='+v.innerHTML.substring(qs,qe);
				}else{
					window.location =uri+'/'+$j('#searchInput').val();
				}
			},
			formatItem:function(row){
				if(row[0]=='do_search'){
					return row[1].replace('$1',$j('#searchInput').val());
				}else if(row[2]=='no_image'){
					return row[1];
				}else{
					return '<img width="44" src="'+ row[2] + '">'+row[1];
				}
			},
			matchSubset:0,
			extraParams:{action:'ajax',rs:'mv_auto_complete_all'},
			paramName:'rsargs[]',
			resultElem:'#mv_ac_choices'
		});
	//var offset = $j('#mv_person_input_'+inx).offset();
	//$j('#mv_person_choices_'+inx).css('left', offset.left-205);
}