mw.addMessages( {
	'mwe-upwiz-tab-file': 'Step 1',
	'mwe-upwiz-tab-details': 'Step 2',
	'mwe-upwiz-tab-thanks': 'Step 3',
	'mwe-upwiz-intro': 'Introductory text (short)',
	'mwe-upwiz-select-files': 'Select files:',
	'mwe-upwiz-add-file-n': 'Add another file',
	'mwe-upwiz-add-file-0': 'Add a file',
	'mwe-upwiz-browse': 'Browse...',
	'mwe-upwiz-transported': 'OK',
	'mwe-upwiz-click-here': 'Click here to select a file',
	'mwe-upwiz-uploading': 'uploading...',
	'mwe-upwiz-remove-upload': 'Remove this file from the list of files to upload',
	'mwe-upwiz-remove-description': 'Remove this description',
	'mwe-upwiz-upload': 'Upload',
	'mwe-upwiz-upload-count': '$1 of $2 files uploaded',
	'mwe-upwiz-progressbar-uploading': 'uploading',
	'mwe-upwiz-remaining': '$1 remaining',
	'mwe-upwiz-intro-details': 'Thank you for uploading your works! Now we need some basic information in order to complete your upload.',
	'mwe-upwiz-provenance-ownwork': 'They are entirely your own work.',
	'mwe-upwiz-provenance-ownwork-assert': 'I, $1, the copyright holder of this work, hereby grant anyone the right to use these works for any purpose, as long as they credit me and share derivative work under the same terms.',
	'mwe-upwiz-provenance-ownwork-assert-note': 'This means you release your work under a double Creative Commons Attribution ShareAlike and GFDL license.',
	'mwe-upwiz-provenance-permission': 'Their author gave you explicit permission to upload them',
	'mwe-upwiz-provenance-website': 'They come from a website',
	'mwe-upwiz-provenance-custom': 'Did you know? You can <a href="$1">customize</a> the default options you see here.',
	'mwe-upwiz-more-options': 'more options...',
	'mwe-upwiz-desc': 'Description in',
	'mwe-upwiz-desc-add-n': 'add a description in another language',
	'mwe-upwiz-desc-add-0': 'add a description',
	'mwe-upwiz-title': 'Title',
	'mwe-upwiz-categories-intro': 'Help people find your works by adding categories',
	'mwe-upwiz-categories-another': 'Add other categories',
	'mwe-upwiz-previously-uploaded': 'This file was previously uploaded to $1 and is already available <a href="$2">here</a>.',
	'mwe-upwiz-about-this-work': 'About this work',
	'mwe-upwiz-media-type': 'Media type',
	'mwe-upwiz-date-created': 'Date created',
	'mwe-upwiz-geotag': 'Location',
	'mwe-upwiz-copyright-info': 'Copyright information',
	'mwe-upwiz-author': 'Author',
	'mwe-upwiz-license': 'License',
	'mwe-upwiz-about-format': 'About the file',
	'mwe-upwiz-autoconverted': 'This file was automatically converted to the $1 format',
	'mwe-upwiz-filename-tag': 'File name:',
	'mwe-upwiz-other': 'Other information',
	'mwe-upwiz-other-prefill': 'Free wikitext field',
	'mwe-upwiz-showall': 'show all',

	'mwe-upwiz-upload-error-bad-filename-extension': 'This wiki does not accept filenames with the extension "$1".',
	'mwe-upwiz-upload-error-duplicate': 'This file was previously uploaded to this wiki.',
	'mwe-upwiz-upload-error-stashed-anyway': 'Post anyway?',
	'mwe-upwiz-ok': 'OK',
	'mwe-upwiz-cancel': 'Cancel',


	// available licenses should be a configuration of the MediaWiki instance,
	// not hardcoded here.
	// but, MediaWiki has no real concept of a License as a first class object -- there are templates and then specially - parsed 
	// texts to create menus -- hack on top of hacks -- a bit too much to deal with ATM
	'mwe-lic-pd': 'Public domain',
	'mwe-lic-cc-0': 'Creative Commons Zero waiver',
	'mwe-lic-cc-by-3.0': 'Creative Commons Attribution 3.0',
	'mwe-lic-cc-by-sa-3.0': 'Creative Commons Attribution ShareAlike 3.0',
	'mwe-lic-gfdl': 'GFDL'
} );


/**
 * Represents the upload -- in its local and remote state. (Possibly those could be separate objects too...)
 * This is our 'model' object if we are thinking MVC. Needs to be better factored, lots of feature envy with the UploadWizard
 */
mw.UploadWizardUpload = function() {
	var _this = this;
	_this._thumbnails = {};
	_this.imageinfo = {};
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

		for (var key in result.upload.imageinfo) {
			_this.imageinfo[key] = result.upload.imageinfo[key];
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
                        'iiurlwidth': width, 
                        'prop':  'imageinfo',
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
						url: 	imageInfo.url
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
mw.UploadWizardUploadInterface = function( filenameAcceptedCb ) {
	var _this = this;

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
		var filename = path.substring( lastFileSeparatorIdx + 1, 10000 );

 		// Capitalise first letter and replace spaces by underscores
 		filename = filename.charAt( 0 ).toUpperCase() + filename.substring( 1, 10000 );
		filename.replace(/ /g, '_' );
		return filename;
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
	_this.description = $j('<textarea name="desc" rows="3" cols="50" class="mwe-upwiz-desc-lang-text"></textarea>').get(0);
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
		return '{{' + _this.languageMenu.value + '|' + _this.description.value + '}}'	
	}
};

/**
 * Object that represents the Details (step 2) portion of the UploadWizard
 * n.b. each upload gets its own details.
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

	_this.descriptionsDiv = $j( '<div class="mwe-upwiz-details-descriptions"></div>' );

	_this.descriptionAdder = $j( '<a id="mwe-upwiz-desc-add"/>' )
					.attr( 'href', '#' )
					.html( gM( 'mwe-upwiz-desc-add-0' ) )
					.click( function( ) { _this.addDescription( ) } );

	_this.descriptionsContainerDiv = 
		$j( '<div class="mwe-upwiz-details-descriptions-container"></div>' )
			.append( $j( '<div class="mwe-upwiz-details-descriptions-title">' + gM( 'mwe-upwiz-desc' ) + '</div>' ) )
			.append( _this.descriptionsDiv )
			.append( $j( '<div class="mwe-upwiz-details-descriptions-add"></div>' )
					.append( _this.descriptionAdder ) );
				

	$j( _this.div )
		.append( _this.macroDiv )
		.append( _this.thumbnailDiv )
		.append( _this.errorDiv )
		.append( $j( _this.dataDiv )
			.append( _this.descriptionsContainerDiv ));
	

	
	// create the basic HTML
	// thumbnail


	// description in [ English ]
	// description field
	// title

	// about this work
	// media type
	// date created
	// location widget

	// copyright info <--- THIS IS THE IMPORTANT BIT
	// Author
	// License

	// About the file...
	
	// Other info
	
	_this.addDescription();
	$j( containerDiv ).append( _this.div );


};

mw.UploadWizardDetails.prototype = {

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
		//_this.setDate();

		//_this.setSource();
		
		//_this.setFilename();

		//_this.setLocation(); // we could be VERY clever with location sensing...
		//_this.setAuthor(); 
	},

	/**
	 *  look up thumbnail info and set it on the form, with loading spinner
	 * @param filename
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
	 * Convert entire details for this file into wikiText, which will then be posted to the file 
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
			'permission' : '',       // leave blank; by default will be "see below"   optional 
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
			information.description += desc.getWikiText();
		} )
	
		var info = '';
		for ( var key in information ) {
			info += '|' + key + '=' + information[key] + "\n";	
		}	

		wikiText += "=={int:filedesc}==\n";

		wikiText += '{{Information\n' + info + '}}\n';
		
		// wikiText += "=={int:license}==\n";
		// XXX get the real one -- usually dual license GFDL / cc - by - sa
		//wikiText += "{{cc-by-sa-3.0}}\n";
		// http://commons.wikimedia.org / wiki / Template:Information

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

		// are we changing the name ( moving the file? ) if so, do that first, and the rest of this submission has to become
		// a callback when that is transported?
			
		// XXX check state of details for okayness ( license selected, at least one desc, sane filename )
		var wikiText = _this.getWikiText();
		mw.log( wikiText );
		// do some api call to edit the info 

		// api.php  ? action = edit & title = Talk:Main_Page & section = new &  summary = Hello%20World & text = Hello%20everyone! & watch &  basetimestamp = 2008 - 03 - 20T17:26:39Z &  token = cecded1f35005d22904a35cc7b736e18%2B%5C
		// caution this may result in a captcha response, which user will have to solve
		// 

		// then, if the filename was changed, do another api call to move the page
		// THIS MAY NOT WORK ON ALL WIKIS. for instance, on Commons, it may be that only admins can move pages. This is another example of how
		//   we need an "incomplete" upload status
		// we are presuming this File page is brand new, so let's not bother with the whole redirection deal. ('noredirect')

		/*
		Note: In this example, all parameters are passed in a GET request just for the sake of simplicity. However, action = move requires POST requests; GET requests will cause an error. Moving Main Pgae ( sic ) and its talk page to Main Page, without creating a redirect
		api.php  ? action = move & from = Main%20Pgae & to = Main%20Page &  reason = Oops,%20misspelling & movetalk & noredirect &  token = 58b54e0bab4a1d3fd3f7653af38e75cb%2B\
		*/


	}	
};



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
		var ui = new mw.UploadWizardUploadInterface( filenameAcceptedCb ); 
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
			_this._uploadsEditingDetails.push( upload );
			upload.extractImageInfo( result );	
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
	}
};


