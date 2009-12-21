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
	* Get search results from the api query.
	*
	* @param {String} search_query Text search string
	*/
	getSearchResults: function( search_query ) {
	
		// call parent for common initialisation:  
		this.parent_getSearchResults();
		
		var _this = this;
		mw.log( 'archive_org:getSearchResults for:' + search_query );
		
		
		// For now force (Ogg video) & url based license
		search_query += ' format:(Ogg video)';
		search_query += ' licenseurl:(http\\:\\/\\/*)';
		
		// Build the request Object
		var request = {
			'q': search_query, // just search for video atm
			'fl':"description,title,identifier,licenseurl,format,license,thumbnail",
			'wt':'json',
			'rows' : this.provider.limit,
			'start' : this.provider.offset
		}
		$j.getJSON( this.provider.api_url + '?json.wrf=?', request, function( data ) {
			_this.addResults( data );
			_this.loading = false;
		} );
	},
	/**
	* Adds the search results to the local resultsObj
	* 
	* @param {Object} data Api result data
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
					'titleKey'	 :  resource.identifier + '.ogv',
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
	*
	* @param {Object} resource Resrouce to add metadata to.
	* @param {Function} callbcak Function called once extra metadata is added.
	*/ 
	addResourceInfoCallback:function( resource, callback ) {
		var _this = this;
		$j.getJSON( 
			_this.downloadUrl + resource.resourceKey + '/format=Ogg+video&callback=?',  
			{ 'avinfo' : 1 }, 
			function( data ) {
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
	* @param {Object} resrouce Resource to get embed HTML from.
	* @parma {Object} options Options for the embeding.
	*/	
	getEmbedHTML: function( resource , options ) {
		mw.log( 'getEmbedHTML:: ' + resource.poster );
		if ( !options )
			options = { };
		var id_attr = ( options['id'] ) ? ' id = "' + options['id'] + '" ': '';
		if ( resource.duration ) {
			var src = resource.src + '?t=0:0:0/' + mw.seconds2npt( resource.duration );
		} else {
			var src = resource.src;
		}
		if ( resource.mime == 'application/ogg' || resource.mime == 'audio/ogg' || resource.mime == 'video/ogg' ) {
			return '<video ' + id_attr + ' src="' + src + '" poster="' + resource.poster + '" type="video/ogg"></video>';
		}
	}
}
