//archive.org uses solr engine: 
//more about solr here: 
//http://lucene.apache.org/solr/

var archiveOrgSearch = function ( initObj){
	return this.init( initObj );
}
archiveOrgSearch.prototype = {
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
		//call parent: 
		this.parent_getSearchResults();
		
		var _this = this;		
		this.loading=true;
		js_log('f:getSearchResults for:' + $j('#rsd_q').val() );		
		//build the query var
		var q = $j('#rsd_q').val();
		//@@todo check advanced options: include audio and images media types
		//for now force (Ogg video) & a creativecommons license
		q+=' format:(Ogg video)';
		q+=' licenseurl:(http\\:\\/\\/*)';
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
			//set result info: 
			this.num_results = data.response.numFound;
		
			for(var resource_id in data.response.docs){
				var resource = data.response.docs[resource_id];
				//make sure the reop is shared
				rObj = {
					'titleKey'	 :resource.identifier,
					'link'		 :'http://www.archive.org/details/' + resource.identifier,				
					'title'		 :resource.title
				};			
				rObj['poster']='http://www.archive.org/download/' + resource.identifier+'/format=thumbnail';
				rObj['poster_ani']='http://www.archive.org/download/' + resource.identifier+'/format=Animated+Gif';
				
				rObj['thumbwidth']=160;
				rObj['thumbheight']=110;				
				rObj['src']='http://www.archive.org/download/' + resource.identifier+'/format=Ogg+video';
				rObj['mime']='application/ogg';
				rObj['pSobj']=_this;				
				
				//set the licence: (rsd is a pointer to the parent remoteSearchDriver ) 
				rObj['license'] = this.rsd.getLicenceFromUrl( resource.licenseurl );								 
				
				this.resultsObj[ resource_id ] =rObj;
				
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
		js_log('getEmbedHTML:: ' + rObj.poster );
		var id_attr = (options['id'])?' id = "' + options['id'] +'" ': '';
		if(rObj.mime == 'application/ogg' || rObj.mime == 'audio/ogg' || rObj.mime=='video/ogg'){
			return '<video ' + id_attr + ' src="' + rObj.src + '" poster="' + rObj.poster + '" type="video/ogg"></video>';
		}
	},		
}