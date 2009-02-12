/*
* api modes (implementations should call these objects which inherit the mvBaseRemoteSearch  
*/
var metavidSearch = function(initObj) {		
	return this.init(initObj);
};
metavidSearch.prototype = {
	reqObj:{  //set up the default request paramaters
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
		var url = this.cp.api_url;
		//add on the req_param
		for(var i in this.reqObj){
			url += '&' + i + '=' + this.reqObj[i];
		}
		//do basic query:
		this.last_query = $j('#rsd_q').val();
		this.last_offset = this.cp.offset;
		url += '&f[0][t]=match&f[0][v]=' + $j('#rsd_q').val();
		//add offset limit: 
		url+='&limit=' + this.cp.limit;
		url+='&offset=' + this.cp.offset;
		
		do_request(url, function(data){ 
			//should have an xml rss data object:
			_this.addRSSData( data , url );
			//do some metavid specific pos processing on the rObj data: 
			for(var i in _this.resultsObj){
				var rObj = _this.resultsObj[i];	
				var proe = parseUri( rObj['roe_url'] );				
				rObj['start_time'] = proe.queryKey['t'].split('/')[0];
				rObj['end_time'] = proe.queryKey['t'].split('/')[1];	
				rObj['stream_name'] = proe.queryKey['stream_name'];
				//transform the title into a wiki_safe title: 			
				//rObj['titleKey'] = proe.queryKey['stream_name'] + '_' + rObj['start_time'].replace(/:/g,'.') + '_' + rObj['end_time'].replace(/:/g,'.') + '.ogg';
				rObj['titleKey'] = proe.queryKey['stream_name'] + '/' + rObj['start_time'] + '/' + rObj['end_time'] + '__.ogg';						
			}			
			//done loading: 
			_this.loading=0;
		});
	},
	getEmbedWikiText:function(rObj, options){
		//if we are using a local copy do the standard b:  
		if( this.cp.local_copy == true)
			return this.parent_getEmbedWikiText(rObj, options);								
		//if local_copy is false and embed metavid extension is enabled: 		
		return 
	},
	getEmbedHTML:function( rObj , options ){
		var id_attr = (options['id'])?' id = "' + options['id'] +'" ': '';
		var style_attr = (options['max_width'])?' style="width:'+options['max_width']+'px;"':'';		
		if(options['only_poster']){
			return '<img ' + id_attr + ' src="' + rObj['poster']+'" ' + style_attr + '>';	
		}else{
			return '<video ' + id_attr + ' roe="' + rObj['roe_url'] + '"></video>';
		}
	},	
	getEmbedObjParsedInfo:function(rObj, eb_id){
		var sources = $j('#'+eb_id).get(0).media_element.getSources();
		rObj.other_versions ='*[' + rObj['roe_url'] + ' XML of all Video Formats and Timed Text]'+"\n";
		for(var i in sources){
			var cur_source = sources[i];
			//rObj.other_versions += '*['+cur_source.getURI() +' ' + cur_source.title +']' + "\n";			
			if( cur_source.id ==  this.cp.target_source_id)
				rObj['url'] = cur_source.getURI();
		}
		js_log('set url to: ' + rObj['url']);
		return rObj;			
	},
	//update rObj for import:
	updateDataForImport:function( rObj ){
		rObj['author']='US Government';
		//convert data to UTC type date:
		var dateExp = new RegExp(/_([0-9]+)\-([0-9]+)\-([0-9]+)/);	
		var dParts = rObj.link.match (dateExp);
		var d = new Date();
		var year_full = (dParts[3].length==2)?'20'+dParts[3].toString():dParts[3];
		d.setFullYear(year_full, dParts[1]-1, dParts[2]);	
		rObj['date'] = 	d.toDateString();		
		rObj['licence_template_tag']='PD-USGov';		
		//update based on new start time: 		
		js_log('url is: ' + rObj.src + ' ns: ' + rObj.start_time + ' ne:' + rObj.end_time);		
						
		return rObj;
	}
}