//javascript for all pages (adds auto_complete for search, and our linkback logo)
if(typeof _global =='undefined'){ 
_global = this;
}
//(needs the jquery lib loaded make sure we have it in $j:
$(document).ready(function() {
	_global['$j'] = jQuery.noConflict();
	mv_setup_search_ac();
});

function mv_setup_search_ac(){
	uri = wgServer +
	((wgServer == null) ? (wgScriptPath + "/index.php") : wgScript);
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
	$j('body').append('<div class="autocomplete" id="mv_ac_choices" ' +
			'style="border:solid black;background:#FFF;position:absolute;left:'+curleft+'px;top:'+curtop+'px;z-index:99;width:300px;display: none;"/>');
	
	$j('#searchInput').autocomplete(
		uri,
		{
			autoFill:false,
			onItemSelect:function(v){		
				console.log('selected:' + v.innerHTML + ' page:'+$j('#searchInput').val());	
				//jump to page: 			
				if($j('#searchInput').val()=='do_search'){
					qs = v.innerHTML.indexOf('<b>')+3;
					qe = v.innerHTML.indexOf('</b>');
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