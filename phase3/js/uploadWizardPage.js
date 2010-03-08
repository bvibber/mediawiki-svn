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
	
	mw.load( 'UploadWizard.UploadWizardTest', function () {

		mw.setDefaultConfig('uploadHandlerClass', null);
		mw.setConfig('userName', wgUserName); 
		mw.setConfig('userLanguage', wgUserLanguage);
		mw.setConfig('fileExtensions', wgFileExtensions);
		mw.setConfig('token', token);
		mw.setConfig('languages', [
				{ code: 'en', name: 'English' },
				{ code: 'fr', name: 'Fran&ccedil;ais' },
				{ code: 'es', name: 'Espagnol' },
			    ]);
		mw.setConfig('thumbnailWidth', 220); // new standard size

		var uploadWizard = new mw.UploadWizard();
		uploadWizard.createInterface(wizardDiv);
	
	});


} );
