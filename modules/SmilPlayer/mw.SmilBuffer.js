/**
* Handles buffer information for the smilObject
*/

mw.SmilBuffer = function( smilObject ){
	return this.init( smilObject );
}

mw.SmilBuffer.prototype = {
	
	// Stores currently loading assets. 
	assetLoadingSet: [],
	
	// A queue for asset loaded callbacks 
	assetLoadingCallbacks : [],
	
	// Stores the percentage loaded of active video elements
	videoLoadedPercent: {},
	
	// Stores the previous percentage buffered ( so we know what elements to check )
	prevBufferPercent : 0,
	
	/**
	* Constructor:
	*/ 
	init: function( smilObject ) {
		this.smil = smilObject;
	},
	
	/**
	 * Get the buffered percent 
	 */
	getBufferedPercent: function(){
		var _this = this;
		
		// If we already have 100% return directly
		if( this.prevBufferPercent == 1 ) {
			return 1;
		}
		
		// Search for elements from the prevBufferPercent + callbackRate
		var bufferedStartTime = ( this.prevBufferPercent * _this.smil.getDuration() );
		
		mw.log("getBufferedPercent:: bufferedStartTime: " + bufferedStartTime );
		
		// Average the buffers of clips in the current time range: 
		var bufferCount =0;
		var totalBufferPerc = 0;		
		var minTimeBuffred = false;
		var maxTimeBuffred = 0;
		this.smil.getBody().getElementsForTime( bufferedStartTime, function( smilElement ){				
			var relativeStartTime = $j( smilElement ).data ( 'parentStartOffset' );
			var nodeBufferedPercent =  _this.getElementPercentLoaded( smilElement );
			mw.log(" asset:" +  $j( smilElement ).attr('id') + ' is buffred:' + nodeBufferedPercent );			
			// Update counters: 
			bufferCount ++;
			totalBufferPerc += nodeBufferedPercent;
			
			var nodeBuffredTime = relativeStartTime + 
				( _this.smil.getBody().getNodeDuration( smilElement ) * nodeBufferedPercent );
			
			mw.log( "nodeBuffredTime:: " + nodeBuffredTime);
			
			// Update min time buffered. 			
			if( minTimeBuffred === false || nodeBuffredTime < minTimeBuffred ) {
				minTimeBuffred = nodeBuffredTime;
			}
			
			// Update the max time buffered
			if( nodeBuffredTime > maxTimeBuffred ){
				maxTimeBuffred = nodeBuffredTime;
			}
		});
		mw.log("getBufferedPercent:: totalBufferPerc: " + totalBufferPerc + ' buffer count: ' + bufferCount );
		
		// Check if all the assets are full for this time rage: 
		if( totalBufferPerc == bufferCount ) {
			if( maxTimeBuffred == 0 )
				return 0;
			this.prevBufferPercent = maxTimeBuffred / _this.smil.getDuration();			
			// update the prevBufferPercent and recurse
			return this.getBufferedPercent();
		}
		// update the previous buffer and return the minimum in range buffer percent 
		this.prevBufferPercent = minTimeBuffred / _this.smil.getDuration();
		mw.log(" percent buffred is: " + this.prevBufferPercent);
		return this.prevBufferPercent;
	},
	
	/**
	 * Start loading every asset in the smil sequence set.  
	 */
	startBuffer: function( ){
		this.continueBufferLoad( 0 );
	},
	
	/**
	 * continueBufferLoad the buffer 
	 */
	continueBufferLoad: function( bufferTime ){
		var _this = this;
		// Get all active elements for requested bufferTime
		this.smil.getBody().getElementsForTime( bufferTime, function( smilElement){
			// Start loading active assets 
			_this.bufferElement( smilElement );
		})
		setTimeout( function(){
			if( _this.getBufferedPercent() == 1 ){
				mw.log( "continueBufferLoad:: done loading buffer "); 
				return ;
			}
			// get the percentage buffered, translated into buffer time and call continueBufferLoad with a timeout
			var timeBuffered = _this.getBufferedPercent() * _this.smil.getDuration();
			mw.log( 'ContinueBufferLoad::Timed buffered: ' + timeBuffered );
			_this.continueBufferLoad( timeBuffered + _this.smil.embedPlayer.monitorRate );
		}, 2000 /* this.smil.embedPlayer.monitorRate */ );
	},
	
	/**
	 * Start buffering an target element
	 */
	bufferElement: function( smilElement ){
		var _this = this;
		
		// If the element is not already in the DOM add it as an invisible element 
		if( $j( '#' + this.smil.getAssetId( smilElement ) ).length == 0 ){
			// Draw the element
			_this.smil.getLayout().drawElement( smilElement );
			// hide the element ( in most browsers this should not cause a flicker 
			// because dom update are enforced at a given framerate
			_this.smil.getLayout().hideElement( smilElement );
		}
		//alert('should have added: ' + $j( '#' + this.smil.getAssetId( smilElement ) ).get(0));
		// Start "loading" the asset (for now just video ) but in theory we could set something up with large images
		switch( this.smil.getRefType( smilElement ) ){
			case 'video':
				// xxx note we may want to "seek" to support offsets 
				$j( '#' + this.smil.getAssetId( smilElement ) ).get(0).load();		
			break;
		}
	},
	
	/**	
	 * Get the percentage of an element that is loaded. 
	 */	
	getElementPercentLoaded: function( smilElement ){
		switch( this.smil.getRefType( smilElement ) ){
			case 'video':
				return this.getVideoPercetLoaded( smilElement );
			break;
		}
		// by default return 1 ( for text and images ) 
		return 1;
	},
	
	/**
	 * Get the percentage of a video asset that has been loaded 
	 */
	getVideoPercetLoaded: function ( smilElement ){
		var _this = this;
		var assetId = this.smil.getAssetId( smilElement );
		var $vid = $j( '#' + assetId );
		
		// if the asset is not in the DOM return zero: 
		if( $vid.length == 0 ){
			return 0 ;
		}
		// check if 100% has already been loaded: 
		if( _this.videoLoadedPercent[ assetId ] == 1 ){
			return 1;
		}
		
		// Check if we have a loader registered 
		if( !this.videoLoadedPercent[ assetId ] ){
			// firefox loading based progress indicator: 
			$vid.bind('progress', function( e ) {			
				if( e.loaded && e.total ) {
					_this.videoLoadedPercent[assetId] =   e.loaded / e.total;
				}
			})	
		}
		
		// Check for buffered attribute ( not all browsers support the progress event ) 
		if( vid && vid.buffered && vid.buffered.end && vid.duration ) {		
			_this.videoLoadedPercent[ assetId ] = (vid.buffered.end(0) / vid.duration);
		}
		
		// Return the updated videoLoadedPercent 
		return _this.videoLoadedPercent[ assetId ];
	},
	
	
	/**
	* Add a callback for when assets loaded and "ready"  
	*/
	addAssetsReadyCallback: function( callback ) {
		mw.log( "addAssetsReadyCallback:: " + this.assetLoadingSet.length  );
		// if no assets are "loading"  issue the callback directly: 
		if ( this.assetLoadingSet.length == 0 ){
			if( callback )
				callback();
			return ;
		}
		// Else we need to add a loading callback ( will be called once all the assets are ready )
		this.assetLoadingCallbacks.push( callback );
	},

	/**
	* Add a asset to the loading set:
	* @param assetId The asset to add to loading set
	*/
	addAssetLoading: function( assetId ) {
		if( $j.inArray( assetId, this.assetLoadingSet ) !== -1 ){
			mw.log("Possible Error: assetId already in loading set: " + assetId ) ;
			return ;
		}
		this.assetLoadingSet.push( assetId );
	},
	
	/**
	* Asset is ready, check queue and issue callback if empty 
	*/
	assetReady: function( assetId ) {
		for( var i=0; i <  this.assetLoadingSet.length ; i++ ){			
			if( assetId == this.assetLoadingSet[i] ) {
				 this.assetLoadingSet.splice( i, 1 );
			}
		}
		if( this.assetLoadingSet.length ===  0 ) {
			while( this.assetLoadingCallbacks.length ) {
				this.assetLoadingCallbacks.shift()();
			}
		}
	},
	
	/**
	 * Check if we can play a given time 
	 * @return {boolean} True if the time can be played, false if we need to buffer
	 */
	canPlayTime: function( smilElement, time ){
		switch( this.smil.getRefType( smilElement ) ){
			case 'video':
				return this.canPlayVideoTime(  smilElement, time );				
			break;
		}
		// by default return true 
		return true;
	},
	
	/**
	 * Register a video loading progress indicator and check the time against the requested time 
	 */
	canPlayVideoTime: function( smilVideoElement, time ){
		var _this = this;
		var assetId = this.smil.getAssetId( smilVideoElement );
		var $vid = $j( '#' + assetId );
		var vid = $j( '#' + assetId ).get( 0 );
		// if the video element is not in the dom its not ready: 
		if( $vid.length == 0 || !$vid.get(0) ){
			return false;
		}		
		/* if we have no metadata return false */
		if( $vid.attr('readyState') == 0 ){
			return false;
		}
		/* if we are asking about a time close to the current time use ready state */
		if( Math.abs( $vid.attr('currentTime') - time ) < 1 ){
			// also see: http://www.whatwg.org/specs/web-apps/current-work/multipage/video.html#dom-media-have_metadata
			if( $vid.attr('readyState') > 2 ){
				return true;
			}
		}
		// Check if _this.videoLoadedPercent is in range of duration
		// xxx might need to take into consideration startOfsset 
		if( _this.getVideoPercetLoaded( smilVideoElement ) > vid.duration / time ){
			return true;
		}
		// not likely that the video is loaded for the requested time, return false
		return false;
	},
	
	videoBufferSeek: function ( smilElement, seekTime, callback ){
		var _this = this;
		// Get the video target: 
		var $vid = $j ( '#' + this.smil.getAssetId( smilElement ) );
		
		// Add the asset to the loading set
		_this.addAssetLoading( $vid.attr('id' ) );
			
		var runSeekCallback = function(){
			// Add a seek binding
			$vid.unbind( 'seeked' ).bind( 'seeked', function(){
				_this.assetReady( $vid.attr('id' ) );
				if( callback ) {
					callback();
				}
			});
			$vid.attr('currentTime', seekTime );
		}
		
		// Read the video state: http://www.w3.org/TR/html5/video.html#dom-media-have_nothing
		if( $vid.attr('readyState') == 0 /* HAVE_NOTHING */ ){ 
			// Check that we have metadata ( so we can issue the seek ) 
			$vid.unbind( 'loadedmetadata' ).bind( 'loadedmetadata', function(){
				runSeekCallback();
			} );
		}else { 
			// Already have metadata directly issue the seek with callback
			runSeekCallback();
		}		
	}
}