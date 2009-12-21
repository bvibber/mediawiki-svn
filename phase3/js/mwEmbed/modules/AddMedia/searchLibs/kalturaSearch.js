/*
 * Kaltura agragated search:  
 */

var kalturaSearch = function ( options ) {
	return this.init( options );
}
kalturaSearch.prototype = {

	// Stores search library pointers
	searchLibs:{ },	
	
	/**
	* Initialize the flickr Search with provided options
	*
	* @param {Object} options Initial options for the kalturaSearch class
	*/
	init:function( options ) {		
		this.options = options;
		var baseSearch = new baseRemoteSearch( options );
		for ( var i in baseSearch ) {
			if ( typeof this[i] == 'undefined' ) {
				this[i] = baseSearch[i];
			} else {
				this['parent_' + i] =  baseSearch[i];
			}
		}
	},
	
	/**
	* Get the Search results setting _loading flag to false once results have been added 
	* 
	* Runs an api call then calls addResults with the resulting data
	* @param {String} search_query Text search string 
	*/
	getSearchResults:function( search_query ) {
		var _this = this;
		mw.log( "Kaltura::getSearchResults" );
		
		// call parent for common initialisation:  
		this.parent_getSearchResults();
		
		// setup the flickr request: 
		var request = {
			's': search_query
		}
		$j.getJSON( this.provider.api_url + '?callback=?', request, function( data ) {
			_this.addResults( data );
			_this.loading = false;
		} );
	},
	
	/**
	* Adds results from kaltura api data response object 
	*
	* @param {Object} data Fliker response data
	*/
	addResults:function( data ) {	
		var _this = this;
		this.provider_libs = { };		
		
		if ( data ) {
			// set result info: 
			//this.num_results = data.photos.total;
			//if ( this.num_results > this.provider.offset + this.provider.limit ) {
			//	this.more_results = true;
			//}
			for ( var resource_id in data ) {
				var result = data[ resource_id ];
				
				// Update mapings: 					
				result['poster'] = result['thumbnail'];		
				result['pSobj'] = _this;
				
				if( !result['titleKey'] && result['src'] ){
					result['titleKey'] = 'File:' + result['src'].split('/').pop();
				}											
				_this.resultsObj[ resource_id ] = result;
				
			}
		}
	},
	
	/**
	* Return image transform via callback
	* Maps the image request to the proper search library helper
	*
	* @param {Object} resource Resource object
	* @param {Number} size Requested size
	* @param {Function} callback Callback function for image resource
	*/ 
	getImageObj: function( resource, size, callback ) {		
		var _this = this;		
		this.getSerachLib( resource.content_provider_id, function( searchLib ){
			searchLib.getImageObj( resource, size, callback );
		});				
	},
	
	/**
	* Get and load provider via id 
	* @param {String} provider_id The id of the content provider
	* @param {Function} callback Function to call once provider search lib is loaded 
	*	callback is passed the search object 						 
	*/ 
	getSerachLib: function( provider_id, callback ){
		var _this = this;
		// Check if we already have the library loaded: 
		if( this.searchLibs[ provider_id ] ){
			callback (  this.searchLibs[ provider_id ] );				
			return ;
		}	
		// Else load the provider lib:
		var provider = this.rsd.content_providers [ provider_id ];
		mw.load( provider.lib + 'Search', function(){
			//Set up the search lib options			
			var options = {			
				'provider': provider,
				// Same remote search driver as KalturaSearch
				'rsd': _this.rsd
			}
			_this.searchLibs[ provider_id ] = new window[ provider.lib + 'Search' ]( options );
			callback ( _this.searchLibs[ provider_id ] );
		} );
	}
	

	
}
