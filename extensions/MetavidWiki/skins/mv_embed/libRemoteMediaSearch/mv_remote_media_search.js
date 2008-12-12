/*
* a library for doing remote media searches 
*  
* initial targeted archives are:
	the local wiki 
	wikimedia commons 
	metavid 
	and archive.org
*/

gMsg['mv_media_search']= 'Media Search';

var default_remote_search_options = {
	'profile':'mediawiki_edit',	
	'target_id':null, //the div that will hold the search interface
	
	'default_provider_id':'all', //all or one of the content_providers ids
	
	//specific to sequence profile
	'p_seq':null		
}
var remoteSearchDriver = function(initObj){
	return this.init( initObj );
}
remoteSearchDriver.prototype = {
	//here we define the set of possible media content providers:
	main_search_options:{
		'selprovider':{
			'title': 'Select Provider'			
		},
		'advanced_search':{
			'title': 'Advanced Options'
		}		
	},
	content_providers:{
		//@@todo seed this via the include call (can be exported to seperate library
		// once we get the script loader integrated) 
		'this_wiki':{
			'enabled':1,
			'checked':1,
			'title':'The Current Wiki',
			'desc': '(should be updated with the proper text)'
		},
		'wiki_commons':{
			'enabled':1,
			'checked':1,			
			'title':'Wikipedia Commons',
			'desc': 'Wikimedia Commons is a media file repository making available public domain '+
			 		'and freely-licensed educational media content (images, sound and video clips) to all.',
			'logo': 'http://upload.wikimedia.org/wikipedia/commons/thumb/7/79/Wiki-commons.png/80px-Wiki-commons.png'
		},
		'archive_org':{
			'enabled':1,
			'checked':1,
			'title' : 'Archive.org',
			'desc'	: 'The Internet Archive, a digital library of cultural artifacts in digital form',
			'logo'  : 'http://www.archive.org/images/logo.jpg'
		},	
		'metavid':{
			'enabled':1,
			'checked':1,
			'title':'Metavid.org',
			'desc': 'Metavid hosts thousands of hours of US house and senate floor proccedings',
			'logo': 'http://metavid.org/w/skins/mvpcf/images/logo.png'
		}
	},	
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
							'<td style="width:100px">'+
								'<h3> Media Search </h3>'+
							'</td>'+
							'<td style="width:190px">'+
								'<input type="text" tabindex="1" value="" maxlength="512" id="q" name="q" '+ 
									'size="20" autocomplete="off"/>'+
							'</td>'+
							'<td style="width:110px">'+
								'<input type="submit" value="' + getMsg('mv_media_search') + '" tabindex="2" '+
									' id="rms_search_button"/>'+
							'</td>'+
							'<td>';
		for(var i in this.main_search_options){
			var mso = this.main_search_options[i];	
			out += '<a src="#" id="mso_'+i+'" >' + mso.title + '</a><br>';
		}
		out+=				'</td>'+
						'</tr>'+
					'</table>';			
		js_log('out: ' + out);							
		//set up the content provider selection div (do this first to get the default cp)
		var cpsdiv = '<div id="cps_options">'+
						'<table style="background:transparent"><tr>';
						
		for( var i in this.content_providers ){
			var cp = this.content_providers[i];
				 
			var checked_attr = ( cp.checked ) ? 'checked':'';
					  
			cpsdiv+='<td '+
						' title="' + cp.title + '" '+ 
						' style="float:left;cursor:pointer;">'+
					'<input class="mv_cps_input" type="checkbox" name="mv_cps" '+ checked_attr+'>'+
					'</td>'+
					'<td>';				
			if( cp.logo ){
				cpsdiv+= '<img src="' + cp.logo + '">'; 
			}else{
				cpsdiv+= cp.title 
			}
			cpsdiv+='</td>';
		}		 		
		cpsdiv+='<tr><td><a id="mso_selprovider_close" href="#">'+getMsg('close')+'</a></td></tr></table></div>';
		
		out+='<div id="rsd_options_bar" style="display:none;width:100%;height:0px;background:#BBB">'+
				cpsdiv +
			 '</div>';
		out+='<div class="rsd_result_bar" style="width:100%;border-top:solid thin black;background:#66F"/>';							
		//close up the control container: 
		out+='</div>';
		//outout the results placeholder:		
		out+='<div class="rmd_results" style="width:100%;height:100%;overflow:auto;" />';
		$j('#'+ this.target_id ).html( out );
	}, 
	add_interface_bindings:function(){
		var _this = this;
		js_log("add_interface_bindings:");
		//setup for this.main_search_options:
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
		//setup binding for search provider check box: 
		//search button: 
		$j('#rms_search_button').click(function(){
			_this.runSearch();
		});
	},
	runSearch:function(){
		//get a remote search object for each search provider:
		for(var i in  
	}		
}

var mvBaseRemoteSearch = function(initObj) {
	return this.init(initObj);
};
mvBaseRemoteSearch.prototype = {
	//default values: 
	thumb_width:80,
	
	completed_req:0,
	num_req:0,
	
	result_display_mode:'box', //box or list or preview
	resultsObj:{},
	//init the object: 
	init:function( initObj ){		
		js_log('mvBaseRemoteSearch:init');
		for(var i in initObj){
			this[i] = initObj[i];
		}	
		
		var _this = this;			
		if(this['target_submit']){
			$j('#'+this['target_submit']).click(function(){
				js_log('doSearch REQ');
				_this.getSearchResults();
			});
		}		
		
		//set up bindings for interface components
		//if(this['target_input'])
			//@@todo autocomplete for titles
			
		//if(this['target_results')
			//@@todo error checking
			
		//check if we are in metavid Temporal semantic media search mode
			//add an "advanced search" button
		
		//add in controls: (find a better place for these / use css)  
		//this seems highly verbose do do a simple control
		var box_dark_url 	= mv_embed_path + 'skins/' + mv_skin_name + '/images/box_layout_icon_dark.png';
		var box_light_url 	= mv_embed_path + 'skins/' + mv_skin_name + '/images/box_layout_icon.png';
		var list_dark_url 	= mv_embed_path + 'skins/' + mv_skin_name + '/images/list_layout_icon_dark.png';
		var list_light_url 	= mv_embed_path + 'skins/' + mv_skin_name + '/images/list_layout_icon.png';
		
		$j('#'+this.target_submit).after('<img id="msc_box_layout" ' +
				'src = "' +  ( (_this.result_display_mode=='box')?box_dark_url:box_light_url ) + '" ' +			
				'style="width:20px;height:20px;cursor:pointer;"> ' + 
			'<img id="msc_list_layout" '+
				'src = "' +  ( (_this.result_display_mode=='list')?list_dark_url:list_light_url ) + '" '+			
				'style="width:20px;height:20px;cursor:pointer;">'
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
		
		//or if we are in plain mediaWiki mode: 	
		return this;
	},
	setDispMode:function(mode){
		js_log('setDispMode:' + mode);
		this.result_display_mode=mode;
		//reformat the results: 
		this.formatOutputResults();
	},	
	//check request done used for when we have multiple requests to check before formating results. 
	checkRequestDone:function(){
		//display output if done: 
		this.completed_req++;
		if(this.completed_req == this.num_req){
			this.formatOutputResults();
		}
	},	
	formatOutputResults:function(){
		js_log('f:formatOutputResults');
		//debugger;
		var o='';
		//output results based on display mode: 
		for( var rInx in this.resultsObj ){
			var resultItem = this.resultsObj[rInx];
			if( this.result_display_mode == 'box' ){
				o+='<div id="mv_result_' + rInx + '" class="mv_clip_box_result" style="width:' + this.thumb_width + 'px;">';
					o+='<img style="width:' + this.thumb_width + 'px;" src="' + resultItem.poster + '">';
				o+='</div>';
			}else if(this.result_display_mode == 'list'){
				o+='<div id="mv_result_' + rInx + '" class="mv_clip_list_result" style="width:90%">';
					o+='<img style="float:left;width:' + this.thumb_width + 'px;" src="' + resultItem.poster + '">';			
					o+= pageObj.revisions[0]['*'];							
				o+='</div>';
				o+='<div style="clear:both" />';
			}			
		}
		js_log('set : ' +this.target_results + ' to ' + o);
		//debugger;
		$j('#'+this.target_results).html(o);
	}	
}

/*
* api modes (implementations should call these objects which inherit the mvBaseRemoteSearch  
*/
var metavidRemoteSearch = function(initObj) {		
	return this.init(initObj);
};
metavidRemoteSearch.prototype = {
	init:function(initObj){
		var baseSearch = new mvBaseRemoteSearch(initObj);
		//inherit:
		for(var i in baseSearch){
			if(typeof this[i] =='undefined'){
				this[i] = baseSearch[i];
			}else{
				this['parent_'+i] =  baseSearch[i];
			}
		}
	}
}

var mediaWikiRemoteSearch = function(initObj) {		
	return this.init(initObj);
};
mediaWikiRemoteSearch.prototype = {
	init:function(initObj){
		var baseSearch = new mvBaseRemoteSearch(initObj);
		//inherit:
		for(var i in baseSearch){
			if(typeof this[i] =='undefined'){
				this[i] = baseSearch[i];
			}else{
				this['parent_'+i] =  baseSearch[i];
			}
		}
	},
	getSearchResults:function(){
		js_log('f:getSearchResults for:' + $j('#'+this.target_input).val() );
		//set results div to "loading"
		$j('#'+this.target_results).html( getMsg('loading_txt') );
		//empty out the current results: 
		this.resultsObj={};
		//do two queries against the Image / File / MVD namespace: 			
		//construct search request:		
		var req_url =this.p_seq.plObj.interface_url.replace(/index\.php/, 'api.php');			
		//build the image request object: 
		var rObj = {
			'action':'query', 
			'generator':'search',
			'gsrsearch': encodeURIComponent( $j('#'+this.target_input).val() ),  
			'gsrnamespace':6, //(only search images)
			'gsrwhat':'title',
			'prop':'imageinfo|revisions|categories',
			'iiprop':'url',
			'iiurlwidth':'80',
			'rvprop':'content'
		};		
		var _this = this;
		//set up the number of request: 
		this.completed_req=0;
		this.num_req=2;
		//setup the number of requests result flag: 				
		do_api_req( rObj, req_url, function(data){				
			//parse the return data
			_this.addMediaWikiAPIResults( data);				
			_this.checkRequestDone();			
		});							
		//also do a request for page titles (would be nice if api could query both at the same time) 
		rObj['gsrwhat']='text';
		do_api_req( rObj, req_url, function(data){
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
				
				this.resultsObj['ref'][page_id]={
					'uri':page.title,
					'poster':page.imageinfo.thumburl,
					'src':page.imageinfo.url,
					'desc':page.revisions['*'],
					'meta':{
						'categories':page.categories
					}
				}
			}
		}else{
			js_log('no results:' + data);
		}
	}
}