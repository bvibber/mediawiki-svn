/**
 * Object to attach to a file name input, to be run on its change() event
 * Largely derived from wgUploadWarningObj in old upload.js
 * Perhaps this could be a jQuery ext
 * @param options   dictionary of options 
 *		selector  required, the selector for the input to check
 * 		processResult   required, closure to execute on results. accepts an object with the following fields
 *			isUnique: boolean
 *			img: thumbnail image (supplied if not unique)
 *			href: the url of the full image
 *			title: normalized title of file
 * 		spinner   required, closure to execute to show progress: accepts true to start, false to stop
 * 		apiUrl    optional url to call for api. falls back to local api url
 * 		delay     optional how long to delay after a change in ms. falls back to configured default
 *		preprocess optional: function to apply to the contents of selector before testing
 */ 
mw.DestinationChecker = function( options ) {

	var _this = this;
	_this.selector = options.selector;		
	_this.spinner = options.spinner;
	_this.processResult = options.processResult;
	
	if (options.apiUrl) {
		_this.apiUrl = options.apiUrl;
	} else {
		_this.apiUrl = mw.getLocalApiUrl();
	}
	
	if (options.preprocess) {
		_this.preprocess = options.preprocess;
	} else {
		_this.preprocess = function (x) { return x };
	}

	if (options.delay) {
		_this.delay = options.delay;
	} else {
		_this.delay = 500; // ms;
	}

	$j( selector ).change( _this.change ).keypress( _this.change );

}

mw.DestinationChecker.prototype = {

	timeoutId: false,

	responseCache: {},

	/**
	 * fire when the input changes value or keypress
	 * will trigger a check of the name if the field has been idle for delay ms.
	 */	
	change: function() {
		var _this = this;

		// if we changed before the old timeout ran, clear that timeout.
		if ( _this.timeoutId ) {
			window.clearTimeout( this.timeoutId );
		}

		// and start another, hoping this time we'll be idle for delay ms.	
		_this.timeoutId = window.setTimeout( 
			function() { _this.checkUnique() },
			_this.delay 
		);

	},

	/**
	 * Async check if a filename is unique. Can be attached to a field's change() event
	 * This is a more abstract version of AddMedia/UploadHandler.js::doDestCheck
	 */
	checkUnique: function() {
		
		var found = false;
		var name = _this.preprocess( $j(_this.input).val() );
		
		if ( _this.responseCache[name] !== undefined ) {
			_this.doResult( name, _this.responseCache[name] );
			return;
		} 

		// set the spinner to spin
		_this.spinner( true );
		
		// Setup the request
		var request = {
			'titles': 'File:' + name,
			'prop':  'imageinfo',
			'iiprop': 'url|mime|size',
			'iiurlwidth': 150
		};

		// Do the destination check ( on the local wiki )
		mw.getJSON( _this.apiUrl, request, function( data ) {			
			// Remove spinner
			_this.spinner( false );
			
			if ( !data || !data.query || !data.query.pages ) {
				// Ignore a null result
				mw.log(" No data in checkUnique result")
				return;
			}

			var result = undefined;

			if ( data.query.pages[-1] ) {
				// No conflict found; this file name is unique
				mw.log(" No pages in checkUnique result");
				result = { unique: true };

			} else {

				for ( var page_id in data.query.pages ) {
					if ( !data.query.pages[ page_id ].imageinfo ) {
						continue;
					}

					// Conflict found, this filename is NOT unique
					mw.log( " conflict! " );

					if ( data.query.normalized ) {
						var ntitle = data.query.normalized[0].to;
					} else {
						var ntitle = data.query.pages[ page_id ].title
					}

					result = {
						unique: false,	
						img: data.query.pages[ page_id ].imageinfo[0],
						title: ntitle,
						href : img.descriptionurl
					};

					break;
				}
			}

			if ( result !== undefined) {
				_this.cachedResult[name] = result;
				_this.processResult( result );
			}
		}
	}

};


/** 
 * jQuery extension to make a field upload-checkable
 */
( function ( $ ) {
	$.fn.destinationChecked = function( options ) {
		var _this = this;
		options.selector = _this;
		new UploadFileNameChecker( options );
		return _this;
	}; 
} )( jQuery );
