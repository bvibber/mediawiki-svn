/*
 * Kaltura aggregated search:  
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
	getProviderResults: function( search_query, callback ) {
		var _this = this;
		
		// setup the flickr request: 
		var request = {
			's': search_query,
			'page': this.provider.offset/this.provider.limit + 1
		}
		mw.log( "Kaltura::getProviderResults query: " + request['s'] + " page: " + request['page']);
		$j.getJSON( this.provider.api_url + '?callback=?', request, function( data ) {
			_this.addResults( data );
			callback( 'ok' );
		} );
	},
	
	/**
	* Adds results from kaltura api data response object 
	*
	* @param {Object} response data
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
			
			// Display option for more results as long as results are coming in
			this.more_results = ( data.length == this.limit )
			
			for ( var resource_id in data ) {
				
				var result = data[ resource_id ];					
				// Update mappings: 					
				result['poster'] = result['thumbnail'];		
				result['pSobj'] = _this;
				result['link'] = result[ 'item_details_page' ];
				
				//@@todo this should be refactored per search library 
				//or gennerated at request time for mediaWiki  
				var ext = this.getMimeExtension( result['mime'] );				
				result['titleKey'] = 'File:' + result['title'] + '.' + ext;				
				
				this.num_results++;
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
	/*
	* Get extra resource info via a library specific callback 
	* NOTE: this info should be included in the original kaltura search results
	*/
	addResourceInfoCallback: function( resource, callback ){
		mw.log('Kaltura: addResourceInfoCallback');
		this.getSerachLib( resource.content_provider_id, function( searchLib ){
			searchLib.addResourceInfoCallback( resource, callback );
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
