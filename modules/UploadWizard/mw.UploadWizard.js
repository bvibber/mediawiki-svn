mw.addMessages( {
	"mwe-upwiz-tab-file": "Step 1",
	"mwe-upwiz-tab-details": "Step 2",
	"mwe-upwiz-tab-thanks": "Step 3",
	"mwe-upwiz-intro": "Introductory text (short)",
	"mwe-upwiz-select-files": "Select files:",
	"mwe-upwiz-add-file-n": "Add another file",
	"mwe-upwiz-add-file-0": "Add a file",
	"mwe-upwiz-browse": "Browse...",
	"mwe-upwiz-transported": "OK",
	"mwe-upwiz-click-here": "Click here to select a file",
	"mwe-upwiz-uploading": "uploading...",
	"mwe-upwiz-remove-upload": "Remove this file from the list of files to upload",
	"mwe-upwiz-remove-description": "Remove this description",
	"mwe-upwiz-upload": "Upload",
	"mwe-upwiz-upload-count": "$1 of $2 files uploaded",
	"mwe-upwiz-progressbar-uploading": "uploading",
	"mwe-upwiz-remaining": "$1 remaining",
	"mwe-upwiz-intro-details": "Thank you for uploading your works! Now we need some basic information in order to complete your upload.",
	"mwe-upwiz-source-ownwork": "They are entirely your own work.",
	"mwe-upwiz-source-ownwork-assert": "I, $1, the copyright holder of this work, hereby grant anyone the right to use these works for any purpose, as long as they credit me and share derivative work under the same terms.",
	"mwe-upwiz-source-ownwork-assert-custom": "I, $1, the copyright holder of this work, hereby publish these works under the following license(s):",
	"mwe-upwiz-source-ownwork-assert-note": "This means you release your work under a double Creative Commons Attribution ShareAlike and GFDL license.",
	"mwe-upwiz-source-permission": "Their author gave you explicit permission to upload them",
	"mwe-upwiz-source-thirdparty": "They come from a website",
	"mwe-upwiz-source-thirdparty-intro" : "Please enter the address where you found each file.",
	"mwe-upwiz-source-thirdparty-custom-intro" : "If all files have the same source, author, and copyright status, you may enter them only once for all of them.",
	"mwe-upwiz-source-thirdparty-license" : "The copyright holder of these works published them under the following license(s):",
	"mwe-upwiz-source-thirdparty-accept": "OK",
	"mwe-upwiz-source-custom": "Did you know? You can <a href=\"$1\">customize</a> the default options you see here.",
	"mwe-upwiz-more-options": "more options...",
	"mwe-upwiz-fewer-options": "fewer options...",
	"mwe-upwiz-desc": "Description in",
	"mwe-upwiz-desc-add-n": "add a description in another language",
	"mwe-upwiz-desc-add-0": "add a description",
	"mwe-upwiz-title": "Title",
	"mwe-upwiz-categories-intro": "Help people find your works by adding categories",
	"mwe-upwiz-categories-another": "Add other categories",
	"mwe-upwiz-previously-uploaded": "This file was previously uploaded to $1 and is already available <a href=\"$2\">here</a>.",
	"mwe-upwiz-about-this-work": "About this work",
	"mwe-upwiz-media-type": "Media type",
	"mwe-upwiz-date-created": "Date created",
	"mwe-upwiz-location": "Location",
	"mwe-upwiz-copyright-info": "Copyright information",
	"mwe-upwiz-author": "Author",
	"mwe-upwiz-license": "License",
	"mwe-upwiz-about-format": "About the file",
	"mwe-upwiz-autoconverted": "This file was automatically converted to the $1 format",
	"mwe-upwiz-filename-tag": "File name:",
	"mwe-upwiz-other": "Other information",
	"mwe-upwiz-other-prefill": "Free wikitext field",
	"mwe-upwiz-showall": "show all",
	"mwe-upwiz-source": "Source",
	"mwe-upwiz-macro-edit-intro": "Please add some descriptions and other information to your uploads, and then press 'Update descriptions'.",
	"mwe-upwiz-macro-edit": "Update descriptions",
	"mwe-upwiz-thanks-intro": "Thanks for uploading your works! You can now use your files on a Wikipedia article or link to them from elsewhere on the web.",
	"mwe-upwiz-thanks-link": "This file is now available at <b><tt>$1</tt></b>.",
	"mwe-upwiz-thanks-wikitext": "To use it in a Wikipedia article, copy this text into an article: ",
	"mwe-upwiz-thanks-url": "To link to it in HTML, copy this HTML code: ",

	"mwe-upwiz-upload-error-bad-filename-extension": "This wiki does not accept filenames with the extension \"$1\".",
	"mwe-upwiz-upload-error-duplicate": "This file was previously uploaded to this wiki.",
	"mwe-upwiz-upload-error-stashed-anyway": "Post anyway?",
	"mwe-upwiz-ok": "OK",
	"mwe-upwiz-cancel": "Cancel",
	"mwe-upwiz-change": "(change)",

	/* copied from mw.UploadHandler :(  */
	"mwe-fileexists" : "A file with this name exists already. Please check <b><tt>$1<\/tt><\/b> if you are not sure if you want to replace it.",
	"mwe-thumbnail-more" : "Enlarge",
	"mwe-upwiz-overwrite" : "Replace the file"
} );



mw.ProgressBar = function( selector ) {
	var _this = this;
	_this.progressBarDiv = $j('<div></div>')
				.addClass("mwe-upwiz-progress-bar")
				.progressbar( { value: 0 } );

	_this.timeRemainingDiv = $j('<div></div>').addClass("mwe-upwiz-etr");

	_this.countDiv = $j('<div></div>').addClass("mwe-upwiz-count");

	_this.beginTime = undefined;
	
	$j( selector ).html( 
		$j('<div />').addClass( 'mwe-upwiz-progress' )
			.append( $j( '<div></div>' )
				.append( _this.progressBarDiv )
				.append( _this.timeRemainingDiv ) )
			.append( $j( _this.countDiv ) )
	);
			
};

mw.ProgressBar.prototype = {

	/**
	 * sets the beginning time (useful for figuring out estimated time remaining)
	 * if time parameter omitted, will set beginning time to now
	 *
	 * @param time  optional; the time this bar is presumed to have started (epoch milliseconds)
	 */ 
	setBeginTime: function( time ) {
		var _this = this;
		_this.beginTime = time ? time : ( new Date() ).getTime();
	},

	/**
	 * sets the total number of things we are tracking
	 * @param total an integer, for display e.g. uploaded 1 of 5, this is the 5
	 */ 
	setTotal: function(total) {
		var _this = this;
		_this.total = total;
	},	

	/**
	 * Show overall progress for the entire UploadWizard
	 * The current design doesn't have individual progress bars, just one giant one.
	 * We did some tricky calculations in startUploads to try to weight each individual file's progress against 
	 * the overall progress.
	 * @param fraction the amount of whatever it is that's done whatever it's done
	 */
	showProgress: function( fraction ) {
		var _this = this;

		_this.progressBarDiv.progressbar( 'value', parseInt( fraction * 100 ) );

		var remainingTime;
		if (_this.beginTime == null) {
			remainingTime = 0;
		} else {	
			remainingTime = _this.getRemainingTime( fraction );
		}

		if ( remainingTime !== null ) {
			_this.timeRemainingDiv
				.html( gM( 'mwe-upwiz-remaining', mw.seconds2npt(parseInt(remainingTime / 1000)) ) );
		}
	},

	/**
	 * Calculate remaining time for all uploads to complete.
	 * 
	 * @param fraction	fraction of progress to show
	 * @return 		estimated time remaining (in milliseconds)
	 */
	getRemainingTime: function ( fraction ) {
		var _this = this;
		if ( _this.beginTime ) {
			var elapsedTime = ( new Date() ).getTime() - _this.beginTime;
			if ( fraction > 0.0 && elapsedTime > 0 ) { // or some other minimums for good data
				var rate = fraction / elapsedTime;
				return parseInt( ( 1.0 - fraction ) / rate ); 
			}
		}
		return null;
	},


	/**
	 * Show the overall count as we upload
	 * @param count  -- the number of items that have done whatever has been done e.g. in "uploaded 2 of 5", this is the 2
	 */
	showCount: function( count ) {
		var _this = this;
		_this.countDiv.html( gM( 'mwe-upwiz-upload-count', [ count, _this.total ] ) );
	}


};



//mw.setConfig('uploadHandlerClass', mw.MockUploadHandler); // ApiUploadHandler?

// available licenses should be a configuration of the MediaWiki instance,
// not hardcoded here.
// but, MediaWiki has no real concept of a License as a first class object -- there are templates and then specially - parsed 
// texts to create menus -- hack on top of hacks -- a bit too much to deal with ATM
mw.UploadWizardLicenseInput = function( div, values ) {
	var _this = this;
	var c = mw.UploadWizardLicenseInput.prototype.count++;

	// XXX get these for real
	_this.licenses = {
		pd:          { template: 'pd', text: 'Public Domain' },
		cc0:         { template: 'cc0', text: 'Creative Commons Zero waiver' },
		cc_by_30:    { template: 'cc-by-30', text: 'Creative Commons Attribution 3.0' },
		cc_by_sa_30: { template: 'cc-by-sa-30', text: 'Creative Commons Attribution ShareAlike 3.0' },
		gfdl:	     { template: 'gfdl', text: 'GFDL (GNU Free Documentation License)' }
	};

	$div = $j( div );
	$j.each( _this.licenses, function( key, data ) {
		var id = 'license_' + key + '_' + c;
		data.input = $j( '<input />' ).attr( { id: id, type: 'checkbox', value: key } ).get(0);
		$div.append( 
			data.input,
			$j( '<label />' ).attr( { 'for': id } ).html( data.text ),
			$j( '<br/>' )
		);
	} );

	if ( values ) {
		_this.setValues( values );
	}
};

mw.UploadWizardLicenseInput.prototype = {
	count: 0,

	/**
	 * Get wikitext representing the licenses selected in the license object
	 * @return wikitext of all applicable license templates.
	 */
	getWikiText: function() {
		var _this = this;
		var wikiText = '';
		$j.each ( _this.licenses, function( key, data ) {
			if (data.input.checked) {
				wikiText += "{" + data.template + "}\n";
			}		
		} );
		return wikiText;
	},

	/**
	 * Sets the value(s) of a license input. Missing values are set to false
	 * @param object of license-key to boolean values, e.g. { cc_by_sa_30: true, gfdl: true }
	 */
	setValues: function( licenseValues ) {
		var _this = this;
		$j.each( _this.licenses, function( key, data ) {
			if ( !! licenseValues[key] ) {
				$j( _this.licenses[key].input ).attr( { 'checked' : 1 } );
			}
		} );	
	},

	/**
	 * Set the default configured licenses - should change per wiki
	 */
	setDefaultValues: function() {
		var _this = this;
		var values = {};
		$j.each( mw.getConfig('defaultLicenses'), function( i, license ) {
			values[license] = true;
		} );
		_this.setValues( values );
	},

	/**
	 * Gets which values are set 
	 * Always returns the full set of licenses, even if most are false
	 * @return object of object of license-key to boolean values, e.g. { cc_by: false; cc_by_sa_30: true, gfdl: true }
	 */ 
	getValues: function() {
		var _this = this;
		var values = {};
		$j.each( _this.licenses, function( key, data ) {
			values[key] = $j( _this.licenses[key].input ).is( ':checked' ); 
		} );
		return values;
	}

};


/**
 * Represents the upload -- in its local and remote state. (Possibly those could be separate objects too...)
 * This is our 'model' object if we are thinking MVC. Needs to be better factored, lots of feature envy with the UploadWizard
 * states:
 *   'new' 'transporting' 'transported' 'details' 'submitting-details' 'complete'  
 * should fork this into two -- local and remote, e.g. filename
 */
mw.UploadWizardUpload = function() {
	var _this = this;
	_this.state = 'new';
	_this.transportWeight = 1;  // default
	_this.detailsWeight = 1; // default
	_this._thumbnails = {};
	_this.imageinfo = {};
	_this.title = undefined;
	_this.filename = undefined;
	_this.originalFilename = undefined;
	_this.mimetype = undefined;
	_this.extension = undefined;
		
	// details 		
	_this.details = new mw.UploadWizardDetails( _this, $j( '#mwe-upwiz-macro-files' ));
	_this.ui = new mw.UploadWizardUploadInterface( _this );

	// handler -- usually ApiUploadHandler
	// _this.handler = new ( mw.getConfig( 'uploadHandlerClass' ) )( _this );
	// _this.handler = new mw.MockUploadHandler( _this );
	_this.handler = new mw.ApiUploadHandler( _this );
};

mw.UploadWizardUpload.prototype = {

	/**
 	 * start
	 */
	start: function() {
		var _this = this;
		_this.setTransportProgress(0.0);
		_this.handler.start();	
		_this.ui.start();
	},


	/**
	 * remove
	 */
	remove: function() {
		var _this = this;
		$j( _this.ui.div ).remove();
		$j( _this.details.div ).remove();
		$j( _this ).trigger( 'removeUpload' );
	},

	/**
	 * Wear our current progress, for observing processes to see
 	 * @param fraction
	 */
	setTransportProgress: function ( fraction ) {
		var _this = this;
		_this.state = 'transporting';
		_this.transportProgress = fraction;
		$j( _this ).trigger( 'transportProgress' );
	},

	/**
	 * To be executed when an individual upload finishes. Processes the result and updates step 2's details 
	 * @param result	the API result in parsed JSON form
	 */
	setTransported: function( result ) {
		var _this = this;
		_this.state = 'transported';
		_this.transportProgress = 1;
		$j( _this ).trigger( 'transported' );

		if ( result.upload && result.upload.imageinfo && result.upload.imageinfo.descriptionurl ) {
			// success
			_this.extractUploadInfo( result );	
			_this.details.populate();
		
		} else if ( result.upload && result.upload.sessionkey ) {
			// there was a warning - type error which prevented it from adding the result to the db 
			if ( result.upload.warnings.duplicate ) {
				var duplicates = result.upload.warnings.duplicate;
				_this.details.errorDuplicate( result.upload.sessionkey, duplicates );
			}

			// and other errors that result in a stash
		} else if ( 0 /* actual failure */ ) {
			// we may want to tag or otherwise queue it as an upload to retry
		}
		
	
	},


	/**
	 * call when the file is entered into the file input
	 * get as much data as possible -- maybe exif, even thumbnail maybe
	 */
	extractLocalFileInfo: function( localFilename ) {
		var _this = this;
		if (false) {  // FileAPI, one day
			_this.transportWeight = getFileSize();
		}
		_this.extension = mw.UploadWizardUtil.getExtension( localFilename );
		// XXX add filename, original filename, extension, whatever else is interesting.
	},


	/** 
 	 * Accept the result from a successful API upload transport, and fill our own info 
	 *
	 * @param result The JSON object from a successful API upload result.
	 */
	extractUploadInfo: function( result ) {
		var _this = this;

		_this.filename = result.upload.filename;
		_this.title = "File:" + _this.filename;

		_this.extractImageInfo( result.upload.imageinfo );

	},

	/**
	 * Extract image info into our upload object 	
	 * Image info is obtained from various different API methods
	 * @param imageinfo JSON object obtained from API result.
	 */
	extractImageInfo: function( imageinfo ) {
		var _this = this;
		for ( var key in imageinfo ) {
			// we get metadata as list of key-val pairs; convert to object for easier lookup. Assuming that EXIF fields are unique.
			if ( key == 'metadata' ) {
				_this.imageinfo.metadata = {};
				if ( imageinfo.metadata && imageinfo.metadata.length ) {
					$j.each( imageinfo.metadata, function( i, pair ) {
						if ( pair !== undefined ) {
							_this.imageinfo.metadata[pair['name'].toLowerCase()] = pair['value'];
						}
					} );
				}
			} else {
				_this.imageinfo[key] = imageinfo[key];
			}
		}
		
		// we should already have an extension, but if we don't... 
		if ( _this.extension === undefined ) {
			var extension = mw.UploadWizardUtil.getExtension( _this.imageinfo.url );
			if ( !extension ) {
				if ( _this.imageinfo.mimetype ) {
					if ( mw.UploadWizardUtil.mimetypeToExtension[ _this.imageinfo.mimetype ] ) {
						extension = mw.UploadWizardUtil.mimetypeToExtension[ _this.imageinfo.mimetype ];			
					} 
				}
			}
		}
	},

	/**
	 * Supply information to create a thumbnail for this Upload. Runs async, with a callback. 
	 * It is assumed you don't call this until it's been transported.
 	 *
	 * XXX should check if we really need this second API call or if we can get MediaWiki to make us a thumbnail URL upon upload
	 *
	 * @param width - desired width of thumbnail (height will scale to match)
	 * @param callback - callback to execute once thumbnail has been obtained -- must accept object with properties of width, height, and url.
	 */
	getThumbnail: function( width, callback ) {
		var _this = this;
		if ( _this._thumbnails[ "width" + width ] !== undefined ) {
			callback( _this._thumbnails[ "width" + width ] );
			return;
		}

		var apiUrl = mw.getLocalApiUrl();

		var params = {
                        'titles': _this.title,
                        'prop':  'imageinfo',
                        'iiurlwidth': width, 
                        'iiprop': 'url'
                };

		mw.getJSON( apiUrl, params, function( data ) {
			if ( !data || !data.query || !data.query.pages ) {
				mw.log(" No data? ")
				// XXX do something about the thumbnail spinner, maybe call the callback with a broken image.
				return;
			}

			if ( data.query.pages[-1] ) {
				// XXX do something about the thumbnail spinner, maybe call the callback with a broken image.
				return;
			}
			for ( var page_id in data.query.pages ) {
				var page = data.query.pages[ page_id ];
				if ( ! page.imageinfo ) {
					// not found? error
				} else {
					var imageInfo = page.imageinfo[0];
					var thumbnail = {
						width: 	imageInfo.thumbwidth,
						height: imageInfo.thumbheight,
						url: 	imageInfo.thumburl
					}
					_this._thumbnails[ "width" + width ] = thumbnail; 
					callback( thumbnail );
				}
			}
		} );

	}
};

/**
 * Create an interface fragment corresponding to a file input, suitable for Upload Wizard.
 * @param upload
 */
mw.UploadWizardUploadInterface = function( upload ) {
	var _this = this;

	_this.upload = upload;

	// may need to collaborate with the particular upload type sometimes
	// for the interface, as well as the uploadwizard. OY.
	_this.div = $j('<div></div>').get(0);

	_this.fileInputCtrl = $j('<input size=40 class="mwe-upwiz-file" name="file" type="file"/>').get(0);

	// XXX better class for helper, we probably have a standard already
	_this.visibleFilename = $j('<div class="mwe-upwiz-visible-file helper">' + gM('mwe-upwiz-click-here') + '</div>');

	// XXX not sure if we will have a filename here -- we may want to autogenerate a "stashed" filename, 
	// with this flow
	_this.filenameCtrl = $j('<input type="hidden" name="filename" value=""/>').get(0); 

	_this.form = $j('<form class="mwe-upwiz-form"></form>')
			.append($j('<div class="mwe-upwiz-file-ctrl-container">')
				.append( _this.fileInputCtrl )
				.append( _this.visibleFilename )
			).append( _this.filenameCtrl ).get( 0 );

	_this.progressMessage = $j('<span class="mwe-upwiz-status-message" style="display: none"></span>').get(0);

	$j( _this.fileInputCtrl ).change( function() { _this.fileChanged() } );

	_this.errorDiv = $j('<div class="mwe-upwiz-upload-error" style="display: none;"></div>').get(0);

	_this.removeCtrl = $j( '<a title="' + gM( 'mwe-upwiz-remove-upload' ) 
					+ '" href="#" class="mwe-upwiz-remove">x</a>' )
				.click( function() { _this.upload.remove() } )
				.get( 0 );



	$j( _this.div ).append( _this.form )
		    .append( _this.progressMessage )
		    .append( _this.errorDiv )
		    .append( _this.removeCtrl );

	// _this.progressBar = ( no progress bar for individual uploads yet )
	// add a details thing to details
	// this should bind only to the FIRST transportProgress
	$j( upload ).bind( 'transportProgress', function(e) { _this.showTransportProgress(); e.stopPropagation() } );
	$j( upload ).bind( 'transported', function(e) { _this.showTransported(); e.stopPropagation(); } );
};


mw.UploadWizardUploadInterface.prototype = {
	/**
	 * Things to do to this interface once we start uploading
	 */
	start: function() {
		var _this = this;
		$j( _this.removeCtrl ).hide();
	},

	/**
	 * Make this interface look "busy" (i.e. spinner) without indicating a particular percentage of file uploaded.
	 * Will be useful for encoding phase of Firefogg, for example.
	 */
	busy: function() {
		var _this = this;
		// for now we implement this as looking like "100% progress"
		// e.g. an animated bar that takes up all the space
		_this.showTransportProgress( 1.0 );
	},

	/**
	 * Show progress by a fraction
	 * @param fraction	The fraction of progress. Float between 0 and 1
	 */
	showTransportProgress: function() {
		var _this = this;
		$j( _this.progressMessage ).addClass('mwe-upwiz-status-progress')
		    			   .html(gM( 'mwe-upwiz-uploading' ))
					   .show();
		// since, in this iteration of the interface, we never need to know 
		// about progress again, let's unbind

		// unbind is broken in jquery 1.4.1 -- raises exception but it still works
		try { 
			$j( _this.upload ).unbind( 'transportProgress' );
		} catch (ex) { }
		
		// update individual progress bar with fraction?
	},

	/**
	 * Execute when this upload is transported; cleans up interface. 
	 * @param result	AJAx result object
	 */
	showTransported: function() {
		var _this = this;
		$j( _this.progressMessage ).removeClass( 'mwe-upwiz-status-progress' )
					   .addClass( 'mwe-upwiz-status-transported' )
		   			   .html( gM( 'mwe-upwiz-transported' ) );
	},

	/**
	 * Run this when the value of the file input has changed. Check the file for various forms of goodness.
	 */
	fileChanged: function() {
		var _this = this;
		_this.clearErrors();
		_this.upload.extractLocalFileInfo( $j( _this.fileInputCtrl ).val() );
		if ( _this.isGoodExtension( _this.upload.extension ) ) {
			_this.updateFilename();
		} else {       
			//_this.error( 'bad-filename-extension', ext );
			alert("bad extension");
		}
	},

	/**
	 * this does two things: 
	 *   1 ) since the file input has been hidden with some clever CSS ( to avoid x-browser styling issues ), 
	 *      update the visible filename
	 *
	 *   2 ) update the filename desired when added to MediaWiki. This should be RELATED to the filename on the filesystem,
	 *      but it should be silently fixed so that it does not trigger uniqueness conflicts. i.e. if server has cat.jpg we change ours to cat_2.jpg.
	 *      This is hard to do in a scalable fashion on the client; we don't want to do 12 api calls to get cat_12.jpg. 
	 *      Ideally we should ask the SERVER for a decently unique filename related to our own. 
	 *	So, at the moment, this is hacked with a guaranteed - unique filename instead.  
	 */
	updateFilename: function() {
		var _this = this;
		var path = $j(_this.fileInputCtrl).attr('value');
		
	
		// visible filename	
		$j( _this.visibleFilename ).removeClass( 'helper' ).html( path );

		// desired filename 
		var filename = _this.convertPathToFilename( path );
		_this.upload.originalFilename = filename;
		// this is a hack to get a filename guaranteed unique.
		uniqueFilename = mw.getConfig( 'userName' ) + "_" + ( new Date() ).getTime() + "_" + filename;
		$j( _this.filenameCtrl ).attr( 'value', uniqueFilename );
		$j( _this.upload ).trigger( 'filenameAccepted' );
	},

	/**
	 * Remove any complaints we had about errors and such
	 * XXX this should be changed to something Theme compatible
	 */
	clearErrors: function() {
		var _this = this;
		$j( _this.div ).removeClass( 'mwe-upwiz-upload-error ');
		$j( _this.errorDiv ).hide().empty();
	},

	/**
	 * Show an error with the upload
	 */
	error: function() {
		var _this = this;
		var args = Array.prototype.slice.call( arguments ); // copies arguments into a real array
		var msg = 'mwe-upwiz-upload-error-' + args[0];
		$j( _this.errorDiv ).append( $j( '<p class="mwe-upwiz-upload-error">' + gM( msg, args.slice( 1 ) ) + '</p>') );
		// apply a error style to entire did
		$j( _this.div ).addClass( 'mwe-upwiz-upload-error' );
		$j( _this.errorDiv ).show();
	},

	/**
	 * Get the extension of the path in fileInputCtrl
	 * @return extension as string 
	 */
	getExtension: function() {
		var _this = this;
		var path = $j(_this.fileInputCtrl).attr('value');
		return mw.UploadWizardUtil.getExtension(path);
	},

	/**
	 * XXX this is common utility code
	 * used when converting contents of a file input and coming up with a suitable "filename" for mediawiki
	 * test: what if path is length 0 
	 * what if path is all separators
	 * what if path ends with a separator character
	 * what if it ends with multiple separator characters
	 *
	 * @param path
	 * @return filename suitable for mediawiki as string
	 */
	convertPathToFilename: function( path ) {
		if (path === undefined || path == '') {
			return '';
		}
		
 		var lastFileSeparatorIdx = Math.max(path.lastIndexOf( '/' ), path.lastIndexOf( '\\' ));
	 	// lastFileSeparatorIdx is now -1 if no separator found, or some index in the string.
		// so, +1, that is either 0 ( beginning of string ) or the character after last separator.
		// caution! could go past end of string... need to be more careful
		var filename = path.substr( lastFileSeparatorIdx + 1 );
		return mw.UploadWizardUtil.pathToTitle( filename );


	
 	},

	/**
	 * XXX this is common utility code
	 * copied because we'll probably need it... stripped from old doDestinationFill
	 * this is used when checking for "bad" extensions in a filename. 
	 * @param ext
	 * @return boolean if extension was acceptable
	 */
	isGoodExtension: function( ext ) {
		var _this = this;
		var found = false;
		var extensions = mw.getConfig('fileExtensions');
		if ( extensions ) {
			for ( var i = 0; i < extensions.length; i++ ) {
				if ( extensions[i].toLowerCase() == ext ) {
					found = true;
				}
			}
		}
		return found;
	}

};	
	
/**
 * Object that represents an indvidual language description, in the details portion of Upload Wizard
 * @param languageCode
 */
mw.UploadWizardDescription = function( languageCode ) {
	var _this = this;

	// Logic copied from MediaWiki:UploadForm.js
	// Per request from Portuguese and Brazilian users, treat Brazilian Portuguese as Portuguese.
	if (languageCode == 'pt-br') {
		languageCode = 'pt';
	// this was also in UploadForm.js, but without the heartwarming justification
	} else if (languageCode == 'en-gb') {
		languageCode = 'en';
	}

	_this.languageMenu = mw.Language.getMenu("lang", languageCode);
	$j(_this.languageMenu).addClass('mwe-upwiz-desc-lang-select');
	_this.description = $j('<textarea name="desc" rows="3" cols="50" class="mwe-upwiz-desc-lang-text"></textarea>')
				.growTextArea().get(0);
	_this.div = $j('<div class="mwe-upwiz-desc-lang-container"></div>')
		       .append( _this.languageMenu )
	               .append( _this.description )
	
};

mw.UploadWizardDescription.prototype = {

	/**
	 * Obtain text of this description, suitable for including into Information template
	 * @return wikitext as a string
	 */
	getWikiText: function() {
		var _this = this;
		var language = $j( _this.languageMenu ).val().trim();
		var fix = mw.getConfig("languageTemplateFixups");
		if (fix[language]) {
			language = fix[language];
		}
		return '{{' + language + '|' + $j( _this.description ).val().trim() + '}}'	
	}
};

/**
 * Object that represents the Details (step 2) portion of the UploadWizard
 * n.b. each upload gets its own details.
 * 
 * XXX a lot of this construction is not really the jQuery way. 
 * The correct thing would be to have some hidden static HTML
 * on the page which we clone and slice up with selectors. Inputs can still be members of the object
 * but they'll be found by selectors, not by creating them as members and then adding them to a DOM structure.
 *
 * XXX this should have styles for what mode we're in 
 *
 * @param UploadWizardUpload
 * @param containerDiv	The div to put the interface into
 */
mw.UploadWizardDetails = function( upload, containerDiv ) {

	var _this = this;
	_this.upload = upload;

	_this.descriptions = [];

	_this.div = $j( '<div class="mwe-upwiz-details-file"></div>' );

	_this.thumbnailDiv = $j( '<div class="mwe-upwiz-thumbnail"></div>' );
	
	_this.errorDiv = $j( '<div class="mwe-upwiz-details-error"></div>' );

	_this.dataDiv = $j( '<div class="mwe-upwiz-details-data"></div>' );

	// descriptions
	_this.descriptionsDiv = $j( '<div class="mwe-upwiz-details-descriptions"></div>' );
	

	_this.descriptionAdder = $j( '<a id="mwe-upwiz-desc-add"/>' )
					.attr( 'href', '#' )
					.html( gM( 'mwe-upwiz-desc-add-0' ) )
					.click( function( ) { _this.addDescription( ) } );
	
	_this.descriptionsContainerDiv = 
		$j( '<div class="mwe-upwiz-details-descriptions-container"></div>' )
			.append( $j( '<div class="mwe-details-label">' + gM( 'mwe-upwiz-desc' ) + '</div>' ) )
			.append( _this.descriptionsDiv )
			.append( $j( '<div class="mwe-upwiz-details-descriptions-add"></div>' )
					.append( _this.descriptionAdder ) );
	// title
	_this.titleInput = $j( '<input type="text" class="mwe-title" size="40"/>' )
				.keyup( function() { 
					_this.setFilenameFromTitle();
				} );
	$j(_this.titleInput).destinationChecked( {
		spinner: function(bool) { _this.toggleDestinationBusy(bool) },
		preprocess: function( name ) { return _this.getFilenameFromTitle() }, // XXX this is no longer a pre-process
		processResult: function( result ) { _this.processDestinationCheck( result ) } 
	} );

	_this.titleErrorDiv = $j('<div></div>');

	_this.titleContainerDiv = $j('<div></div>')
		.append( $j( '<div class="mwe-details-label"></div>' ).append( gM( 'mwe-upwiz-title' ) ) )
		.append( $j( '<div class="mwe-details-title"></div>' ).append( _this.titleInput ) )
		.append( _this.titleErrorDiv );

	_this.moreDetailsDiv = $j('<div class="mwe-more-details"></div>');

	// more details ctrl 
	// XXX change class of button to have arrow pointing in different directions
	// XXX standard jQuery "blind" effect seems to cause a jQuery error, why?
	_this.moreDetailsCtrl = $j('<a class=".mwe-upwiz-more"/>')
		.append( gM( 'mwe-upwiz-more-options' ) ).click( function() {
			_this.moreDetailsOpen = !_this.moreDetailsOpen;
			_this.moreDetailsOpen ? $j( _this.moreDetailsDiv ).show() 
					      : $j( _this.moreDetailsDiv ).hide();
		} );
	_this.moreDetailsOpen = false;
	_this.moreDetailsDiv.hide();

	_this.moreDetailsCtrlDiv = $j( '<div class="mwe-details-more-options"></div>' )
		.append( _this.moreDetailsCtrl );

	
	_this.dateInput = $j( '<input type="text" class="mwe-date" size="20"/>' );
	// XXX suddenly this isn't working. Seems to be a problem with monobook. If I datepicker-ify an input outside the 
	// content area, it works. Vector is fine
	$j( _this.dateInput ).datepicker( { 	
		dateFormat: 'yy-mm-dd', // oddly, this means yyyy-mm-dd
		buttonImage: '/js/mwEmbed/skins/common/images/calendar.gif',
		buttonImageOnly: false  // XXX determine what this does, docs are confusing
	} );

	_this.locationInput = $j( '<input type="text" class="mwe-location" size="20"/>' );

	var aboutThisWorkDiv = $j('<div></div>')
		.append( $j( '<h5 class="mwe-details-more-subhead">' ).append( gM( 'mwe-upwiz-about-this-work' ) ) )
		.append( $j( '<div class="mwe-details-more-subdiv">' )
			.append( $j( '<div></div>' )
				.append( $j( '<div class="mwe-details-more-label"></div>' ).append( gM( 'mwe-upwiz-date-created' ) ) )
				.append( $j( '<div class="mwe-details-more-input"></div>' ).append( _this.dateInput ) ) 
			)
			.append( $j ( '<div style="display: none;"></div>' ) // see prefillLocation
				.append( $j( '<div class="mwe-details-more-label"></div>' ).append( gM( 'mwe-upwiz-location' ) ) )
				.append( $j( '<div class="mwe-details-more-input"></div>' ).append( _this.locationInput ) ) 
			)
		);

	// XXX why is rows=1 giving me two rows. Is this growTextArea's fault?
	_this.sourceInput = $j('<textarea class="mwe-source" rows="1" cols="40"></textarea>' ).growTextArea();
	_this.sourceDiv = $j( '<div></div>' )
		.append( $j( '<div class="mwe-details-more-label"></div>' ).append( gM( 'mwe-upwiz-source' ) ) )
		.append( $j( '<div class="mwe-details-more-input"></div>' ).append( _this.sourceInput ) ); 
	

	_this.authorInput = $j('<textarea class="mwe-author" rows="1" cols="40"></textarea>' ).growTextArea();
	_this.authorDiv = $j( '<div></div>' )
		.append( $j( '<div class="mwe-details-more-label"></div>' ).append( gM( 'mwe-upwiz-author' ) ) )
		.append( $j( '<div class="mwe-details-more-input"></div>' ).append( _this.authorInput ) );


	var licenseInputDiv = $j('<div></div>');
	_this.licenseInput = new mw.UploadWizardLicenseInput( licenseInputDiv );
	_this.licenseDiv = $j( '<div></div>' )
		.append( $j( '<div class="mwe-details-more-label"></div>' ).append( gM( 'mwe-upwiz-license' ) ) )
		.append( $j( '<div class="mwe-details-more-input"></div>' ).append( licenseInputDiv ) );

	var copyrightInfoDiv = $j('<div></div>')
		.append( $j( '<h5 class="mwe-details-more-subhead">' ).append( gM( 'mwe-upwiz-copyright-info' ) ) )
		.append( $j( '<div class="mwe-details-more-subdiv">' )
			.append( _this.sourceDiv, 
				 _this.authorDiv, 
				 _this.licenseDiv ) );
	

	var aboutTheFileDiv = $j('<div></div>')
		.append( $j( '<h5 class="mwe-details-more-subhead">' ).append( gM( 'mwe-upwiz-about-format' ) ) ) 
		.append( $j( '<div class="mwe-details-more-subdiv">' )
			.append( $j( '<div></div>' )
				.append( $j( '<div class="mwe-details-more-label"></div>' ).append( gM( 'mwe-upwiz-filename-tag' ) ) )
				.append( $j( '<div id="mwe-upwiz-details-filename" class="mwe-details-more-input"></div>' ) ) ) );
	
	_this.otherInformationInput = $j( '<textarea class="mwe-upwiz-other-textarea" rows="3" cols="40"></textarea>' );
	var otherInformationDiv = $j('<div></div>')	
		.append( $j( '<h5 class="mwe-details-more-subhead">' ).append( gM( 'mwe-upwiz-other' ) ) ) 
		.append( _this.otherInformationInput );
	

	$j( _this.div )
		.append( _this.thumbnailDiv )
		.append( _this.errorDiv )
		.append( $j( _this.dataDiv )
			.append( _this.descriptionsContainerDiv )
			.append( _this.titleContainerDiv )
			.append( _this.moreDetailsCtrlDiv )
			.append( $j( _this.moreDetailsDiv ) 
				.append( aboutThisWorkDiv )
				.append( copyrightInfoDiv )
				.append( aboutTheFileDiv )
				.append( otherInformationDiv )
			)
		);

	_this.addDescription();
	$j( containerDiv ).append( _this.div );


};

mw.UploadWizardDetails.prototype = {

	/**
	 * Sets the filename from the title plus this upload's extension.
	 */
	setFilenameFromTitle: function() {
		var _this = this;
		// "File:" is the constant NS_FILE, defined in Namespaces.php. Usually unchangeable?
		_this.filename = "File:" + _this.getFilenameFromTitle();
		$j( '#mwe-upwiz-details-filename' ).text( _this.filename );		
			
	},

	/**
	 * Gets a filename from the human readable title, using upload's extension.
	 * @return Filename
	 */ 
	getFilenameFromTitle: function() {
		var _this = this;
		var name = $j( _this.titleInput ).val();
		return mw.UploadWizardUtil.pathToTitle( name ) + '.' + _this.upload.extension;
	},


	/**
	 * show file destination field as "busy" while checking 
	 * @param busy boolean true = show busy-ness, false = remove
	 */
	toggleDestinationBusy: function ( busy ) {
		var _this = this;
		if (busy) {
			_this.titleInput.addClass( "busy" );
		} else {
			_this.titleInput.removeClass( "busy" );
		}
	},
	
	/**
	 * Process the result of a destination filename check.
	 * See mw.DestinationChecker.js for documentation of result format 
	 * XXX would be simpler if we created all these divs in the DOM and had a more jquery-friendly way of selecting
 	 * attrs. Instead we create & destroy whole interface each time. Won't someone think of the DOM elements?
	 * @param result
	 */
	processDestinationCheck: function( result ) {
		var _this = this;

		if ( result.isUnique ) {
			_this.titleErrorDiv.hide().empty();
			_this.ignoreWarningsInput = undefined;
			return;
		}

		// result is NOT unique
		var title = result.title;
		var img = result.img;
		var href = result.href;
	
		_this.ignoreWarningsInput = $j("<input />").attr( { type: 'checkbox', name: 'ignorewarnings' } ); 
	
		var $fileAlreadyExists = $j('<div />')
			.append(				
				gM( 'mwe-fileexists', 
					$j('<a />')
					.attr( { target: '_new', href: href } )
					.text( title )
				),
				$j('<br />'),
				_this.ignoreWarningsInput,
				gM('mwe-upwiz-overwrite')
			);
		
		var $imageLink = $j('<a />')
			.addClass( 'image' )
			.attr( { target: '_new', href: href } )
			.append( 
				$j( '<img />')
				.addClass( 'thumbimage' )
				.attr( {
					'width' : img.thumbwidth,
					'height' : img.thumbheight,
					'border' : 0,
					'src' : img.thumburl,
					'alt' : title
				} )
			);
			
		var $imageCaption = $j( '<div />' )
			.addClass( 'thumbcaption' )
			.append( 
				$j('<div />')
				.addClass( "magnify" )
				.append(
					$j('<a />' )
					.addClass( 'internal' )
					.attr( {
						'title' : gM('mwe-thumbnail-more'),
						'href' : href
					} ),
					
					$j( '<img />' )
					.attr( {
						'border' : 0,
						'width' : 15,
						'height' : 11,
						'src' : mw.getConfig( 'images_path' ) + 'magnify-clip.png'
					} ), 
					
					$j('<span />')
					.html( gM( 'mwe-fileexists-thumb' ) )
				)													
			);

		$j( _this.titleErrorDiv ).html(
			$j('<span />')  // dummy argument since .html() only takes one arg
				.append(
					$fileAlreadyExists,
					$j( '<div />' )
						.addClass( 'thumb tright' )
						.append(
							$j( '<div />' )
							.addClass( 'thumbinner' )
							.css({
								'width' : ( parseInt( img.thumbwidth ) + 2 ) + 'px;'
							})
							.append( 
								$imageLink, 
								$imageCaption
							)					
						)
				)
		).show();

	}, 

	/**
	 * Do anything related to a change in the number of descriptions
	 */
	recountDescriptions: function() {
		var _this = this;
		// if there is some maximum number of descriptions, deal with that here
		$j( _this.descriptionAdder ).html( gM( 'mwe-upwiz-desc-add-' + ( _this.descriptions.length == 0 ? '0' : 'n' )  )  );
	},


	/**
	 * Add a new description
	 */
	addDescription: function() {
		var _this = this;
		var languageCode = _this.descriptions.length ? mw.Language.UNKNOWN : mw.getConfig('userLanguage' );
		var description = new mw.UploadWizardDescription( languageCode  );

		description.removeCtrl = $j('<a title="' + gM( 'mwe-upwiz-remove-description' ) + '" href="#">x</a>' )
					.addClass('mwe-upwiz-remove' )
					.addClass('mwe-upwiz-remove-desc' )
					.click( function() { _this.removeDescription( description  ) }  )
					.get( 0  );
		$j( description.div  ).append( description.removeCtrl  );

		$j( _this.descriptionsDiv ).append( description.div  );
		_this.descriptions.push( description  );
		_this.recountDescriptions();
	},

	/**
	 * Remove a description 
	 * @param description
	 */
	removeDescription: function( description  ) {
		var _this = this;
		$j( description.div ).remove();
		mw.UploadWizardUtil.removeItem( _this.descriptions, description  );
		_this.recountDescriptions();
	},

	/**
	 * Display an error with details
	 * XXX this is a lot like upload ui's error -- should merge
	 */
	error: function() {
		var _this = this;
		var args = Array.prototype.slice.call( arguments  ); // copies arguments into a real array
		var msg = 'mwe-upwiz-upload-error-' + args[0];
		$j( _this.errorDiv ).append( $j( '<p class="mwe-upwiz-upload-error">' + gM( msg, args.slice( 1 ) ) + '</p>' ) );
		// apply a error style to entire did
		$j( _this.div ).addClass( 'mwe-upwiz-upload-error' );
		$j( _this.dataDiv ).hide();
		$j( _this.errorDiv ).show();
	},

	/**
	 * Given the API result pull some info into the form ( for instance, extracted from EXIF, desired filename )
	 * @param result	Upload API result object
	 */
	populate: function() {
		var _this = this;
		mw.log( "populating details from upload" );
		_this.setThumbnail( mw.getConfig( 'thumbnailWidth' ) ); 
		_this.prefillDate();
		_this.prefillSource();
		_this.prefillAuthor(); 
		_this.prefillTitle();
		_this.prefillFilename();
		_this.prefillLocation(); 
	},

	/**
	 *  look up thumbnail info and set it on the form, with loading spinner
	 *
	 * @param width
	 */
	setThumbnail: function( width ) {
		var _this = this;

		var callback = function( thumbnail ) { 
			// side effect: will replace thumbnail's loadingSpinner
			_this.thumbnailDiv.html(
				$j('<a/>')
					.attr( { 'href': _this.upload.imageinfo.descriptionurl,
						 'target' : '_new' } )
					.append(
						$j( '<img/>' )
							.addClass( "mwe-upwiz-thumbnail" )
							.attr( 'width',  thumbnail.width )
							.attr( 'height', thumbnail.height )
							.attr( 'src',    thumbnail.url ) ) );
		};

		_this.thumbnailDiv.loadingSpinner();
		_this.upload.getThumbnail( width, callback );

	},

	/**
	 * Check if we got an EXIF date back; otherwise use today's date; and enter it into the details 
	 * XXX We ought to be using date + time here...
	 * EXIF examples tend to be in ISO 8601, but the separators are sometimes things like colons, and they have lots of trailing info
	 * (which we should actually be using, such as time and timezone)
	 */
	prefillDate: function() {
		var _this = this;
		var yyyyMmDdRegex = /^(\d\d\d\d)[:\/-](\d\d)[:\/-](\d\d)\D.*/;
		var dateStr;
		var metadata = _this.upload.imageinfo.metadata;
		$j.each([metadata.datetimeoriginal, metadata.datetimedigitized, metadata.datetime, metadata['date']], 
			function( i, imageinfoDate ) {
				if ( imageinfoDate !== undefined ) {
					var d = imageinfoDate.trim();
					if ( d.match( yyyyMmDdRegex ) ) { 
						dateStr = d.replace( yyyyMmDdRegex, "$1-$2-$3" );
						return false; // break from $j.each
					}
				}
			}
		);
		// if we don't have EXIF or other metadata, let's use "now"
		// XXX if we have FileAPI, it might be clever to look at file attrs, saved 
		// in the upload object for use here later, perhaps
		function pad( n ) { 
			return n < 10 ? "0" + n : n;
		}

		if (dateStr === undefined) {
			d = new Date();
			dateStr = d.getUTCFullYear() + '-' + pad(d.getUTCMonth()) + '-' + pad(d.getUTCDate());
		}

		// ok by now we should definitely have a date string formatted in YYYY-MM-DD
		$j( _this.dateInput ).val( dateStr );
	},

	/**
	 * Set the title of the thing we just uploaded, visibly
	 * Note: the interface's notion of "filename" versus "title" is the opposite of MediaWiki
	 */
	prefillTitle: function() {
		var _this = this;
		var titleExt = mw.UploadWizardUtil.titleToPath( _this.upload.originalFilename );
		var title = titleExt.replace( /\.\w+$/, '' );
		$j( _this.titleInput ).val( title );
	},

	/**
	 * Set the title of the thing we just uploaded, visibly
	 * Note: the interface's notion of "filename" versus "title" is the opposite of MediaWiki
	 */
	prefillFilename: function() {
		var _this = this;
		_this.setFilenameFromTitle();
	},

	/**
 	 * Prefill location inputs (and/or scroll to position on map) from image info and metadata
	 *
	 * At least for my test images, the EXIF parser on MediaWiki is not giving back any data for
	 *  GPSLatitude, GPSLongitude, or GPSAltitudeRef. It is giving the lat/long Refs, the Altitude, and the MapDatum 
	 * So, this is broken until we fix MediaWiki's parser, OR, parse it ourselves somehow 
	 *
	 *    in Image namespace
	 *		GPSTag		Long ??
	 *
	 *    in GPSInfo namespace
	 *    GPSVersionID	byte*	2000 = 2.0.0.0
	 *    GPSLatitude	rational 
	 *    GPSLatitudeRef	ascii (N | S)  or North | South 
	 *    GPSLongitude	rational
	 *    GPSLongitudeRef   ascii (E | W)    or East | West 
	 *    GPSAltitude	rational
	 *    GPSAltitudeRef	byte (0 | 1)    above or below sea level
	 *    GPSImgDirection	rational
	 *    GPSImgDirectionRef  ascii (M | T)  magnetic or true north
	 *    GPSMapDatum 	ascii		"WGS-84" is the standard
	 *
	 *  A 'rational' is a string like this:
	 *	"53/1 0/1 201867/4096"	--> 53 deg  0 min   49.284 seconds 
	 *	"2/1 11/1 64639/4096"    --> 2 deg  11 min  15.781 seconds
	 *	"122/1"             -- 122 m  (altitude)
	 */
	prefillLocation: function() {
		var _this = this;
		var metadata = _this.upload.imageinfo.metadata;
		if (metadata === undefined) {
			return;
		}
		

	},

	/**
	 * Given a decimal latitude and longitude, return filled out {{Location}} template
	 * @param latitude decimal latitude ( -90.0 >= n >= 90.0 ; south = negative )
	 * @param longitude decimal longitude ( -180.0 >= n >= 180.0 ; west = negative )
	 * @param scale (optional) how rough the geocoding is. 
	 * @param heading (optional) what direction the camera is pointing in. (decimal 0.0-360.0, 0 = north, 90 = E)
	 * @return string with WikiText which will geotag this record
	 */
	coordsToWikiText: function(latitude, longitude, scale, heading) {
		//Wikipedia
		//http://en.wikipedia.org/wiki/Wikipedia:WikiProject_Geographical_coordinates#Parameters
		// http://en.wikipedia.org/wiki/Template:Coord
		//{{coord|61.1631|-149.9721|type:landmark_globe:earth_region:US-AK_scale:150000_source:gnis|name=Kulis Air National Guard Base}}
		
		//Wikimedia Commons
		//{{Coor dms|41|19|20.4|N|19|38|36.7|E}}
		//{{Location}}

	},

	/**
	 * If there is a way to figure out source from image info, do so here
	 * XXX user pref?
	 */
	prefillSource: function() {
		// we have no way to do this AFAICT
	},

	/**
	 * Prefill author (such as can be determined) from image info and metadata
	 * XXX user pref?
	 */
	prefillAuthor: function() {
		var _this = this;
		if (_this.upload.imageinfo.metadata.author !== undefined) {
			$j( _this.authorInput ).val( _this.upload.imageinfo.metadata.author );
		}
	
	},
	
	/**
	 * Prefill license (such as can be determined) from image info and metadata
	 * XXX user pref?
	 */
	prefillLicense: function() {
		var _this = this;
		var copyright = _this.upload.imageinfo.metadata.copyright;
		if (copyright !== undefined) {
			if (copyright.match(/\bcc-by-sa\b/i)) {
				// set license to be that CC-BY-SA
			} else if (copyright.match(/\bcc-by\b/i)) {
				// set license to be that
			} else if (copyright.match(/\bcc-zero\b/i)) {
				// set license to be that
				// XXX any other licenses we could guess from copyright statement
			} else {
				$j( _this.licenseInput ).val( copyright );
			}
		}
	},


	
	/**
	 * Convert entire details for this file into wikiText, which will then be posted to the file 
	 * XXX there is a WikiText sanitizer in use on UploadForm -- use that here, or port it 
	 * @return wikitext representing all details
	 */
	getWikiText: function() {
		var _this = this;
		wikiText = '';
	

		// http://commons.wikimedia.org / wiki / Template:Information
	
		// can we be more slick and do this with maps, applys, joins?
		var information = { 
			'description' : '',	 // {{lang|description in lang}}*   required
			'date' : '',		 // YYYY, YYYY-MM, or YYYY-MM-DD     required  - use jquery but allow editing, then double check for sane date.
			'source' : '',    	 // {{own}} or wikitext    optional 
			'author' : '',		 // any wikitext, but particularly {{Creator:Name Surname}}   required
			'permission' : '',       // leave blank unless OTRS pending; by default will be "see below"   optional 
			'other_versions' : '',   // pipe separated list, other versions     optional
			'other_fields' : ''      // ???     additional table fields 
		};
		
		// sanity check the descriptions -- do not have two in the same lang
		// all should be a known lang
		if ( _this.descriptions.length === 0 ) {
			// ruh roh
			// we should not even allow them to press the button ( ? ) but then what about the queue...
		}
		$j.each( _this.descriptions, function( i, desc ) {
			information['description'] += desc.getWikiText();
		} )
	

		// XXX add a sanity check here for good date
		information['date'] = $j( _this.dateInput ).val().trim();
		
		information['source'] = $j( _this.sourceInput ).val().trim();
		information['author'] = $j( _this.authorInput ).val().trim();
		
		var info = '';
		for ( var key in information ) {
			info += '|' + key + '=' + information[key] + "\n";	
		}	

		wikiText += "=={{int:filedesc}}==\n";

		wikiText += '{{Information\n' + info + '}}\n';

	
		wikiText += "=={int:licenses}==\n";
		
		wikiText += _this.licenseInput.getWikiText();

		// add a location template

		// add an "anything else" template if needed
		var otherInfoWikiText = $j( _this.otherInformationInput ).val().trim();
		if ( otherInfoWikiText != '' ) {
			wikiText += "=={int:otherinfo}==\n";
			wikiText += otherInfoWikiText;
		}

		return wikiText;	
	},

	/**
	 * Check if we are ready to post wikitext
	 */
	isReady: function() {
		// somehow, all the various issues discovered with this upload should be present in a single place
		// where we can then check on
		// perhaps as simple as _this.issues or _this.agenda
	},

	/**
	 * Post wikitext as edited here, to the file
	 * XXX This should be split up -- one part should get wikitext from the interface here, and the ajax call
	 * should be be part of upload
	 */
	submit: function() {
		var _this = this;


		// are we okay to submit?
		// all necessary fields are ready
		// check descriptions
		// the filename is in a sane state
		var desiredFilename = _this.filename;
		shouldRename = ( desiredFilename != _this.upload.title );

		// if ok to go			
		// XXX lock down the interface, spinnerify
		// else
		// point out problems


		// XXX check state of details for okayness ( license selected, at least one desc, sane filename )
		var wikiText = _this.getWikiText();
		mw.log( wikiText );
	
		var params = {
			action: 'edit',
			token: mw.getConfig( 'token' ),
			title: _this.upload.title,
			// section: 0, ?? causing issues?
			text: wikiText,
			summary: "User edited page with " + mw.UploadWizard.userAgent,
			// notminor: 1,
			// basetimestamp: _this.upload.imageinfo.timestamp,  ( conflicts? )
			nocreate: 1
		};

		var endCallback = function() { _this.completeDetailsSubmission(); }	

		mw.log( "editing!" );
		mw.log( params );
		var callback = function( result ) {
			mw.log( result );
			mw.log( "successful edit" );
			if ( shouldRename ) {
				_this.rename( desiredFilename, endCallback );	
			} else {
				endCallback();
			}
		}

		_this.upload.state = 'submitting-details';
		_this.showProgress();
		mw.getJSON( params, callback );
	},

	/**
	 * Rename the file
         *
	 *  THIS MAY NOT WORK ON ALL WIKIS. for instance, on Commons, it may be that only admins can move pages. This is another example of how
	 *  we need an "incomplete" upload status
	 *  we are presuming this File page is brand new, so let's not bother with the whole redirection deal. ('noredirect')
	 *
	 * use _this.ignoreWarningsInput (if it exists) to check if we can blithely move the file or if we have a problem if there
	 * is a file by that name already there
	 *
	 * @param filename to rename this file to
 	 */
	rename: function( title, endCallback ) {
		var _this = this;
		mw.log("renaming!");
		params = {
			action: 'move',
			from: _this.upload.title,
			to: title,
			reason: "User edited page with " + mw.UploadWizard.userAgent,
			movetalk: '',
			noredirect: '', // presume it's too new 
			token: mw.getConfig('token'),
		};
		mw.log(params);
		// despite the name, getJSON magically changes this into a POST request (it has a list of methods and what they require).
		mw.getJSON( params, function( data ) {
			// handle errors later
			// possible error data: { code = 'missingtitle' } -- orig filename not there
			// and many more
	
			// which should match our request.
			// we should update the current upload filename
			// then call the uploadwizard with our progress

			// success is
			// { move = { from : ..., reason : ..., redirectcreated : ..., to : .... }
			if (data !== undefined && data.move !== undefined && data.move.to !== undefined) {
				_this.upload.title = data.move.to;
				_this.refreshImageInfo( _this.upload, _this.upload.title, endCallback );
			}
		} );
	},

	/** 
	 * Get new image info, for instance, after we renamed an image
	 *
	 * @param upload an UploadWizardUpload object
	 * @param title  title to look up remotely
	 * @param endCallback  execute upon completion
	 */
	refreshImageInfo: function( upload, title, endCallback ) {
		var params = {
                        'titles': title,
                        'prop':  'imageinfo',
                        'iiprop': 'timestamp|url|user|size|sha1|mime|metadata'
                };
		// XXX timeout callback?
		mw.getJSON( params, function( data ) {
			if ( data && data.query && data.query.pages ) {
				if ( ! data.query.pages[-1] ) {
					for ( var page_id in data.query.pages ) {
						var page = data.query.pages[ page_id ];
						if ( ! page.imageinfo ) {
							// not found? error
						} else {
							upload.extractImageInfo( page.imageinfo[0] );
						}
					}
				}	
			}
			endCallback();
		} );
	},

	showProgress: function() {
		var _this = this;
		_this.div.disableInputsFade();
		// XXX spinnerize
		_this.upload.detailsProgress = 1.0;
	},

	completeDetailsSubmission: function() {
		var _this = this;
		_this.upload.state = 'complete';
		// XXX de-spinnerize
		_this.div.enableInputsFade();
	},

	/** 
	 * Sometimes we wish to lock certain copyright or license info from being changed
	 */
	lockSource: function() {
		var _this = this;
		_this.sourceDiv.hide();
	},
		

	/** 
	 * Sometimes we wish to lock certain copyright or license info from being changed
	 */
	lockAuthor: function() {
		var _this = this;
		_this.authorDiv.hide();
	},

	/** 
	 * Sometimes we wish to lock certain copyright or license info from being changed
	 */
	lockLicense: function() {
		var _this = this;
		_this.licenseDiv.hide();
	}

		
}


/**
 * Object that reperesents the entire multi-step Upload Wizard
 */
mw.UploadWizard = function() {

	this.uploads = [];

};


mw.UploadWizard.userAgent = "UploadWizard (alpha) on " + $j.browser.name + " " + $j.browser.version;


mw.UploadWizard.prototype = {
	maxUploads: 10,  // XXX get this from config 
	maxSimultaneousUploads: 2,   //  XXX get this from config
	tabs: [ 'file', 'details', 'thanks' ],

	/*
	// list possible upload handlers in order of preference
	// these should all be in the mw.* namespace
	// hardcoded for now. maybe some registry system might work later, like, all
	// things which subclass off of UploadHandler
	uploadHandlers: [
		'FirefoggUploadHandler',
		'XhrUploadHandler',
		'ApiIframeUploadHandler',
		'SimpleUploadHandler',
		'NullUploadHandler'
	],

	/*
	 * We can use various UploadHandlers based on the browser's capabilities. Let's pick one.
	 * For example, the ApiUploadHandler should work just about everywhere, but XhrUploadHandler
	 *   allows for more fine-grained upload progress
	 * @return valid JS upload handler class constructor function
	getUploadHandlerClass: function() {
		// return mw.MockUploadHandler;
		return mw.ApiUploadHandler;
		/*
		var _this = this;
		for ( var i = 0; i < uploadHandlers.length; i++ ) {
			var klass = mw[uploadHandlers[i]];
			if ( klass != undefined && klass.canRun( this.config )) {
				return klass;
			}
		}
		// this should never happen; NullUploadHandler should always work
		return null;
	},
	*/
	
	/**
	 * create the basic interface to make an upload in this div
	 * @param div	The div in the DOM to put all of this into.
	 */
	createInterface: function( selector ) {
		var _this = this;
		var div = $j( selector ).get(0);
		div.innerHTML = 
	
		       '<div id="mwe-upwiz-tabs">'
		       + '<ul>'
		       +   '<li id="mwe-upwiz-tab-file">'     + gM('mwe-upwiz-tab-file')     + '</li>'
		       +   '<li id="mwe-upwiz-tab-details">'  + gM('mwe-upwiz-tab-details')  + '</li>'
		       +   '<li id="mwe-upwiz-tab-thanks">'   + gM('mwe-upwiz-tab-thanks')   + '</li>'
		       + '</ul>'
		       + '</div>'


		       + '<div id="mwe-upwiz-content">'
		       +   '<div id="mwe-upwiz-tabdiv-file">'
		       +     '<div id="mwe-upwiz-intro">' + gM('mwe-upwiz-intro') + '</div>'
		       +     '<div id="mwe-upwiz-select-files">' + gM('mwe-upwiz-select-files') + '</div>'	
		       +     '<div id="mwe-upwiz-files"></div>'	
		       +     '<div><a id="mwe-upwiz-add-file">' + gM("mwe-upwiz-add-file-0") + '</a></div>'
		       +     '<div><button id="mwe-upwiz-upload-ctrl" disabled="disabled">' + gM("mwe-upwiz-upload") + '</button></div>'
		       +     '<div id="mwe-upwiz-progress"></div>'
		       +     '<div style="clear: left;"></div>'
		       +   '</div>'
		       +   '<div id="mwe-upwiz-tabdiv-details">'
		       +     '<div id="mwe-upwiz-macro">'
		       +       '<div id="mwe-upwiz-macro-choice">' 
		       +  	'<div>' + gM( 'mwe-upwiz-intro-details' ) + '</div>'
		       +  	'<div id="mwe-upwiz-macro-deeds">'
		       +  	  '<div id="mwe-upwiz-macro-deed-ownwork" class="mwe-upwiz-deed">'
		       +               '<div class="mwe-upwiz-deed-option-title">'
		       +                 '<span class="mwe-upwiz-deed-header mwe-closed"><a class="mwe-upwiz-deed-header-link">' + gM( 'mwe-upwiz-source-ownwork' ) + '</a></span>'
		       +                 '<span class="mwe-upwiz-deed-header mwe-open" style="display: none;">' 
		       +   		   gM( 'mwe-upwiz-source-ownwork' ) 
		       +   		 ' <a class="mwe-upwiz-macro-deeds-return">' + gM( 'mwe-upwiz-change' ) + '</a>'
		       +  	         '</span>'
		       +               '</div>' // more deed stuff set up below
		       +  	     '<div class="mwe-upwiz-deed-form" style="display: none"></div>'		
		       +            '</div>'
		       +  	  '<div id="mwe-upwiz-macro-deed-thirdparty" class="mwe-upwiz-deed">'
		       +               '<div class="mwe-upwiz-deed-option-title">'
		       +                 '<span class="mwe-upwiz-deed-header mwe-closed"><a class="mwe-upwiz-deed-header-link">' + gM( 'mwe-upwiz-source-thirdparty' ) + '</a></span>'
		       +                 '<span class="mwe-upwiz-deed-header mwe-open" style="display: none;">' 
		       +   		   gM( 'mwe-upwiz-source-thirdparty' ) 
		       +   		 ' <a class="mwe-upwiz-macro-deeds-return">' + gM( 'mwe-upwiz-change' ) + '</a>'
		       +  	         '</span>'
		       +               '</div>' // more deed stuff set up below
		       +  	     '<div class="mwe-upwiz-deed-form" style="display: none"></div>'		
		       +  	  '</div>'
		       +  	'</div>'
		       +       '</div>'
		       +       '<div id="mwe-upwiz-macro-edit" style="display: none">'
		       +  	'<div class="mwe-upwiz-macro-edit-submit">' 
		       +  	  '<p>' + gM( 'mwe-upwiz-macro-edit-intro' ) + '</p>' 
		       +          '</div>' // button added below
		       +  	'<div id="mwe-upwiz-macro-progress"></div>'
		       +          '<div id="mwe-upwiz-macro-files"></div>'
		       +  	'<div class="mwe-upwiz-macro-edit-submit"></div>' // button added below			
		       +       '</div>'
		       +     '</div>'
		       +   '</div>'
		       +   '<div id="mwe-upwiz-tabdiv-thanks">'
		       +     '<div id="mwe-upwiz-thanks"></div>'
                       +   '</div>'
		       + '</div>'

		       + '<div id="mwe-upwiz-clearing"></div>';

		// within FILE tab div
		// select files:
		//     place for file interfaces
		$j('#mwe-upwiz-add-file').click( function() { _this.addUpload() } );
		$j('#mwe-upwiz-upload-ctrl').click( function() { _this.startUploads() } );

		_this.setupDeedOwnWork();
		_this.setupDeedThirdParty();

		// open and close the various deeds
		$j( '.mwe-upwiz-deed-header-link' ).click( function() { 
			_this.showDeed( $j( this ).parents( '.mwe-upwiz-deed' ) ); 
		} );
		$j( '.mwe-upwiz-macro-deeds-return' ).click( function() { _this.showDeedChoice() } );

		// buttons to submit all details and go on to the thanks page, at the top and bottom of the page.
		$j( '.mwe-upwiz-macro-edit-submit' ).each( function() {
			$j( this ).append( $j( '<input />' )
				.addClass( 'mwe-details-submit' )
				.attr( { type: 'submit', value: gM( 'mwe-upwiz-macro-edit' ) } )
				.click( function() { 
					// move to the top of the page to see the progress bar
					$j( 'html' ).scrollTop( 0 );
					_this.detailsSubmit( function() { 
						_this.prefillThanksPage();
						_this.moveToTab('thanks');
					} );
				} ) );
		} );

	
		// add one to start
		_this.addUpload();

		// "select" the first tab - highlight, make it visible, hide all others
		_this.moveToTab('file');
	},

	/**
	 * Advance one "step" in the wizard interface.
	 * @param selectedTabName
	 */
	moveToTab: function( selectedTabName ) {
		var _this = this;
		for ( var i = 0; i < _this.tabs.length; i++ ) {
			var tabName = _this.tabs[i];
			var tabDiv = $j( '#mwe-upwiz-tabdiv-' + tabName );
			var tab = $j( '#mwe-upwiz-tab-' + tabName );
			if ( selectedTabName == tabName ) {
				tabDiv.show();
				tab.addClass( 'mwe-upwiz-tab-highlight' );
			} else {
				tabDiv.hide();
				tab.removeClass( 'mwe-upwiz-tab-highlight' );
			}
		}
		// XXX possibly select appropriate form field to begin work
	},

	/**
	 * add an Upload
	 *   we create the upload interface, a handler to transport it to the server,
	 *   and UI for the upload itself and the "details" at the second step of the wizard.
	 *   Finally stuff it into an array of uploads. 
	 * @return boolean success
	 */
	addUpload: function() {
		var _this = this;
		if ( _this.uploads.length == _this.maxUploads ) {
			return false;
		}

		var upload = new mw.UploadWizardUpload();
		$j( upload ).bind( 'filenameAccepted', function(e) { _this.updateFileCounts();  e.stopPropagation(); } );
		$j( upload ).bind( 'removeUpload', function(e) { _this.removeUpload( upload ); e.stopPropagation(); } );
		// this is only the UI one, so is the result even going to be there?

		// bind to some error state

		_this.uploads.push( upload );
		
		$j( "#mwe-upwiz-files" ).append( upload.ui.div );

		_this.updateFileCounts();

		return true;
	},

	/**
	 * Remove an upload from our array of uploads, and the HTML UI 
	 * We can remove the HTML UI directly, as jquery will just get the parent.
         * We need to grep through the array of uploads, since we don't know the current index. 
	 *
	 * @param upload
	 */
	removeUpload: function( upload ) {
		var _this = this;
		mw.UploadWizardUtil.removeItem( _this.uploads, upload );
		_this.updateFileCounts();
	},

	/**
	 * This is useful to clean out unused upload file inputs if the user hits GO.
	 * We are using a second array to iterate, because we will be splicing the main one, _this.uploads
	 */
	removeEmptyUploads: function() {
		var _this = this;
		var toRemove = [];
		for ( var i = 0; i < _this.uploads.length; i++ ) {
			if ( _this.uploads[i].ui.fileInputCtrl.value == "" ) {
				toRemove.push( _this.uploads[i] );
			}
		};
		for ( var i = 0; i < toRemove.length; i++ ) {
			toRemove[i].remove();
		}
	},

	/**
	 * Manage transitioning all of our uploads from one state to another -- like from "new" to "uploaded".
 	 * Shows progress bar with estimated time remaining.
	 *
	 * There are too many args here. How to fix?
	 * This is starting to feel like an object.
	 *
	 * @param beginState   what state the upload should be in before starting.
	 * @param progressState  the state to set the upload to while it's doing whatever 
	 * @param endState   the state to set the upload to after it's done whatever 
	 * @param progressProperty  the property on the upload showing current progress of whatever
	 * @param weightProperty    the property on the upload giving how heavy to weight this item in total progress calculation
	 * @param  starter	 function, taking single argument (upload) which starts the process we're interested in 
	 * @param progressBarSelector where to put the progress bar
	 * @param endCallback    function to call when all uploads are in the end state.
	 */
	makeTransitioner: function( beginState, 
				    progressState, 
				    endState, 
				    progressProperty, 
				    weightProperty, 
				    progressBarSelector,
				    starter, 
				    endCallback ) {
		
		var wizard = this;

		var totalWeight = 0.0;
		$j.each( wizard.uploads, function( i, upload ) {
			totalWeight += upload[weightProperty];
		} );
		var totalCount = wizard.uploads.length;

		var progressBar = new mw.ProgressBar( progressBarSelector );
		progressBar.setTotal( totalCount );

		transitioner = function() {
			var fraction = 0.0;
			var uploadsToStart = wizard.maxSimultaneousUploads;
			var endStateCount = 0;
			$j.each( wizard.uploads, function(i, upload) {
				if ( upload.state == endState ) {
					endStateCount++;
				} else if ( upload.state == progressState ) {
					uploadsToStart--;
				} else if ( ( upload.state == beginState ) && ( uploadsToStart > 0 ) ) {
					starter( upload );
					uploadsToStart--;
				}
				if (upload[progressProperty] !== undefined) {
					fraction += upload[progressProperty] * ( upload[weightProperty] / totalWeight );
				}
			} );

			// perhaps this could be collected into a single progressbar obj
			progressBar.showProgress( fraction );
			progressBar.showCount( endStateCount );
	
			// build in a little delay even for the end state, so user can see progress bar in a complete state.	
			var nextAction = (endStateCount == totalCount) ? endCallback : transitioner
			setTimeout( nextAction, wizard.transitionerDelay );
		}

		progressBar.setBeginTime();
		transitioner();
	},

	transitionerDelay: 300,  // milliseconds

		

	/**
	 * Kick off the upload processes.
	 * Does some precalculations, changes the interface to be less mutable, moves the uploads to a queue, 
	 * and kicks off a thread which will take from the queue.
	 */
	startUploads: function() {
		var _this = this;
		_this.removeEmptyUploads();
		// remove the upload button, and the add file button
		$j( '#mwe-upwiz-upload-ctrl' ).hide();
		$j( '#mwe-upwiz-add-file' ).hide();
		
		// remove ability to change files
		// ideally also hide the "button"... but then we require styleable file input CSS trickery
		// although, we COULD do this just for files already in progress...

		// it might be interesting to just make this creational -- attach it to the dom element representing 
		// the progress bar and elapsed time	
		_this.makeTransitioner(
			'new', 
			'transporting', 
			'transported', 
			'transportProgress', 
			'transportWeight', 
			'#mwe-upwiz-progress',
			function( upload ) {
				upload.start();
			},
		        function() { 
				$j.each( _this.uploads, function(i, upload) {
					upload.state = 'details';
				} );
				_this.moveToTab('details') 
		  	} 
		);
	},

	
	/**
	 * Occurs whenever we need to update the interface based on how many files are there or have transported
	 * Also detects if all uploads have transported and kicks off the process that eventually gets us to Step 2.
	 */
	updateFileCounts: function() {
		var _this = this;

		// Can we enable the "add an upload" button, and what should the text on the button show?
		$j( '#mwe-upwiz-add-file' ).html( gM( 'mwe-upwiz-add-file-' + ( _this.uploads.length === 0 ? '0' : 'n' )) );
		if ( _this.uploads.length < _this.maxUploads ) {
			$j( '#mwe-upwiz-add-file' ).removeAttr( 'disabled' );
		} else {
			$j( '#mwe-upwiz-add-file' ).attr( 'disabled', true );
		}


		// Can we enable the "start uploads" button?
		var hasFile;
		$j.each( _this.uploads, function (i, upload) {
			if ( upload.originalFilename ) {
				hasFile = true; 
				return false; // break $j.each
			}
		} );

		if ( hasFile ) {
			$j( '#mwe-upwiz-upload-ctrl' ).removeAttr( 'disabled' ); 
		} else {
			$j( '#mwe-upwiz-upload-ctrl' ).attr( 'disabled', 'disabled' ); 
		}

	},

	/**
	 * Submit all edited details and other metadata
	 * Works just like startUploads -- parallel simultaneous submits with progress bar.
	 */
	detailsSubmit: function( endCallback ) {
		var _this = this;
		// some details blocks cannot be submitted (for instance, identical file hash)
		_this.removeBlockedDetails();

		// check that it's even possible to submit all
		
		// remove all controls
		//$j( '#mwe-upwiz-upload-ctrl' ).hide();
		//$j( '#mwe-upwiz-add-file' ).hide();
		
		// remove ability to edit details
		// maybe add some sort of greyish semi-opaque thing
		
		// add the upload progress bar, with ETA
		// add in the upload count 
		_this.makeTransitioner(
			'details', 
			'submitting-details', 
			'complete', 
			'detailsProgress', 
			'detailsWeight', 
			'#mwe-upwiz-macro-progress',
			function( upload ) {
				upload.details.submit();
			},
			endCallback
		);
	},

	/**
	 * Removes(?) details that we can't edit for whatever reason -- might just advance them to a different state?
	 */
	removeBlockedDetails: function() {
		
	},


	// might as well hardcode more of this?
	prefillThanksPage: function() {
		var _this = this;
		
		var thanksDiv = $j( '#mwe-upwiz-thanks' );

		thanksDiv.append( $j( '<p>' ).append( gM( 'mwe-upwiz-thanks-intro' ) ) );
		var width = mw.getConfig( 'thumbnailWidth' );

		$j.each( _this.uploads, function(i, upload) {
			var thumbnailDiv = $j( '<div></div>' ).addClass( 'mwe-upwiz-links-thumbnail' );
			thanksDiv.append( thumbnailDiv );

			/* this is copied code, evil */
			var callback = function( thumbnail ) { 
				// side effect: will replace thumbnail's loadingSpinner
				thumbnailDiv.html(
					$j('<a>')
						.attr( { 'href': upload.imageinfo.descriptionurl,
							 'target': '_new' } )
						.append(
							$j( '<img/>' )
								.addClass( "mwe-upwiz-thumbnail" )
								.attr( 'width',  thumbnail.width )
								.attr( 'height', thumbnail.height )
								.attr( 'src',    thumbnail.url ) ) );
			};

			thumbnailDiv.loadingSpinner();
			upload.getThumbnail( width, callback );
			/* end evil copied code */

			var thumbTitle = upload.title.replace(/^File/, 'Image'); // XXX is this really necessary?
			var thumbWikiText = "[[" + thumbTitle + "|thumb|right]]";

			thanksDiv.append(
				$j( '<div></div>' )
					.addClass( 'mwe-upwiz-thanks-links' )
					.append( $j('<p/>')
						.append( gM( 'mwe-upwiz-thanks-link',
							      $j( '<a />' )
								.attr( { target: '_new', href: upload.imageinfo.descriptionurl } )
								.text( upload.title ) ) ) ) 
					.append( $j('<p/>')
						.append( gM( 'mwe-upwiz-thanks-wikitext' ),
							 $j( '<textarea></textarea>' )
								.addClass( 'mwe-thanks-input-textarea' )
								.append( thumbWikiText ) ) )
					.append( $j('<p/>')
						.append( gM( 'mwe-upwiz-thanks-url' ),
							 $j( '<input />' )
								.addClass( 'mwe-thanks-input' )
								.attr( { type: 'text', value: upload.imageinfo.descriptionurl } ) ) )

			);

		} ); 
	},

	/**
	 *
	 */
	pause: function() {

	},

	/**
	 *
	 */
	stop: function() {

	},
	/**
	 * Go back to original source choice. 
	 * Assumed that we are in details mode.
	 */
	showDeedChoice: function() {
		$j( '#mwe-upwiz-macro-deeds' ).find( '.mwe-upwiz-deed-header.mwe-open' ).hide();
		$j( '#mwe-upwiz-macro-deeds' ).find( '.mwe-upwiz-deed-header.mwe-closed' ).show();
		$j( '#mwe-upwiz-macro-deeds' ).find( '.mwe-upwiz-deed' ).fadeIn( 'fast' );
		$j( '#mwe-upwiz-macro-deeds' ).find( '.mwe-upwiz-deed-form' ).fadeOut('fast');
	},


	/**
	 * From the deed choice page, show the 'own work' deed
	 */
	showDeed: function( selector ) {
		$j( selector ).find( '.mwe-upwiz-deed-header.mwe-open' ).show();
		$j( selector ).find( '.mwe-upwiz-deed-header.mwe-closed' ).hide();
		$j( selector ).siblings().fadeOut( 'fast' );
		$j( selector ).find( '.mwe-upwiz-deed-form' ).fadeIn( 'fast' );
	},

	
	/**
	 * Set up the form for the deed option that says these uploads are all the user's own work.
	 */
	setupDeedOwnWork: function() {
		var _this = this;

		var sourceInput = $j( '<input />').attr( { name: "source", value: "{{ownwork}}" } );
		var authorInput = $j( '<input />').attr( { name: "author" } ); // value set below
		var licenseInputDiv = $j( '<div></div>' );
		var licenseInput = new mw.UploadWizardLicenseInput( licenseInputDiv );
		licenseInput.setDefaultValues();

		// we don't use 'customizable' here, but the applyMacroDeed function will use this to determine
		// what can and can't be customized for each individual file.
		$j( authorInput ).data( 'customizable', true );			
		$j( licenseInput ).data( 'customizable', true );			

		var standardDiv = $j( '<div />' )
			.append( 
				$j( '<input />') 
					.attr( { id: 'mwe-upwiz-deed-accept-default', type: 'checkbox' } )
					.click( function() {
						licenseInput.setDefaultValues();
						_this.applyMacroDeed( sourceInput, authorInput, licenseInput );
						_this.showDetailsFiles();
					} )
					.addClass( 'mwe-checkbox-hang-indent' ),
				$j( '<p />' )
					.addClass( 'mwe-checkbox-hang-indent-text' )
					.html( gM( 'mwe-upwiz-source-ownwork-assert', 
						$j( authorInput )
							.addClass( 'mwe-upwiz-sign' ) ) ),
				$j( '<p />' )
					.addClass( 'mwe-checkbox-hang-indent-text' )
					.addClass( 'mwe-small-print' )
					.html( gM ( 'mwe-upwiz-source-ownwork-assert-note' ) )
			);

		var toggleCustomDiv = $j( '<div />' )
	
		var customDiv = $j('<div/>')
			.append( 
				$j( '<input />') 
					.attr( { id: 'mwe-upwiz-deed-thirdparty-accept', type: 'checkbox' } )
					.click( function() {
						_this.applyMacroDeed( sourceInput, authorInput, licenseInput );
						_this.showDetailsFiles();
					} )
					.addClass( 'mwe-checkbox-hang-indent' ),
				$j( '<p />' )
					.addClass( 'mwe-checkbox-hang-indent-text' )
					.html( gM( 'mwe-upwiz-source-ownwork-assert-custom', 
						$j( '<input />' )
							.attr( { name: 'author' } )
							.addClass( 'mwe-upwiz-sign' ) ) ),
				licenseInputDiv )
			.hide();

		hiddenInputsDiv = $j( '<span />' ).hide().append( sourceInput );

		$j( '#mwe-upwiz-macro-deed-ownwork .mwe-upwiz-deed-form' ).
			append( standardDiv, 
				toggleCustomDiv, 
				customDiv,
				hiddenInputsDiv );
		
		_this.makeCustomToggler( standardDiv, toggleCustomDiv, customDiv );

		// synchronize both username signatures
		// set initial value to configured username
		// if one changes all the others change
		$j( '.mwe-upwiz-sign' )
			.attr( { value: mw.getConfig( 'userName' ) } )
			.keyup( function() { 
				var thisInput = this;
				var thisVal = $j( thisInput ).val();
				$j.each( $j( '.mwe-upwiz-sign' ), function( i, input ) {
					if (thisInput !== input) {
						$j( input ).val( thisVal );
					}
				} );
			} );
	
	},

	/**
	 * Set up the deed for when you "found pics on a website", i.e. there's a third party
	 */
	setupDeedThirdParty: function() {
		var _this = this;

		var sourceInput = $j('<textarea class="mwe-source" name="source" rows="1" cols="40"></textarea>' ).growTextArea();
		var authorInput = $j('<textarea class="mwe-author" name="author" rows="1" cols="40"></textarea>' ).growTextArea();
		var licenseInputDiv = $j( '<div></div>' );
		var licenseInput = new mw.UploadWizardLicenseInput( licenseInputDiv );
		licenseInput.setDefaultValues();

		// we don't use 'customizable' here, but the applyMacroDeed function will use this to determine
		// what can and can't be customized for each individual file.
		$j( sourceInput ).data( 'customizable', true );			
		$j( authorInput ).data( 'customizable', true );			
		$j( licenseInput ).data( 'customizable', true );

		var standardDiv = $j( '<div />' )
			.append( 
				$j( '<p />' ).html( gM( 'mwe-upwiz-source-thirdparty-intro' ) ),
				$j( '<input />' ).attr( { type: 'submit' } ).addClass( "mwe-upwiz-deed-thirdparty-accept" ) 
			);
				 
		
		var toggleCustomDiv = $j( '<div />' );

		var customDiv = $j( '<div />' )
			.append( 
				$j( '<div />' ).append( gM( 'mwe-upwiz-source-thirdparty-custom-intro' ) ),
				$j( '<div />' )
					.addClass( "mwe-upwiz-thirdparty-fields" )
					.append( $j( '<label />' )
							.attr( { 'for' : 'source' } )
							.text( gM( 'mwe-upwiz-source' ) ) )
					.append( sourceInput ),
				$j( '<div />' )
					.addClass( "mwe-upwiz-thirdparty-fields" )
					.append( $j( '<label />' )
							.attr( { 'for' : 'author' } )
							.text( gM( 'mwe-upwiz-author' ) ) )
					.append( authorInput ),
				$j( '<div />' ).text( gM( 'mwe-upwiz-source-thirdparty-license' ) ),
				licenseInputDiv,
				$j( '<input />' ).attr( { type: 'submit' } ).addClass( "mwe-upwiz-deed-thirdparty-accept" ) 
			).hide();


		$j( '#mwe-upwiz-macro-deed-thirdparty .mwe-upwiz-deed-form' ).
			append( standardDiv, 
				toggleCustomDiv, 
				customDiv );
		
		_this.makeCustomToggler( standardDiv, toggleCustomDiv, customDiv );

		// upgrade both buttons to take us to the next page with details	
		$j( '.mwe-upwiz-deed-thirdparty-accept' )
			.attr( { value: gM( 'mwe-upwiz-source-thirdparty-accept' ) } )
			.click( function() {
				_this.applyMacroDeed( sourceInput, authorInput, licenseInput );
				_this.showDetailsFiles();
			} );	


	},


	/**
	 * There is a common pattern of having standard options, and then a "more options" panel which 
	 * disables the standard options panel. 
	 *
	 * It is assumed that standardDiv, toggleDiv, and customDiv are all contiguous, going vertically
	 * down the page. 
	 * 
	 * We do not do anything to disable the inputs on either panel. So far, we've implemented these 
	 * in a way that the 'custom' panel is always controlling everything, but the standard panel just
	 * enters values into the custom one behind the scenes.
	 *
	 * @param standardDiv the div representing the standard options
	 * @param toggleDiv the div which has the control to open and shut custom options
	 * @param customDiv the div containing the custom options
	 */
	makeCustomToggler: function ( standardDiv, toggleDiv, customDiv ) {
		$j( toggleDiv )
			.addClass( 'mwe-more-options closed' )
			.append( $j( '<a />' ) 
				.click( function() { 
					var open = ! ( $j( this ).data( 'open' ) ) ;
					$j( this ).data( 'open', open );
					// on toggle:
					if ( open ) {
						// set out class to show the "close" message
						$j( this ).removeClass( "closed" );
						$j( this ).addClass( "open" );
						$j( this ).text( gM( 'mwe-upwiz-fewer-options' ) );
						// show the more options
						customDiv.show();
						standardDiv.disableInputsFade();
					} else {
						$j( this ).removeClass( "open" );
						$j( this ).removeClass( "closed" );
						$j( this ).text( gM( 'mwe-upwiz-more-options' ) );
						// hide the more options
						customDiv.hide();
						standardDiv.enableInputsFade();
					}
				} )
				.data( 'open', false )
				.text( gM( 'mwe-upwiz-more-options' ) ) );
	},

	/**
	 * Given deed / copyright information from the "macro" stage, modify the properties and editable details of each upload
	 * @param sourceInput  an HTML text form element input
	 * @param authorInput  an HTML text form element input
	 * @param licenseInput an mw.UploadWizardLicenseInput object
	 */
	applyMacroDeed: function( sourceInput, authorInput, licenseInput ) {
		var _this = this;
		// copy the values from our macro inputs into each upload
		// if an element has the $j().data() property 'customizable', it is allowed to appear in the details to be changed
		// otherwise should be unchangeable. We let the details object sort out how a 'locked' interface looks, which could be as simple
		// as just hiding the input.
		$j.each( _this.uploads, function( i, upload ) {

			$j( upload.details.sourceInput ).val( $j( sourceInput ).val() );
			if ( !( $j( sourceInput ).data( 'customizable' ) ) ) {
				upload.details.lockSource();
			} 
			
			$j( upload.details.authorInput ).val( $j( authorInput ).val() );
			if ( !( $j( authorInput ).data( 'customizable' ) ) ) {
				upload.details.lockAuthor();
			} 

			upload.details.licenseInput.setValues( licenseInput.getValues() );	
			if ( !( $j( licenseInput ).data( 'customizable' ) ) ) {
				upload.details.lockLicense();
			} 

		} );

	},
		
	/**
	 * Transition from fiddling around with the deeds to editing details
	 */
	showDetailsFiles: function() {	
		$j( '#mwe-upwiz-macro-choice' ).fadeOut( 'fast' ); 
		$j( '#mwe-upwiz-macro-edit' ).fadeIn( 'fast' );
	},

};

/**
 * Miscellaneous utilities
 */
mw.UploadWizardUtil = {
	/**
	 * remove an item from an array. Tests for === identity to remove the item
	 *  XXX the entire rationale for this file may be wrong. 
	 *  XXX The jQuery way would be to query the DOM for objects, not to keep a separate array hanging around
	 * @param items  the array where we want to remove an item
	 * @param item	 the item to remove
	 */
	removeItem: function( items, item ) {
		for ( var i = 0; i < items.length; i++ ) {
			if ( items[i] === item ) {
				items.splice( i, 1 );
				break;
			}
		}
	},

	/** 
	 * Capitalise first letter and replace spaces by underscores
	 * @param filename (basename, without directories)
	 * @return typical title as would appear on MediaWiki
	 */
	pathToTitle: function ( filename ) {
		return mw.ucfirst( filename.replace(/ /g, '_' ) );
	},

	/** 
	 * Capitalise first letter and replace underscores by spaces
	 * @param title typical title as would appear on MediaWiki
	 * @return plausible local filename, with spaces changed to underscores.
	 */
	titleToPath: function ( title ) {
		return mw.ucfirst( title.replace(/_/g, ' ' ) );
	},

	/** 
 	 * Slice extension off a path
	 * We assume that extensions are 1-4 characters in length
	 * @param path to file, like "foo/bar/baz.jpg"
	 * @return extension, like ".jpg" or undefined if it doesn't look lke an extension.
	 */
	getExtension: function( path ) {
		var extension = undefined;
		var idx = path.lastIndexOf( '.' );
		if (idx > 0 && ( idx > ( path.length - 5 ) ) && ( idx < ( path.length - 1 ) )  ) {
			extension = path.substr( idx + 1 ).toLowerCase();
		}
		return extension;
	},

	/**
	 * Last resort to guess a proper extension
	 */
	mimetypeToExtension: {
		'image/jpeg': 'jpg',
		'image/gif': 'gif'
		// fill as needed
	}


};

/**
 * Upper-case the first letter of a string. XXX move to common library
 * @param string
 * @return string with first letter uppercased.
 */
mw.ucfirst = function( s ) {
	return s.substring(0,1).toUpperCase() + s.substr(1);
};


/**
 * jQuery extension. Makes a textarea automatically grow if you enter overflow
 * (This feature was in the old Commons interface with a confusing arrow icon; it's nicer to make it automatic.)
 */
jQuery.fn.growTextArea = function( options ) {

	// this is a jquery-style object

	// in MSIE, this makes it possible to know what scrollheight is 
	// Technically this means text could now dangle over the edge, 
	// but it shouldn't because it will always grow to accomodate very quickly.
	if ($j.msie) {
		this.each( function(i, textArea) {
			textArea.style.overflow = 'visible';
		} );
	}

	var resizeIfNeeded = function() {
		// this is the dom element
		while (this.scrollHeight > this.offsetHeight) {
			this.rows++;
		}
	};

	this.change(resizeIfNeeded).keyup(resizeIfNeeded);

	return this;
};

jQuery.fn.disableInputsFade = function( options ) {
	this.fadeTo( 'fast', 0.5 );
	$j.each( this.find( 'input' ), function( i, input ) {
		if ( input.disabled ) {
			$j( input ).data( { wasDisabled: true } );
		}
		input.disabled = true;
	} );
	return this;
};

jQuery.fn.enableInputsFade = function( options ) {
	$j.each( this.find( 'input' ), function( i, input ) {
		if ( ! $j( input ).data( 'wasDisabled' ) ) {
			input.disabled = false;
		}
	} );
	this.fadeTo( 'fast', 1.0 );
	return this;
};
