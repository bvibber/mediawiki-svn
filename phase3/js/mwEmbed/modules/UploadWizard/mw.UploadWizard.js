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
	"mwe-upwiz-provenance-ownwork": "They are entirely your own work.",
	"mwe-upwiz-provenance-ownwork-assert": "I, $1, the copyright holder of this work, hereby grant anyone the right to use these works for any purpose, as long as they credit me and share derivative work under the same terms.",
	"mwe-upwiz-provenance-ownwork-assert-note": "This means you release your work under a double Creative Commons Attribution ShareAlike and GFDL license.",
	"mwe-upwiz-provenance-permission": "Their author gave you explicit permission to upload them",
	"mwe-upwiz-provenance-website": "They come from a website",
	"mwe-upwiz-provenance-custom": "Did you know? You can <a href=\"$1\">customize</a> the default options you see here.",
	"mwe-upwiz-more-options": "more options...",
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
	

	"mwe-upwiz-upload-error-bad-filename-extension": "This wiki does not accept filenames with the extension \"$1\".",
	"mwe-upwiz-upload-error-duplicate": "This file was previously uploaded to this wiki.",
	"mwe-upwiz-upload-error-stashed-anyway": "Post anyway?",
	"mwe-upwiz-ok": "OK",
	"mwe-upwiz-cancel": "Cancel",

	/* copied from mw.UploadHandler :(  */
	"mwe-fileexists" : "A file with this name exists already. Please check <b><tt>$1<\/tt><\/b> if you are not sure if you want to change it.",
	"mwe-thumbnail-more" : "Enlarge"
} );

	// available licenses should be a configuration of the MediaWiki instance,
	// not hardcoded here.
	// but, MediaWiki has no real concept of a License as a first class object -- there are templates and then specially - parsed 
	// texts to create menus -- hack on top of hacks -- a bit too much to deal with ATM



/**
 * Represents the upload -- in its local and remote state. (Possibly those could be separate objects too...)
 * This is our 'model' object if we are thinking MVC. Needs to be better factored, lots of feature envy with the UploadWizard
 */
mw.UploadWizardUpload = function() {
	var _this = this;
	_this._thumbnails = {};
	_this.imageinfo = {};
	_this.title = undefined;
	_this.filename = undefined;
	_this.originalFilename = undefined;
};

mw.UploadWizardUpload.prototype = {
	/** 
 	 * Accept the result from a successful API upload transport, and fill our own info 
	 *
	 * @param result The JSON object from a successful API upload result.
	 */
	extractImageInfo: function( result ) {
		var _this = this;

		_this.filename = result.upload.filename;
		_this.title = "File:" + _this.filename;

		for ( var key in result.upload.imageinfo ) {
			// we get metadata as list of key-val pairs; convert to object for easier lookup. Assuming that EXIF fields are unique.
			if ( key === 'metadata' ) {
				_this.imageinfo.metadata = {};
				if ( result.upload && result.upload.imageinfo && result.upload.imageinfo.metadata ) {
					$j.each( result.upload.imageinfo.metadata, function( i, pair ) {
						if (pair !== undefined) {
							_this.imageinfo.metadata[pair['name'].toLowerCase()] = pair['value'];
						}
					} );
				}
			} else {
				_this.imageinfo[key] = result.upload.imageinfo[key];
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
			callback( _this.thumbnails[ "width" + width ] );
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
 * @param filenameAcceptedCb 	Execute if good filename entered into this interface; useful for knowing if we're ready to upload
 */
mw.UploadWizardUploadInterface = function( upload, filenameAcceptedCb ) {
	var _this = this;

	_this.upload = upload;
	_this.filenameAcceptedCb = filenameAcceptedCb;

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


	$j( _this.div ).append( _this.form )
		    .append( _this.progressMessage )
		    .append( _this.errorDiv );

	// _this.progressBar = ( no progress bar for individual uploads yet )
	// add a details thing to details
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
		_this.progress( 1.0 );
	},

	/**
	 * Show progress by a fraction
	 * @param fraction	The fraction of progress. Float between 0 and 1
	 */
	progress: function( fraction ) {
		var _this = this;
		$j( _this.progressMessage ).addClass('mwe-upwiz-status-progress')
		    			   .html(gM( 'mwe-upwiz-uploading' ))
					   .show();
		// update individual progress bar with fraction?
	},

	/**
	 * Execute when this upload is transported; cleans up interface. 
	 * @param result	AJAx result object
	 */
	transported: function( result ) {
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
		var ext = _this.getExtension();
		if ( _this.isGoodExtension( ext ) ) {
			_this.updateFilename();
		} else {       
			_this.error( 'bad-filename-extension', ext );
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
		$j(_this.filenameCtrl).attr( 'value', uniqueFilename );
		_this.filenameAcceptedCb();
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
		return path.substr( path.lastIndexOf( '.' ) + 1 ).toLowerCase();
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
		var language = $j( _this.languageMenu ).trim().val();
		var fix = mw.getConfig("languageTemplateFixups");
		if (fix[language]) {
			language = fix[language];
		}
		return '{{' + language + '|' + $j( _this.description ).trim().val() + '}}'	
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
 * @param UploadWizardUpload
 * @param containerDiv	The div to put the interface into
 */
mw.UploadWizardDetails = function( upload, containerDiv ) {

	var _this = this;
	_this.upload = upload;

	_this.descriptions = [];

	_this.div = $j( '<div class="mwe-upwiz-details-file"></div>' );

	_this.macroDiv = $j( '<div class="mwe-upwiz-macro"></div>' )
		.append( $j( '<input type="submit" value="test edit"/>' ).click( function( ) { _this.submit( ) } ));

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
				.keydown( function() { 
					$j( _this.filenameInput ).val( mw.UploadWizardUtil.titleToPath( $j(_this.titleInput).val() ) );
				});
	$j(_this.titleInput).destinationChecked( {
		spinner: function(bool) { _this.toggleDestinationBusy(bool) },
		preprocess: mw.UploadWizardUtil.pathToTitle, // stateless, so we don't need the object
		processResult: function(result) { _this.processDestinationCheck(result) }
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
	$j( _this.dateInput ).datepicker( { 	
		dateFormat: 'yyyy-mm-dd',
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
	_this.authorInput = $j('<textarea class="mwe-author" rows="1" cols="40"></textarea>' ).growTextArea();
	_this.licenseInput = $j('<input type="text" class="mwe-license" size="30" />' );
	var sourceDiv = $j( '<div></div>' )
		.append( $j( '<div class="mwe-details-more-label"></div>' ).append( gM( 'mwe-upwiz-source' ) ) )
		.append( $j( '<div class="mwe-details-more-input"></div>' ).append( _this.sourceInput ) ); 
	

	var copyrightInfoDiv = $j('<div></div>')
		.append( $j( '<h5 class="mwe-details-more-subhead">' ).append( gM( 'mwe-upwiz-copyright-info' ) ) )
		.append( $j( '<div class="mwe-details-more-subdiv">' )
			.append( sourceDiv )
			.append( $j( '<div></div>' )
				.append( $j( '<div class="mwe-details-more-label"></div>' ).append( gM( 'mwe-upwiz-author' ) ) )
				.append( $j( '<div class="mwe-details-more-input"></div>' ).append( _this.authorInput ) ) 
			)
			.append( $j( '<div></div>' )
				.append( $j( '<div class="mwe-details-more-label"></div>' ).append( gM( 'mwe-upwiz-license' ) ) )
				.append( $j( '<div class="mwe-details-more-input"></div>' ).append( _this.licenseInput ) ) 
			)
		);

	
	_this.filenameInput = $j('<input type="text" class="mwe-filename" size="30" />' )
				.keydown( function() { 
					$j( _this.titleInput ).val( _this.filenameToTitle( $j(_this.filenameInput).val() ) );
				});

	var aboutTheFileDiv = $j('<div></div>')
		.append( $j( '<h5 class="mwe-details-more-subhead">' ).append( gM( 'mwe-upwiz-about-format' ) ) ) 
		.append( $j( '<div class="mwe-details-more-subdiv">' )
			.append( $j( '<div></div>' )
				.append( $j( '<div class="mwe-details-more-label"></div>' ).append( gM( 'mwe-upwiz-filename-tag' ) ) )
				.append( $j( '<div class="mwe-details-more-input"></div>' )
					.append( "File:" ) // this is the constant NS_FILE, defined in Namespaces.php. Usually unchangeable?
					.append( _this.filenameInput ) 
				)
			)
		);
	
	var otherInformationInput = $j( '<textarea class="mwe-upwiz-other-textarea" rows="3" cols="40"></textarea>' );
	var otherInformationDiv = $j('<div></div>')	
		.append( $j( '<h5 class="mwe-details-more-subhead">' ).append( gM( 'mwe-upwiz-other' ) ) ) 
		.append( otherInformationInput );
	

	$j( _this.div )
		.append( _this.macroDiv )   // XXX this is wrong; it's not part of a file's details
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
	 */
	processDestinationCheck: function( result ) {
		var _this = this;
		
		if ( result.unique ) {
			// do nothing
			return;
		}

		// result is NOT unique
		var title = result.title;
		var img = result.img;
		var href = result.href;
		
		var $fileAlreadyExists = $j('<div />')
		.append(				
			gM( 'mwe-fileexists', 
				$j('<a />')
				.attr( { target: '_new', href: href } )
				.text( title )
			)
		)
		
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
		$j( _this.titleErrorDiv ).append(
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
		);		

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
	 * Display an error but check before proceeding -- useful for cases where user can override a failed upload due to 
	 *  hash collision
	 *  just like error but with ok / cancel
	 * @param sessionKey
	 * @param duplicates
	 */
	errorDuplicate: function( sessionKey, duplicates ) {
		var _this = this;
		/*
		TODO - do something clever to get page URLs and image URLs 
		var duplicatePageTitles = result.upload.warnings.duplicate;
		var duplicates = [];
		for ( var i = 0; i < duplicates.length; i++ ) {
			imageInfo = mw.getJSON( undefined, 
						 {'titles' : duplicatePageTitles[i], 'prop' : 'imageinfo'})
						 function() { _this.renderUploads() } );
			duplicates.push( {
				// ?? async, so we should insert later...
			} ) 
		}
		*/
		_this.error( 'duplicate' );
		// add placeholder spinners to div, and then fetch the thumbnails and so on async
		// meanwhile...
		//$j( _this.errorDiv ).append(
		//	$j('<form></form>'); 
		// same as normal error but you get to ok / cancel, which resubmits with ignore warnings
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
				$j('<a>')
					.attr( 'href', _this.upload.imageinfo.descriptionurl )
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
		$j( _this.titleInput ).val( mw.UploadWizardUtil.pathToTitle( _this.upload.originalFilename ) );
	},

	/**
	 * Set the title of the thing we just uploaded, visibly
	 * Note: the interface's notion of "filename" versus "title" is the opposite of MediaWiki
	 */
	prefillFilename: function() {
		var _this = this;
		$j( _this.filenameInput ).val( mw.UploadWizardUtil.titleToPath( _this.upload.title ) );
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
		if (_this.upload.imageinfo.metadata.artist !== undefined) {
			$j( _this.authorInput ).val( _this.upload.imageinfo.metadata.artist );
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
		information['date'] = $j( _this.dateInput ).trim().val();
		
		information['source'] = $j( _this.sourceInput ).trim().val();
		information['author'] = $j( _this.authorInput ).trim().val();
		
		var info = '';
		for ( var key in information ) {
			info += '|' + key + '=' + information[key] + "\n";	
		}	

		wikiText += "=={{int:filedesc}}==\n";

		wikiText += '{{Information\n' + info + '}}\n';
		
		// wikiText += "=={int:license}==\n";
		// XXX get the real one -- usually dual license GFDL / cc - by - sa
		//wikiText += "{{cc-by-sa-3.0}}\n";
		// http://commons.wikimedia.org / wiki / Template:Information

		// add a location template

		// add an "anything else" template
		wikiText += $j( _this.otherInformationInput ).trim().val();

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
	 */
	submit: function() {
		var _this = this;
		// are we okay to submit?
		// check descriptions
			
		// XXX check state of details for okayness ( license selected, at least one desc, sane filename )
		var wikiText = _this.getWikiText();
		mw.log( wikiText );
	
		var params = {
			action: 'edit',
			token: mw.getConfig('token'),
			title: _this.upload.title,
			// section: 0, ?? causing issues?
			text: wikiText,
			summary: "User edited page with " + mw.UploadWizard.userAgent,
			// notminor: 1,
			// basetimestamp: _this.upload.imageinfo.timestamp,  (conflicts?)
			nocreate: 1
		};
		mw.log("editing!");
		mw.log(params);
		var callback = function(result) {
			mw.log(result);
			mw.log("successful edit");
			alert("posted successfully");
		}
		mw.getJSON(params, callback);

		// then, if the filename was changed, do another api call to move the page

		// THIS MAY NOT WORK ON ALL WIKIS. for instance, on Commons, it may be that only admins can move pages. This is another example of how
		//   we need an "incomplete" upload status
		// we are presuming this File page is brand new, so let's not bother with the whole redirection deal. ('noredirect')

		/*
		Note: In this example, all parameters are passed in a GET request just for the sake of simplicity. However, action = move requires POST requests; GET requests will cause an error. Moving Main Pgae ( sic ) and its talk page to Main Page, without creating a redirect
		api.php  ? action = move & from = Main%20Pgae & to = Main%20Page &  reason = Oops,%20misspelling & movetalk & noredirect &  token = 58b54e0bab4a1d3fd3f7653af38e75cb%2B\
		*/


	}


}


/**
 * Object that reperesents the entire multi-step Upload Wizard
 */
mw.UploadWizard = function() {

	this.uploadHandlerClass = mw.getConfig('uploadHandlerClass') || this.getUploadHandlerClass();
	this.isTransported = false;
	
	this.uploads = [];
	// leading underline for privacy. DO NOT TAMPER.
	this._uploadsQueued = [];
	this._uploadsInProgress = [];
	this._uploadsTransported = [];
	this._uploadsEditingDetails = [];
	this._uploadsCompleted = [];

	this.uploadsBeginTime = null;	

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
	 */
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
		*/
	},
	
	/**
	 * create the basic interface to make an upload in this div
	 * @param div	The div in the DOM to put all of this into.
	 */
	createInterface: function( div ) {
		var _this = this;
		div.innerHTML = 
	
		       '<div id="mwe-upwiz-tabs">'
		       + '<ul>'
		       +   '<li id="mwe-upwiz-tab-file">'     + gM('mwe-upwiz-tab-file')     + '</li>'
		       +   '<li id="mwe-upwiz-tab-details">'  + gM('mwe-upwiz-tab-details')  + '</li>'
		       +   '<li id="mwe-upwiz-tab-thanks">'   + gM('mwe-upwiz-tab-thanks')   + '</li>'
		       + '</ul>'
		       + '</div>'


		       + '<div id="mwe-upwiz-content">'
		       + '<div id="mwe-upwiz-tabdiv-file">'
		       +   '<div id="mwe-upwiz-intro">' + gM('mwe-upwiz-intro') + '</div>'
		       +     '<div id="mwe-upwiz-select-files">' + gM('mwe-upwiz-select-files') + '</div>'	
		       +     '<div id="mwe-upwiz-files"></div>'	
		       +     '<div><a id="mwe-upwiz-add-file">' + gM("mwe-upwiz-add-file-0") + '</a></div>'
		       +     '<div><button id="mwe-upwiz-upload-ctrl" disabled="disabled">' + gM("mwe-upwiz-upload") + '</button></div>'
		       +     '<div id="mwe-upwiz-progress" style="display:none">'
		       + 	'<div>'
		       +           '<div id="mwe-upwiz-progress-bar"></div>'
		       + 	   '<div id="mwe-upwiz-etr"></div>'
		       +        '</div>'	
		       + 	'<div id="mwe-upwiz-count"></div>'
		       +     '</div>'
		       +     '<div style="clear: left;"></div>'
		       +   '</div>'
		       + '</div>'
		       + '<div id="mwe-upwiz-tabdiv-details">'
		       +   '<div id="mwe-upwiz-details-macro"></div>'
		       +   '<div id="mwe-upwiz-details-files"></div>'
		       + '</div>'
		       + '<div id="mwe-upwiz-tabdiv-thanks">'
		       +   '<div id="mwe-upwiz-thanks"></div>'
                       + '</div>'
		       +'</div>'

		       + '<div id="mwe-upwiz-clearing"></div>';

		// within FILE tab div
		// select files:
		//     place for file interfaces
		$j('#mwe-upwiz-add-file').click( function() { _this.addUpload() } );
		$j('#mwe-upwiz-upload-ctrl').click( function() { _this.startUploads() } );
	
		// Create global progress bar
		$j( '#mwe-upwiz-progress-bar' ).progressbar({
			value: 0
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
			tabName = _this.tabs[i];
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
		var idx = _this.uploads.length;  // or?
		if ( idx == _this.maxUploads ) {
			return false;
		}

		var upload = new mw.UploadWizardUpload();

		// XXX much of the following should be moved to UploadWizardUpload constructor.

		// UI
		// originalFilename is the basename of the file on our file system. This is what we really wanted ( probably ).
		// the system will upload with a temporary filename and we'll get that back from the API return when we upload
		var filenameAcceptedCb = function() {
			_this.updateFileCounts(); 
		};
		var ui = new mw.UploadWizardUploadInterface( upload, filenameAcceptedCb ); 
		ui.removeCtrl = $j( '<a title="' + gM( 'mwe-upwiz-remove-upload' ) 
						+ '" href="#" class="mwe-upwiz-remove">x</a>' )
					.click( function() { _this.removeUpload( upload ) } )
					.get( 0 );
		$j( ui.div ).append( ui.removeCtrl );

		upload.ui = ui;
		// handler -- usually ApiUploadHandler
		upload.handler = new _this.uploadHandlerClass( upload.ui );

		// this is for UI only...
		upload.handler.addProgressCb( function( fraction ) { _this.uploadProgress( upload, fraction ) } );

		// this is only the UI one, so is the result even going to be there?
		upload.handler.addTransportedCb( function( result ) { _this.uploadTransported( upload, result ) } );

		// not sure about this...UI only?
		// this will tell us that at least one of our uploads has had an error -- may change messaging,
		// like, please fix below
		upload.handler.addErrorCb( function( error ) { _this.uploadError( upload, error ) } );

		// details 		
		upload.details = new mw.UploadWizardDetails( upload, $j( '#mwe-upwiz-details-files' ));


		_this.uploads.push( upload );
		
		$j( "#mwe-upwiz-files" ).append( upload.ui.div );




		//$j("#testac").languageMenu();

		// update the uploadUi to add files - we may be over limit 
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
		$j( upload.ui.div ).remove();
		$j( upload.details.div ).remove();
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
			_this.removeUpload( toRemove[i] );
		}
	},

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
	
		// XXX we just want to VISUALLY lock it in -- disabling this seems to remove it from form post	
		// $j( '.mwe-upwiz-file' ).attr( 'disabled', 'disabled' ); 

		// add the upload progress bar, with ETA
		// add in the upload count 
		$j( '#mwe-upwiz-progress' ).show();
	
		_this.uploadsBeginTime = ( new Date() ).getTime();
		
		var canGetFileSize = ( _this.uploadHandlerClass.prototype.getFileSize !== undefined );

		// queue the uploads
		_this.totalWeight = 0;
		for ( var i = 0; i < _this.uploads.length; i++ ) {
			var upload = _this.uploads[i];

			// we may want to do something clever here to detect
			// whether this is a real or dummy weight
			if ( canGetFileSize ) {
				upload.weight = upload.getFileSize();
			} else {
				upload.weight = 1;
			}
			_this.totalWeight += upload.weight;

			_this._uploadsQueued.push( upload );
		}
		setTimeout( function () { _this._startUploadsQueued(); }, 0 );
	},

	/**
	 * Uploads must be 'queued' to be considered for uploading
	 *  making this another thread of execution, because we want to avoid any race condition
	 * this way, this is the only "thread" that can start uploads
	 * it may miss a newly transported upload but it will get it eventually
	 *
	 */
	_startUploadsQueued: function() {
		var _this = this;
		var uploadsToStart = Math.min( _this.maxSimultaneousUploads - _this._uploadsInProgress.length, 
					       _this._uploadsQueued.length );
		mw.log( "_startUploadsQueued: should start " + uploadsToStart + " uploads" );
		while ( uploadsToStart-- ) {
			var upload = _this._uploadsQueued.shift();
			_this._uploadsInProgress.push( upload );
			upload.handler.start();
		}
		if ( _this._uploadsQueued.length ) {
			setTimeout( function () { _this._startUploadsQueued(); }, 1000 );
		}
	},


	/**
	 * Show overall progress for the entire UploadWizard
	 * The current design doesn't have individual progress bars, just one giant one.
	 * We did some tricky calculations in startUploads to try to weight each individual file's progress against 
	 * the overall progress.
	 */
	showProgress: function() {
		var _this = this;
		if ( _this.isTransported ) {
			return;
		}

		var fraction = 0;
		for ( var i = 0; i < _this.uploads.length; i++ ) {
			var upload = _this.uploads[i]; 
			mw.log( "progress of " + upload.ui.fileInputCtrl.value + " = " + upload.progress );
			fraction += upload.progress * ( upload.weight / _this.totalWeight );
		}
		_this.showProgressBar( fraction );
	
		var remainingTime = _this.getRemainingTime( _this.uploadsBeginTime, fraction );
		if ( remainingTime !== null ) {
			_this.showRemainingTime( remainingTime );
		}
	},
	
	/**
	 * Show the progress bar for the entire Upload Wizard.
	 * @param fraction	fraction transported (float between 0 and 1)
	 */
	showProgressBar: function( fraction ) {		
		$j( '#mwe-upwiz-progress-bar' ).progressbar( 'value', parseInt( fraction * 100 ) );
	},

	/**
	 * Show remaining time for all the uploads
	 * XXX should be localized - x hours, x minutes, x seconds
	 * @param remainingTime		estimated time remaining in milliseconds
	 */
	showRemainingTime: function( remainingTime ) { 
		$j( '#mwe-upwiz-etr' ).html( gM( 'mwe-upwiz-remaining', mw.seconds2npt(parseInt(remainingTime / 1000)) ) );
	},


	/**
	 * Calculate remaining time for all uploads to complete.
	 * 
	 * @param beginTime	time in whatever unit getTime returns, presume epoch milliseconds
	 * @param fractionTransported	fraction transported
	 * @return 	time in whatever units getTime() returns; presumed milliseconds
	 */
	getRemainingTime: function ( beginTime, fractionTransported ) {
		if ( beginTime ) {
			var elapsedTime = ( new Date() ).getTime() - beginTime;
			if ( fractionTransported > 0.0 && elapsedTime > 0 ) { // or some other minimums for good data
				var rate = fractionTransported / elapsedTime;
				return parseInt( ( 1.0 - fractionTransported ) / rate ); 
			}
		}
		return null;
	},

	/**
	 * Record the progress of an individual upload
 	 * okay we are in a confusing state here -- are we asking for progress to be stored in the uploadhandler for our perusal or
	 * to be explicitly forwarded to us
	 * @param upload	an Upload object
	 * @param progress	fraction of progress (float between 0 and 1)
	 */
	uploadProgress: function( upload, progress ) {
		mw.log("upload progress is " + progress);
		var _this = this;
		upload.progress = progress;
		_this.showProgress();
	},

	/**
	 * To be executed when an individual upload finishes. Processes the result and updates step 2's details 
	 * @param upload 	an Upload object
	 * @param result	the API result in parsed JSON form
	 */
	uploadTransported: function( upload, result ) {
		var _this = this;
		_this._uploadsTransported.push( upload );
		mw.UploadWizardUtil.removeItem( _this._uploadsInProgress, upload );

		if ( result.upload && result.upload.imageinfo && result.upload.imageinfo.descriptionurl ) {
			// success
			mw.log("detailing");
			_this._uploadsEditingDetails.push( upload );
			mw.log("extract info");
			upload.extractImageInfo( result );	
			mw.log("populate");
			upload.details.populate();
		
		} else if ( result.upload && result.upload.sessionkey ) {
			// there was a warning - type error which prevented it from adding the result to the db 
			if ( result.upload.warnings.duplicate ) {
				var duplicates = result.upload.warnings.duplicate;
				_this.details.errorDuplicate( result.upload.sessionkey, duplicates );
			}

			// XXX namespace collision
			// and other errors that result in a stash
		} else if ( 0 /* actual failure */ ) {
			// we may want to tag or otherwise queue it as an upload to retry
		}
	
		_this.updateFileCounts();
	},


	/**
	 * Occurs whenever we need to update the interface based on how many files are there or have transported
	 * Also detects if all uploads have transported and kicks off the process that eventually gets us to Step 2.
	 */
	updateFileCounts: function() {
		mw.log( "update counts" );
		var _this = this;
		$j( '#mwe-upwiz-add-file' ).html( gM( 'mwe-upwiz-add-file-' + ( _this.uploads.length === 0 ? '0' : 'n' )) );
		if ( _this.uploads.length < _this.maxUploads ) {
			$j( '#mwe-upwiz-add-file' ).removeAttr( 'disabled' );
		} else {
			$j( '#mwe-upwiz-add-file' ).attr( 'disabled', true );
		}

		var hasFile;
		for ( var i = 0; i < _this.uploads.length; i++ ) {
			var upload = _this.uploads[i];
			if ( upload.ui.fileInputCtrl.value != "" ) {
				hasFile = true; 
			}
		}
		if ( hasFile ) {
			$j( '#mwe-upwiz-upload-ctrl' ).removeAttr( 'disabled' ); 
		} else {
			$j( '#mwe-upwiz-upload-ctrl' ).attr( 'disabled', 'disabled' ); 
		}

		
		$j( '#mwe-upwiz-count' ).html( gM( 'mwe-upwiz-upload-count', [ _this._uploadsTransported.length, _this.uploads.length ] ) );

		if ( _this.uploads.length > 0 && _this._uploadsTransported.length == _this.uploads.length ) {
			// is this enough to stop the progress monitor?
			_this.isTransported = true;
			// set progress to 100%
			_this.showProgressBar( 1 );
			_this.showRemainingTime( 0 );
			
			// XXX then should make the progress bar not have the animated lines when done. Solid blue, or fade away or something.
			// likewise, the remaining time should disappear, fadeout maybe.
					
			// do some sort of "all done" thing for the UI - advance to next tab maybe.
			_this.moveToTab( 'details' );
		}

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

	//
	// entire METADATA TAB
	//

	/**
	 *
	 */
	createDetails: function() {

	},

	/**
	 *
	 */
	submitDetails: function() {

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

