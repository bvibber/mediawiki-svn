/*
* a library for doing remote media searches 
*  
* initial targeted archives are:
	the local wiki 
	wikimedia commons 
	metavid 
	and archive.org
*/

gMsg['mv_media_search']	= 'Media Search';
gMsg['rsd_box_layout'] 	= 'Box layout';
gMsg['rsd_list_layout'] = 'List Layout';
gMsg['rsd_results_desc']= 'Results <b>$0</b> of <b>$1</b>';
gMsg['rsd_layout'] = 	  'Layout:';

var default_remote_search_options = {
	'profile':'mediawiki_edit',	
	'target_id':null, //the div that will hold the search interface
	
	'default_provider_id':'all', //all or one of the content_providers ids
	
	'instance_name': null, //a globally accessible callback instance name
	'default_query':'', //default search query
	//specific to sequence profile
	'p_seq':null
}
var remoteSearchDriver = function(initObj){
	return this.init( initObj );
}
remoteSearchDriver.prototype = {
	results_cleared:false,
	//here we define the set of possible media content providers:
	main_search_options:{
		'selprovider':{
			'title': 'Select Providers'			
		},
		'advanced_search':{
			'title': 'Advanced Options'
		}		
	},
	content_providers:{				
		/*content_providers documentation: 			
			@enabled: whether the search provider can be selected
			@checked: whether the search provideer will show up as seletable tab (todo: user prefrence) 
			@d: 	  if the current cp should be displayed (only one should be the default) 
			@title:   the title of the search provider
			@desc: 	  can use html... todo: need to localize
			@api_url: the url to query against given the library type: 
			@lib: 	  the search library to use corresponding to the 
						search object ie: 'mediaWiki' = new mediaWikiSearchSearch() 
			@local : if the content provider assets need to be imported or not.  
		*/ 		 
		'this_wiki':{
			'enabled':0,
			'checked':0,
			'd'		:0,
			'title'	:'The Current Wiki',
			'desc'	: '(should be updated with the proper text)',
			'api_url': wgScriptPath + '/api.php',
			'lib'	:'mediaWiki',
			'local'	:true
		},
		'wiki_commons':{
			'enabled':1,
			'checked':1,
			'd'		:0,
			'title'	:'Wikipedia Commons',
			'desc'	: 'Wikimedia Commons is a media file repository making available public domain '+
			 		'and freely-licensed educational media content (images, sound and video clips) to all.',		
			'api_url':'http://commons.wikimedia.org/w/api.php',
			'lib'	:'mediaWiki',
			'search_title':false, //disable title search
			'local'	:true
		},
		'metavid':{
			'enabled':1,
			'checked':1,
			'd'		:1,			
			'title'	:'Metavid.org',
			'desc'	: 'Metavid hosts thousands of hours of US house and senate floor proceedings',
			'api_url':'http://localhost/wiki/index.php?title=Special:MvExportSearch',
			'lib'	: 'metavid',
			'local'	:false 
		},
		'archive_org':{
			'enabled':0,
			'checked':0,
			'd'		:0,
			'title' : 'Archive.org',
			'desc'	: 'The Internet Archive, a digital library of cultural artifacts',
			'lib'	: 'archive',
			'local'	: false
		}
	},	
	//some default layout values:		
	thumb_width 		: 80,
	result_display_mode : 'box', //box or list or preview
	
	init:function( initObj ){
		js_log('remoteSearchDriver:init');
		for( var i in default_remote_search_options ) {
			if( initObj[i]){
				this[ i ] = initObj[i];
			}else{
				this[ i ] =default_remote_search_options[i]; 
			}			
		}
		this.init_interface_html();
		this.add_interface_bindings();
	},	
	//sets up the initial html interface 
	init_interface_html:function(){
		var out = '<div class="rsd_control_container" style="width:100%">' + 
					'<table style="width:100%">' +
						'<tr>'+
							'<td style="width:110px">'+
								'<h3> Media Search </h3>'+
							'</td>'+
							'<td style="width:190px">'+
								'<input type="text" tabindex="1" value="' + this.default_query + '" maxlength="512" id="rsd_q" name="rsd_q" '+ 
									'size="20" autocomplete="off"/>'+
							'</td>'+
							'<td style="width:115px">'+
								'<input type="submit" value="' + getMsg('mv_media_search') + '" tabindex="2" '+
									' id="rms_search_button"/>'+
							'</td>'+
							'<td>';
			out += '<a src="#" id="mso_selprovider" >Select Providers</a><br>';
			out += '<a src="#" id="mso_cancel" >Cancel</a><br>';
			out +=			'</td>'+
						'</tr>'+
					'</table>';			
		js_log('out: ' + out);									
				
		out+='<div id="rsd_options_bar" style="display:none;width:100%;height:0px;background:#BBB">';
			//set up the content provider selection div (do this first to get the default cp)
			out+= '<div id="cps_options">';												
			for( var cp_id in this.content_providers ){
				var cp = this.content_providers[cp_id];				 
				var checked_attr = ( cp.checked ) ? 'checked':'';					  
				out+='<div  title="' + cp.title + '" '+ 
						' style="float:left;cursor:pointer;">'+
						'<input class="mv_cps_input" type="checkbox" name="mv_cps" '+ checked_attr+'>';

				out+= '<img alt="'+cp.title+'" src="' + mv_embed_path + 'skins/' + mv_skin_name + '/remote_search/' + cp_id + '_tab.png">'; 				
				out+='</div>';
			}		 		
			out+='<div style="clear:both"/><a id="mso_selprovider_close" href="#">'+getMsg('close')+'</a></div>';
		out+='</div>';				
		//close up the control container: 
		out+='</div>';
		//search provider tabs based on "checked" and "enabled" and "combined tab"
		out+='<div id="rsd_results_container">';				
		out+='</div>';							
		$j('#'+ this.target_id ).html( out );
		//draw the tabs: 
		this.drawTabs();
		//run the default search: 
		this.runSearch();
	}, 
	add_interface_bindings:function(){
		var _this = this;
		js_log("add_interface_bindings:");		
		//setup for this.main_search_options:
		$j('#mso_cancel').click(function(){
			$j('#modalbox').fadeOut("normal",function(){
				$j(this).remove();
				$j('#mv_overlay').remove();
			});
		});
		
		$j('#mso_selprovider,#mso_selprovider_close').click(function(){
			if($j('#rsd_options_bar:hidden').length !=0 ){
				$j('#rsd_options_bar').animate({
					'height':'110px',
					'opacity':1
				}, "normal");
			}else{
				$j('#rsd_options_bar').animate({
					'height':'0px',
					'opacity':0					
				}, "normal", function(){
					$j(this).hide();
				})
			}
		});		
		//setup bindings for search tabs: 
		$j('.rsd_cp_tabs li').click(function(){
			_this.selectTab( $j(this).attr('id').replace(/rsd_tab_/, '') );
		});
		
		//setup binding for search provider check box: 
		//search button: 
		$j('#rms_search_button').click(function(){
			_this.runSearch();
		});
	},
	runSearch: function(){
		var _this = this;						
		//set loading div: 
		$j('#rsd_results').append('<div style="postion:absolute;top:0px;left:0px;height:100%;width:100%;'+
		 	'-moz-opacity:.50; filter:alpha(opacity=50); opacity:.50;z-index:100">' + 			
				'<img style="padding:10px;" src="'+ 
					mv_embed_path + 'skins/' + mv_skin_name + '/images/loading_ani.gif">'+
			'</div>');		
		//get a remote search object for each search provider and run the search
		for(var cp_id in  this.content_providers){
			cp = this.content_providers[ cp_id ];
			//only run the search for default item (unless combined is selected) 
			if( !cp.d || this.disp_item == 'combined' )
				continue;			
			//check if we need to update: 
			if(typeof cp.sObj != 'undefined'){
				if(cp.sObj.last_query == $j('#rsd_q').val())
					continue;					
			}			
			//else we need to run the search: 
			var iObj = {'cp':cp, 'rsd':this};			
			eval('cp.sObj = new '+cp.lib+'Search(iObj);');
			if(!cp.sObj)
				js_log('Error: could not find search lib for ' + cp_id);						
			//do search:
			cp.sObj.getSearchResults();							
		}	
		this.checkResultsDone();
	},	
	checkResultsDone: function(){
		var _this = this;
		var loading_done = true;
		for(var cp_id in  this.content_providers){
			cp = this.content_providers[ cp_id ];
			if(typeof cp['sObj'] != 'undefined'){
				if( cp.sObj.loading )
					loading_done=false; 
			}
		}
		if(loading_done){
			this.drawOutputResults();
		}else{			
			setTimeout( _this.instance_name + '.checkResultsDone()', 250);
		}		 
	},
	drawTabs: function(){
		//add the tabs to the rsd_results container: 
		var o= '<ul class="rsd_cp_tabs" >';
			o+='<li id="rsd_tab_combined" ><img src="' + mv_embed_path + 'skins/'+mv_skin_name+ '/remote_search/combined_tab.png"></li>';		 			 	
			for(var cp_id in  this.content_providers){
				var cp = this.content_providers[cp_id];
				if( cp.enabled && cp.checked){
					var class_attr = (cp.d)?'class="rsd_selected"':'';
					o+='<li id="rsd_tab_'+cp_id+'" ' + class_attr + '><img src="' + mv_embed_path + 'skins/' + mv_skin_name + '/remote_search/' + cp_id + '_tab.png">';
				}
			}
		o+='</ul>';		
		//outout the resource results holder	
		o+='<div id="rsd_results" />';				
		$j('#rsd_results_container').html(o);
	},	
	drawOutputResults: function(){		
		js_log('f:drawOutputResults');				
		var _this = this;			
		var o='';
		$j('#rsd_results').empty();
		//output the results bar / controls
		_this.setResultBarControl();
		
		//output all the results (hide based on tab selection) 
		for(var cp_id in  this.content_providers){
			cp = this.content_providers[ cp_id ];
			//output results based on display mode & input: 
			if(typeof cp['sObj'] != 'undefined'){
				$j.each(cp.sObj.resultsObj, function(rInx, resultItem){					
					var disp = ( cp.d ) ? '' : 'display:none;';
					if( _this.result_display_mode == 'box' ){
						o+='<div id="mv_result_' + rInx + '" class="mv_clip_box_result res_' + cp_id +'" style="' + disp + 'width:' +
								_this.thumb_width + 'px;height:'+ parseInt(_this.thumb_width) +'px">';
							o+='<img style="width:' + _this.thumb_width + 'px;" src="' + resultItem.poster + '">';
						o+='</div>';
					}else if(_this.result_display_mode == 'list'){
						o+='<div id="mv_result_' + rInx + '" class="mv_clip_list_result res_' + cp_id +'" style="' + disp + 'width:90%">';
							o+='<img style="float:left;width:' + _this.thumb_width + 'px;" src="' + resultItem.poster + '">';			
							o+= cp.sObj.specialFormatDesc( resultItem.desc ) ;							
						o+='</div>';
						o+='<div style="clear:both" />';
					}			
				});	
			}						
		}		
		js_log('should add to rsd:' + o);
		//put in the new output:  
		$j('#rsd_results').append( o );
	},
	setResultBarControl:function( ){
		var _this = this;
		var box_dark_url 	= mv_embed_path + 'skins/' + mv_skin_name + '/images/box_layout_icon_dark.png';
		var box_light_url 	= mv_embed_path + 'skins/' + mv_skin_name + '/images/box_layout_icon.png';
		var list_dark_url 	= mv_embed_path + 'skins/' + mv_skin_name + '/images/list_layout_icon_dark.png';
		var list_light_url 	= mv_embed_path + 'skins/' + mv_skin_name + '/images/list_layout_icon.png';
		
		
		$j('#rsd_results').append('<div id="rds_results_bar">'+
			'<span style="position:relative;top:-5px;font-style:italic;">'+
				getMsg('rsd_layout')+' '+
			'</span>'+
				'<img id="msc_box_layout" ' +
					'title = "' + getMsg('rsd_box_layout') + '" '+ 
					'src = "' +  ( (_this.result_display_mode=='box')?box_dark_url:box_light_url ) + '" ' +			
					'style="width:20px;height:20px;cursor:pointer;"> ' + 
				'<img id="msc_list_layout" '+
					'title = "' + getMsg('rsd_list_layout') + '" '+
					'src = "' +  ( (_this.result_display_mode=='list')?list_dark_url:list_light_url ) + '" '+			
					'style="width:20px;height:20px;cursor:pointer;">'+			
			'<span style="position:absolute;right:5px;">'+ getMsg('rsd_results_desc', new Array('1 - 20', '420'))+'</span>'+
			'</div>'
		);
				
		$j('#msc_box_layout').hover(function(){			
			$j(this).attr("src", box_dark_url );
		}, function(){ 
			$j(this).attr("src",  ( (_this.result_display_mode=='box')?box_dark_url:box_light_url ) );		
		}).click(function(){	
			$j(this).attr("src", box_dark_url);
			$j('#msc_list_layout').attr("src", list_light_url);
			_this.setDispMode('box');
		});
		
		$j('#msc_list_layout').hover(function(){
			$j(this).attr("src", list_dark_url);
		}, function(){
			$j(this).attr("src", ( (_this.result_display_mode=='list')?list_dark_url:list_light_url ) );		
		}).click(function(){
			$j(this).attr("src", list_dark_url);
			$j('#msc_box_layout').attr("src", box_light_url);
			_this.setDispMode('list');
		});
	},
	selectTab:function( selected_cp_id ){
		js_log('select tab: ' + selected_cp_id);
		this.disp_item =selected_cp_id;
		//set display to unselected: 
		for(var cp_id in  this.content_providers){
			cp = this.content_providers[ cp_id ];
			if( (selected_cp_id == 'combined' && cp.checked ) || selected_cp_id == cp_id){
				cp.d = 1;
			}else{
				cp.d = 0;
			}			
		}	
		//redraw tabs
		this.drawTabs();
		//update the search results: 
		this.runSearch();	 		
	},
	setDispMode:function(mode){
		js_log('setDispMode:' + mode);
		this.result_display_mode=mode;	
		//run /update search display:
		this.drawOutputResults();
	}
}
//default values: 
// tag_name@{attribute}
var rsd_default_rss_item_mapping = {
	'poster'	: 'media:thumbnail@url',
	'roe_url'	: 'media:roe_embed@url',
	'link'		: 'link',
	'desc'		: 'description'
}
var mvBaseRemoteSearch = function(initObj) {
	return this.init(initObj);
};
mvBaseRemoteSearch.prototype = {
	
	completed_req:0,
	num_req:0,	
		
	resultsObj:{},
	//init the object: 
	init:function( initObj ){		
		js_log('mvBaseRemoteSearch:init');
		for(var i in initObj){
			this[i] = initObj[i];
		}
		return this;
	},
	/*
	* Parses and adds video rss based input format
	* @data XML 		data to parse
	* @provider_url 	the source url (used to gennerate absolute links)  
	*/
	addRSSData:function( data , provider_url ){
		var _this = this;
		var http_host = '';
		var http_path = '';		
		if(provider_url){
			pUrl =  parseUri( provider_url );
			http_host = pUrl.protocol +'://'+ pUrl.authority;  
			http_path = pUrl.directory;
		}
		items = data.getElementsByTagName('item');
		$j.each(data.getElementsByTagName('item'), function(inx, item){		
			var rObj ={};			
			for(var i in rsd_default_rss_item_mapping){								
				var selector = rsd_default_rss_item_mapping[i].split('@');
				
				var tag_name = selector[0];
				var attr_name = null;								
				
				if( selector[1] )
					attr_name = selector[1];
				
				//grab the first match 
				var node = item.getElementsByTagName( tag_name )[0];
				//js_log('node: ' + node +  ' nv:' +  $j(node).html() + ' nv[0]'+ node.innerHTML + 
				//' cn' + node.childNodes[0].nodeValue  );				
					
				if( node!=null && attr_name == null ){
					if( node.childNodes[0] != null){			
						rObj[i] =  node.textContent;						
					}			
				}	
								
				if( node!=null && attr_name != null)
					rObj[i] = $j(node).attr( attr_name );									
			}	
			//make relative urls absolute:
			var url_param = new Array('src', 'poster'); 
			for(var j=0; j < url_param.length; j++){
				var p = url_param[j];
				if(typeof rObj[p] != 'undefined'){
					if( rObj[p].substr(0,1)=='/' ){				
						rObj[p] = http_host + rObj[p];
					}
					if( parseUri( rObj[i] ).host ==  rObj[p]){
						rObj[p] = http_host + http_path + rObj[p];
					}
				}
			}				
			_this.resultsObj[inx] = rObj;			
		});
	},
	/*
	* by default just retun the desc unmodified
	*/
	specialFormatDesc:function( desc_html) {
		return desc_html;
	}
}
/*
* api modes (implementations should call these objects which inherit the mvBaseRemoteSearch  
*/
var metavidSearch = function(initObj) {		
	return this.init(initObj);
};
metavidSearch.prototype = {
	rObj:{  //set up the default request paramaters
		'order':'recent',
		'feed_format':'rss'		
	},
	init:function( initObj ){
		//init base class and inherit: 
		var baseSearch = new mvBaseRemoteSearch( initObj );
		for(var i in baseSearch){
			if(typeof this[i] =='undefined'){
				this[i] = baseSearch[i];
			}else{
				this['parent_'+i] =  baseSearch[i];
			}
		}
	},
	getSearchResults:function(){
		var _this = this;
		//start loading:
		_this.loading= 1;
		js_log('metavidSearch::getSearchResults()');
		//proccess all options
		url = this.cp.api_url;
		//add on the req_param
		for(var i in this.rObj){
			url += '&' + i + '=' + this.rObj[i];
		}
		//do basic query:
		url += '&f[0][t]=match&f[0][v]=' + $j('#rsd_q').val();
		do_request(url, function(data){ 
			//should have an xml rss data object:
			_this.addRSSData( data , url );
			//done loading: 
			_this.loading=0;
		});
	},
	addRSSData:function(data, url){
		this.parent_addRSSData(data, url);
		//special metavid rss feed proccessing: strip mv_rss_view_only
		
	},
	/*
	* special format description output. 
	*/
	specialFormatDesc:function( desc_html ){	
		return desc_html;
	}		
}

var mediaWikiSearch = function( initObj ) {		
	return this.init( initObj );
};
mediaWikiSearch.prototype = {
	init:function( initObj ){
		//init base class and inherit: 
		var baseSearch = new mvBaseRemoteSearch( initObj );
		for(var i in baseSearch){
			if(typeof this[i] =='undefined'){
				this[i] = baseSearch[i];
			}else{
				this['parent_'+i] =  baseSearch[i];
			}
		}
	},
	getSearchResults:function(){
		var _this = this;
		this.loading=true;
		js_log('f:getSearchResults for:' + $j('#'+this.target_input).val() );		
		//empty out the current results: 
		this.resultsObj={};
		//do two queries against the Image / File / MVD namespace: 								
		//build the image request object: 
		var rObj = {
			'action':'query', 
			'generator':'search',
			'gsrsearch': encodeURIComponent( $j('#'+this.target_input).val() ),  
			'gsrnamespace':6, //(only search the "file" namespace (audio, video, images)
			'gsrwhat':'title',
			'prop':'imageinfo|revisions|categories',
			'iiprop':'url',
			'iiurlwidth':'80',
			'rvprop':'content'
		};				
		//set up the number of request: 
		this.completed_req=0;
		this.num_req=2;
		//setup the number of requests result flag: 				
		do_api_req( rObj, this.cp.api_url , function(data){				
			//parse the return data
			_this.addResults( data);				
			_this.checkRequestDone();			
		});							
		//also do a request for page titles (would be nice if api could query both at the same time) 
		rObj['gsrwhat']='text';
		do_api_req( rObj, this.cp.api_url , function(data){
			//parse the return data
			_this.addResults( data);
			_this.checkRequestDone();
		});			
	},
	addResults:function( data ){	
		//make sure we have pages to idoerate: 
		if(data.query && data.query.pages){
			for(var page_id in  data.query.pages){
				var page =  data.query.pages[ page_id ];				
				this.resultsObj[page_id]={
					'uri':page.title,
					'poster':page.imageinfo.thumburl,
					'src':page.imageinfo.url,
					'desc':page.revisions[0]['*'],
					'meta':{
						'categories':page.categories
					}
				}
			}
		}else{
			js_log('no results:' + data);
		}
	},	
	//check request done used for when we have multiple requests to check before formating results. 
	checkRequestDone:function(){
		//display output if done: 
		this.completed_req++;
		if(this.completed_req == this.num_req){
			this.loading = 0;
		}
	}
}