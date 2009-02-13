//archive.org uses solr engine: 
//more about solr here: 
//http://lucene.apache.org/solr/

//if we ever have another remote repository using solr we could abstract thouse pieces into a seperate lib

var solrArchiveSearch = function ( initObj){
	return this.init( initObj );
}
solrArchiveSearch.prototype = {
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
		//inherit the cp settings for 
	},
	getSearchResults:function(){
		var _this = this;
		this.loading=true;
		js_log('f:getSearchResults for:' + $j('#rsd_q').val() );
		//build the query var
		var q = $j('#rsd_q').val();
		//@@todo check advanced options: 
		q+=' format:(Ogg video)';
		q+=' licenseurl:(http\\:\\/\\/creativecommons\\.org*)';
		var reqObj = {
			'q': q, //just search for video atm
			'fl':"description,title,identifier,licenseurl,format,license,thumbnail",			
			'wt':'json',			
			'rows':'30',
			'indent':'yes'			
		}									
		do_api_req( {
			'data':reqObj, 
			'url':this.cp.api_url,
			'jsonCB':'json.wrf'
			}, function(data){
				_this.addResults( data);
				_this.loading = false;
			}
		);
	},
	addResults:function( data ){		
		var _this = this;	
		if(data.response && data.response.docs){
			for(var resource_id in data.response.docs){
				var resource = data.response.docs[resource_id];
				//make sure the reop is shared
				rObj = {
					'titleKey'	 :resource.identifier,
					'link'		 :'http://www.archive.org/details/' + resource.identifier,				
					'title'		 :resource.title
				};			
				rObj['poster']='http://www.archive.org/download/'+resource.identifier+'/format=thumbnail';
				rObj['thumbwidth']=160;
				rObj['thumbheight']=110;				
				rObj['src']='http://www.archive.org/download/'+ +resource.identifier+'/format=Ogg+video';
				rObj['mime']='application/ogg';		
				rObj['pSobj']=_this;				
				this.resultsObj[resource_id] =rObj;
				
				//likely a audio clip if no poster and type application/ogg 
				//@@todo we should return audio/ogg for the mime type or some other way to specify its "audio" 
						
				//this.num_results++;	
				//for(var i in this.resultsObj[page_id]){
				//	js_log('added: '+ i +' '+ this.resultsObj[page_id][i]);
				//}
			}
		}		
	},
	getEmbedHTML: function( rObj , options) {
		//alert('archive.org support not yet ready');
	},		
}