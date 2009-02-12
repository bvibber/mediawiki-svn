//archive.org uses solr engine: 
//more about solr here: 
//http://lucene.apache.org/solr/
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
		//build the req: 
		var reqObj = {
			'q':$j('#rsd_q').val() + ' format:(Ogg video)', //just search for video atm
			'fl':"description,identifier,licenseurl,format,license,thumbnail",			
			'wt':'json',
			'rows':'30',
			'indent':'yes'			
		}									
		do_api_req( {
			'data':reqObj, 
			'url':this.cp.api_url
			}, function(data){
				js_log('got data: ' + data);
			}
		});
	}		
}