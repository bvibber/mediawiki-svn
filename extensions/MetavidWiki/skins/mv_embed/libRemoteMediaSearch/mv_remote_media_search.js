/*
* a library for doing remote media searches 
*/

var remoteSearchDriver = function(initObj){
	return this.init( initObj );
}
remoteSearchDriver.prototype = {
	init:function( initObj ){
		//do profile check: 
		if( initObj.profile == 'mediawiki_edit'){
			
		}
		if( initObj.profile == 'sequence'){
			
		}
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