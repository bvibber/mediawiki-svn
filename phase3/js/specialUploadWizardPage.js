/*
 * This script is run on [[Special:Upload]].
 * Creates an interface for uploading files in multiple steps, hence "wizard"
 */


mw.ready( function() {
	
	// steal the edit token from the existing page	
	var token = $j( "#wpEditToken" ).val();

	var licensingDiv = $j('<div id="upload-licensing" class="upload-section">Licensing tutorial</div>').get(0);
	var wizardDiv    = $j('<div id="upload-wizard" class="upload-section"></div>').get(0);
	$j('#bodyContent').empty().append(licensingDiv).append(wizardDiv);
	
	// change title on page too -- this doesn't have to be internationalized yet since this is just for testing
	// when it has its own page it will have a real title
	$j('#firstHeading').html("Upload wizard");

	// configure languages
	// mdale does this statically, we'll do this for now since it will have about the same impact as loading another library anyway
	
	mw.load( 'UploadWizard.UploadWizardTest', function () {
		
		mw.setConfig('debug', true); 

		mw.setDefaultConfig('uploadHandlerClass', null);
		mw.setConfig('userName', wgUserName); 
		mw.setConfig('userLanguage', wgUserLanguage);
		mw.setConfig('fileExtensions', wgFileExtensions);
		mw.setConfig('token', token);
		mw.setConfig('thumbnailWidth', 220); // new standard size

		// not for use with all wikis. 
		// The ISO 639 code for the language tagalog is "tl".
		// Normally we name templates for languages by the ISO 639 code.
		// Commons already had a template called 'tl', though.
		// so, this workaround will cause tagalog descriptions to be saved with this template instead.
		mw.setConfig('languageTemplateFixups', { tl: 'tgl' });

		var uploadWizard = new mw.UploadWizard();
		uploadWizard.createInterface(wizardDiv);
	
	});


} );
