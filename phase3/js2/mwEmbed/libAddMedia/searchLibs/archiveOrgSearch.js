/*
* Archive.org Search
* 
* archive.org uses the solr engine: 
* more about solr here:  
* http://lucene.apache.org/solr/
*/

var archiveOrgSearch = function ( iObj ) {
	return this.init( iObj );
}
archiveOrgSearch.prototype = {
	// Archive.org constants: 
	downloadUrl : 'http://www.archive.org/download/',
	detailsUrl : 'http://www.archive.org/details/',
	/*
	* Inititalize the archiveOrgSearch class.
	* archiveOrgSearch inherits the baseSearch class 
	*/
	init:function( options ) {		
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
	* Get search results from the api query
	*/
	getSearchResults:function() {
		// call parent: 
		this.parent_getSearchResults();
		var _this = this;
		js_log( 'f:getSearchResults for:' + $j( '#rsd_q' ).val() );
		
		// Build the query var
		var q = $j( '#rsd_q' ).val();
		
		// For now force (Ogg video) & url based license
		q += ' format:(Ogg video)';
		q += ' licenseurl:(http\\:\\/\\/*)';
		
		// Build the request Object
		var reqObj = {
			'q': q, // just search for video atm
			'fl':"description,title,identifier,licenseurl,format,license,thumbnail",
			'wt':'json',
			'rows' : this.provider.limit,
			'start' : this.provider.offset
		}
		do_api_req( {
			'data':reqObj,
			'url':this.provider.api_url,
			'jsonCB':'json.wrf'
			}, function( data ) {
				_this.addResults( data );
				_this.loading = false;
			}
		);
	},
	/**
	* Adds the search results to the local resultsObj
	*/
	addResults:function( data ) {
		var _this = this;
		if ( data.response && data.response.docs ) {
			// Set result info: 
			this.num_results = data.response.numFound;
		
			for ( var resource_id in data.response.docs ) {
				var resource = data.response.docs[resource_id];
				var resource = {
					// @@todo we should add .ogv or oga if video or audio:
					'titleKey'	 :  resource.identifier + '.ogg',
					'resourceKey':  resource.identifier,
					'link'		 : _this.detailsUrl + resource.identifier,
					'title'		 : resource.title,
					'poster'	 : _this.downloadUrl + resource.identifier + '/format=thumbnail',
					'poster_ani' : _this.downloadUrl + resource.identifier + '/format=Animated+Gif',
					'thumbwidth' : 160,
					'thumbheight': 110,
					'desc'		 : resource.description,
					'src'		 : _this.downloadUrl + resource.identifier + '/format=Ogg+video',
					'mime'		 : 'application/ogg',
					// Set the license: (rsd is a pointer to the parent remoteSearchDriver )		 
					'license'	 : this.rsd.getLicenseFromUrl( resource.licenseurl ),
					'pSobj'		 :_this
					
				};
				this.resultsObj[ resource_id ] = resource;
			}
		}
	},
	/**
	* Get media metadata via a archive.org special entry point "avinfo"
	*/ 
	addResourceInfoCallback:function( resource, callback ) {
		var _this = this;
		do_api_req( {
			'data': { 'avinfo' : 1 },
			'url':_this.downloadUrl + resource.resourceKey + '/format=Ogg+video'
		}, function( data ) {
			if ( data['length'] )
				resource.duration = data['length'];
			if ( data['width'] )
				resource.width = data['width'];
			if ( data['height'] )
				resource.height = data['height'];
								   
			callback();
		} );
	},
	
	/**
	* Returns html to embed a given result Object ( resource ) 
	*/	
	getEmbedHTML: function( resource , options ) {
		js_log( 'getEmbedHTML:: ' + resource.poster );
		if ( !options )
			options = { };
		var id_attr = ( options['id'] ) ? ' id = "' + options['id'] + '" ': '';
		if ( resource.duration ) {
			var src = resource.src + '?t=0:0:0/' + seconds2npt( resource.duration );
		} else {
			var src = resource.src;
		}
		if ( resource.mime == 'application/ogg' || resource.mime == 'audio/ogg' || resource.mime == 'video/ogg' ) {
			return '<video ' + id_attr + ' src="' + src + '" poster="' + resource.poster + '" type="video/ogg"></video>';
		}
	}
}
