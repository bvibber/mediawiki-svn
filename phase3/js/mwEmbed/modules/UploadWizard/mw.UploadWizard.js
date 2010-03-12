mw.addMessages({
	'mwe-upwiz-tab-file': 'Step 1',
	'mwe-upwiz-tab-metadata': 'Step 2',
	'mwe-upwiz-tab-thanks': 'Step 3',
	'mwe-upwiz-intro': 'Introductory text (short)',
	'mwe-upwiz-select-files': 'Select files:',
	'mwe-upwiz-add-file-n': 'Add another file',
	'mwe-upwiz-add-file-0': 'Add a file',
	'mwe-upwiz-browse': 'Browse...',
	'mwe-upwiz-completed': 'OK',
	'mwe-upwiz-click-here': 'Click here to select a file',
	'mwe-upwiz-uploading': 'uploading...',
	'mwe-upwiz-remove-upload': 'Remove this file from the list of files to upload',
	'mwe-upwiz-remove-description': 'Remove this description',
	'mwe-upwiz-upload': 'Upload',
	'mwe-upwiz-upload-count': '$1 of $2 files uploaded',
	'mwe-upwiz-progressbar-uploading': 'uploading',
	'mwe-upwiz-remaining': '$1 remaining',
	'mwe-upwiz-intro-metadata': 'Thank you for uploading your works! Now we need some basic information in order to complete your upload.',
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
	// but, MediaWiki has no real concept of a License as a first class object -- there are templates and then specially-parsed 
	// texts to create menus -- hack on top of hacks -- a bit too much to deal with ATM
	'mwe-lic-pd': 'Public domain',
	'mwe-lic-cc-0': 'Creative Commons Zero waiver',
	'mwe-lic-cc-by-3.0': 'Creative Commons Attribution 3.0',
	'mwe-lic-cc-by-sa-3.0': 'Creative Commons Attribution ShareAlike 3.0',
	'mwe-lic-gfdl': 'GFDL'
});



// this interface only works for the wizard... should say so in class name
// XXX there is a CSS/scripting trick to get X-browser consistent file input, plus one-click file input
// It will not work in Netscape 4, Explorer 4, or Netscape 3. 
// We probably don't care, but...  http://www.quirksmode.org/dom/inputfile.html in case we do
mw.UploadWizardUploadInterface = function(filenameAcceptedCb) {
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
				.append(_this.fileInputCtrl)
				.append(_this.visibleFilename)
			).append(_this.filenameCtrl).get(0);

	_this.progressMessage = $j('<span class="mwe-upwiz-status-message" style="display: none"></span>').get(0);

	$j(_this.fileInputCtrl).change( function() { _this.fileChanged() } );

	_this.errorDiv = $j('<div class="mwe-upwiz-upload-error" style="display: none;"></div>').get(0);


	$j(_this.div).append(_this.form)
		    .append(_this.progressMessage)
		    .append(_this.errorDiv);

	// _this.progressBar = (no progress bar for individual uploads yet)
	// add a metadata thing to metadata
};

mw.UploadWizardUploadInterface.prototype = {
	/* start! */
	start: function() {
		var _this = this;
		$j(_this.removeCtrl).hide();
	},

	/* generically busy, but not a fraction. Encoding, or transports that don't know progress */
	busy: function() {
		var _this = this;
		// for now we implement this as looking like "100% progress"
		// e.g. an animated bar that takes up all the space
		_this.progress(1.0);
	},

	/* show progress with a certain fraction */
	progress: function(fraction) {
		var _this = this;
		$j(_this.progressMessage).addClass('mwe-upwiz-status-progress')
		   			 .html(gM( 'mwe-upwiz-uploading' ))
					 .show();
		// update individual progress bar with fraction?
	},

	// this is just completed in the sense that it's all uploaded. There may be other errors?
	// really need to rethink the UI / metadata separation
	completed: function(result) {
		var _this = this;
		$j(_this.progressMessage).removeClass('mwe-upwiz-status-progress')
					 .addClass('mwe-upwiz-status-completed')
		   			 .html(gM( 'mwe-upwiz-completed' ));
	},

	fileChanged: function() {
		var _this = this;
		_this.clearErrors();
		var ext = _this.getExtension();
		if (_this.isGoodExtension(ext)) {
			_this.updateFilename();
		} else {       
			_this.error('bad-filename-extension', ext);
		}
	},

	// this does two things: 
	//   1) since the file input has been hidden with some clever CSS (to avoid x-browser styling issues), 
	//      update the visible filename
	//
	//   2) update the filename desired when added to MediaWiki. This should be RELATED to the filename on the filesystem,
	//      but it should be silently fixed so that it does not trigger uniqueness conflicts. i.e. if server has cat.jpg we change ours to cat_2.jpg.
	//      This is hard to do in a scalable fashion on the client; we don't want to do 12 api calls to get cat_12.jpg. 
	//      Ideally we should ask the SERVER for a decently unique filename related to our own. 
	//	So, at the moment, this is hacked with a guaranteed-unique filename instead.  
	updateFilename: function() {
		var _this = this;
		var path = $j(_this.fileInputCtrl).attr('value');
	
		// visible filename	
		$j(_this.visibleFilename).removeClass('helper').html(path);

		// desired filename 
		var filename = _this.convertPathToFilename(path);
		// this is a hack to get a filename guaranteed unique.
		uniqueFilename = mw.getConfig('userName') + "_" + (new Date()).getTime() + "_" + filename;
		$j(_this.filenameCtrl).attr('value', uniqueFilename);
		_this.filenameAcceptedCb();
	},

	clearErrors: function() {
		var _this = this;
		// XXX this should be changed to something Theme compatible
		$j(_this.div).removeClass('mwe-upwiz-upload-error');
		$j(_this.errorDiv).hide().empty();
	},

	error: function() {
		var _this = this;
		var args = Array.prototype.slice.call(arguments); // copies arguments into a real array
		var msg = 'mwe-upwiz-upload-error-' + args[0];
		$j(_this.errorDiv).append($j('<p class="mwe-upwiz-upload-error">' + gM(msg, args.slice(1)) + '</p>'));
		// apply a error style to entire did
		$j(_this.div).addClass('mwe-upwiz-upload-error');
		$j(_this.errorDiv).show();
	},

	// arguably should be about filename, not path?
	// may check for common bad patterns here, like DSC_NNNNN, filenames too short, too long, etc.
	// steal that from the commons js
	getExtension: function() {
		var _this = this;
		var path = $j(_this.fileInputCtrl).attr('value');
		return path.substr( path.lastIndexOf( '.' ) + 1 ).toLowerCase();
	},

	// XXX this is common utility code
	// used when converting contents of a file input and coming up with a suitable "filename" for mediawiki
	// test: what if path is length 0 
	// what if path is all separators
	// what if path ends with a separator character
	// what if it ends with multiple separator characters
	convertPathToFilename: function(path) {
		if (path === undefined || path == '') {
			return '';
		}
		
 		var lastFileSeparatorIdx = Math.max(path.lastIndexOf( '/' ), path.lastIndexOf( '\\' ));
	 	// lastFileSeparatorIdx is now -1 if no separator found, or some index in the string.
		// so, +1, that is either 0 (beginning of string) or the character after last separator.
		// caution! could go past end of string... need to be more careful
		var filename = path.substring( lastFileSeparatorIdx + 1, 10000 );

 		// Capitalise first letter and replace spaces by underscores
 		filename = filename.charAt( 0 ).toUpperCase() + filename.substring( 1, 10000 );
		filename.replace(/ /g, '_' );
		return filename;
 	},

	// XXX this is common utility code
	// XXX unused, copied because we'll probably need it... stripped from old doDestinationFill
	// this is used when checking for "bad" extensions in a filename. 
	isGoodExtension: function(ext) {
		var _this = this;
		var found = false;
		var extensions = mw.getConfig('fileExtensions');
		if (extensions) {
			for ( var i = 0; i < extensions.length; i++ ) {
				if ( extensions[i].toLowerCase() == ext ) {
					found = true;
				}
			}
		}
		return found;
	}

};	


mw.UploadWizard = function() {

	this.uploadHandlerClass = mw.getConfig('uploadHandlerClass') || this.getUploadHandlerClass();
	this.isCompleted = false;
	
	this.uploads = [];
	// leading underline for privacy. DO NOT TAMPER.
	this._uploadsQueued = [];
	this._uploadsInProgress = [];
	this._uploadsCompleted = [];

	this.uploadsBeginTime = null;	

	return this;
};
	
mw.UploadWizardDescription = function(languageCode) {
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
		       .append(_this.languageMenu)
	               .append(_this.description)
	
};

mw.UploadWizardDescription.prototype = {

	getWikiText: function() {
		var _this = this;
		return '{{' + _this.languageMenu.value + '|' + _this.description.value + '}}'	
	}
};

mw.UploadWizardMetadata = function(containerDiv) {

	var _this = this;
	_this.descriptions = [];

	_this.div = $j('<div class="mwe-upwiz-metadata-file"></div>');

	_this.macroDiv = $j('<div class="mwe-upwiz-macro"></div>')
		.append($j('<input type="submit" value="test edit"/>').click(function() { _this.submit() }));

	_this.thumbnailDiv = $j('<div class="mwe-upwiz-thumbnail"></div>');
	
	_this.errorDiv = $j('<div class="mwe-upwiz-metadata-error"></div>');

	_this.dataDiv = $j('<div class="mwe-upwiz-metadata-data"></div>');

	_this.descriptionsDiv = $j('<div class="mwe-upwiz-metadata-descriptions"></div>');

	_this.descriptionAdder = $j('<a id="mwe-upwiz-desc-add"/>')
					.attr('href', '#')
					.html( gM('mwe-upwiz-desc-add-0') )
					.click( function() { _this.addDescription() } );

	_this.descriptionsContainerDiv = 
		$j('<div class="mwe-upwiz-metadata-descriptions-container"></div>')
			.append( $j('<div class="mwe-upwiz-metadata-descriptions-title">' + gM('mwe-upwiz-desc') + '</div>') )
			.append(_this.descriptionsDiv)
			.append( $j('<div class="mwe-upwiz-metadata-descriptions-add"></div>')
					.append(_this.descriptionAdder) );
				

	$j(_this.div)
		.append(_this.macroDiv)
		.append(_this.thumbnailDiv)
		.append(_this.errorDiv)
		.append($j(_this.dataDiv)
			.append(_this.descriptionsContainerDiv));
	

	
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
	$j(containerDiv).append(_this.div);


};

mw.UploadWizardMetadata.prototype = {

	recountDescriptions: function() {
		var _this = this;
		// if there is some maximum number of descriptions, deal with that here
		$j(_this.descriptionAdder).html( gM('mwe-upwiz-desc-add-' + (_this.descriptions.length == 0 ? '0' : 'n') ) );
	},


	addDescription: function() {
		var _this = this;
		var languageCode = _this.descriptions.length ? mw.Language.UNKNOWN : mw.getConfig('userLanguage');
		var description = new mw.UploadWizardDescription(languageCode);

		description.removeCtrl = $j('<a title="' + gM( 'mwe-upwiz-remove-description') + '" href="#">x</a>')
					.addClass('mwe-upwiz-remove')
					.addClass('mwe-upwiz-remove-desc')
					.click( function() { _this.removeDescription(description) } )
					.get(0);
		$j(description.div).append(description.removeCtrl);

		$j(_this.descriptionsDiv).append(description.div);
		_this.descriptions.push(description);
		_this.recountDescriptions();
	},

	removeDescription: function(description) {
		var _this = this;
		$j(description.div).remove();
		mw.UploadWizardUtil.removeItem(_this.descriptions, description);
		_this.recountDescriptions();
	},

	// this is a lot like upload ui's error -- should merge
	error: function() {
		var _this = this;
		var args = Array.prototype.slice.call(arguments); // copies arguments into a real array
		var msg = 'mwe-upwiz-upload-error-' + args[0];
		$j(_this.errorDiv).append($j('<p class="mwe-upwiz-upload-error">' + gM(msg, args.slice(1)) + '</p>'));
		// apply a error style to entire did
		$j(_this.div).addClass('mwe-upwiz-upload-error');
		$j(_this.dataDiv).hide();
		$j(_this.errorDiv).show();
	},

	// just like error but with ok/cancel
	errorDuplicate: function(sessionKey, duplicates) {
		var _this = this;
		/*
		TODO - do something clever to get page URLs and image URLs 
		var duplicatePageTitles = result.upload.warnings.duplicate;
		var duplicates = [];
		for (var i = 0; i < duplicates.length; i++) {
			imageInfo = mw.getJSON(undefined, 
						 {'titles' : duplicatePageTitles[i], 'prop' : 'imageinfo'})
						 function() { _this.renderUploads() });
			duplicates.push({
				// ?? async, so we should insert later...
			}) 
		}
		*/
		_this.error('duplicate');
		// add placeholder spinners to div, and then fetch the thumbnails and so on async
		// meanwhile...
		//$j(_this.errorDiv).append(
		//	$j('<form></form>'); 
		// same as normal error but you get to ok/cancel, which resubmits with ignore warnings
	},

	// given the API result pull some info into the form (for instance, extracted from EXIF, desired filename)
	populateFromResult: function(result) {
		var _this = this;
		var upload = result.upload;
		mw.log("populating from result");
		_this.setThumbnail(upload.filename, mw.getConfig('thumbnailWidth')); 
		//_this.setSource(upload. result);
		
		//_this.setFilename(upload.filename);

		//_this.setDescription(); // is there anything worthwhile here? image comment?
		//_this.setDate(upload.metadata);	
		//_this.setLocation(upload.metadata); // we could be VERY clever with location sensing...
		//_this.setAuthor(_this.config.user, upload.exif.Copyright);
	},

	// look up thumbnail info and set it 
	setThumbnail: function(filename, width) {
		var _this = this;

		var callback = function(imageInfo) { 
			var thumbnail = $j('<img class="mwe-upwiz-thumbnail"/>').get(0);
			thumbnail.width = imageInfo.thumbwidth;
			thumbnail.height = imageInfo.thumbheight;
			thumbnail.src = imageInfo.thumburl;
			// side effect: will replace thumbnail's loadingSpinner
			_this.thumbnailDiv.html(thumbnail);
		};

		_this.thumbnailDiv.loadingSpinner();
		_this.getThumbnail("File:" + filename, width, callback);

	},

	// use iinfo to get thumbnail info
	// this API method can be used to get a lot of thumbnails at once, but that may not be so useful for us ATM
	// this is mostly ripped off from mw.UploadHandler's doDestCheck, but: stripped of UI, does only one, does not check for name collisions.
	getThumbnail: function(title, width, setThumbnailCb, apiUrl) {

		if (apiUrl === undefined) {
			apiUrl = mw.getLocalApiUrl();
		}

		var params = {
                        'titles': title,
                        'iiurlwidth': width, 
                        'prop':  'imageinfo',
                        'iiprop': 'url|mime|size'
                };

		mw.getJSON(apiUrl, params, function( data ) {
			if ( !data || !data.query || !data.query.pages ) {
				mw.log(" No data? ")
				return;
			}

			if (data.query.pages[-1]) {
				// not found ? error
			}
			for ( var page_id in data.query.pages ) {
				var page = data.query.pages[ page_id ];
				if (! page.imageinfo ) {
					// not found? error
				} else {
					var imageInfo = page.imageinfo[0];
					setThumbnailCb(imageInfo);
				}
			}
		});
	},


	getWikiText: function() {
		var _this = this;
		wikiText = '';
	

		// http://commons.wikimedia.org/wiki/Template:Information
	
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
		if (_this.descriptions.length === 0) {
			// ruh roh
			// we should not even allow them to press the button (?) but then what about the queue...
		}
		$j.each(_this.descriptions, function(i, description) {
			information.description += descriptions.getWikiText();
		})
	
		var info = '';
		for (var key in information) {
			info += '|' + key + '=' + information[key] + "\n";	
		}	

		wikiText += "=={int:filedesc}==\n";

		wikiText += '{{Information\n' + info + '}}\n';
		
		// wikiText += "=={int:license}==\n";
		// XXX get the real one -- usually dual license GFDL/cc-by-sa
		//wikiText += "{{cc-by-sa-3.0}}\n";
		// http://commons.wikimedia.org/wiki/Template:Information

		return wikiText;	
	},

	isReady: function() {
		// somehow, all the various issues discovered with this upload should be present in a single place
		// where we can then check on
		// perhaps as simple as _this.issues or _this.agenda
	},

	submit: function() {
		var _this = this;
		// are we okay to submit?
		// check descriptions

		// are we changing the name (moving the file?) if so, do that first, and the rest of this submission has to become
		// a callback when that is completed?
			
		// XXX check state of metadata for okayness (license selected, at least one desc, sane filename)
		var wikiText = _this.getWikiText();
		mw.log(wikiText);
		// do some api call to edit the info 

		// api.php  ? action=edit & title=Talk:Main_Page & section=new &  summary=Hello%20World & text=Hello%20everyone! & watch &  basetimestamp=2008-03-20T17:26:39Z &  token=cecded1f35005d22904a35cc7b736e18%2B%5C
		// caution this may result in a captcha response, which user will have to solve
		// 

		// then, if the filename was changed, do another api call to move the page
		// THIS MAY NOT WORK ON ALL WIKIS. for instance, on Commons, it may be that only admins can move pages. This is another example of how
		//   we need an "incomplete" upload status
		// we are presuming this File page is brand new, so let's not bother with the whole redirection deal. ('noredirect')

		/*
		Note: In this example, all parameters are passed in a GET request just for the sake of simplicity. However, action=move requires POST requests; GET requests will cause an error. Moving Main Pgae (sic) and its talk page to Main Page, without creating a redirect
		api.php  ? action=move & from=Main%20Pgae & to=Main%20Page &  reason=Oops,%20misspelling & movetalk & noredirect &  token=58b54e0bab4a1d3fd3f7653af38e75cb%2B\
		*/


	}	
};


mw.UploadWizard.prototype = {
	maxUploads: 10,  // XXX get this from config 
	maxSimultaneousUploads: 2,   //  XXX get this from config
	tabs: [ 'file', 'metadata', 'thanks' ],

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

	// let's figure out exactly what we can use.
	//
	getUploadHandlerClass: function() {
		var _this = this;
		for (var i = 0; i < uploadHandlers.length; i++) {
			var klass = mw[uploadHandlers[i]];
			if (klass != undefined && klass.canRun(this.config)) {
				return klass;
			}
		}
		// this should never happen; NullUploadHandler should always work
		return null;
	},
	*/

	// later we will do some testing to see if they can support more advanced UploadHandlers, like 
	// an XHR based one or Firefogg
	getUploadHandlerClass: function() {
		// return mw.MockUploadHandler;
		return mw.ApiUploadHandler;
	},

	// create the basic interface to make an upload in this div
	createInterface: function(div) {
		var _this = this;
		div.innerHTML = 
	
		       '<div id="mwe-upwiz-tabs">'
		       + '<ul>'
		       +   '<li id="mwe-upwiz-tab-file">'     + gM('mwe-upwiz-tab-file')     + '</li>'
		       +   '<li id="mwe-upwiz-tab-metadata">' + gM('mwe-upwiz-tab-metadata') + '</li>'
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
		       + '<div id="mwe-upwiz-tabdiv-metadata">'
		       +   '<div id="mwe-upwiz-metadata-macro"></div>'
		       +   '<div id="mwe-upwiz-metadata-files"></div>'
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
		});

		// add one to start
		_this.addUpload();
		
		// "select" the first tab - highlight, make it visible, hide all others
		_this.moveToTab('file');
	},

	moveToTab: function(selectedTabName) {
		var _this = this;
		for (var i=0; i < _this.tabs.length; i++) {
			tabName = _this.tabs[i];
			var tabDiv = $j('#mwe-upwiz-tabdiv-' + tabName);
			var tab = $j('#mwe-upwiz-tab-' + tabName);
			if (selectedTabName == tabName) {
				tabDiv.show();
				tab.addClass('mwe-upwiz-tab-highlight');
			} else {
				tabDiv.hide();
				tab.removeClass('mwe-upwiz-tab-highlight');
			}
		}
		// XXX possibly select appropriate form field to begin work
	},

	// add an Upload, with controls.
	// XXX study what Mdale is doing to create this file form controls... he has full control over CSS etc and the browsing button.
	addUpload: function() {
		var _this = this;
		var idx = _this.uploads.length;  // or?
		if (idx == _this.maxUploads) {
			return false;
		}

		// could (should?) be an object, but so far it doesn't have its own methods really. 
		// plus, here in UploadWizard, we have additional concepts like metadata...
		var upload = {};

		// API	
		// XXX hardcoded for now. Maybe passed through config or guessed at here.
		// upload.api =  new mw.UploadApiProcessor(function(result) { _this.uploadCompleted);


		// UI
		// originalFilename is the basename of the file on our file system. This is what we really wanted (probably).
		// the system will upload with a temporary filename and we'll get that back from the API return when we upload
		var filenameAcceptedCb = function() {
			_this.updateFileCounts(); 
		};
		var ui = new mw.UploadWizardUploadInterface(filenameAcceptedCb); 
		ui.removeCtrl = $j('<a title="' + gM( 'mwe-upwiz-remove-upload') 
						+ '" href="#" class="mwe-upwiz-remove">x</a>')
					.click( function() { _this.removeUpload(upload) } )
					.get(0);
		$j(ui.div).append(ui.removeCtrl);

		upload.ui = ui;
		// handler -- usually ApiUploadHandler
		upload.handler = new _this.uploadHandlerClass(upload.ui);

		// this is for UI only...
		upload.handler.addProgressCb( function(fraction) { _this.uploadProgress(upload, fraction) } );

		// this is only the UI one, so is the result even going to be there?
		upload.handler.addCompletedCb( function(result) { _this.uploadCompleted(upload, result) } );

		// not sure about this...UI only?
		// this will tell us that at least one of our uploads has had an error -- may change messaging,
		// like, please fix below
		upload.handler.addErrorCb( function(error) { _this.uploadError(upload, error) } );

		// metadata		
		upload.metadata = new mw.UploadWizardMetadata($j('#mwe-upwiz-metadata-files'));


		
		_this.uploads.push(upload);
		
		$j("#mwe-upwiz-files").append(upload.ui.div);




		//$j("#testac").languageMenu();

		// update the uploadUi to add files - we may be over limit 
		_this.updateFileCounts();

	},

	/* Remove an upload from our array of uploads, and the HTML UI 
	   We can remove the HTML UI directly, as jquery will just get the parent.
           We need to grep through the array of uploads, since we don't know the current index. */
	removeUpload: function(upload) {
		var _this = this;
		$j(upload.ui.div).remove();
		$j(upload.metadata.div).remove();
		mw.UploadWizardUtil.removeItem(_this.uploads, upload);
		_this.updateFileCounts();
	},

	// using a second array to iterate, because we will be splicing the main one, _this.uploads
	removeEmptyUploads: function() {
		var _this = this;
		var toRemove = [];
		for (var i = 0; i < _this.uploads.length; i++) {
			if (_this.uploads[i].ui.fileInputCtrl.value == "") {
				toRemove.push(_this.uploads[i]);
			}
		};
		for (var i = 0; i < toRemove.length; i++) {
			_this.removeUpload(toRemove[i]);
		}
	},

	startUploads: function() {
		var _this = this;
		_this.removeEmptyUploads();
		// remove the upload button, and the add file button
		$j('#mwe-upwiz-upload-ctrl').hide();
		$j('#mwe-upwiz-add-file').hide();
		
		// remove ability to change files
		// ideally also hide the "button"... but then we require styleable file input CSS trickery
		// although, we COULD do this just for files already in progress...
	
		// XXX we just want to VISUALLY lock it in -- disabling this seems to remove it from form post	
		// $j('.mwe-upwiz-file').attr('disabled', 'disabled'); 

		// add the upload progress bar, with ETA
		// add in the upload count 
		$j('#mwe-upwiz-progress').show();
	
		_this.uploadsBeginTime = (new Date()).getTime();
		
		var canGetFileSize = (_this.uploadHandlerClass.prototype.getFileSize !== undefined);

		// queue the uploads
		_this.totalWeight = 0;
		for (var i = 0; i < _this.uploads.length; i++) {
			var upload = _this.uploads[i];

			// we may want to do something clever here to detect
			// whether this is a real or dummy weight
			if (canGetFileSize) {
				upload.weight = upload.getFileSize();
			} else {
				upload.weight = 1;
			}
			_this.totalWeight += upload.weight;

			_this._uploadsQueued.push(upload);
		}
		setTimeout( function () { _this._startUploadsQueued(); }, 0 );
	},

	// making this another thread of execution, because we want to avoid any race condition
	// this way, this is the only "thread" that can start uploads
	// it may miss a newly completed upload but it will get it eventually
	_startUploadsQueued: function() {
		var _this = this;
		var uploadsToStart = Math.min(_this.maxSimultaneousUploads - _this._uploadsInProgress.length, _this._uploadsQueued.length);
		mw.log("_startUploadsQueued: should start " + uploadsToStart + " uploads");
		while (uploadsToStart--) {
			var upload = _this._uploadsQueued.shift();
			_this._uploadsInProgress.push(upload);
			upload.handler.start();
		}
		if (_this._uploadsQueued.length) {
			setTimeout( function () { _this._startUploadsQueued(); }, 1000 );
		}
	},


	// could be a spinning loop by itself, but this is annoying to debug
	// and then we'd have to be careful to note the state-transition to completed once and only once. Race conditions.
	showProgress: function() {
		var _this = this;
		if (_this.isCompleted) {
			return;
		}

		//var updateFileCounts = false;
		var fraction = 0;
		for (var i = 0; i < _this.uploads.length; i++) {
			var upload = _this.uploads[i]; 
			mw.log("progress of " + upload.ui.fileInputCtrl.value + " = " + upload.progress);
			fraction += upload.progress * (upload.weight / _this.totalWeight);
		}
		_this.showProgressBar(fraction);
	
		var remainingTime = _this.getRemainingTime(_this.uploadsBeginTime, fraction);
		if (remainingTime !== null) {
			_this.showRemainingTime(remainingTime);
		}
	},
	
	// show the progress bar
	showProgressBar: function(fraction) {		
		$j( '#mwe-upwiz-progress-bar' ).progressbar( 'value', parseInt( fraction * 100 ) );
	},

	// show remaining time for all the uploads
	// remainingTime is in milliseconds
	// XXX should be localized - x hours, x minutes, x seconds
	showRemainingTime: function(remainingTime) { 
		$j( '#mwe-upwiz-etr' ).html( gM( 'mwe-upwiz-remaining', mw.seconds2npt(parseInt(remainingTime / 1000)) ) );
	},


	// XXX should be refactored with the very similar code in mw.UploadInterface.js:updateProgress	
	// returns time in whatever units getTime() returns; presumed milliseconds
	getRemainingTime: function (beginTime, fractionCompleted) {
		if ( beginTime ) {
			var elapsedTime = ( new Date() ).getTime() - beginTime;
			if (fractionCompleted > 0.0 && elapsedTime > 0) { // or some other minimums for good data
				var rate = fractionCompleted / elapsedTime;
				return parseInt( ( 1.0 - fractionCompleted ) / rate ); 
			}
		}
		return null;
	},

	// okay we are in a confusing state here -- are we asking for progress to be stored in the uploadhandler for our perusal or
	// to be explicitly forwarded to us
	uploadProgress: function(upload, progress) {
		mw.log("upload progress is " + progress);
		var _this = this;
		upload.progress = progress;
		_this.showProgress();
	},

	uploadCompleted: function(upload, result) {
		var _this = this;
		_this._uploadsCompleted.push(upload);
		mw.UploadWizardUtil.removeItem(_this._uploadsInProgress, upload);

		if ( result.upload && result.upload.imageinfo && result.upload.imageinfo.descriptionurl ) {
			// success
			setTimeout( function() { 
				upload.metadata.populateFromResult(result); }, 0 );
		
		} else if (result.upload && result.upload.sessionkey) {
			// there was a warning-type error which prevented it from adding the result to the db 
			if (result.upload.warnings.duplicate) {
				var duplicates = result.upload.warnings.duplicate;
				_this.metadata.errorDuplicate(result.upload.sessionkey, duplicates);
			}

			// XXX namespace collision
			// and other errors that result in a stash
		} else if (0 /* actual failure */) {
			// we may want to tag or otherwise queue it as an upload to retry
		}
	
		_this.updateFileCounts();
	},


	// depending on number of file upoads, change link text (and/or disable it)
	// change button disabled/enabled
	updateFileCounts: function() {
		mw.log("update counts");
		var _this = this;
		$j('#mwe-upwiz-add-file').html(gM('mwe-upwiz-add-file-' + (_this.uploads.length === 0 ? '0' : 'n')));
		if (_this.uploads.length < _this.maxUploads) {
			$j('#mwe-upwiz-add-file').removeAttr('disabled');
		} else {
			$j('#mwe-upwiz-add-file').attr('disabled', true);
		}

		var hasFile;
		for (var i = 0; i < _this.uploads.length; i++) {
			var upload = _this.uploads[i];
			if (upload.ui.fileInputCtrl.value != "") {
				hasFile = true; 
			}
		}
		if (hasFile) {
			$j('#mwe-upwiz-upload-ctrl').removeAttr('disabled'); 
		} else {
			$j('#mwe-upwiz-upload-ctrl').attr('disabled', 'disabled'); 
		}

		
		$j('#mwe-upwiz-count').html( gM('mwe-upwiz-upload-count', [ _this._uploadsCompleted.length, _this.uploads.length ]) );

		if (_this.uploads.length > 0 && _this._uploadsCompleted.length == _this.uploads.length) {
			// is this enough to stop the progress monitor?
			_this.isCompleted = true;
			// set progress to 100%
			_this.showProgressBar(1);
			_this.showRemainingTime(0);
			
			// XXX then should make the progress bar not have the animated lines when done. Solid blue, or fade away or something.
			// likewise, the remaining time should disappear, fadeout maybe.
					
			// do some sort of "all done" thing for the UI - advance to next tab maybe.
			_this.moveToTab('metadata');
		}

	},


	pause: function() {

	},

	stop: function() {

	},

	//
	// entire METADATA TAB
	//

	createMetadata: function() {

	},

	submitMetadata: function() {

	},

	

};

// XXX refactor? or, reconsider approach -- this is an awful hack in some ways
// The jQuery way would be to query the DOM for objects, not to keep a separate array hanging around
mw.UploadWizardUtil = {
	removeItem: function(items, item) {
		for (var i = 0; i < items.length; i++) {
			if (items[i] === item) {
				items.splice(i, 1);
			}
		}
	}
};


